<?php 
// handlers/messageForm.php
//
// handler for the daily message form
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
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/handlers/messa#$ |
// +----------------------------------------------------------------------+


require_once ('../config/config.php'); // main configuration
require_once('../config/scheduleConfig.php'); // schedule configuration
require_once('../inc/global.php');
require_once('../inc/logging.php');

$action = $_POST['action'];
$ts = (int)$_POST['ts'];
$adminID = $_POST['adminID'];
$adminPW = md5($_POST['adminPW']);
$message_text = $_POST['message_text'];
if(isset($_POST['showAllShifts'])){ $showAllShifts = true;}
if(isset($_POST['id'])){ $id = (int)$_POST['id'];}

$year = date("Y", $ts);
$month = date("m", $ts);
$date = date("d", $ts);
$shiftID = shiftNameToID(tsToShiftName($ts));
if(isset($showAllShifts)){ $shiftID = 0;}


// start MySQL connection
$conn = mysql_connect()   or die("ERROR: I'm sorry, the MySQL connection failed at mysql_connect.");
mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!");

//AUTHENTICATION
if($requireAuthDailyMessage)
{
    $query = 'SELECT pwdMD5,rightsLevel FROM roster WHERE EMTid="'.mysql_real_escape_string($adminID).'";';
    $result = mysql_query($query) or die ("Auth Query Error");
    $row = mysql_fetch_array($result);
    $rightsLevel = $row['rightsLevel'];
    if(($adminPW <> $row['pwdMD5']) || ($rightsLevel < $minRightsDailyMessage))
    {
	die("ERROR: Invalid Admin Username or Password. This action requires authentication.");
    }
}

if($action == "remove" && (! isset($id)))
{
    die("ERROR: You cannot remove a message that does not exist.");
}



if($action=='remove')
{
    //remove 
    $query = 'UPDATE schedule_dailyMessage SET deprecated=1 WHERE sched_message_id='.$id.';';
    $result = mysql_query($query) or die ("ERROR: Query Error1");
    logMessageForm($id, null, $adminID, 1, "handlers/messageForm.php", $action, $query);
}
else
{
    //remove 
    $query = "";
    if(isset($id))
    {
	$query = 'UPDATE schedule_dailyMessage SET deprecated=1 WHERE sched_message_id='.$id.';';
	$result = mysql_query($query) or die ("ERROR: Query Error2");
    }
    // add new
    $query2 = "INSERT INTO schedule_dailyMessage SET shift_start_ts=".$ts.",message_text='".$message_text."',sched_year=".$year.",sched_month=".$month.",sched_date=".$date.",sched_shift_id=".$shiftID.";";
    $result = mysql_query($query2) or die ("ERROR: Query Error3");
    $newID = mysql_insert_id();
    logMessageForm($id, $newID, $adminID, 1, "handlers/messageForm.php", $action, ($query.$query2));
}

echo "OK.";
?>