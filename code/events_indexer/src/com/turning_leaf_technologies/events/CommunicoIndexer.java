package com.turning_leaf_technologies.events;

import com.turning_leaf_technologies.strings.AspenStringUtils;
import org.apache.commons.codec.binary.Base64;
import org.apache.http.HttpEntity;
import org.apache.http.StatusLine;
import org.apache.http.client.config.RequestConfig;
import org.apache.http.client.methods.CloseableHttpResponse;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.CloseableHttpClient;
import org.apache.http.impl.client.HttpClientBuilder;
import org.apache.http.impl.client.HttpClients;
import org.apache.http.util.EntityUtils;
import org.apache.logging.log4j.Logger;
import org.apache.solr.client.solrj.SolrServerException;
import org.apache.solr.client.solrj.impl.BaseHttpSolrClient;
import org.apache.solr.client.solrj.impl.ConcurrentUpdateHttp2SolrClient;
import org.apache.solr.common.SolrInputDocument;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import javax.net.ssl.HttpsURLConnection;
import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.SocketTimeoutException;
import java.net.URL;
import java.nio.charset.StandardCharsets;
import java.sql.*;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.time.LocalDate;
import java.util.*;
import java.util.Date;
import java.util.zip.CRC32;

import static java.util.Calendar.YEAR;

class CommunicoIndexer {
	private final Logger logger;
	private final long settingsId;
	private final String name;
	private String baseUrl;
	private final String clientId;
	private final String clientSecret;
	private final int numberOfDaysToIndex;
	private final long lastUpdateOfAllEvents;
	private boolean runFullIndexCommunico = false;

	private final Connection aspenConn;
	private final EventsIndexerLogEntry logEntry;
	private final HashMap<String, CommunicoEvent> existingEvents = new HashMap<>();
	private final HashSet<String> librariesToShowFor = new HashSet<>();
	private final static CRC32 checksumCalculator = new CRC32();

	//Communico API Info
	private String communicoAPIToken;
	private String communicoAPITokenType;
	private long communicoAPIExpiration;

	private PreparedStatement addEventStmt;
	private PreparedStatement deleteEventStmt;
	private PreparedStatement addRegistrantStmt;
	private PreparedStatement deleteRegistrantStmt;

	private final Long startTimeForLogging;


	private final ConcurrentUpdateHttp2SolrClient solrUpdateServer;

	CommunicoIndexer(long settingsId, String name, String baseUrl, String clientId, String clientSecret, int numberOfDaysToIndex , long lastUpdateOfAllEvents, ConcurrentUpdateHttp2SolrClient solrUpdateServer, Connection aspenConn, Logger logger) {
		this.settingsId = settingsId;
		this.name = name;
		this.baseUrl = baseUrl;
		this.logger = logger;
		if (this.baseUrl.endsWith("/")) {
			this.baseUrl = this.baseUrl.substring(0, this.baseUrl.length() - 1);
		}
		if (this.baseUrl.endsWith("events")) {
			this.baseUrl = this.baseUrl.substring(0, this.baseUrl.length() - 1);
		}
		this.clientId = clientId;
		this.clientSecret = clientSecret;
		this.aspenConn = aspenConn;
		this.solrUpdateServer = solrUpdateServer;
		this.numberOfDaysToIndex = numberOfDaysToIndex;
		this.lastUpdateOfAllEvents = lastUpdateOfAllEvents;

		logEntry = new EventsIndexerLogEntry("Communico " + name, aspenConn, logger);
		Date startTime = new Date();
		startTimeForLogging = startTime.getTime() / 1000;

		try {
			addEventStmt = aspenConn.prepareStatement("INSERT INTO communico_events SET settingsId = ?, externalId = ?, title = ?, rawChecksum =?, rawResponse = ?, deleted = 0 ON DUPLICATE KEY UPDATE title = VALUES(title), rawChecksum = VALUES(rawChecksum), rawResponse = VALUES(rawResponse), deleted = 0", Statement.RETURN_GENERATED_KEYS);
			deleteEventStmt = aspenConn.prepareStatement("UPDATE communico_events SET deleted = 1 where id = ?");

			PreparedStatement getLibraryScopesStmt = aspenConn.prepareStatement("SELECT subdomain from library inner join library_events_setting on library.libraryId = library_events_setting.libraryId WHERE settingSource = 'communico' AND settingId = ?");
			getLibraryScopesStmt.setLong(1, settingsId);
			ResultSet getLibraryScopesRS = getLibraryScopesStmt.executeQuery();
			while (getLibraryScopesRS.next()){
				librariesToShowFor.add(getLibraryScopesRS.getString("subdomain").toLowerCase());
			}

		} catch (Exception e) {
			logEntry.incErrors("Error setting up statements ", e);
		}

		try {
			//noinspection SpellCheckingInspection
			addRegistrantStmt = aspenConn.prepareStatement("INSERT INTO user_events_registrations SET userId = ?, userBarcode = ?, sourceId = ?, waitlist = 0", Statement.RETURN_GENERATED_KEYS);
			deleteRegistrantStmt = aspenConn.prepareStatement("DELETE FROM user_events_registrations WHERE userId = ? AND sourceId = ?");
		} catch (Exception e) {
			logEntry.incErrors("Error setting up registration statements ", e);
		}

		loadExistingEvents();
	}

