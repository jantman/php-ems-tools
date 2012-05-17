-- MySQL dump 10.11
--
-- Host: localhost    Database: callin
-- ------------------------------------------------------
-- Server version	5.0.67

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `callerid_translation`
--

DROP TABLE IF EXISTS `callerid_translation`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `callerid_translation` (
  `CID` varchar(15) default NULL,
  `EMTid` varchar(10) default NULL,
  `status` varchar(25) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `callins`
--

DROP TABLE IF EXISTS `callins`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `callins` (
  `callin_id` int(10) unsigned NOT NULL auto_increment,
  `start_ts` int(11) default NULL,
  `cid` varchar(15) default NULL,
  `DTMF_select` varchar(5) default NULL,
  `EMTid` varchar(10) default NULL,
  `manual_entry` int(1) default '0',
  `end_ts` int(11) default NULL,
  `is_cleared` int(1) default '0',
  `status` varchar(20) default NULL,
  `cleared_ts` int(11) default NULL,
  `cleared_by` varchar(30) default NULL,
  PRIMARY KEY  (`callin_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1865 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `dtmf_options`
--

DROP TABLE IF EXISTS `dtmf_options`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `dtmf_options` (
  `dtmf_key` int(2) NOT NULL,
  `name` varchar(30) default NULL,
  `description` varchar(50) default NULL,
  PRIMARY KEY  (`dtmf_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `dtmf_options`
--

LOCK TABLES `dtmf_options` WRITE;
/*!40000 ALTER TABLE `dtmf_options` DISABLE KEYS */;
INSERT INTO `dtmf_options` VALUES (1,'ToBuilding','to HQ'),(2,'ToScene','to Scene'),(3,'AtHQ','At HQ Currently'),(4,'Cancel','CANCELED RESPONSE');
/*!40000 ALTER TABLE `dtmf_options` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unknown_calls`
--

DROP TABLE IF EXISTS `unknown_calls`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `unknown_calls` (
  `unknown_call_id` int(10) unsigned NOT NULL auto_increment,
  `cid` varchar(60) default NULL,
  `start_ts` int(11) default NULL,
  PRIMARY KEY  (`unknown_call_id`)
) ENGINE=MyISAM AUTO_INCREMENT=326 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-05-17  3:28:12
