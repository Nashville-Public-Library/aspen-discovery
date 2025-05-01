package com.turning_leaf_technologies.reindexer;

import com.turning_leaf_technologies.encryption.EncryptionUtils;
import com.turning_leaf_technologies.indexing.IndexingUtils;
import com.turning_leaf_technologies.indexing.Scope;
import com.turning_leaf_technologies.strings.AspenStringUtils;
import org.apache.logging.log4j.Logger;
import org.apache.solr.client.solrj.SolrQuery;
import org.apache.solr.client.solrj.SolrServerException;
import org.apache.solr.client.solrj.impl.ConcurrentUpdateHttp2SolrClient;
import org.apache.solr.client.solrj.impl.Http2SolrClient;
import org.apache.solr.client.solrj.response.QueryResponse;
import org.apache.solr.common.SolrDocument;
import org.apache.solr.common.SolrDocumentList;
import org.apache.solr.common.SolrInputDocument;
import org.ini4j.Ini;
import org.json.JSONObject;

import java.io.IOException;
import java.io.InputStream;
import java.net.URL;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.HashMap;
import java.util.HashSet;
import java.util.TreeSet;

class UserListIndexer {
	private Connection dbConn;
	private final Logger logger;
	private ConcurrentUpdateHttp2SolrClient updateServer;
	private Http2SolrClient groupedWorkServer;
	private TreeSet<Scope> scopes;
	private HashMap<Long, Long> librariesByHomeLocation = new HashMap<>();
	private HashMap<Long, String> locationCodesByHomeLocation = new HashMap<>();
	private HashSet<Long> usersThatCanShareLists = new HashSet<>();
	private Http2SolrClient openArchivesServer;
	private final String serverName;
	private final String baseUrl;

	UserListIndexer(String serverName, Ini configIni, Connection dbConn, Logger logger){
		this.serverName = serverName;
		this.dbConn = dbConn;
		this.logger = logger;
		this.baseUrl = configIni.get("Site", "url");
		// Load a list of all list publishers, including those with permissions via patron type.
		try {
			String listPublishersSQL =
				"SELECT DISTINCT userId " +
					"FROM ( " +
						// Users with the permission via directly assigned roles.
						"    SELECT ur.userId " +
						"    FROM user_roles ur " +
						"    INNER JOIN roles r ON ur.roleId = r.roleId " +
						"    INNER JOIN role_permissions rp ON r.roleId = rp.roleId " +
						"    INNER JOIN permissions p ON rp.permissionId = p.id " +
						"    WHERE p.name = 'Include Lists In Search Results' " +
						"    UNION " +
						// Users with the permission via patron type assigned roles.
						"    SELECT u.id as userId " +
						"    FROM user u " +
						"    INNER JOIN ptype pt ON u.patronType = pt.pType " +
						"    INNER JOIN roles r ON pt.assignedRoleId = r.roleId " +
						"    INNER JOIN role_permissions rp ON r.roleId = rp.roleId " +
						"    INNER JOIN permissions p ON rp.permissionId = p.id " +
						"    WHERE p.name = 'Include Lists In Search Results' AND pt.assignedRoleId > 0 " +
					") AS list_publishers";
			PreparedStatement listPublishersStmt = dbConn.prepareStatement(listPublishersSQL);
			ResultSet listPublishersRS = listPublishersStmt.executeQuery();
			while (listPublishersRS.next()){
				usersThatCanShareLists.add(listPublishersRS.getLong(1));
			}
			listPublishersRS.close();
			listPublishersStmt.close();
		}catch (Exception e){
			logger.error("Error loading a list of users with the listPublisher role");
		}

		String solrPort = configIni.get("Index", "solrPort");
		if (solrPort == null || solrPort.isEmpty()) {
			solrPort = configIni.get("Reindex", "solrPort");
			if (solrPort == null || solrPort.isEmpty()) {
				solrPort = "8080";
			}
		}
		String solrHost = configIni.get("Index", "solrHost");
		if (solrHost == null || solrHost.isEmpty()) {
			solrHost = configIni.get("Reindex", "solrHost");
			if (solrHost == null || solrHost.isEmpty()) {
				solrHost = "solr";
			}
		}

		Http2SolrClient http2Client = new Http2SolrClient.Builder().build();
		try {
			updateServer = new ConcurrentUpdateHttp2SolrClient.Builder("http://" + solrHost + ":" + solrPort + "/solr/lists", http2Client)
					.withThreadCount(1)
					.withQueueSize(25)
					.build();
		}catch (OutOfMemoryError e) {
			logger.error("Unable to create solr client, out of memory", e);
			System.exit(-7);
		}
		//Get the search version from system variables
		int searchVersion = 1;
		try {
			PreparedStatement searchVersionStmt = dbConn.prepareStatement("SELECT searchVersion from system_variables");
			ResultSet searchVersionRS = searchVersionStmt.executeQuery();
			if (searchVersionRS.next()){
				searchVersion = searchVersionRS.getInt("searchVersion");
			}
		}catch (Exception e){
			logger.error("Error loading search version", e);
		}
		Http2SolrClient.Builder groupedWorkHttpBuilder;
		if (searchVersion == 1) {
			groupedWorkHttpBuilder = new Http2SolrClient.Builder("http://" + solrHost + ":" + solrPort + "/solr/grouped_works");
		}else{
			groupedWorkHttpBuilder = new Http2SolrClient.Builder("http://" + solrHost + ":" + solrPort + "/solr/grouped_works_v2");
		}
		groupedWorkServer = groupedWorkHttpBuilder.build();

		Http2SolrClient.Builder openArchivesHttpBuilder = new Http2SolrClient.Builder("http://" + solrHost + ":" + solrPort + "/solr/open_archives");
		openArchivesServer = openArchivesHttpBuilder.build();

		scopes = IndexingUtils.loadScopes(dbConn, logger);
	}

