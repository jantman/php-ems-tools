-- MySQL dump 10.10
--
-- Host: localhost    Database: pcr
-- ------------------------------------------------------
-- Server version	5.0.26-log

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
-- Table structure for table `calls`
--

DROP TABLE IF EXISTS `calls`;
CREATE TABLE `calls` (
  `Pkey` int(10) unsigned NOT NULL auto_increment,
  `RunNumber` int(10) unsigned NOT NULL,
  `date_ts` int(10) unsigned default NULL,
  `date_date` date default NULL,
  `submit_ts` int(10) unsigned default NULL,
  `updated_ts` int(10) unsigned default NULL,
  `pt_num` tinyint(3) unsigned default NULL,
  `pt_total` tinyint(3) unsigned default NULL,
  `patient_pkey` int(10) unsigned default NULL,
  `pt_age` int(10) unsigned default NULL,
  `pt_loc_at_scene` varchar(45) default NULL,
  `pt_physician` varchar(45) default NULL,
  `call_loc_id` int(10) unsigned NOT NULL default '0',
  `outcome` varchar(30) default NULL,
  `is_deprecated` tinyint(1) unsigned default '0',
  `deprecated_by_id` int(10) unsigned default NULL,
  `deprecates_id` int(10) unsigned default NULL,
  `is_duty_call` tinyint(1) unsigned default '0',
  `call_type` varchar(60) default NULL,
  `signature_ID` varchar(10) default NULL,
  `signature_auth` tinyint(1) unsigned default '0',
  `PtTransferredTo` varchar(50) default NULL,
  `Passengers` varchar(50) default NULL,
  `EquipmentLeft` varchar(50) default NULL,
  `is_second_rig` tinyint(1) unsigned default '0',
  PRIMARY KEY  (`Pkey`),
  KEY `patients_id` (`patient_pkey`),
  KEY `call_loc_id` (`call_loc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `calls_MA`
--

DROP TABLE IF EXISTS `calls_MA`;
CREATE TABLE `calls_MA` (
  `callsMA_id` int(10) unsigned NOT NULL auto_increment,
  `RunNumber` int(10) unsigned NOT NULL,
  `City` varchar(100) default NULL,
  `State` varchar(10) default NULL,
  `is_deprecated` tinyint(1) unsigned default '0',
  `deprecated_by_id` int(10) unsigned default NULL,
  `deprecates_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`callsMA_id`),
  KEY `RunNumber` (`RunNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `calls_als`
--

DROP TABLE IF EXISTS `calls_als`;
CREATE TABLE `calls_als` (
  `calls_als_id` int(10) unsigned NOT NULL auto_increment,
  `RunNumber` int(10) unsigned default NULL,
  `als_unit` varchar(10) default NULL,
  `als_status` varchar(30) default NULL,
  `is_deprecated` tinyint(1) unsigned default '0',
  `deprecated_by_id` int(10) unsigned default NULL,
  `deprecates_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`calls_als_id`),
  KEY `RunNumber` (`RunNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `calls_check`
--

DROP TABLE IF EXISTS `calls_check`;
CREATE TABLE `calls_check` (
  `RunNumber` int(10) unsigned NOT NULL,
  `check_ts` int(10) unsigned default NULL,
  `check_by` varchar(20) default NULL,
  `needs_review` tinyint(1) default '0',
  PRIMARY KEY  (`RunNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `calls_clinical`
--

DROP TABLE IF EXISTS `calls_clinical`;
CREATE TABLE `calls_clinical` (
  `cc_id` int(10) unsigned NOT NULL auto_increment,
  `RunNumber` int(10) unsigned default NULL,
  `chief_complaint` varchar(50) default NULL,
  `time_of_onset` varchar(50) default NULL,
  `aid_given` varchar(50) default NULL,
  `aid_given_by` varchar(50) default NULL,
  `allergies` varchar(255) default NULL,
  `medications` varchar(255) default NULL,
  `pt_hx` text,
  `remarks` text,
  `is_deprecated` tinyint(1) unsigned default '0',
  `deprecated_by_id` int(10) unsigned default NULL,
  `deprecates_id` int(10) unsigned default NULL,
  `has_loss_of_consc` tinyint(1) unsigned default '0',
  PRIMARY KEY  (`cc_id`),
  KEY `RunNumber` (`RunNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `calls_crew`
--

DROP TABLE IF EXISTS `calls_crew`;
CREATE TABLE `calls_crew` (
  `cc_id` int(10) unsigned NOT NULL auto_increment,
  `RunNumber` int(10) unsigned default NULL,
  `EMTid` varchar(10) default NULL,
  `is_driver_to_scene` tinyint(1) unsigned default '0',
  `is_driver_to_hosp` tinyint(1) unsigned default '0',
  `is_driver_to_bldg` tinyint(1) unsigned default '0',
  `is_on_duty` tinyint(1) unsigned default '0',
  `is_on_scene` tinyint(1) unsigned default '0',
  `is_deprecated` tinyint(1) unsigned default '0',
  `deprecated_by_id` int(10) unsigned default NULL,
  `deprecates_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`cc_id`),
  KEY `RunNumber` (`RunNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `calls_edits`
--

DROP TABLE IF EXISTS `calls_edits`;
CREATE TABLE `calls_edits` (
  `edits_id` int(10) unsigned NOT NULL auto_increment,
  `ts` int(10) unsigned default NULL,
  `auth_username` varchar(45) default NULL,
  `auth_EMTid` varchar(6) default NULL,
  `auth_method` varchar(45) default NULL,
  `remote_ip` varchar(45) default NULL,
  `edit_type` varchar(10) default NULL,
  `auth_allowed_reason` varchar(60) default NULL,
  `table_name` varchar(45) default NULL,
  `deprecated_by_id` int(10) unsigned default NULL,
  `deprecates_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`edits_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `calls_injured_area`
--

DROP TABLE IF EXISTS `calls_injured_area`;
CREATE TABLE `calls_injured_area` (
  `cia_id` int(10) unsigned NOT NULL auto_increment,
  `RunNumber` int(10) unsigned default NULL,
  `name` varchar(45) default NULL,
  `is_deprecated` tinyint(1) unsigned default '0',
  `deprecated_by_id` int(10) unsigned default NULL,
  `deprecates_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`cia_id`),
  KEY `RunNumber` (`RunNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `calls_locations`
--

DROP TABLE IF EXISTS `calls_locations`;
CREATE TABLE `calls_locations` (
  `Pkey` int(10) unsigned NOT NULL auto_increment,
  `StreetNumber` varchar(6) default NULL,
  `Street` varchar(50) default NULL,
  `AptNumber` varchar(10) default NULL,
  `place_name` varchar(50) default NULL,
  `City` varchar(100) default NULL,
  `State` varchar(10) NOT NULL,
  `Intsct_Street` varchar(50) default NULL,
  `is_deprecated` tinyint(1) unsigned default '0',
  `deprecated_by_id` int(10) unsigned default NULL,
  `deprecates_id` int(10) unsigned default NULL,
  `call_loc_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`Pkey`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `calls_times`
--

DROP TABLE IF EXISTS `calls_times`;
CREATE TABLE `calls_times` (
  `ct_id` int(10) unsigned NOT NULL auto_increment,
  `RunNumber` int(10) unsigned NOT NULL,
  `dispatched` int(10) unsigned default NULL,
  `inservice` int(10) unsigned default NULL,
  `onscene` int(10) unsigned default NULL,
  `enroute` int(10) unsigned default NULL,
  `arrived` int(10) unsigned default NULL,
  `available` int(10) unsigned default NULL,
  `outservice` int(10) unsigned default NULL,
  `is_deprecated` tinyint(1) unsigned default '0',
  `deprecated_by_id` int(10) unsigned default NULL,
  `deprecates_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`ct_id`),
  KEY `RunNumber` (`RunNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `calls_tx`
--

DROP TABLE IF EXISTS `calls_tx`;
CREATE TABLE `calls_tx` (
  `ct_id` int(10) unsigned NOT NULL auto_increment,
  `RunNumber` int(10) unsigned default NULL,
  `treatment` varchar(45) default NULL,
  `quantity_or_rate` varchar(45) default NULL,
  `is_deprecated` tinyint(1) unsigned default '0',
  `deprecated_by_id` int(10) unsigned default NULL,
  `deprecates_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`ct_id`),
  KEY `RunNumber` (`RunNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='calls - treatments';

--
-- Table structure for table `calls_units`
--

DROP TABLE IF EXISTS `calls_units`;
CREATE TABLE `calls_units` (
  `cu_id` int(10) unsigned NOT NULL auto_increment,
  `RunNumber` int(10) unsigned NOT NULL,
  `unit` varchar(10) default NULL,
  `start_mileage` decimal(11,2) default NULL,
  `end_mileage` decimal(11,2) default NULL,
  `total_mileage` decimal(11,2) default NULL,
  `is_deprecated` tinyint(1) unsigned default '0',
  `deprecated_by_id` int(10) unsigned default NULL,
  `deprecates_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`cu_id`),
  KEY `RunNumber` (`RunNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `calls_vitals`
--

DROP TABLE IF EXISTS `calls_vitals`;
CREATE TABLE `calls_vitals` (
  `cv_id` int(10) unsigned NOT NULL auto_increment,
  `RunNumber` int(10) unsigned default NULL,
  `vitals_set_number` tinyint(3) unsigned default NULL,
  `time` varchar(10) default NULL,
  `bp` varchar(10) default NULL,
  `pulse` varchar(10) default NULL,
  `resps` varchar(10) default NULL,
  `lung_sounds` varchar(25) default NULL,
  `consciousness` varchar(20) default NULL,
  `pupils_left` varchar(20) default NULL,
  `pupils_right` varchar(20) default NULL,
  `skin_temp` varchar(10) default NULL,
  `skin_moisture` varchar(10) default NULL,
  `skin_color` varchar(10) default NULL,
  `is_deprecated` tinyint(1) unsigned default '0',
  `deprecated_by_id` int(10) unsigned default NULL,
  `deprecates_id` int(10) unsigned default NULL,
  `spo2` varchar(10) default NULL,
  PRIMARY KEY  (`cv_id`),
  KEY `RunNumber` (`RunNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `opt_CallType`
--

DROP TABLE IF EXISTS `opt_CallType`;
CREATE TABLE `opt_CallType` (
  `oct_id` int(10) unsigned NOT NULL auto_increment,
  `oct_name` varchar(50) default NULL,
  `oct_order` int(10) unsigned default NULL,
  PRIMARY KEY  (`oct_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `opt_CallType`
--

LOCK TABLES `opt_CallType` WRITE;
/*!40000 ALTER TABLE `opt_CallType` DISABLE KEYS */;
INSERT INTO `opt_CallType` VALUES (1,'Allergic Reaction',1),(2,'Behavioral',2),(3,'Bicycle Crash',3),(4,'Blunt Trauma',4),(5,'Burns',5),(6,'Cardiac - Other',6),(7,'Cardiac Arrest',7),(8,'Diabetic',8),(9,'Drowning/Near Drowning',9),(10,'Environmental (Heat/Cold)',10),(11,'Fall',11),(12,'Firearm',12),(13,'GI Complaint',13),(14,'Machinery',14),(15,'Motor Vehicle Crash',15),(16,'Neurological (CVA/Stroke)',16),(18,'OB/GYN',18),(19,'Other - Event Standby',60),(20,'Other - Fire Standby',61),(21,'Other - Misc. Non-Emerg.',62),(22,'Other - Misc. Standby',63),(23,'Other - Transport',64),(24,'Other - Vehicle Maintenance',65),(25,'Other Medical',25),(26,'Other Trauma',26),(27,'Pedestrian - MVC',27),(28,'Penetrating Trauma',28),(29,'Poisoning/Overdose',29),(30,'Respiratory',30),(31,'Seizures',31),(32,'Transport',32),(33,'Unconscious/Syncope',33),(34,'Weakness/Malaise/Fever',34);
/*!40000 ALTER TABLE `opt_CallType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opt_Cities`
--

DROP TABLE IF EXISTS `opt_Cities`;
CREATE TABLE `opt_Cities` (
  `oC_id` int(10) unsigned NOT NULL auto_increment,
  `oC_State` varchar(3) default NULL,
  `oC_City` varchar(100) default NULL,
  `oC_is_MA` tinyint(1) default '0',
  `oC_is_default` tinyint(1) unsigned default '0',
  PRIMARY KEY  (`oC_id`)
) ENGINE=MyISAM AUTO_INCREMENT=548 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `opt_Cities`
--

LOCK TABLES `opt_Cities` WRITE;
/*!40000 ALTER TABLE `opt_Cities` DISABLE KEYS */;
INSERT INTO `opt_Cities` VALUES (12,'NJ','Avenel',0,0),(13,'NJ','Bayonne',0,0),(14,'NJ','Bloomfield',0,0),(15,'NJ','Fairfield',0,0),(16,'NJ','Boonton',0,0),(17,'NJ','West Caldwell',0,0),(18,'NJ','Carteret',0,0),(19,'NJ','Cedar Grove',0,0),(20,'NJ','Cliffside Park',0,0),(21,'NJ','Clifton',0,0),(22,'NJ','Cranford',0,0),(23,'NJ','East Orange',0,0),(24,'NJ','Edgewater',0,0),(25,'NJ','Essex Fells',0,0),(26,'NJ','Fairview',0,0),(27,'NJ','Fanwood',0,0),(28,'NJ','Fort Lee',0,0),(29,'NJ','Garfield',0,0),(30,'NJ','Garwood',0,0),(31,'NJ','Glen Ridge',0,0),(32,'NJ','Kearny',0,0),(33,'NJ','Hoboken',0,0),(34,'NJ','North Arlington',0,0),(35,'NJ','Kenilworth',0,0),(36,'NJ','Lake Hiawatha',0,0),(37,'NJ','Lincoln Park',0,0),(38,'NJ','Linden',0,0),(39,'NJ','Livingston',0,0),(40,'NJ','Maplewood',0,0),(41,'NJ','Millburn',0,0),(42,'NJ','Montclair',0,0),(43,'NJ','Verona',0,0),(44,'NJ','Montville',0,0),(45,'NJ','Mountain Lakes',0,0),(46,'NJ','North Bergen',0,0),(47,'NJ','Orange',0,0),(48,'NJ','West Orange',0,0),(49,'NJ','Parsippany',0,0),(50,'NJ','Passaic',0,0),(51,'NJ','Wallington',0,0),(52,'NJ','Pine Brook',0,0),(53,'NJ','Warren',0,0),(54,'NJ','North Plainfield',0,0),(55,'NJ','Port Reading',0,0),(56,'NJ','Rahway',0,0),(57,'NJ','Clark',0,0),(58,'NJ','Colonia',0,0),(59,'NJ','Roseland',0,0),(60,'NJ','Rutherford',0,0),(61,'NJ','Lyndhurst',0,0),(62,'NJ','Carlstadt',0,0),(63,'NJ','East Rutherford',0,0),(64,'NJ','Moonachie',0,0),(65,'NJ','Wood Ridge',0,0),(66,'NJ','Scotch Plains',0,0),(67,'NJ','Sewaren',0,0),(68,'NJ','Short Hills',0,0),(69,'NJ','South Orange',0,0),(70,'NJ','South Plainfield',0,0),(71,'NJ','Springfield',0,0),(72,'NJ','Towaco',0,0),(73,'NJ','Union',0,0),(74,'NJ','Weehawken',0,0),(75,'NJ','Vauxhall',0,0),(76,'NJ','Westfield',0,0),(77,'NJ','Mountainside',0,0),(78,'NJ','Guttenberg',0,0),(79,'NJ','Secaucus',0,0),(80,'NJ','Woodbridge',0,0),(81,'NJ',NULL,0,0),(82,'NJ','Newark',0,0),(83,'NJ','Belleville',0,0),(84,'NJ','Nutley',0,0),(85,'NJ','Irvington',0,0),(86,'NJ','Elizabeth',0,0),(87,'NJ','Roselle',0,0),(88,'NJ','Roselle Park',0,0),(89,'NJ','Hillside',0,0),(90,'NJ','Jersey City',0,0),(91,'NJ','Allendale',0,0),(92,'NJ','Bloomingdale',0,0),(93,'NJ','Kinnelon',0,0),(94,'NJ','Elmwood Park',0,0),(95,'NJ','Fair Lawn',0,0),(96,'NJ','Franklin',0,0),(97,'NJ','Franklin Lakes',0,0),(98,'NJ','Glenwood',0,0),(99,'NJ','Hamburg',0,0),(100,'NJ','Haskell',0,0),(101,'NJ','Hewitt',0,0),(102,'NJ','Highland Lakes',0,0),(103,'NJ','Ho Ho Kus',1,0),(104,'NJ','West Paterson',0,0),(105,'NJ','Mc Afee',0,0),(106,'NJ','Mahwah',0,0),(107,'NJ','Midland Park',0,0),(108,'NJ','Newfoundland',0,0),(109,'NJ','Oakland',0,0),(110,'NJ','Milton',0,0),(111,'NJ','Ogdensburg',0,0),(112,'NJ','Pequannock',0,0),(113,'NJ','Pompton Lakes',0,0),(114,'NJ','Pompton Plains',0,0),(115,'NJ','Ramsey',0,0),(116,'NJ','Ridgewood',1,0),(117,'NJ','Glen Rock',0,0),(118,'NJ','Ringwood',0,0),(119,'NJ','Riverdale',0,0),(120,'NJ','Upper Saddle Riv',0,0),(121,'NJ','Stockholm',0,0),(122,'NJ','Sussex',0,0),(123,'NJ','Vernon',0,0),(124,'NJ','Waldwick',0,0),(125,'NJ','Wanaque',0,0),(126,'NJ','Wayne',0,0),(127,'NJ','West Milford',0,0),(128,'NJ','Wyckoff',1,0),(129,'NJ','Paterson',0,0),(130,'NJ','Hawthorne',0,0),(131,'NJ','Haledon',0,0),(132,'NJ','Totowa',0,0),(133,'NJ','Hackensack',0,0),(134,'NJ','Bogota',0,0),(135,'NJ','Hasbrouck Height',0,0),(136,'NJ','Leonia',0,0),(137,'NJ','South Hackensack',0,0),(138,'NJ','Maywood',0,0),(139,'NJ','Teterboro',0,0),(140,'NJ','Alpine',0,0),(141,'NJ','Bergenfield',0,0),(142,'NJ','Closter',0,0),(143,'NJ','Cresskill',0,0),(144,'NJ','Demarest',0,0),(145,'NJ','Dumont',0,0),(146,'NJ','Emerson',0,0),(147,'NJ','Englewood',0,0),(148,'NJ','Englewood Cliffs',0,0),(149,'NJ','Harrington Park',0,0),(150,'NJ','Haworth',0,0),(151,'NJ','Hillsdale',0,0),(152,'NJ','Little Ferry',0,0),(153,'NJ','Lodi',0,0),(154,'NJ','Montvale',0,0),(155,'NJ','New Milford',0,0),(156,'NJ','Rockleigh',0,0),(157,'NJ','Norwood',0,0),(158,'NJ','Oradell',0,0),(159,'NJ','Palisades Park',0,0),(160,'NJ','Paramus',0,0),(161,'NJ','Park Ridge',0,0),(162,'NJ','Ridgefield',0,0),(163,'NJ','Ridgefield Park',0,0),(164,'NJ','River Edge',0,0),(165,'NJ','Saddle Brook',0,0),(166,'NJ','Teaneck',0,0),(167,'NJ','Tenafly',0,0),(168,'NJ','Old Tappan',0,0),(169,'NJ','Suburban',0,0),(170,'NJ','Shrewsbury',0,0),(171,'NJ','Fort Monmouth',0,0),(172,'NJ','Fair Haven',0,0),(173,'NJ','Allenhurst',0,0),(174,'NJ','Ocean',0,0),(175,'NJ','Atlantic Highlan',0,0),(176,'NJ','Avon By The Sea',0,0),(177,'NJ','Belford',0,0),(178,'NJ','Wall',0,0),(179,'NJ','Bradley Beach',0,0),(180,'NJ','Cliffwood',0,0),(181,'NJ','Colts Neck',0,0),(182,'NJ','Deal',0,0),(183,'NJ','Eatontown',0,0),(184,'NJ','Manalapan',0,0),(185,'NJ','Farmingdale',0,0),(186,'NJ','Freehold',0,0),(187,'NJ','Hazlet',0,0),(188,'NJ','Howell',0,0),(189,'NJ','Fort Hancock',0,0),(190,'NJ','Holmdel',0,0),(191,'NJ','Keansburg',0,0),(192,'NJ','Keyport',0,0),(193,'NJ','Leonardo',0,0),(194,'NJ','Lincroft',0,0),(195,'NJ','Little Silver',0,0),(196,'NJ','Long Branch',0,0),(197,'NJ','Marlboro',0,0),(198,'NJ','Matawan',0,0),(199,'NJ','New Monmouth',0,0),(200,'NJ','Monmouth Beach',0,0),(201,'NJ','Morganville',0,0),(202,'NJ','Neptune City',0,0),(203,'NJ','Oakhurst',0,0),(204,'NJ','Ocean Grove',0,0),(205,'NJ','Oceanport',0,0),(206,'NJ','Port Monmouth',0,0),(207,'NJ','Sea Bright',0,0),(208,'NJ','Spring Lake',0,0),(209,'NJ','West Long Branch',0,0),(210,'NJ','Mine Hill',0,0),(211,'NJ','Andover',0,0),(212,'NJ','Augusta',0,0),(213,'NJ','Belvidere',0,0),(214,'NJ','Blairstown',0,0),(215,'NJ','Branchville',0,0),(216,'NJ','Montague',0,0),(217,'NJ','Budd Lake',0,0),(218,'NJ','Califon',0,0),(219,'NJ','Columbia',0,0),(220,'NJ','Delaware',0,0),(221,'NJ','Denville',0,0),(222,'NJ','Flanders',0,0),(223,'NJ','Great Meadows',0,0),(224,'NJ','Hackettstown',0,0),(225,'NJ','Hibernia',0,0),(226,'NJ','Hopatcong',0,0),(227,'NJ','Johnsonburg',0,0),(228,'NJ','Kenvil',0,0),(229,'NJ','Lafayette',0,0),(230,'NJ','Lake Hopatcong',0,0),(231,'NJ','Landing',0,0),(232,'NJ','Layton',0,0),(233,'NJ','Ledgewood',0,0),(234,'NJ','Long Valley',0,0),(235,'NJ','Mount Arlington',0,0),(236,'NJ','Netcong',0,0),(237,'NJ','Fredon Township',0,0),(238,'NJ','Oxford',0,0),(239,'NJ','Port Murray',0,0),(240,'NJ','Rockaway',0,0),(241,'NJ','Randolph',0,0),(242,'NJ','Sparta',0,0),(243,'NJ','Stanhope',0,0),(244,'NJ','Succasunna',0,0),(245,'NJ','Mount Tabor',0,0),(246,'NJ','Washington',0,0),(247,'NJ','Wharton',0,0),(248,'NJ','Summit',0,0),(249,'NJ','Basking Ridge',0,0),(250,'NJ','Bedminster',0,0),(251,'NJ','Berkeley Heights',0,0),(252,'NJ','Bernardsville',0,0),(253,'NJ','Cedar Knolls',0,0),(254,'NJ','Chatham',0,0),(255,'NJ','Chester',0,0),(256,'NJ','Far Hills',0,0),(257,'NJ','Florham Park',0,0),(258,'NJ','Gillette',0,0),(259,'NJ','Gladstone',0,0),(260,'NJ','Green Village',0,0),(261,'NJ','East Hanover',0,0),(262,'NJ','Madison',0,0),(263,'NJ','Mendham',0,0),(264,'NJ','Millington',0,0),(265,'NJ','Greystone Park',0,0),(266,'NJ','Morristown',0,0),(267,'NJ','Mount Freedom',0,0),(268,'NJ','New Providence',0,0),(269,'NJ','New Vernon',0,0),(270,'NJ','Peapack',0,0),(271,'NJ','Pottersville',0,0),(272,'NJ','Stirling',0,0),(273,'NJ','Whippany',0,0),(274,'NJ','Alloway',0,0),(275,'NJ','Cherry Hill',0,0),(276,'NJ','Winslow',0,0),(277,'NJ','Barnegat',0,0),(278,'NJ','Barnegat Light',0,0),(279,'NJ','Barrington',0,0),(280,'NJ','Harvey Cedars',0,0),(281,'NJ','Berlin',0,0),(282,'NJ','Beverly',0,0),(283,'NJ','Birmingham',0,0),(284,'NJ','Bridgeport',0,0),(285,'NJ','Browns Mills',0,0),(286,'NJ','Burlington',0,0),(287,'NJ','Chatsworth',0,0),(288,'NJ','Clarksboro',0,0),(289,'NJ','Laurel Springs',0,0),(290,'NJ','Columbus',0,0),(291,'NJ','Deepwater',0,0),(292,'NJ','Gibbsboro',0,0),(293,'NJ','Gibbstown',0,0),(294,'NJ','Glassboro',0,0),(295,'NJ','Glendora',0,0),(296,'NJ','Gloucester City',0,0),(297,'NJ','Bellmawr',0,0),(298,'NJ','Grenloch',0,0),(299,'NJ','Haddonfield',0,0),(300,'NJ','Haddon Heights',0,0),(301,'NJ','Hainesport',0,0),(302,'NJ','Batsto',0,0),(303,'NJ','Hancocks Bridge',0,0),(304,'NJ','Harrisonville',0,0),(305,'NJ','Jobstown',0,0),(306,'NJ','Juliustown',0,0),(307,'NJ','Voorhees',0,0),(308,'NJ','Lawnside',0,0),(309,'NJ','Willingboro',0,0),(310,'NJ','Lumberton',0,0),(311,'NJ','Magnolia',0,0),(312,'NJ','Manahawkin',0,0),(313,'NJ','Mantua',0,0),(314,'NJ','Maple Shade',0,0),(315,'NJ','Marlton',0,0),(316,'NJ','Mount Laurel',0,0),(317,'NJ','Medford Lakes',0,0),(318,'NJ','Mickleton',0,0),(319,'NJ','Moorestown',0,0),(320,'NJ','Mount Ephraim',0,0),(321,'NJ','Eastampton Twp',0,0),(322,'NJ','Mount Royal',0,0),(323,'NJ','Mullica Hill',0,0),(324,'NJ','National Park',0,0),(325,'NJ','New Lisbon',0,0),(326,'NJ','Palmyra',0,0),(327,'NJ','Paulsboro',0,0),(328,'NJ','Pedricktown',0,0),(329,'NJ','Pemberton',0,0),(330,'NJ','Carneys Point',0,0),(331,'NJ','Pennsville',0,0),(332,'NJ','Pitman',0,0),(333,'NJ','Quinton',0,0),(334,'NJ','Rancocas',0,0),(335,'NJ','Richwood',0,0),(336,'NJ','Delanco',0,0),(337,'NJ','Cinnaminson',0,0),(338,'NJ','Runnemede',0,0),(339,'NJ','Salem',0,0),(340,'NJ','Sewell',0,0),(341,'NJ','Sicklerville',0,0),(342,'NJ','Somerdale',0,0),(343,'NJ','Stratford',0,0),(344,'NJ','Swedesboro',0,0),(345,'NJ','Thorofare',0,0),(346,'NJ','Tuckerton',0,0),(347,'NJ','Southampton',0,0),(348,'NJ','Waterford Works',0,0),(349,'NJ','Wenonah',0,0),(350,'NJ','West Berlin',0,0),(351,'NJ','West Creek',0,0),(352,'NJ','Westville',0,0),(353,'NJ','Williamstown',0,0),(354,'NJ','Deptford',0,0),(355,'NJ','Woodbury Heights',0,0),(356,'NJ','Woodstown',0,0),(357,'NJ','Camden',0,0),(358,'NJ','Audubon',0,0),(359,'NJ','Oaklyn',0,0),(360,'NJ','Collingswood',0,0),(361,'NJ','Merchantville',0,0),(362,'NJ','Delair',0,0),(363,'NJ','Smithville',0,0),(364,'NJ','Avalon',0,0),(365,'NJ','Brigantine',0,0),(366,'NJ','North Cape May',0,0),(367,'NJ','Cape May Court H',0,0),(368,'NJ','Cape May Point',0,0),(369,'NJ','Egg Harbor City',0,0),(370,'NJ','Elwood',0,0),(371,'NJ','Linwood',0,0),(372,'NJ','Marmora',0,0),(373,'NJ','New Gretna',0,0),(374,'NJ','Northfield',0,0),(375,'NJ','Ocean City',0,0),(376,'NJ','Ocean View',0,0),(377,'NJ','Pleasantville',0,0),(378,'NJ','Egg Harbor Township',0,0),(379,'NJ','Port Republic',0,0),(380,'NJ','Rio Grande',0,0),(381,'NJ','Townsends Inlet',0,0),(382,'NJ','Somers Point',0,0),(383,'NJ','South Dennis',0,0),(384,'NJ','Stone Harbor',0,0),(385,'NJ','Strathmere',0,0),(386,'NJ','Villas',0,0),(387,'NJ','Whitesboro',0,0),(388,'NJ','North Wildwood',0,0),(389,'NJ','Corbin City',0,0),(390,'NJ','Seabrook',0,0),(391,'NJ','Buena',0,0),(392,'NJ','Cedarville',0,0),(393,'NJ','Clayton',0,0),(394,'NJ','Deerfield Street',0,0),(395,'NJ','Delmont',0,0),(396,'NJ','Dorchester',0,0),(397,'NJ','Dorothy',0,0),(398,'NJ','Elmer',0,0),(399,'NJ','Estell Manor',0,0),(400,'NJ','Fortescue',0,0),(401,'NJ','Franklinville',0,0),(402,'NJ','Greenwich',0,0),(403,'NJ','Heislerville',0,0),(404,'NJ','Landisville',0,0),(405,'NJ','Leesburg',0,0),(406,'NJ','Malaga',0,0),(407,'NJ','Mauricetown',0,0),(408,'NJ','Mays Landing',0,0),(409,'NJ','Millville',0,0),(410,'NJ','Milmay',0,0),(411,'NJ','Minotola',0,0),(412,'NJ','Monroeville',0,0),(413,'NJ','Newfield',0,0),(414,'NJ','Newport',0,0),(415,'NJ','Newtonville',0,0),(416,'NJ','Port Elizabeth',0,0),(417,'NJ','Port Norris',0,0),(418,'NJ','Richland',0,0),(419,'NJ','Rosenhayn',0,0),(420,'NJ','Shiloh',0,0),(421,'NJ','Vineland',0,0),(422,'NJ','Atlantic City',0,0),(423,'NJ','Margate City',0,0),(424,'NJ','Longport',0,0),(425,'NJ','Ventnor City',0,0),(426,'NJ','Allentown',0,0),(427,'NJ','Belle Mead',0,0),(428,'NJ','Bordentown',0,0),(429,'NJ','Clarksburg',0,0),(430,'NJ','Cookstown',0,0),(431,'NJ','Cranbury',0,0),(432,'NJ','Creamridge',0,0),(433,'NJ','Crosswicks',0,0),(434,'NJ','Florence',0,0),(435,'NJ','Hightstown',0,0),(436,'NJ','Hopewell',0,0),(437,'NJ','Jackson',0,0),(438,'NJ','Kingston',0,0),(439,'NJ','Lambertville',0,0),(440,'NJ','New Egypt',0,0),(441,'NJ','Pennington',0,0),(442,'NJ','Perrineville',0,0),(443,'NJ','Plainsboro',0,0),(444,'NJ','Princeton',0,0),(445,'NJ','Princeton Juncti',0,0),(446,'NJ','Ringoes',0,0),(447,'NJ','Rocky Hill',0,0),(448,'NJ','Roebling',0,0),(449,'NJ','Roosevelt',0,0),(450,'NJ','Skillman',0,0),(451,'NJ','Stockton',0,0),(452,'NJ','Titusville',0,0),(453,'NJ','Windsor',0,0),(454,'NJ','Wrightstown',0,0),(455,'NJ','Trenton',0,0),(456,'NJ','Hamilton',0,0),(457,'NJ','Mercerville',0,0),(458,'NJ','Yardville',0,0),(459,'NJ','West Trenton',0,0),(460,'NJ','Fort Dix',0,0),(461,'NJ','Mc Guire Afb',0,0),(462,'NJ','Lawrenceville',0,0),(463,'NJ','Lakewood',0,0),(464,'NJ','Allenwood',0,0),(465,'NJ','Bayville',0,0),(466,'NJ','Beachwood',0,0),(467,'NJ','Osbornsville',0,0),(468,'NJ','Brick',0,0),(469,'NJ','Brielle',0,0),(470,'NJ','Forked River',0,0),(471,'NJ','Island Heights',0,0),(472,'NJ','Lakehurst Naec',0,0),(473,'NJ','Lanoka Harbor',0,0),(474,'NJ','Lavallette',0,0),(475,'NJ','Manasquan',0,0),(476,'NJ','Mantoloking',0,0),(477,'NJ','Ocean Gate',0,0),(478,'NJ','Pine Beach',0,0),(479,'NJ','Bay Head',0,0),(480,'NJ','Sea Girt',0,0),(481,'NJ','Seaside Heights',0,0),(482,'NJ','Seaside Park',0,0),(483,'NJ','Toms River',0,0),(484,'NJ','Waretown',0,0),(485,'NJ','Whiting',0,0),(486,'NJ','Annandale',0,0),(487,'NJ','Pattenburg',0,0),(488,'NJ','Bloomsbury',0,0),(489,'NJ','Bound Brook',0,0),(490,'NJ','Bridgewater',0,0),(491,'NJ','Clinton',0,0),(492,'NJ','Dayton',0,0),(493,'NJ','Green Brook',0,0),(494,'NJ','East Brunswick',0,0),(495,'NJ','Edison',0,0),(496,'NJ','Flagtown',0,0),(497,'NJ','Flemington',0,0),(498,'NJ','Franklin Park',0,0),(499,'NJ','Kendall Park',0,0),(500,'NJ','Frenchtown',0,0),(501,'NJ','Glen Gardner',0,0),(502,'NJ','Hampton',0,0),(503,'NJ','Helmetta',0,0),(504,'NJ','High Bridge',0,0),(505,'NJ','Iselin',0,0),(506,'NJ','Jamesburg',0,0),(507,'NJ','Keasbey',0,0),(508,'NJ','Lebanon',0,0),(509,'NJ','Manville',0,0),(510,'NJ','Martinsville',0,0),(511,'NJ','Metuchen',0,0),(512,'NJ','Middlesex',0,0),(513,'NJ','Milford',0,0),(514,'NJ','Milltown',0,0),(515,'NJ','Monmouth Junctio',0,0),(516,'NJ','Neshanic Station',0,0),(517,'NJ','Piscataway',0,0),(518,'NJ','Old Bridge',0,0),(519,'NJ','Oldwick',0,0),(520,'NJ','Parlin',0,0),(521,'NJ','Perth Amboy',0,0),(522,'NJ','Fords',0,0),(523,'NJ','Alpha',0,0),(524,'NJ','Pittstown',0,0),(525,'NJ','Raritan',0,0),(526,'NJ','Sayreville',0,0),(527,'NJ','Somerset',0,0),(528,'NJ','North Branch',0,0),(529,'NJ','Laurence Harbor',0,0),(530,'NJ','South Bound Broo',0,0),(531,'NJ','South River',0,0),(532,'NJ','Spotswood',0,0),(533,'NJ','Stewartsville',0,0),(534,'NJ','Three Bridges',0,0),(535,'NJ','Whitehouse Station',0,0),(536,'NJ','New Brunswick',0,0),(537,'NJ','North Brunswick',0,0),(538,'NJ','Highland Park',0,0),(539,'NJ','Governors Island',0,0),(540,'NJ','Rochelle Park',0,0),(541,'NJ','West New York',0,0),(542,'NJ','Saddle River',0,0),(543,'NJ','Westwood',0,0),(544,'NJ','River Vale',0,0),(545,'NJ','Prospect Park',0,0),(546,'NJ','Plainfield',0,0),(547,'NJ','Woodcliff Lake',0,0);
/*!40000 ALTER TABLE `opt_Cities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opt_TransportTo`
--

DROP TABLE IF EXISTS `opt_TransportTo`;
CREATE TABLE `opt_TransportTo` (
  `ott_id` int(10) unsigned NOT NULL auto_increment,
  `ott_name` varchar(50) default NULL,
  `ott_order` int(10) unsigned default NULL,
  PRIMARY KEY  (`ott_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `opt_TransportTo`
--

LOCK TABLES `opt_TransportTo` WRITE;
/*!40000 ALTER TABLE `opt_TransportTo` DISABLE KEYS */;
INSERT INTO `opt_TransportTo` VALUES (17,'None',1),(18,'Valley',2),(19,'Pts Home',3),(20,'Hackensack',4),(21,'St Joes Paterson',5),(22,'St Joes Wayne',6),(23,'Englewood',7),(24,'Good Samaritan',8),(25,'Holy Name',9),(26,'Bergen Regional',10),(27,'Other (See Comments)',11);
/*!40000 ALTER TABLE `opt_TransportTo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opt_streets`
--

DROP TABLE IF EXISTS `opt_streets`;
CREATE TABLE `opt_streets` (
  `os_id` int(10) unsigned NOT NULL auto_increment,
  `state_abbrev` varchar(3) default NULL,
  `city` varchar(100) default NULL,
  `street` varchar(100) default NULL,
  `zipcode` varchar(12) default NULL,
  PRIMARY KEY  (`os_id`)
) ENGINE=MyISAM AUTO_INCREMENT=270 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `opt_streets`
--

LOCK TABLES `opt_streets` WRITE;
/*!40000 ALTER TABLE `opt_streets` DISABLE KEYS */;
INSERT INTO `opt_streets` VALUES (135,'NJ','Midland Park','Aqueduct Ave','07432'),(136,'NJ','Midland Park','Arminda Ave','07432'),(137,'NJ','Midland Park','Baldin Dr','07432'),(138,'NJ','Midland Park','Balsam St','07432'),(139,'NJ','Midland Park','Bank St','07432'),(140,'NJ','Midland Park','Belle Ct','07432'),(141,'NJ','Midland Park','Birch St','07432'),(142,'NJ','Midland Park','Brandon Rd','07432'),(143,'NJ','Midland Park','Brautigam Ln','07432'),(144,'NJ','Midland Park','Busteed Dr','07432'),(145,'NJ','Midland Park','Butternut Ave','07432'),(146,'NJ','Midland Park','Burma Rd','07432'),(147,'NJ','Midland Park','Canterbury Dr','07432'),(148,'NJ','Midland Park','Cantrell Rd','07432'),(149,'NJ','Midland Park','Cedar St','07432'),(150,'NJ','Midland Park','Center St','07432'),(151,'NJ','Midland Park','Central Ave','07432'),(152,'NJ','Midland Park','Chamberlain Pl','07432'),(153,'NJ','Midland Park','Chestnut St','07432'),(154,'NJ','Midland Park','Clinton Ave','07432'),(155,'NJ','Midland Park','College Rd','07432'),(156,'NJ','Midland Park','Colonial Rd','07432'),(157,'NJ','Midland Park','Colonial Dr','07432'),(158,'NJ','Midland Park','Coombs Ln','07432'),(159,'NJ','Midland Park','Cornell St','07432'),(160,'NJ','Midland Park','Cottage St','07432'),(161,'NJ','Midland Park','Crest Dr','07432'),(162,'NJ','Midland Park','Cross Ave','07432'),(163,'NJ','Midland Park','Cyphers Ln','07432'),(164,'NJ','Midland Park','Dairy St','07432'),(165,'NJ','Midland Park','De Heer Ct','07432'),(166,'NJ','Midland Park','Demund Ln','07432'),(167,'NJ','Midland Park','Donna Ln','07432'),(168,'NJ','Midland Park','Drews Ln','07432'),(169,'NJ','Midland Park','East Center St','07432'),(170,'NJ','Midland Park','East Payne Ave','07432'),(171,'NJ','Midland Park','East Summit Ave','07432'),(172,'NJ','Midland Park','Englishman Dr','07432'),(173,'NJ','Midland Park','Erie Ave','07432'),(174,'NJ','Midland Park','Estes Ct','07432'),(175,'NJ','Midland Park','Evergreen St','07432'),(176,'NJ','Midland Park','Fairhaven Dr','07432'),(177,'NJ','Midland Park','Fairview Ave','07432'),(178,'NJ','Midland Park','Faner Rd','07432'),(179,'NJ','Midland Park','Fifth St','07432'),(180,'NJ','Midland Park','First St','07432'),(181,'NJ','Midland Park','Floral Ln','07432'),(182,'NJ','Midland Park','Foster Ct','07432'),(183,'NJ','Midland Park','Franklin Ave','07432'),(184,'NJ','Midland Park','Garret Pl','07432'),(185,'NJ','Midland Park','Glen Ave','07432'),(186,'NJ','Midland Park','Gobel Ter','07432'),(187,'NJ','Midland Park','Godwin Ave','07432'),(188,'NJ','Midland Park','Goffle Ave','07432'),(189,'NJ','Midland Park','Golon Ct','07432'),(190,'NJ','Midland Park','Greenwood Ave','07432'),(191,'NJ','Midland Park','Grove St','07432'),(192,'NJ','Midland Park','Habben Ave','07432'),(193,'NJ','Midland Park','Hampshire Rd','07432'),(194,'NJ','Midland Park','Heights Rd','07432'),(195,'NJ','Midland Park','Hemlock St','07432'),(196,'NJ','Midland Park','Hiawatha Ave','07432'),(197,'NJ','Midland Park','Highland Ave','07432'),(198,'NJ','Midland Park','Highwood Ave','07432'),(199,'NJ','Midland Park','Hill St','07432'),(200,'NJ','Midland Park','Hillside Ave','07432'),(201,'NJ','Midland Park','Hilton Ave','07432'),(202,'NJ','Midland Park','Irving St','07432'),(203,'NJ','Midland Park','Kew Ct','07432'),(204,'NJ','Midland Park','Lake Ave','07432'),(205,'NJ','Midland Park','Lake View Dr','07432'),(206,'NJ','Midland Park','Linden Pl','07432'),(207,'NJ','Midland Park','Linwood Ave','07432'),(208,'NJ','Midland Park','Logan Dr','07432'),(209,'NJ','Midland Park','Madison Ave','07432'),(210,'NJ','Midland Park','Maltbie Ave','07432'),(211,'NJ','Midland Park','Maple Ave','07432'),(212,'NJ','Midland Park','Maple St','07432'),(213,'NJ','Midland Park','Meadow Ct','07432'),(214,'NJ','Midland Park','Meda Pl','07432'),(215,'NJ','Midland Park','Midland Ave','07432'),(216,'NJ','Midland Park','Miedama Pl','07432'),(217,'NJ','Midland Park','Millington Dr','07432'),(218,'NJ','Midland Park','Monroe St','07432'),(219,'NJ','Midland Park','Morrow Rd','07432'),(220,'NJ','Midland Park','Mountain Ave','07432'),(221,'NJ','Midland Park','Mulders Ln','07432'),(222,'NJ','Midland Park','Myrtle Ave','07432'),(223,'NJ','Midland Park','Oak Ave','07432'),(224,'NJ','Midland Park','Oak Hill Rd','07432'),(225,'NJ','Midland Park','Orchard St','07432'),(226,'NJ','Midland Park','Park Ave','07432'),(227,'NJ','Midland Park','Parker Pl','07432'),(228,'NJ','Midland Park','Parmeter Ct','07432'),(229,'NJ','Midland Park','Paterson Ave','07432'),(230,'NJ','Midland Park','Payne Ave','07432'),(231,'NJ','Midland Park','Pierce Ave','07432'),(232,'NJ','Midland Park','Pine St','07432'),(233,'NJ','Midland Park','Plane St','07432'),(234,'NJ','Midland Park','Pleasant Ave','07432'),(235,'NJ','Midland Park','Post St','07432'),(236,'NJ','Midland Park','Princeton Ave','07432'),(237,'NJ','Midland Park','Prospect St','07432'),(238,'NJ','Midland Park','Rea Ave','07432'),(239,'NJ','Midland Park','Roetman Ct','07432'),(240,'NJ','Midland Park','Rogers Ct','07432'),(241,'NJ','Midland Park','Rubble St','07432'),(242,'NJ','Midland Park','Second St','07432'),(243,'NJ','Midland Park','Short St','07432'),(244,'NJ','Midland Park','Sicomac Ave','07432'),(245,'NJ','Midland Park','Smith Ln','07432'),(246,'NJ','Midland Park','Smith Pl','07432'),(247,'NJ','Midland Park','Smithfield Rd','07432'),(248,'NJ','Midland Park','South Rea Ave','07432'),(249,'NJ','Midland Park','Spruce St','07432'),(250,'NJ','Midland Park','Sunset Ave','07432'),(251,'NJ','Midland Park','Susan Ave','07432'),(252,'NJ','Midland Park','Third St','07432'),(253,'NJ','Midland Park','Van Blarcom Ave','07432'),(254,'NJ','Midland Park','Van Dyke Ave','07432'),(255,'NJ','Midland Park','Vreeland Ave','07432'),(256,'NJ','Midland Park','Waldo Ave','07432'),(257,'NJ','Midland Park','Walnut St','07432'),(258,'NJ','Midland Park','Wedlake Ct','07432'),(259,'NJ','Midland Park','West St','07432'),(260,'NJ','Midland Park','Westbrook Ave','07432'),(261,'NJ','Midland Park','West Summit Ave','07432'),(262,'NJ','Midland Park','West View Pl','07432'),(263,'NJ','Midland Park','West View Ter','07432'),(264,'NJ','Midland Park','Woodside Ave','07432'),(265,'NJ','Midland Park','Wostbrock Ln','07432'),(266,'NJ','Midland Park','Witte Dr','07432'),(267,'NJ','Midland Park','Zimmer Ave','07432'),(268,'NJ','Midland Park','Zimmerman Ct','07432'),(269,'NJ','Midland Park','Fourth Street','07432');
/*!40000 ALTER TABLE `opt_streets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patients`
--

DROP TABLE IF EXISTS `patients`;
CREATE TABLE `patients` (
  `Pkey` int(10) unsigned NOT NULL auto_increment,
  `patient_id` int(10) unsigned NOT NULL,
  `FirstName` varchar(50) default NULL,
  `LastName` varchar(50) default NULL,
  `MiddleName` varchar(50) default NULL,
  `DOB` date default NULL,
  `Sex` varchar(6) default NULL,
  `StreetNumber` varchar(6) default NULL,
  `Street` varchar(50) default NULL,
  `AptNumber` varchar(10) default NULL,
  `State` varchar(10) default NULL,
  `City` varchar(100) default NULL,
  `is_from_old_calls` tinyint(1) unsigned NOT NULL default '0',
  `is_deprecated` tinyint(1) unsigned NOT NULL default '0',
  `deprecated_by_id` int(10) unsigned default NULL,
  `deprecates_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`Pkey`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-05-17  3:22:32