	private void loadExistingEvents() {
		try {
			PreparedStatement eventsStmt = aspenConn.prepareStatement("SELECT * from communico_events WHERE settingsId = ? and deleted = 0");
			eventsStmt.setLong(1, this.settingsId);
			ResultSet existingEventsRS = eventsStmt.executeQuery();
			while (existingEventsRS.next()) {
				CommunicoEvent event = new CommunicoEvent(existingEventsRS);
				existingEvents.put(event.getExternalId(), event);
			}
		} catch (SQLException e) {
			logEntry.incErrors("Error loading existing events for Communico " + name, e);
		}
	}

	private HashMap<Long, EventRegistrations> loadExistingRegistrations(String sourceId) {
		HashMap<Long, EventRegistrations> existingRegistrations = new HashMap<>();
		try {
			PreparedStatement regStmt = aspenConn.prepareStatement("SELECT * from user_events_registrations WHERE sourceId = ?");
			regStmt.setString(1, sourceId);
			ResultSet existingRegistrationsRS = regStmt.executeQuery();
			while (existingRegistrationsRS.next()) {
				EventRegistrations communicoRegistrations = new EventRegistrations(existingRegistrationsRS);
				existingRegistrations.put(communicoRegistrations.getUserId(), communicoRegistrations);
			}
		} catch (SQLException e) {
			logEntry.incErrors("Error loading existing registrations for Communico " + name, e);
		}
		return existingRegistrations;
	}

	private final SimpleDateFormat dateParser = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
	private final SimpleDateFormat eventDayFormatter = new SimpleDateFormat("yyyy-MM-dd");
	private final SimpleDateFormat eventWeekFormatter = new SimpleDateFormat("yyyy-ww");
	private final SimpleDateFormat eventMonthFormatter = new SimpleDateFormat("yyyy-MM");
	private final SimpleDateFormat eventYearFormatter = new SimpleDateFormat("yyyy");

