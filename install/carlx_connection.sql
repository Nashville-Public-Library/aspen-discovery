-- MySQL dump 10.13  Distrib 5.7.23, for Win64 (x86_64)
--
-- Host: localhost    Database: pueblo
-- ------------------------------------------------------
-- Server version	5.7.23-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `account_profiles`
--

LOCK TABLES `account_profiles` WRITE;
/*!40000 ALTER TABLE `account_profiles` DISABLE KEYS */;
INSERT INTO `account_profiles` (id, name, driver, loginConfiguration, authenticationMethod, vendorOpacUrl, patronApiUrl, recordSource, weight, oAuthClientId, oAuthClientSecret, databaseHost, databaseName, databaseUser, databasePassword, databasePort, sipHost, sipPort, ils) VALUES
                               (2,'ils','CarlX','barcode_pin','ils','{ilsUrl}','{ilsUrl}','ils',0, '{ilsClientId}', '{ilsClientSecret}','{ilsDatabaseHost}','{ilsDatabaseName}','{ilsDatabaseUser}','{ilsDatabasePassword}','{ilsDatabasePort}', '{sip2Host}', '{sip2Port}', 'carlx');
/*!40000 ALTER TABLE `account_profiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `indexing_profiles`
--

LOCK TABLES `indexing_profiles` WRITE;
/*!40000 ALTER TABLE `indexing_profiles` DISABLE KEYS */;
INSERT INTO `indexing_profiles` (id, name, marcPath, marcEncoding, indexingClass, recordUrlComponent, formatSource, recordNumberTag, recordNumberSubfield, recordNumberPrefix, itemTag, itemRecordNumber, useItemBasedCallNumbers, callNumber, location, shelvingLocation, collection, volume, barcode, iType, dateCreated, dateCreatedFormat, format, catalogDriver, filenamesToInclude, doAutomaticEcontentSuppression, lastCheckinFormat, status, lastCheckinDate, dueDate, dueDateFormat, lastYearCheckouts, yearToDateCheckouts, totalRenewals,totalCheckouts, iCode2, noteSubfield)
                         VALUES (1,'ils','/data/aspen-discovery/{sitename}/ils/marc','UTF8','CarlX','Record','bib','910', 'a', 'CARL','949','b',1,'c','j','l','l','','b','m','x','yyyyMMdd','m','CarlX','.*\\.ma?rc',1, '', 's', '', 'k', 'yyyyMMdd', '', 'v', '', 'w', '', '');
/*!40000 ALTER TABLE `indexing_profiles` ENABLE KEYS */;
UNLOCK TABLES;


--
-- Dumping data for table `library_records_to_include`
--

LOCK TABLES `library_records_to_include` WRITE;
/*!40000 ALTER TABLE `library_records_to_include` DISABLE KEYS */;
INSERT INTO `library_records_to_include` (id, libraryId, indexingProfileId, location, subLocation, includeHoldableOnly, includeItemsOnOrder, includeEContent, weight, iType, audience, format, marcTagToMatch, marcValueToMatch, includeExcludeMatches, urlToMatch, urlReplacement, locationsToExclude, subLocationsToExclude, markRecordsAsOwned) VALUES (1,1,1,'.*','.*',0,1,1,1,'','','','','',1,'','','','', 1);
/*!40000 ALTER TABLE `library_records_to_include` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `status_map_values`
--

LOCK TABLES `status_map_values` WRITE;
/*!40000 ALTER TABLE `status_map_values` DISABLE KEYS */;
/*!40000 ALTER TABLE `status_map_values` ENABLE KEYS */;
INSERT INTO `status_map_values` (indexingProfileId, value, status, groupedStatus, suppress) VALUES
                                (1,'C','Checked Out','Checked Out',0),
								(1,'CT','Charged Temporary','Checked Out',0),
								(1,'H','On Hold Shelf','Checked Out',0),
								(1,'HP','Hold Pending','Checked Out',0),
								(1,'HT','On Hold Shelf','Checked Out',0),
								(1,'I','In Transit','In Transit',0),
								(1,'IH','In Transit for Hold','Checked Out',0),
								(1,'IT','In Transit','In Transit',0),
								(1,'L','Lost','Currently Unavailable',0),
								(1,'LT','Lost Temporary','Currently Unavailable',0),
								(1,'N','Ordered','On Order',0),
								(1,'O','On Order','On Order',0),
								(1,'R','Received','On Order',0),
								(1,'RC','Received','On Order',0),
								(1,'RF','Received','On Order',0),
								(1,'RG','Received as Gift','On Order',0),
								(1,'S','On Shelf','On Shelf',0),
								(1,'SC','Library Use Only','Library Use Only',0),
								(1,'SD','Coming Soon','Coming Soon',0),
								(1,'SG','Damaged','Currently Unavailable',0),
								(1,'SI','On Display','On Shelf',0),
								(1,'SM','Missing','Currently Unavailable',0),
								(1,'SO','Empty Case','Currently Unavailable',0),
								(1,'SP','In Processing','In Processing',0),
								(1,'ST','On Shelf Temporary','Currently Unavailable',0),
								(1,'SW','Withdrawn','Currently Unavailable',1),
								(1,'SX','Not on Shelf','Currently Unavailable',0),
								(1,'T','Traced','Currently Unavailable',0);
UNLOCK TABLES;

--
-- Dumping data for table `translation_maps`
--

LOCK TABLES `translation_maps` WRITE;
/*!40000 ALTER TABLE `translation_maps` DISABLE KEYS */;
INSERT INTO `translation_maps` VALUES (1,1,'collection',0),(2,1,'location',0),(3,1,'shelf_location',0),(5,1,'itype',0);
/*!40000 ALTER TABLE `translation_maps` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

UPDATE modules set enabled = 1 where name = 'Koha';

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-11-18  8:22:06
