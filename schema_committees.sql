-- MySQL dump 10.10
--
-- Host: localhost    Database: php_ems_tools_devel
-- ------------------------------------------------------
-- Server version	5.0.26

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
-- Table structure for table `committee_positions`
--

DROP TABLE IF EXISTS `committee_positions`;
CREATE TABLE `committee_positions` (
  `comm_pos_id` int(10) unsigned NOT NULL auto_increment,
  `comm_pos_name` varchar(50) default NULL,
  `max_per_committee` int(11) default NULL,
  PRIMARY KEY  (`comm_pos_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `committee_positions`
--

LOCK TABLES `committee_positions` WRITE;
/*!40000 ALTER TABLE `committee_positions` DISABLE KEYS */;
INSERT INTO `committee_positions` VALUES (1,'Chairman',1),(2,'Co-Chairman',-1),(3,'Member',-1);
/*!40000 ALTER TABLE `committee_positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `committees`
--

DROP TABLE IF EXISTS `committees`;
CREATE TABLE `committees` (
  `comm_id` int(10) unsigned NOT NULL auto_increment,
  `comm_name` varchar(50) default NULL,
  PRIMARY KEY  (`comm_id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `committees`
--

LOCK TABLES `committees` WRITE;
/*!40000 ALTER TABLE `committees` DISABLE KEYS */;
INSERT INTO `committees` VALUES (1,'Membership'),(2,'Fund Drive'),(3,'Uniforms'),(4,'20<sup>th</sup> District'),(5,'Insurance'),(6,'Good & Welfare'),(7,'Points & LOSAP'),(8,'Activities'),(9,'Publicity'),(10,'Awards'),(11,'Building & Grounds'),(12,'By-Laws'),(13,'Safety'),(14,'Grievance'),(15,'Computer'),(16,'Education'),(17,'Ambulance Maintenance');
/*!40000 ALTER TABLE `committees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `members_committees`
--

DROP TABLE IF EXISTS `members_committees`;
CREATE TABLE `members_committees` (
  `memb_comm_id` int(10) unsigned NOT NULL auto_increment,
  `EMTid` varchar(10) default NULL,
  `comm_id` int(10) unsigned default NULL,
  `pos_id` int(10) unsigned default NULL,
  `appointed_ts` int(11) default NULL,
  `removed_ts` int(11) default NULL,
  PRIMARY KEY  (`memb_comm_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `members_committees`
--

LOCK TABLES `members_committees` WRITE;
/*!40000 ALTER TABLE `members_committees` DISABLE KEYS */;
INSERT INTO `members_committees` VALUES (1,'66',1,1,1230789600,NULL),(2,'59',1,3,1230789600,NULL),(3,'5',2,1,1230789600,NULL),(4,'23',3,1,1230789600,NULL),(5,'29',3,3,1230789600,NULL),(6,'66',3,3,1230789600,NULL),(7,'2',4,1,1230789600,NULL),(8,'56',4,3,1230789600,NULL),(14,'18',5,3,1231172258,NULL),(12,'52',4,3,1231172218,NULL),(13,'6',5,1,1231172258,NULL),(15,'50',5,3,1231172258,NULL);
/*!40000 ALTER TABLE `members_committees` ENABLE KEYS */;
UNLOCK TABLES;


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2009-01-05 16:22:23