	void indexEvents() {
		GregorianCalendar nextYear = new GregorianCalendar();
		nextYear.setTime(new Date());
		nextYear.add(YEAR, 1);
		JSONArray communicoEvents = getCommunicoEvents();
		if (communicoEvents == null) {
			logEntry.incErrors("Did not get any events returned from the Communico API");
			return;
		}

		if (runFullIndexCommunico){
			try {
				solrUpdateServer.deleteByQuery("type:event_communico AND source:" + this.settingsId);
				//3-19-2019 Don't commit so the index does not get cleared during run (but will clear at the end).
			} catch (BaseHttpSolrClient.RemoteSolrException rse) {
				logEntry.incErrors("Solr is not running properly, try restarting " + rse);
				System.exit(-1);
			} catch (Exception e) {
				logEntry.incErrors("Error deleting from index ", e);
			}
		}

		Date lastDateToIndex = new Date();
		long numberOfDays = numberOfDaysToIndex * 24L;
		lastDateToIndex.setTime(lastDateToIndex.getTime() + (numberOfDays * 60 * 60 * 1000));

		logEntry.incNumEvents(communicoEvents.length());
		for (int i = 0; i < communicoEvents.length(); i++){
			try {
				JSONObject curEvent = communicoEvents.getJSONObject(i);
				checksumCalculator.reset();
				String rawResponse = curEvent.toString();
				checksumCalculator.update(rawResponse.getBytes());
				long checksum = checksumCalculator.getValue();

				int eventIdRaw = curEvent.getInt("eventId");
				String eventId = Integer.toString(eventIdRaw);

				boolean eventExists = existingEvents.containsKey(eventId);

				String sourceId = "communico_" + settingsId + "_" + eventId;

				//Add the event to solr
				try {
					SolrInputDocument solrDocument = new SolrInputDocument();
					solrDocument.addField("id", sourceId);
					solrDocument.addField("identifier", eventId);
					solrDocument.addField("type", "event_communico");
					solrDocument.addField("source", settingsId);
					solrDocument.addField("url", baseUrl + "/" + eventId);
					int boost = 1;

					solrDocument.addField("last_indexed", new Date());

					//Make sure the start date is within the range of dates we are indexing
					Date startDate = getDateForKey(curEvent,"eventStart");
					solrDocument.addField("start_date", startDate);
					if (startDate == null || startDate.after(lastDateToIndex)) {
						continue;
					}

					solrDocument.addField("start_date_sort", startDate.getTime() / 1000);
					Date endDate = getDateForKey(curEvent,"eventEnd");
					solrDocument.addField("end_date", endDate);

					//Only add events for the next year
					if (startDate.after(nextYear.getTime())){
						continue;
					}
					HashSet<String> eventDays = new HashSet<>();
					HashSet<String> eventWeeks = new HashSet<>();
					HashSet<String> eventMonths = new HashSet<>();
					HashSet<String> eventYears = new HashSet<>();
					Date tmpDate = (Date)startDate.clone();

					if (tmpDate.equals(endDate) || tmpDate.after(endDate)){
						eventDays.add(eventDayFormatter.format(tmpDate));
						eventWeeks.add(eventWeekFormatter.format(tmpDate));
						eventMonths.add(eventMonthFormatter.format(tmpDate));
						eventYears.add(eventYearFormatter.format(tmpDate));
					}else {
						while (tmpDate.before(endDate)) {
							eventDays.add(eventDayFormatter.format(tmpDate));
							eventWeeks.add(eventWeekFormatter.format(tmpDate));
							eventMonths.add(eventMonthFormatter.format(tmpDate));
							eventYears.add(eventYearFormatter.format(tmpDate));
							tmpDate.setTime(tmpDate.getTime() + 24 * 60 * 60 * 1000);
						}
					}
					//Boost based on start date, we will give preference to anything in the next 30 days
					Date today = new Date();
					if (startDate.before(today) || startDate.equals(today)){
						boost += 30;
					}else{
						long daysInFuture = (startDate.getTime() - today.getTime()) / (1000 * 60 * 60 * 24);
						if (daysInFuture > 30){
							daysInFuture = 30;
						}
						boost += (int) (30 - daysInFuture);
					}
					solrDocument.addField("event_day", eventDays);
					solrDocument.addField("event_week", eventWeeks);
					solrDocument.addField("event_month", eventMonths);
					solrDocument.addField("event_year", eventYears);

					if (curEvent.getString("subTitle").isEmpty() || curEvent.isNull("subTitle")){
						solrDocument.addField("title", curEvent.getString("title"));
					}else {
						//Important info is kept in subtitle, concat main title and subtitle to keep the important info
						String fullTitle = curEvent.getString("title") + ": " + curEvent.getString("subTitle");
						solrDocument.addField("title", fullTitle);
					}

					solrDocument.addField("branch", AspenStringUtils.trimTrailingPunctuation(curEvent.getString("locationName")));

					if (curEvent.isNull("eventType")) {
						solrDocument.addField("event_type", "Undefined");
					} else {
						solrDocument.addField("event_type", curEvent.getString("eventType"));
					}

					//roomName returns null instead of empty string, need to check if null
					if (curEvent.isNull("roomName")){
						solrDocument.addField("room", "");
					}else{
						solrDocument.addField("room", AspenStringUtils.trimTrailingPunctuation(curEvent.getString("roomName")));
					}

					solrDocument.addField("age_group", getNameStringsForKeyCommunico(curEvent, "ages"));
					solrDocument.addField("program_type", getNameStringsForKeyCommunico(curEvent, "types"));
					//solrDocument.addField("internal_category", getNameStringsForKeyCommunico(curEvent, "searchTags"));

					solrDocument.addField("registration_required", curEvent.getBoolean("registration") ? "Yes" : "No");

					solrDocument.addField("description", curEvent.getString("shortDescription"));

					//eventImage returns null instead of empty string, need to check if null
					if (curEvent.isNull("eventImage")){
						solrDocument.addField("image_url", "");
					}else {
						solrDocument.addField("image_url", curEvent.getString("eventImage"));
					}

					solrDocument.addField("library_scopes", librariesToShowFor);

					if (boost < 1){
						boost = 1;
					}
					solrDocument.addField("boost", boost);

					solrUpdateServer.add(solrDocument);
				} catch (SolrServerException | IOException e) {
					logEntry.incErrors("Error adding event to solr ", e);
				}

				//Add the event to the database
				try {
					addEventStmt.setLong(1, settingsId);
					addEventStmt.setString(2, eventId);
					addEventStmt.setString(3, curEvent.getString("title"));
					addEventStmt.setLong(4, checksum);
					addEventStmt.setString(5, rawResponse);
					addEventStmt.executeUpdate();
				} catch (SQLException e) {
					logEntry.incErrors("Error adding event to database " , e);
				}

				if (eventExists){
					existingEvents.remove(eventId);
					logEntry.incUpdated();
				}else{
					logEntry.incAdded();
				}

				logger.warn("Fetching registration info for event " + eventId);
				//Fetch registrations here and add to DB - for events that require registration ONLY
				if (curEvent.getBoolean("registration") && curEvent.getInt("totalRegistrants") != 0){
					JSONArray communicoEventRegistrants = getRegistrations(Integer.valueOf(eventId));
					HashMap<Long, EventRegistrations> registrationsForEvent = loadExistingRegistrations(sourceId);

					HashSet<String> uniqueBarcodesRegistered = new HashSet<>();
					if (communicoEventRegistrants != null) {
						for (int j = 0; j < communicoEventRegistrants.length(); j++) {
							try {
								JSONObject curRegistrant = communicoEventRegistrants.getJSONObject(j);

								if (!curRegistrant.isNull("librarycard") && !curRegistrant.getString("librarycard").isEmpty()){
									uniqueBarcodesRegistered.add(curRegistrant.getString("librarycard"));
								}
							} catch (JSONException e) {
								logEntry.incErrors("Error getting JSON information ", e);
							}
						}

						for (String uniqueBarcodeRegistered : uniqueBarcodesRegistered) {
							try {
								PreparedStatement getUserIdStmt = aspenConn.prepareStatement("SELECT id FROM user WHERE cat_username = ?");
								getUserIdStmt.setString(1, uniqueBarcodeRegistered);
								ResultSet getUserIdRS = getUserIdStmt.executeQuery();
								while (getUserIdRS.next()) {
									long userId = getUserIdRS.getLong("id");
									if (registrationsForEvent.containsKey(userId)) {
										registrationsForEvent.remove(userId);
									} else {
										addRegistrantStmt.setLong(1, userId);
										addRegistrantStmt.setString(2, uniqueBarcodeRegistered);
										addRegistrantStmt.setString(3, sourceId);
										addRegistrantStmt.executeUpdate();
									}
								}
							} catch (SQLException e) {
								logEntry.incErrors("Error adding registrant info to database ", e);
							}
						}

						for(EventRegistrations registrantInfo : registrationsForEvent.values()){
							try {
								deleteRegistrantStmt.setLong(1, registrantInfo.getUserId());
								deleteRegistrantStmt.setString(2, registrantInfo.getSourceId());
								deleteRegistrantStmt.executeUpdate();
							}catch (SQLException e) {
								logEntry.incErrors("Error deleting registration info ", e);
							}
						}
					}
				}
			} catch (JSONException e) {
				logEntry.incErrors("Error getting JSON information ", e);
			}
		}

		if (runFullIndexCommunico){
			logger.warn("Checking for duplicates of events");
			for(CommunicoEvent eventInfo : existingEvents.values()){
				try {
					deleteEventStmt.setLong(1, eventInfo.getId());
					deleteEventStmt.executeUpdate();
				} catch (SQLException e) {
					logEntry.incErrors("Error deleting event ", e);
				}
				try {
					solrUpdateServer.deleteById("communico_" + settingsId + "_" + eventInfo.getExternalId());
				} catch (Exception e) {
					logEntry.incErrors("Error deleting event by id ", e);
				}
				logEntry.incDeleted();
			}
		}

		logger.warn("Updating solr");
		try {
			solrUpdateServer.commit(false, false, true);
		} catch (Exception e) {
			logEntry.incErrors("Error in final commit while finishing extract, shutting down", e);
			logEntry.setFinished();
			logEntry.saveResults();
			System.exit(-3);
		}

		logEntry.addNote("Indexing Finished");
		logEntry.setFinished();
	}

