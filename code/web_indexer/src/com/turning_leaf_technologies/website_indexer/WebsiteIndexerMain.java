package com.turning_leaf_technologies.website_indexer;

import com.turning_leaf_technologies.config.ConfigUtil;
import com.turning_leaf_technologies.file.JarUtil;
import com.turning_leaf_technologies.logging.LoggingUtil;
import com.turning_leaf_technologies.strings.StringUtils;
import org.apache.logging.log4j.Logger;
import org.apache.solr.client.solrj.impl.BinaryRequestWriter;
import org.apache.solr.client.solrj.impl.ConcurrentUpdateSolrClient;
import org.ini4j.Ini;

import java.sql.*;
import java.util.Date;

public class WebsiteIndexerMain {
	private static Logger logger;

	public static void main(String[] args) {
		String serverName;
		if (args.length == 0) {
			serverName = StringUtils.getInputFromCommandLine("Please enter the server name");
			if (serverName.length() == 0) {
				System.out.println("You must provide the server name as the first argument.");
				System.exit(1);
			}
		} else {
			serverName = args[0];
		}

		String processName = "web_indexer";
		logger = LoggingUtil.setupLogging(serverName, processName);

		//Get the checksum of the JAR when it was started so we can stop if it has changed.
		long myChecksumAtStart = JarUtil.getChecksumForJar(logger, processName, "./" + processName + ".jar");

		while (true) {
			Date startTime = new Date();
			logger.info("Starting " + processName + ": " + startTime.toString());

			// Read the base INI file to get information about the server (current directory/cron/config.ini)
			Ini configIni = ConfigUtil.loadConfigFile("config.ini", serverName, logger);

			//Connect to the aspen database
			Connection aspenConn = connectToDatabase(configIni);

			try {
				String solrPort = configIni.get("Reindex", "solrPort");
				ConcurrentUpdateSolrClient solrUpdateServer = setupSolrClient(solrPort);

				PreparedStatement getSitesToIndexStmt = aspenConn.prepareStatement("SELECT * from website_indexing_settings");
				ResultSet sitesToIndexRS = getSitesToIndexStmt.executeQuery();
				while (sitesToIndexRS.next()) {
					Long websiteId = sitesToIndexRS.getLong("id");
					String websiteName = sitesToIndexRS.getString("name");
					String siteUrl = sitesToIndexRS.getString("siteUrl");
					String pageTitleExpression = sitesToIndexRS.getString("pageTitleExpression");
					String descriptionExpression = sitesToIndexRS.getString("descriptionExpression");
					String searchCategory = sitesToIndexRS.getString("searchCategory");
					String fetchFrequency = sitesToIndexRS.getString("indexFrequency");
					String pathsToExclude = sitesToIndexRS.getString("pathsToExclude");
					long lastFetched = sitesToIndexRS.getLong("lastIndexed");
					boolean fullReload = false;
					boolean needsIndexing = false;
					long currentTime = new Date().getTime() / 1000;
					if (sitesToIndexRS.wasNull() || lastFetched == 0) {
						needsIndexing = true;
						fullReload = true;
					} else {
						//'daily', 'weekly', 'monthly', 'yearly', 'once'
						switch (fetchFrequency) {
							case "hourly": //Legacy, no longer in the interface
							case "daily":
								needsIndexing = lastFetched < (currentTime - 24 * 60 * 60);
								break;
							case "weekly":
								needsIndexing = lastFetched < (currentTime - 7 * 24 * 60 * 60);
								break;
							case "monthly":
								needsIndexing = lastFetched < (currentTime - 30 * 24 * 60 * 60);
								break;
							case "yearly":
								needsIndexing = lastFetched < (currentTime - 3655 * 24 * 60 * 60);
								break;
						}
					}
					if (needsIndexing) {
						WebsiteIndexLogEntry logEntry = createDbLogEntry(websiteName, startTime, aspenConn);
						WebsiteIndexer indexer = new WebsiteIndexer(websiteId, websiteName, searchCategory, siteUrl, pageTitleExpression, descriptionExpression, pathsToExclude, fullReload, logEntry, aspenConn, solrUpdateServer);
						indexer.spiderWebsite();

						//TODO: Update the lastIndex time

						logEntry.setFinished();
					}
				}

				//Index all content entered within Aspen (pages, resources, etc)
				PreparedStatement getBasicPagesStmt = aspenConn.prepareStatement("SELECT count(*) as numBasicPages from web_builder_basic_page");
				ResultSet getBasicPagesRS = getBasicPagesStmt.executeQuery();
				int numBasicPages = 0;
				if (getBasicPagesRS.next()){
					numBasicPages = getBasicPagesRS.getInt("numBasicPages");
				}
				getBasicPagesRS.close();
				getBasicPagesStmt.close();
				PreparedStatement getResourcesStmt = aspenConn.prepareStatement("SELECT count(*) as numResources from web_builder_resource");
				ResultSet getResourcesRS = getResourcesStmt.executeQuery();
				int numResources = 0;
				if (getResourcesRS.next()){
					numResources = getResourcesRS.getInt("numResources");
				}
				getResourcesRS.close();
				getResourcesStmt.close();
				if ((numBasicPages > 0) || (numResources > 0)){
					boolean fullReload = true;
					WebsiteIndexLogEntry logEntry = createDbLogEntry("Web Builder Content", startTime, aspenConn);
					WebBuilderIndexer indexer = new WebBuilderIndexer(fullReload, logEntry, aspenConn, solrUpdateServer);
					indexer.indexContent();
				}

			} catch (SQLException e) {
				logger.error("Error processing websites to index", e);
			}

			//Check to see if the jar has changes, and if so quit
			if (myChecksumAtStart != JarUtil.getChecksumForJar(logger, processName, "./" + processName + ".jar")){
				break;
			}
			//Pause 15 minutes before running the next export
			try {
				Thread.sleep(1000 * 60 * 15);
			} catch (InterruptedException e) {
				logger.info("Thread was interrupted");
			}
		}
	}

