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
-- Table structure for table `addBk`
--

DROP TABLE IF EXISTS `addBk`;
CREATE TABLE `addBk` (
  `pKey` int(10) NOT NULL auto_increment,
  `company` tinytext,
  `description` tinytext,
  `contact` tinytext,
  `address` tinytext,
  `phone1` tinytext,
  `phone2` tinytext,
  `fax` tinytext,
  `email` tinytext,
  `notes` blob,
  `web` tinytext,
  PRIMARY KEY  (`pKey`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Table structure for table `archive_roster`
--

DROP TABLE IF EXISTS `archive_roster`;
CREATE TABLE `archive_roster` (
  `EMTid` tinytext NOT NULL,
  `LastName` tinytext NOT NULL,
  `FirstName` tinytext NOT NULL,
  `password` tinytext,
  `rightsLevel` tinyint(4) NOT NULL default '0',
  `status` text NOT NULL,
  `driver` tinyint(1) NOT NULL default '1',
  `Address` text,
  `HomePhone` tinytext,
  `CellPhone` tinytext,
  `Email` tinytext,
  `CPR` int(11) default NULL,
  `EMT` int(11) default NULL,
  `HazMat` int(11) default NULL,
  `BBP` int(11) default NULL,
  `ICS100` int(11) default NULL,
  `ICS200` int(11) default NULL,
  `NIMS` int(11) default NULL,
  `Pkey` int(11) NOT NULL auto_increment,
  `SpouseName` varchar(30) character set latin1 collate latin1_bin default NULL,
  `pwdMD5` tinytext,
  `shownAs` varchar(15) default NULL,
  `unitID` tinytext,
  `textEmail` tinytext,
  `position` tinytext,
  `comm1` tinytext,
  `comm1pos` tinytext,
  `comm2` tinytext,
  `comm2pos` tinytext,
  `officer` tinytext,
  `PHTLS` int(11) default NULL,
  `NREMT` int(11) default NULL,
  `FR` int(11) default NULL,
  `trustee` tinytext,
  `comm3` tinytext,
  `comm3pos` tinytext,
  `dateJoined_ts` int(11) default NULL,
  `dateActive_ts` int(11) default NULL,
  `WorkPhone` varchar(15) default NULL,
  `OtherPhone` varchar(15) default NULL,
  `reminder_emails` tinyint(1) default '1',
  `memberDN` varchar(200) default NULL,
  `userDN` varchar(200) default NULL,
  `uid` int(10) default NULL,
  `username` varchar(50) default NULL,
  `archive_ts` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `update_ts` timestamp NOT NULL default '0000-00-00 00:00:00',
  `cellProvider` int(10) unsigned default NULL,
  `phone3` varchar(15) default NULL,
  `phone4` varchar(15) default NULL,
  `phone5` varchar(15) default NULL,
  `cal_key` varchar(100) default NULL,
  PRIMARY KEY  (`Pkey`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=latin1;

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE `attendance` (
  `att_id` int(10) unsigned NOT NULL auto_increment,
  `date_ts` int(10) unsigned default NULL,
  `start_ts` int(10) unsigned default NULL,
  `end_ts` int(10) unsigned default NULL,
  `type` varchar(50) default NULL,
  `EMTid` varchar(10) default NULL,
  `status` varchar(30) default NULL,
  PRIMARY KEY  (`att_id`)
) ENGINE=MyISAM AUTO_INCREMENT=762 DEFAULT CHARSET=latin1;

--
-- Temporary table structure for view `changes_view`
--

DROP TABLE IF EXISTS `changes_view`;
/*!50001 DROP VIEW IF EXISTS `changes_view`*/;
/*!50001 CREATE TABLE `changes_view` (
  `sched_change_ID` int(10) unsigned,
  `deprecated_sched_ID` int(11),
  `deprecated_by_sched_ID` int(11),
  `change_ts` int(11),
  `admin_username` varchar(50),
  `remote_host` varchar(50),
  `php_auth_username` varchar(50),
  `action` varchar(50),
  `form` varchar(20),
  `old_EMTid` varchar(10),
  `old_start_ts` int(11),
  `old_end_ts` int(11),
  `old_year` smallint(6),
  `old_month` tinyint(4),
  `old_date` tinyint(4),
  `old_shift_id` tinyint(4),
  `new_EMTid` varchar(10),
  `new_start_ts` int(11),
  `new_end_ts` int(11),
  `new_year` smallint(6),
  `new_month` tinyint(4),
  `new_date` tinyint(4),
  `new_shift_id` tinyint(4)
) */;

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
-- Table structure for table `eso_comments`
--

DROP TABLE IF EXISTS `eso_comments`;
CREATE TABLE `eso_comments` (
  `eso_cmt_id` int(10) unsigned NOT NULL auto_increment,
  `cmt_ts` int(10) unsigned default NULL,
  `cmt_text` tinytext,
  `cmt_admin_EMTid` varchar(10) default NULL,
  `cmt_e_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`eso_cmt_id`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=latin1;

--
-- Table structure for table `eso_equipment`
--

DROP TABLE IF EXISTS `eso_equipment`;
CREATE TABLE `eso_equipment` (
  `e_id` int(10) unsigned NOT NULL auto_increment,
  `e_emod_id` int(10) unsigned default NULL,
  `e_et_id` int(10) unsigned default NULL,
  `e_serial` varchar(100) default NULL,
  `e_size` varchar(20) default NULL,
  `e_comment` varchar(100) default NULL,
  PRIMARY KEY  (`e_id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

--
-- Table structure for table `eso_events`
--

DROP TABLE IF EXISTS `eso_events`;
CREATE TABLE `eso_events` (
  `eso_evt_id` int(10) unsigned NOT NULL auto_increment,
  `evt_ts` int(10) unsigned default NULL,
  `evt_equip_id` int(10) unsigned default NULL,
  `evt_status_id` int(10) unsigned default NULL,
  `evt_comment` varchar(100) default NULL,
  `evt_EMTid` varchar(10) default NULL,
  `evt_is_deprecated` tinyint(1) default '0',
  `evt_admin_EMTid` varchar(10) default NULL,
  PRIMARY KEY  (`eso_evt_id`)
) ENGINE=MyISAM AUTO_INCREMENT=74 DEFAULT CHARSET=latin1;

--
-- Table structure for table `eso_opt_equipTypes`
--

DROP TABLE IF EXISTS `eso_opt_equipTypes`;
CREATE TABLE `eso_opt_equipTypes` (
  `et_id` int(10) unsigned NOT NULL auto_increment,
  `et_name` varchar(30) default NULL,
  PRIMARY KEY  (`et_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eso_opt_equipTypes`
--

LOCK TABLES `eso_opt_equipTypes` WRITE;
/*!40000 ALTER TABLE `eso_opt_equipTypes` DISABLE KEYS */;
INSERT INTO `eso_opt_equipTypes` VALUES (1,'Pager'),(2,'Radio'),(3,'Uniform');
/*!40000 ALTER TABLE `eso_opt_equipTypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eso_opt_mfr`
--

DROP TABLE IF EXISTS `eso_opt_mfr`;
CREATE TABLE `eso_opt_mfr` (
  `em_id` int(10) unsigned NOT NULL auto_increment,
  `em_name` varchar(30) default NULL,
  `et_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`em_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eso_opt_mfr`
--

LOCK TABLES `eso_opt_mfr` WRITE;
/*!40000 ALTER TABLE `eso_opt_mfr` DISABLE KEYS */;
INSERT INTO `eso_opt_mfr` VALUES (1,'Motorola',1),(2,'Motorola',2),(3,'Uniform',3);
/*!40000 ALTER TABLE `eso_opt_mfr` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eso_opt_model`
--

DROP TABLE IF EXISTS `eso_opt_model`;
CREATE TABLE `eso_opt_model` (
  `emod_id` int(10) unsigned NOT NULL auto_increment,
  `emod_name` varchar(30) default NULL,
  `emod_mfr_id` int(10) unsigned default NULL,
  `emod_et_id` int(10) unsigned default NULL,
  `emod_model_num` varchar(30) default NULL,
  `emod_has_size` tinyint(1) default '0',
  PRIMARY KEY  (`emod_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Table structure for table `eso_opt_status`
--

DROP TABLE IF EXISTS `eso_opt_status`;
CREATE TABLE `eso_opt_status` (
  `es_id` int(10) unsigned NOT NULL auto_increment,
  `es_name` varchar(20) default NULL,
  `es_is_final` tinyint(1) unsigned default '0',
  `es_default_new` tinyint(1) default '0',
  `es_in_stock` tinyint(1) default '0',
  PRIMARY KEY  (`es_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `eso_opt_status`
--

LOCK TABLES `eso_opt_status` WRITE;
/*!40000 ALTER TABLE `eso_opt_status` DISABLE KEYS */;
INSERT INTO `eso_opt_status` VALUES (1,'New',0,1,1),(2,'Issued',0,0,0),(4,'Out for Service',0,0,0),(5,'In Stock',0,0,1),(6,'Broken',0,0,0),(7,'Possible Problem',0,0,0),(8,'Unknown/Lost',0,0,0),(9,'Lost',1,0,0),(10,'Stolen',1,0,0),(11,'Decommissioned',1,0,0);
/*!40000 ALTER TABLE `eso_opt_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eso_uploads`
--

DROP TABLE IF EXISTS `eso_uploads`;
CREATE TABLE `eso_uploads` (
  `eu_id` int(10) unsigned NOT NULL auto_increment,
  `eu_name` varchar(60) default NULL,
  `eu_type` varchar(100) default NULL,
  `eu_size_b` int(10) unsigned default NULL,
  `eu_content` longblob,
  `eu_mime_type` varchar(100) default NULL,
  `eu_ts` int(10) unsigned default NULL,
  `eu_eid` int(10) unsigned default NULL,
  `eu_comment` varchar(150) default NULL,
  PRIMARY KEY  (`eu_id`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=latin1;

--
-- Table structure for table `log_external`
--

DROP TABLE IF EXISTS `log_external`;
CREATE TABLE `log_external` (
  `pKey` int(11) NOT NULL auto_increment,
  `timestamp` int(11) default NULL,
  `auth_user` tinytext,
  `auth_type` tinytext,
  `URL` tinytext,
  `local_filename` tinytext,
  `remoteIP` tinytext,
  `client` tinytext,
  `additional` text,
  `appID` tinytext,
  PRIMARY KEY  (`pKey`)
) ENGINE=MyISAM AUTO_INCREMENT=15157 DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM AUTO_INCREMENT=94 DEFAULT CHARSET=latin1;

--
-- Table structure for table `opt_cell_providers`
--

DROP TABLE IF EXISTS `opt_cell_providers`;
CREATE TABLE `opt_cell_providers` (
  `ocp_id` int(10) unsigned NOT NULL auto_increment,
  `ocp_name` varchar(50) NOT NULL,
  `email_format` varchar(100) default NULL,
  PRIMARY KEY  (`ocp_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `opt_cell_providers`
--

LOCK TABLES `opt_cell_providers` WRITE;
/*!40000 ALTER TABLE `opt_cell_providers` DISABLE KEYS */;
INSERT INTO `opt_cell_providers` VALUES (1,'Verizon','$$number$$@vtext.com'),(2,'AT&T','$$number$$@txt.att.net'),(3,'Sprint','$$number$$@messaging.sprintpcs.com'),(4,'T-Mobile','$$number$$@tmomail.net');
/*!40000 ALTER TABLE `opt_cell_providers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opt_equip`
--

DROP TABLE IF EXISTS `opt_equip`;
CREATE TABLE `opt_equip` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type_id` int(10) unsigned default NULL,
  `mfr` varchar(100) default NULL,
  `model` varchar(100) default NULL,
  `model_num` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `opt_equip`
--

LOCK TABLES `opt_equip` WRITE;
/*!40000 ALTER TABLE `opt_equip` DISABLE KEYS */;
INSERT INTO `opt_equip` VALUES (1,2,'Motorola','Minitor 5','xxx'),(2,2,'Motorola','Minitor 4','xxx');
/*!40000 ALTER TABLE `opt_equip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opt_units`
--

DROP TABLE IF EXISTS `opt_units`;
CREATE TABLE `opt_units` (
  `oU_pkey` int(10) unsigned NOT NULL auto_increment,
  `oU_unitID` varchar(20) default NULL,
  `oU_order` int(10) unsigned default NULL,
  PRIMARY KEY  (`oU_pkey`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Table structure for table `responding_notices`
--

DROP TABLE IF EXISTS `responding_notices`;
CREATE TABLE `responding_notices` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `start_ts` int(10) unsigned default NULL,
  `end_ts` int(10) unsigned default NULL,
  `type` varchar(10) default NULL,
  `message` varchar(500) default NULL,
  `is_killed` tinyint(1) unsigned default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Table structure for table `rigCheck`
--

DROP TABLE IF EXISTS `rigCheck`;
CREATE TABLE `rigCheck` (
  `pKey` int(11) NOT NULL auto_increment,
  `timeStamp` int(11) default NULL,
  `crew1` tinytext,
  `crew2` tinytext,
  `crew3` tinytext,
  `crew4` tinytext,
  `rig` tinytext,
  `comments` text,
  `stillBroken` text,
  `sigID` tinytext,
  `OK` text,
  `NG` text,
  `mileage` int(6) default NULL,
  PRIMARY KEY  (`pKey`)
) ENGINE=MyISAM AUTO_INCREMENT=361 DEFAULT CHARSET=latin1;

--
-- Table structure for table `rigcheck_items`
--

DROP TABLE IF EXISTS `rigcheck_items`;
CREATE TABLE `rigcheck_items` (
  `itm_id` int(10) unsigned NOT NULL auto_increment,
  `sec_id` int(10) unsigned default NULL,
  `rig_id` varchar(20) default NULL,
  `title` varchar(100) default NULL,
  `item_order` int(10) unsigned default NULL,
  `is_deprecated` tinyint(1) unsigned default '0',
  PRIMARY KEY  (`itm_id`)
) ENGINE=MyISAM AUTO_INCREMENT=189 DEFAULT CHARSET=latin1;

--
-- Table structure for table `rigcheck_rigs`
--

DROP TABLE IF EXISTS `rigcheck_rigs`;
CREATE TABLE `rigcheck_rigs` (
  `rig_id` varchar(20) NOT NULL,
  PRIMARY KEY  (`rig_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Table structure for table `rigcheck_sections`
--

DROP TABLE IF EXISTS `rigcheck_sections`;
CREATE TABLE `rigcheck_sections` (
  `sec_id` int(10) unsigned NOT NULL auto_increment,
  `rig_id` varchar(20) default NULL,
  `title` varchar(50) default NULL,
  `parent_sec_id` int(10) unsigned default NULL,
  `col_num` tinyint(3) unsigned default NULL,
  `col_order` int(10) unsigned default NULL,
  `is_deprecated` tinyint(1) unsigned default '0',
  PRIMARY KEY  (`sec_id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

--
-- Table structure for table `roster`
--

DROP TABLE IF EXISTS `roster`;
CREATE TABLE `roster` (
  `EMTid` tinytext NOT NULL,
  `LastName` tinytext NOT NULL,
  `FirstName` tinytext NOT NULL,
  `password` tinytext,
  `rightsLevel` tinyint(4) NOT NULL default '0',
  `status` text NOT NULL,
  `driver` tinyint(1) NOT NULL default '1',
  `Address` text,
  `HomePhone` tinytext,
  `CellPhone` tinytext,
  `Email` tinytext,
  `CPR` int(11) default NULL,
  `EMT` int(11) default NULL,
  `HazMat` int(11) default NULL,
  `BBP` int(11) default NULL,
  `ICS100` int(11) default NULL,
  `ICS200` int(11) default NULL,
  `NIMS` int(11) default NULL,
  `Pkey` int(11) NOT NULL auto_increment,
  `SpouseName` varchar(30) character set latin1 collate latin1_bin default NULL,
  `pwdMD5` tinytext,
  `shownAs` varchar(15) default NULL,
  `unitID` tinytext,
  `textEmail` tinytext,
  `position` tinytext,
  `comm1` tinytext,
  `comm1pos` tinytext,
  `comm2` tinytext,
  `comm2pos` tinytext,
  `officer` tinytext,
  `PHTLS` int(11) default NULL,
  `NREMT` int(11) default NULL,
  `FR` int(11) default NULL,
  `trustee` tinytext,
  `comm3` tinytext,
  `comm3pos` tinytext,
  `dateJoined_ts` int(11) default NULL,
  `dateActive_ts` int(11) default NULL,
  `WorkPhone` varchar(15) default NULL,
  `OtherPhone` varchar(15) default NULL,
  `reminder_emails` tinyint(1) default '1',
  `memberDN` varchar(200) default NULL,
  `userDN` varchar(200) default NULL,
  `uid` int(10) default NULL,
  `username` varchar(50) default NULL,
  `update_ts` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `cellProvider` int(10) unsigned default NULL,
  `cal_key` varchar(100) default NULL,
  `phone3` varchar(15) default NULL,
  `phone4` varchar(15) default NULL,
  `phone5` varchar(15) default NULL,
  PRIMARY KEY  (`Pkey`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=latin1;

--
-- Table structure for table `schedule`
--

DROP TABLE IF EXISTS `schedule`;
CREATE TABLE `schedule` (
  `sched_entry_id` int(11) NOT NULL auto_increment,
  `EMTid` varchar(10) default NULL,
  `start_ts` int(11) default NULL,
  `end_ts` int(11) default NULL,
  `sched_year` smallint(6) default NULL,
  `sched_month` tinyint(4) default NULL,
  `sched_date` tinyint(4) default NULL,
  `sched_shift_id` tinyint(4) default NULL,
  `deprecated` tinyint(4) default '0',
  PRIMARY KEY  (`sched_entry_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15270 DEFAULT CHARSET=latin1;

--
-- Table structure for table `schedule_changes`
--

DROP TABLE IF EXISTS `schedule_changes`;
CREATE TABLE `schedule_changes` (
  `sched_change_ID` int(10) unsigned NOT NULL auto_increment,
  `deprecated_sched_ID` int(11) default NULL,
  `deprecated_by_sched_ID` int(11) default NULL,
  `change_ts` int(11) default NULL,
  `admin_username` varchar(50) default NULL,
  `remote_host` varchar(50) default NULL,
  `php_auth_username` varchar(50) default NULL,
  `form` varchar(20) default NULL,
  `queries` text,
  `admin_auth_success` tinyint(1) default '0',
  `auth_type` varchar(50) default NULL,
  `action` varchar(50) default NULL,
  PRIMARY KEY  (`sched_change_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=10168 DEFAULT CHARSET=latin1;

--
-- Table structure for table `schedule_dailyMessage`
--

DROP TABLE IF EXISTS `schedule_dailyMessage`;
CREATE TABLE `schedule_dailyMessage` (
  `sched_message_id` int(11) NOT NULL auto_increment,
  `shift_start_ts` int(11) default NULL,
  `sched_year` smallint(6) default NULL,
  `sched_month` tinyint(4) default NULL,
  `sched_date` tinyint(4) default NULL,
  `sched_shift_id` tinyint(4) default NULL,
  `message_text` varchar(100) default NULL,
  `deprecated` tinyint(4) default '0',
  PRIMARY KEY  (`sched_message_id`)
) ENGINE=MyISAM AUTO_INCREMENT=152 DEFAULT CHARSET=latin1;

--
-- Table structure for table `schedule_message_changes`
--

DROP TABLE IF EXISTS `schedule_message_changes`;
CREATE TABLE `schedule_message_changes` (
  `sched_message_change_ID` int(10) unsigned NOT NULL auto_increment,
  `deprecated_message_ID` int(11) default NULL,
  `deprecated_by_message_ID` int(11) default NULL,
  `change_ts` int(11) default NULL,
  `admin_username` varchar(50) default NULL,
  `auth_type` varchar(50) default NULL,
  `admin_auth_success` tinyint(1) default NULL,
  `remote_host` varchar(50) default NULL,
  `php_auth_username` varchar(50) default NULL,
  `form` varchar(20) default NULL,
  `action` varchar(50) default NULL,
  `queries` text,
  PRIMARY KEY  (`sched_message_change_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=167 DEFAULT CHARSET=latin1;

--
-- Table structure for table `schedule_shifts`
--

DROP TABLE IF EXISTS `schedule_shifts`;
CREATE TABLE `schedule_shifts` (
  `sched_shift_id` tinyint(4) NOT NULL auto_increment,
  `shiftName` varchar(30) default NULL,
  `shiftTitle` varchar(30) default NULL,
  `deprecated` int(11) default '0',
  PRIMARY KEY  (`sched_shift_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `schedule_shifts`
--

LOCK TABLES `schedule_shifts` WRITE;
/*!40000 ALTER TABLE `schedule_shifts` DISABLE KEYS */;
INSERT INTO `schedule_shifts` VALUES (1,'day','Day',0),(2,'night','Night',0);
/*!40000 ALTER TABLE `schedule_shifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `web_sessions`
--

DROP TABLE IF EXISTS `web_sessions`;
CREATE TABLE `web_sessions` (
  `sessid` varchar(100) default NULL,
  `start_ts` int(11) default NULL,
  `last_load_ts` int(11) default NULL,
  `EMTid` varchar(10) default NULL,
  `remote_ip` varchar(15) default NULL,
  `user_agent` varchar(400) default NULL,
  `expire_ts` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Final view structure for view `changes_view`
--

/*!50001 DROP TABLE IF EXISTS `changes_view`*/;
/*!50001 DROP VIEW IF EXISTS `changes_view`*/;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`jantman`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `changes_view` AS select `sc`.`sched_change_ID` AS `sched_change_ID`,`sc`.`deprecated_sched_ID` AS `deprecated_sched_ID`,`sc`.`deprecated_by_sched_ID` AS `deprecated_by_sched_ID`,`sc`.`change_ts` AS `change_ts`,`sc`.`admin_username` AS `admin_username`,`sc`.`remote_host` AS `remote_host`,`sc`.`php_auth_username` AS `php_auth_username`,`sc`.`action` AS `action`,`sc`.`form` AS `form`,`s1`.`EMTid` AS `old_EMTid`,`s1`.`start_ts` AS `old_start_ts`,`s1`.`end_ts` AS `old_end_ts`,`s1`.`sched_year` AS `old_year`,`s1`.`sched_month` AS `old_month`,`s1`.`sched_date` AS `old_date`,`s1`.`sched_shift_id` AS `old_shift_id`,`s2`.`EMTid` AS `new_EMTid`,`s2`.`start_ts` AS `new_start_ts`,`s2`.`end_ts` AS `new_end_ts`,`s2`.`sched_year` AS `new_year`,`s2`.`sched_month` AS `new_month`,`s2`.`sched_date` AS `new_date`,`s2`.`sched_shift_id` AS `new_shift_id` from ((`schedule_changes` `sc` left join `schedule` `s1` on((`sc`.`deprecated_sched_ID` = `s1`.`sched_entry_id`))) left join `schedule` `s2` on((`sc`.`deprecated_by_sched_ID` = `s2`.`sched_entry_id`))) */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-05-17  3:22:32