	private Date getDateForKey(JSONObject curEvent, String keyName) {
		if (curEvent.isNull(keyName)) {
			return null;
		} else {
			String date = curEvent.getString(keyName);
			try {
				return dateParser.parse(date);
			} catch (ParseException e) {
				logEntry.incErrors("Error parsing date " + date, e);
				return null;
			}
		}
	}

	private HashSet<String> getNameStringsForKeyCommunico(JSONObject curEvent, String keyName) {
		HashSet<String> values = new HashSet<>();
		if (!curEvent.isNull(keyName)){
			JSONArray keyArray = curEvent.getJSONArray(keyName);
			for (int i = 0; i < keyArray.length(); i++){
				values.add(keyArray.getString(i));
			}
		}
		return values;
	}

	private boolean connectToCommunico() throws SocketTimeoutException {
		//Authentication documentation: http://communicocollege.com/1137

		//Check to see if we already have a valid token
		if (communicoAPIToken != null){
			if (communicoAPIExpiration - new Date().getTime() > 0){
				logEntry.addNote("token is still valid");
				return true;
			}else{
				logEntry.incErrors("Token has expired");
			}
		}
		//Connect to the API to get our token
		HttpURLConnection conn;
		try {
			URL emptyIndexURL = new URL("https://api.communico.co/v3/token");
			conn = (HttpURLConnection) emptyIndexURL.openConnection();
			if (conn instanceof HttpsURLConnection) {
				HttpsURLConnection sslConn = (HttpsURLConnection) conn;
				sslConn.setHostnameVerifier((hostname, session) -> {
					//Do not verify host names
					return true;
				});
			}
			conn.setRequestMethod("POST");
			conn.setRequestProperty("Content-Type", "application/x-www-form-urlencoded;charset=UTF-8");
			String encoded = Base64.encodeBase64String((clientId + ":" + clientSecret).getBytes());
			conn.setRequestProperty("Authorization", "Basic " + encoded);
			conn.setRequestProperty("Host", "api.communico.co");
			conn.setReadTimeout(30000);
			conn.setConnectTimeout(30000);
			conn.setDoOutput(true);

			OutputStreamWriter wr = new OutputStreamWriter(conn.getOutputStream(), StandardCharsets.UTF_8);
			wr.write("grant_type=client_credentials");
			wr.flush();
			wr.close();

			StringBuilder response = new StringBuilder();
			if (conn.getResponseCode() == 200) {
				// Get the response
				BufferedReader rd = new BufferedReader(new InputStreamReader(conn.getInputStream()));
				String line;
				while ((line = rd.readLine()) != null) {
					response.append(line);
				}
				rd.close();
				JSONObject parser = new JSONObject(response.toString());
				communicoAPIToken = parser.getString("access_token");
				communicoAPITokenType = parser.getString("token_type");
				communicoAPIExpiration = new Date().getTime() + (parser.getLong("expires_in") * 1000) - 10000;
			} else {
				// Get any errors
				BufferedReader rd = new BufferedReader(new InputStreamReader(conn.getErrorStream()));
				String line;
				while ((line = rd.readLine()) != null) {
					response.append(line);
				}
				rd.close();
				logEntry.incErrors("Did not get an access token from the Communico Authentication service: " + response);
				logger.error(clientId + ":" + clientSecret);
				return false;
			}
		} catch (SocketTimeoutException toe){
			logEntry.incErrors("Timeout connecting to Communico Authentication service");
			throw toe;
		} catch (Exception e) {
			logEntry.incErrors("Error connecting to Communico API", e );
			return false;
		}
		return true;
	}

