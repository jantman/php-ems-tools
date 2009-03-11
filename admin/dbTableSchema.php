<?php
// admin/dbTableSchema.php
//
// This includes the SQL to setup all tables. It is included in the install
// script and also can be dumped for use in a .sql file.
//
// +----------------------------------------------------------------------+
// | PHP EMS Tools      http://www.php-ems-tools.com                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006, 2007 Jason Antman.                               |
// |                                                                      |
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation; either version 3 of the License, or    |
// | (at your option) any later version.                                  |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to:                           |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+
// |Please use the above URL for bug reports and feature/support requests.|
// +----------------------------------------------------------------------+
// | Authors: Jason Antman <jason@jasonantman.com>                        |
// +----------------------------------------------------------------------+
// | $LastChangedRevision::                                             $ |
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/admin/dbTableS#$ |
// +----------------------------------------------------------------------+


$dbTableSchemaA = array(); // the array that holds all queries to setup the table
// setup is an array of arrays of strings like (tableName, description, query)

    // define the queries
$addBk = "CREATE TABLE `addBk` (`pKey` int(10) NOT NULL auto_increment, `company` tinytext, `description` tinytext, `contact` tinytext, `address` tinytext, `phone1` tinytext, `phone2` tinytext, `fax` tinytext, `email` tinytext, `notes` blob, `web` tinytext, PRIMARY KEY  (`pKey`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
$dbTableSchemaA[] = array('name' => 'addBk', 'description' => "(organization's web-accessible address book)", 'query' => $addBk);

$rigCheck = "CREATE TABLE `rigCheck` ( `pKey` int(11) NOT NULL auto_increment, `timeStamp` int(11) default NULL, `crew1` tinytext, `crew2` tinytext, `crew3` tinytext, `crew4` tinytext, `rig` tinytext, `comments` text, `stillBroken` text, `sigID` tinytext, `OK` text, `NG` text, `mileage` int(6) default NULL, PRIMARY KEY  (`pKey`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
$dbTableSchemaA[] = array('name' => 'rigCheck', 'description' => "(blank rigCheck table)", 'query' => $rigCheck);

$roster = "CREATE TABLE `roster` ( `EMTid` tinytext NOT NULL, `LastName` tinytext NOT NULL, `FirstName` tinytext NOT NULL, `password` tinytext, `rightsLevel` tinyint(4) NOT NULL default '0', `status` text NOT NULL, `driver` tinyint(1) NOT NULL default '1', `Address` text, `HomePhone` tinytext, `CellPhone` tinytext, `Email` tinytext, `CPR` int(11) default NULL, `EMT` int(11) default NULL, `HazMat` int(11) default NULL, `BBP` int(11) default NULL, `ICS100` int(11) default NULL, `ICS200` int(11) default NULL, `NIMS` int(11) default NULL, `Pkey` int(11) NOT NULL default '0', `SpouseName` varchar(30) character set latin1 collate latin1_bin default NULL, `pwdMD5` tinytext, `shownAs` varchar(15) default NULL, `unitID` tinytext, `textEmail` tinytext, `position` tinytext, `comm1` tinytext, `comm1pos` tinytext, `comm2` tinytext, `comm2pos` tinytext, `officer` tinytext, `PHTLS` int(11) default NULL, `NREMT` int(11) default NULL, `FR` int(11) default NULL, `trustee` tinytext, `comm3` tinytext, `comm3pos` tinytext, `OtherPositions` text, `OtherCerts` text) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
$dbTableSchemaA[] = array('name' => 'roster', 'description' => '(empty roster table)', 'query' => $roster);

$sched_change_temp = "CREATE TABLE `schedule_change_template` ( `pKey` int(11) NOT NULL auto_increment, `timestamp` int(11) default NULL, `query` text, `EMTid` tinytext, `host` tinytext, `address` tinytext, `form` tinytext, PRIMARY KEY  (`pKey`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
$dbTableSchemaA[] = array('name' => 'sched_change_temp', 'description' => '(template for the montly schedule change tracking table)', 'query' => $sched_change_temp);

$schedule = "CREATE TABLE `schedule_template` ( `PKey` int(11) NOT NULL auto_increment, `Date` tinyint(4) NOT NULL default '0', `1ID` varchar(6) default NULL, `1Start` time default NULL, `1End` time default NULL, `2ID` varchar(6) default NULL, `2Start` time default NULL, `2End` time default NULL, `3ID` varchar(6) default NULL, `3Start` time default NULL, `3End` time default NULL, `4ID` varchar(6) default NULL, `4Start` time default NULL, `4End` time default NULL, `5ID` varchar(6) default NULL, `5Start` time default NULL, `5End` time default NULL, `6ID` varchar(6) default NULL, `6Start` time default NULL, `6End` time default NULL, `message` varchar(50) default NULL, PRIMARY KEY  (`PKey`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
$dbTableSchemaA[] = array('name' => 'schedule_template', 'description' => '(blank schedule template table)', 'query' => $schedule);

$schedule_data = "INSERT INTO `schedule_template` VALUES (1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,8,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,10,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,11,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,12,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,13,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,14,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,15,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,16,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,17,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,18,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,19,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(20,20,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,21,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(22,22,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,23,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,24,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(25,25,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(26,26,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(27,27,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(28,28,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(29,29,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(30,30,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(31,31,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(32,32,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);";
$dbTableSchemaA[] = array('name' => 'schedule_template data', 'description' => '(inserting date data into schedule template)', 'query' => $schedule_data);

?>