	private static ConcurrentUpdateSolrClient setupSolrClient(String solrPort) {
		ConcurrentUpdateSolrClient.Builder solrBuilder = new ConcurrentUpdateSolrClient.Builder("http://localhost:" + solrPort + "/solr/website_pages");
		solrBuilder.withThreadCount(1);
		solrBuilder.withQueueSize(25);
		ConcurrentUpdateSolrClient updateServer = solrBuilder.build();
		updateServer.setRequestWriter(new BinaryRequestWriter());

		return updateServer;
	}

	private static Connection connectToDatabase(Ini configIni) {
		Connection aspenConn = null;
		try {
			String databaseConnectionInfo = ConfigUtil.cleanIniValue(configIni.get("Database", "database_aspen_jdbc"));
			if (databaseConnectionInfo != null) {
				aspenConn = DriverManager.getConnection(databaseConnectionInfo);
			} else {
				logger.error("Aspen database connection information was not provided");
				System.exit(1);
			}

		} catch (Exception e) {
			logger.error("Error connecting to aspen database", e);
			System.exit(1);
		}
		return aspenConn;
	}

	private static WebsiteIndexLogEntry createDbLogEntry(String websiteName, Date startTime, Connection aspenConn) {
		//Remove log entries older than 45 days
		long earliestLogToKeep = (startTime.getTime() / 1000) - (60 * 60 * 24 * 45);
		try {
			int numDeletions = aspenConn.prepareStatement("DELETE from website_index_log WHERE startTime < " + earliestLogToKeep).executeUpdate();
			logger.info("Deleted " + numDeletions + " old log entries");
		} catch (SQLException e) {
			logger.error("Error deleting old log entries", e);
		}

		//Start a log entry
		WebsiteIndexLogEntry logEntry = new WebsiteIndexLogEntry(websiteName, aspenConn, logger);
		logEntry.saveResults();
		return logEntry;
	}
}