	public JSONArray getCommunicoEvents() {
		try {
			if (connectToCommunico()){
				JSONArray events = new JSONArray();
				//Give a 5-minute timeout
				int timeout = 600;
				RequestConfig config = RequestConfig.custom()
					.setConnectTimeout(timeout * 1000)
					.setConnectionRequestTimeout(timeout * 1000)
					.setSocketTimeout(timeout * 1000).build();
				try (CloseableHttpClient httpclient = HttpClientBuilder.create().setDefaultRequestConfig(config).build()) {
					int start = 0;
					int limit = 200;
					int totalRecords = 0;
					boolean hasMoreRecords = true;

					long now = new Date().getTime() / 1000;
					long fullDayAgo = now - 24 * 60 * 60;
					if (lastUpdateOfAllEvents < fullDayAgo){
						runFullIndexCommunico = true;
					}
					LocalDate localNow = LocalDate.now();
					LocalDate lastDateToIndex = localNow.plusDays(numberOfDaysToIndex);

					//we don't need to always run a full index, most important on the first ever index
					if (runFullIndexCommunico || lastUpdateOfAllEvents==0) {
						while (hasMoreRecords) {
							hasMoreRecords = false;
							//max limit of 250, need to rebuild URL for each run to set correct start number
							String apiEventsURL = "https://api.communico.co/v3/attend/events";
							apiEventsURL += "?start=" + start + "&limit=200";
							apiEventsURL += "&startDate=" + localNow;
							apiEventsURL += "&endDate=" + lastDateToIndex;
							//Need to request the fields we want as many are "optional" and aren't returned unless asked for
							//noinspection SpellCheckingInspection
							apiEventsURL += "&privateEvents=false&staffOnly=false&fields=ages,searchTags,registration,eventImage,eventType,registrationOpens,registrationCloses,eventRegistrationUrl,thirdPartyRegistration,waitlist,maxAttendees,totalRegistrants,totalWaitlist,maxWaitlist,types&sortBy=eventStart&sortOrder=ascending";
							HttpGet apiRequest = new HttpGet(apiEventsURL);
							apiRequest.addHeader("Authorization", communicoAPITokenType + " " + communicoAPIToken);
							logEntry.addNote("Loading events from " + apiEventsURL);
							logEntry.saveResults();

							//Process all the events
							for (int j = 0; j < 3; j++) {
								try (CloseableHttpResponse response1 = httpclient.execute(apiRequest)) {
									StatusLine status = response1.getStatusLine();
									HttpEntity entity1 = response1.getEntity();
									if (status.getStatusCode() == 200) {
										String response = EntityUtils.toString(entity1);
										JSONObject response2 = new JSONObject(response);
										JSONObject data = response2.getJSONObject("data");
										JSONArray events1 = data.getJSONArray("entries");
										for (int i = 0; i < events1.length(); i++) {
											JSONObject event = events1.getJSONObject(i);
											if ((!event.getString("modified").equals("canceled"))) {
												events.put(events1.get(i));
											}
										}
										totalRecords = data.getInt("total");
										if (start + limit < totalRecords) {
											hasMoreRecords = true;
											start = start + limit;
										}
										break;
									} else {
										if (j == 2) {
											logEntry.incErrors("Did not get a good response calling " + apiEventsURL + " got " + status.getStatusCode());
										}else {
											Thread.sleep(500);
										}
									}
								} catch (Exception e) {
									if (j == 2) {
										logEntry.incErrors("Error getting events from " + apiEventsURL, e);
									}else {
										Thread.sleep(500);
									}
								}
							}
						}
						if (!logEntry.hasErrors()) {
							//Update the last time we ran the update in settings
							PreparedStatement updateExtractTime;
							updateExtractTime = aspenConn.prepareStatement("UPDATE communico_settings set lastUpdateOfAllEvents = ? WHERE id = ?");
							updateExtractTime.setLong(1, startTimeForLogging);
							updateExtractTime.setLong(2, this.settingsId);
							updateExtractTime.executeUpdate();
						} else {
							logEntry.addNote("Not setting last extract time since there were problems extracting products from the API");
						}
					} else {
						Date today = new Date();
						today.setTime(System.currentTimeMillis());
						Date yesterday = new Date(today.getTime() - (1000 * 60 * 60 * 24));
						while (hasMoreRecords) {
							//max limit of 250
							String apiEventsURL = "https://api.communico.co/v3/attend/events";
							apiEventsURL += "?start=" + start + "&limit=200";
							apiEventsURL += "&endDate=" + lastDateToIndex;
							//Need to request the fields we want as many are "optional" and aren't returned unless asked for
							//noinspection SpellCheckingInspection
							apiEventsURL += "&privateEvents=false&staffOnly=false&fields=ages,searchTags,registration,eventImage,eventType,registrationOpens,registrationCloses,eventRegistrationUrl,thirdPartyRegistration,waitlist,maxAttendees,totalRegistrants,totalWaitlist,maxWaitlist,types&sortBy=eventLastUpdated&sortOrder=descending";
							HttpGet apiRequest = new HttpGet(apiEventsURL);
							apiRequest.addHeader("Authorization", communicoAPITokenType + " " + communicoAPIToken);
							logEntry.addNote("Loading events from " + apiEventsURL);
							logEntry.saveResults();

							//Process all the events
							for (int j = 0; j < 3; j++) {
								try (CloseableHttpResponse response1 = httpclient.execute(apiRequest)) {
									StatusLine status = response1.getStatusLine();
									HttpEntity entity1 = response1.getEntity();
									if (status.getStatusCode() == 200) {
										String response = EntityUtils.toString(entity1);
										JSONObject response2 = new JSONObject(response);
										JSONObject data = response2.getJSONObject("data");
										JSONArray events1 = data.getJSONArray("entries");
										for (int i = 0; i < events1.length(); i++) {
											JSONObject event = events1.getJSONObject(i);
											if ((!event.getString("modified").equals("canceled"))) {
												events.put(events1.get(i));
											}
											Date updateDate = getDateForKey(event, "eventLastUpdated");
											if (Objects.requireNonNull(updateDate).before(yesterday)) {
												hasMoreRecords = false;
												break;
											}
										}
										totalRecords = data.getInt("total");
										if (start + limit < totalRecords && hasMoreRecords) {
											start = start + limit;
										}
										break;
									} else {
										if (j == 2) {
											logEntry.incErrors("Did not get a good response calling " + apiEventsURL + " got " + status.getStatusCode());
										} else {
											Thread.sleep(500);
										}
									}
								} catch (Exception e) {
									if (j == 2) {
										logEntry.incErrors("Error getting events from " + apiEventsURL, e);
									} else {
										Thread.sleep(500);
									}
								}
							}
						}
					}
					logEntry.addNote("Finished loading events");
					logEntry.saveResults();
				} catch (Exception e) {
					logEntry.incErrors("Error creating HTTP client", e);
				}
				return events;
			}
		} catch (Exception e) {
			logEntry.incErrors("Error getting events", e);
		}
		return null;
	}