	void close() {
		this.dbConn = null;

		try {
			groupedWorkServer.close();
			groupedWorkServer = null;
		}catch (Exception e) {
			logger.error("Error closing grouped work server ", e);
			System.exit(-5);
		}

		try {
			openArchivesServer.close();
			openArchivesServer = null;
		}catch (Exception e) {
			logger.error("Error closing open archives server ", e);
			System.exit(-5);
		}

		try {
			updateServer.close();
			updateServer = null;
		}catch (Exception e) {
			logger.error("Error closing update server ", e);
			System.exit(-5);
		}

		scopes = null;
		librariesByHomeLocation = null;
		locationCodesByHomeLocation = null;
		usersThatCanShareLists = null;
	}

	Long processPublicUserLists(boolean fullReindex, long lastReindexTime, ListIndexingLogEntry logEntry) {
		long numListsProcessed = 0L;
		long numListsIndexed = 0;
		try{
			PreparedStatement listsStmt;
			PreparedStatement numListsStmt;
			if (fullReindex){
				//Delete all lists from the index
				updateServer.deleteByQuery("recordtype:list");
				//Get a list of all public lists
				numListsStmt = dbConn.prepareStatement("select count(id) as numLists from user_list WHERE deleted = 0 AND public = 1 and searchable = 1");
				listsStmt = dbConn.prepareStatement("SELECT user_list.id as id, deleted, public, searchable, title, description, user_list.created, dateUpdated, username, firstname, lastname, IF(user_list.displayListAuthor=0, 'Library Staff', displayName) AS displayName, homeLocationId, user_id from user_list INNER JOIN user on user_id = user.id WHERE public = 1 AND searchable = 1 AND deleted = 0");
			}else{
				//Get a list of all lists that were changed since the last update
				//Have to process all lists because one could have been deleted, made private, or made non-searchable.
				numListsStmt = dbConn.prepareStatement("select count(id) as numLists from user_list WHERE dateUpdated >= ?");
				numListsStmt.setLong(1, lastReindexTime);
				listsStmt = dbConn.prepareStatement("SELECT user_list.id as id, deleted, public, searchable, title, description, user_list.created, dateUpdated, username, firstname, lastname, IF(user_list.displayListAuthor=0, 'Library Staff', displayName) AS displayName, homeLocationId, user_id from user_list INNER JOIN user on user_id = user.id WHERE dateUpdated >= ?");
				listsStmt.setLong(1, lastReindexTime);
			}

			PreparedStatement getTitlesForListStmt = dbConn.prepareStatement("SELECT source, sourceId, notes from user_list_entry WHERE listId = ?");
			PreparedStatement getLibraryForHomeLocation = dbConn.prepareStatement("SELECT libraryId, locationId from location");
			PreparedStatement getCodeForHomeLocation = dbConn.prepareStatement("SELECT code, locationId from location");

			ResultSet librariesByHomeLocationRS = getLibraryForHomeLocation.executeQuery();
			while (librariesByHomeLocationRS.next()){
				librariesByHomeLocation.put(librariesByHomeLocationRS.getLong("locationId"), librariesByHomeLocationRS.getLong("libraryId"));
			}
			librariesByHomeLocationRS.close();

			ResultSet codesByHomeLocationRS = getCodeForHomeLocation.executeQuery();
			while (codesByHomeLocationRS.next()){
				locationCodesByHomeLocation.put(codesByHomeLocationRS.getLong("locationId"), codesByHomeLocationRS.getString("code"));
			}
			codesByHomeLocationRS.close();

			ResultSet allPublicListsRS = listsStmt.executeQuery();
			ResultSet numListsRS = numListsStmt.executeQuery();
			if (numListsRS.next()){
				logEntry.setNumLists(numListsRS.getInt("numLists"));
			}

			while (allPublicListsRS.next()){
				if (updateSolrForList(fullReindex, updateServer, getTitlesForListStmt, allPublicListsRS, lastReindexTime, logEntry)){
					numListsIndexed++;
				}
				if (numListsIndexed % 500 == 0) {
					if (!fullReindex) {
						updateServer.commit(false, false, true);
					}
					logEntry.saveResults();
				}
				numListsProcessed++;
			}
			if (numListsProcessed > 0){
				logEntry.addNote("Calling final commit");
				logEntry.saveResults();
				updateServer.commit(false, false, true);
			}

		} catch (IOException e) {
			logEntry.incErrors("Error processing public lists quitting", e);
			System.exit(-8);
		}catch (Exception e){
			logger.error("Error processing public lists", e);
		}
		logger.debug("Indexed lists: processed " + numListsProcessed + " indexed " + numListsIndexed);
		return numListsProcessed;
	}

	private boolean updateSolrForList(boolean fullReindex, ConcurrentUpdateHttp2SolrClient updateServer, PreparedStatement getTitlesForListStmt, ResultSet allPublicListsRS, long lastReindexTime, ListIndexingLogEntry logEntry) throws SQLException, SolrServerException, IOException {
		UserListSolr userListSolr = new UserListSolr(this);
		long listId = allPublicListsRS.getLong("id");

		int deleted = allPublicListsRS.getInt("deleted");
		int isPublic = allPublicListsRS.getInt("public");
		int isSearchable = allPublicListsRS.getInt("searchable");
		long userId = allPublicListsRS.getLong("user_id");
		boolean indexed = false;
		if (!fullReindex && (deleted == 1 || isPublic == 0 || isSearchable == 0)) {
			updateServer.deleteByQuery("id:" + listId);
			logEntry.incDeleted();
		} else {
			logger.info("Processing list " + listId + " " + allPublicListsRS.getString("title"));
			userListSolr.setId(listId);
			userListSolr.setTitle(allPublicListsRS.getString("title"));
			userListSolr.setDescription(allPublicListsRS.getString("description"));
			long created = allPublicListsRS.getLong("created");
			long dateUpdated = allPublicListsRS.getLong("dateUpdated");
			userListSolr.setCreated(created);
			userListSolr.setDateUpdated(dateUpdated);

			try {
				String displayName = EncryptionUtils.decryptString(allPublicListsRS.getString("displayName"), serverName, logEntry);
				String firstName = EncryptionUtils.decryptString(allPublicListsRS.getString("firstname"), serverName, logEntry);
				String lastName = EncryptionUtils.decryptString(allPublicListsRS.getString("lastname"), serverName, logEntry);
				String userName = allPublicListsRS.getString("username");

				if (userName.equalsIgnoreCase("nyt_user")) {
					userListSolr.setOwnerCanShareListsInSearchResults(true);
				} else {
					userListSolr.setOwnerCanShareListsInSearchResults(usersThatCanShareLists.contains(userId));
				}
				if (displayName != null && !displayName.isEmpty()) {
					userListSolr.setAuthor(displayName);
				} else {
					if (firstName == null) firstName = "";
					if (lastName == null) lastName = "";
					String firstNameFirstChar = "";
					if (!firstName.isEmpty()) {
						firstNameFirstChar = firstName.charAt(0) + ". ";
					}
					userListSolr.setAuthor(firstNameFirstChar + lastName);
				}

				long patronHomeLibrary = allPublicListsRS.getLong("homeLocationId");
				if (librariesByHomeLocation.containsKey(patronHomeLibrary)) {
					userListSolr.setOwningLibrary(librariesByHomeLocation.get(patronHomeLibrary));
				} else {
					//Don't know the owning library for some reason, most likely this is an admin user.
					userListSolr.setOwningLibrary(-1);
				}

				//Don't know the owning location
				userListSolr.setOwningLocation(locationCodesByHomeLocation.getOrDefault(patronHomeLibrary, ""));

				//Get information about all the list titles.
				getTitlesForListStmt.setLong(1, listId);
				ResultSet allTitlesRS = getTitlesForListStmt.executeQuery();
				PreparedStatement getListDisplayNameAndAuthorStmt = dbConn.prepareStatement("SELECT title, displayName FROM user_list INNER JOIN user ON user_id = user.id WHERE user_list.id = ?");
				while (allTitlesRS.next()) {
					String source = allTitlesRS.getString("source");
					String sourceId = allTitlesRS.getString("sourceId");
					if (!allTitlesRS.wasNull()) {
						if (!sourceId.isEmpty() && source.equals("GroupedWork")) {
							// Skip archive object Ids
							SolrQuery query = new SolrQuery();
							query.setQuery("id:" + sourceId);
							query.setFields("title_display", "author_display");

							try {
								QueryResponse response = groupedWorkServer.query(query);
								SolrDocumentList results = response.getResults();
								//Should only ever get one response
								if (!results.isEmpty()) {
									SolrDocument curWork = results.get(0);
									userListSolr.addListTitle("grouped_work", sourceId, curWork.getFieldValue("title_display"), curWork.getFieldValue("author_display"));
								}
							} catch (Exception e) {
								logger.error("Error loading information about title " + sourceId);
							}
						} else if (source.equals("OpenArchives")) {
							// Skip archive object Ids
							SolrQuery query = new SolrQuery();
							query.setQuery("id:" + sourceId);
							query.setFields("title", "creator");

							try {
								QueryResponse response = openArchivesServer.query(query);
								SolrDocumentList results = response.getResults();
								//Should only ever get one response
								if (!results.isEmpty()) {
									SolrDocument curWork = results.get(0);
									userListSolr.addListTitle("open_archives", sourceId, curWork.getFieldValue("title"), curWork.getFieldValue("creator"));
								}
							} catch (Exception e) {
								logger.error("Error loading information about title " + sourceId);
							}
						} else if (source.equals("Lists")) {
							getListDisplayNameAndAuthorStmt.setString(1, sourceId);
							ResultSet listDisplayNameAndAuthorRS = getListDisplayNameAndAuthorStmt.executeQuery();
							if (listDisplayNameAndAuthorRS.next()) {
								String decryptedName = EncryptionUtils.decryptString(listDisplayNameAndAuthorRS.getString("displayName"), serverName, logEntry);
								userListSolr.addListTitle("lists", sourceId, listDisplayNameAndAuthorRS.getString("title"), decryptedName);
							}
							listDisplayNameAndAuthorRS.close();
						} else if (source.equals("EbscoEds")) {
							//Get title and author with a JSON request
							URL getTitleAuthorUrl = new URL(baseUrl + "/EBSCO/JSON?method=getTitleAuthor&id=" + sourceId);
							Object titleAuthorRaw = getTitleAuthorUrl.getContent();
							if (titleAuthorRaw instanceof InputStream) {
								String titleAuthorJson = AspenStringUtils.convertStreamToString((InputStream) titleAuthorRaw);
								JSONObject titleAuthorResult = new JSONObject(titleAuthorJson);
								if (titleAuthorResult.getBoolean("success")) {
									userListSolr.addListTitle(source, sourceId, titleAuthorResult.getString("title"), titleAuthorResult.getString("author"));
								}
							}
						} else if (source.equals("Ebscohost")) {
							//Get title and author with a JSON request
							URL getTitleAuthorUrl = new URL(baseUrl + "/EBSCOhost/JSON?method=getTitleAuthor&id=" + sourceId);
							Object titleAuthorRaw = getTitleAuthorUrl.getContent();
							if (titleAuthorRaw instanceof InputStream) {
								String titleAuthorJson = AspenStringUtils.convertStreamToString((InputStream) titleAuthorRaw);
								JSONObject titleAuthorResult = new JSONObject(titleAuthorJson);
								if (titleAuthorResult.getBoolean("success")) {
									userListSolr.addListTitle(source, sourceId, titleAuthorResult.getString("title"), titleAuthorResult.getString("author"));
								}
							}
						} else if (source.equals("Summon")) {
							//Get title and authoe with a JSON request
							URL getTitleAuthorUrl = new URL(baseUrl + "/Summon/JSON?method=getTitleAuthor&id=" + sourceId);
							Object titleAuthorRaw = getTitleAuthorUrl.getContent();
							if (titleAuthorRaw instanceof InputStream) {
								String titleAuthorJson = AspenStringUtils.convertStreamToString((InputStream) titleAuthorRaw);
								JSONObject titleAuthorResult = new JSONObject(titleAuthorJson);
								if (titleAuthorResult.getBoolean("success")) {
									userListSolr.addListTitle(source, sourceId, titleAuthorResult.getString("title"), titleAuthorResult.getString("author"));
								}
							} else {
								logEntry.incErrors("Unhandled source " + source);
							}
							//TODO: Handle other types of objects within a User List
							//people, etc.
						}
					}
				}
				getListDisplayNameAndAuthorStmt.close();
				if (userListSolr.getNumTitles() >= 3) {
					// Index in the solr catalog
					SolrInputDocument document = userListSolr.getSolrDocument();
					if (document != null) {
						updateServer.add(document);
						if (created > lastReindexTime) {
							logEntry.incAdded();
						} else {
							logEntry.incUpdated();
						}
						indexed = true;
					} else {
						updateServer.deleteByQuery("id:" + listId);
						logEntry.incSkipped();
					}
				} else {
					updateServer.deleteByQuery("id:" + listId);
					logEntry.incSkipped();
				}
			} catch (Exception e) {
				updateServer.deleteByQuery("id:" + listId);
				logEntry.addNote("Could not decrypt user information for " + listId + " - " + e);
				logEntry.incSkipped();
			}

		}
		return indexed;
	}
	TreeSet<Scope> getScopes() {
		return this.scopes;
	}
}