	private JSONArray getRegistrations(Integer eventId) {
		try {
			JSONArray eventRegistrations = new JSONArray();
			try (CloseableHttpClient httpclient = HttpClients.createDefault()) {

				String apiRegistrationsURL = "https://api.communico.co/v3/attend/events/" + eventId + "/registrants";
				HttpGet apiRequest = new HttpGet(apiRegistrationsURL);
				apiRequest.addHeader("Authorization", communicoAPITokenType + " " + communicoAPIToken);
				try (CloseableHttpResponse response1 = httpclient.execute(apiRequest)) {
					StatusLine status = response1.getStatusLine();
					HttpEntity entity1 = response1.getEntity();
					if (status.getStatusCode() == 200) {
						String response = EntityUtils.toString(entity1);
						JSONObject response2 = new JSONObject(response);
						JSONObject data = response2.getJSONObject("data");
						eventRegistrations = data.getJSONArray("entries");
					}
				} catch (Exception e) {
					logEntry.incErrors("Error getting event registrations from " + apiRegistrationsURL, e);
					return null;
				}
			}catch (Exception e) {
				logEntry.incErrors("Error creating HTTP Client defaults", e);
				return null;
			}
			return eventRegistrations;
		} catch(Exception e){
			logEntry.incErrors("Error getting event registrations", e);
		}
		return null;
	}
}
