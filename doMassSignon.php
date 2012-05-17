<?php
// doMassSignon.php
//
// Simple form to allow members to sign on for multiple shifts at once.
// second version, 2010-07-20 for second schedule version
//
// +----------------------------------------------------------------------+
// | PHP EMS Tools      http://www.php-ems-tools.com                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006-2010 Jason Antman.                                |
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
//      $Id: massSignOns.php,v 1.4 2007/09/20 00:00:40 jantman Exp $

// this file will import the user's customization
require_once('./config/config.php');
require_once('./config/scheduleConfig.php'); // schedule configuration
require_once('./inc/sched.php');
require_once('./inc/global.php');
require_once('./inc/logging.php');
require_once('./inc/massSignon.php.inc');
require_once('./inc/'.$config_i18n_filename);

// get the URL variables
if(! empty($_POST['year'])){ $year = (int)$_POST['year'];} else { die("No year specified.\n");}
if(! empty($_POST['month'])){ $month = (int)$_POST['month'];} else { die("No month specified.\n");}
if(! empty($_POST['shift'])){ $shift = (int)$_POST['shift'];} else { die("No shift specified.\n");}
if(! empty($_POST['EMTid'])){ $EMTid = $_POST['EMTid'];} else { die("No EMTid specified.\n");}
if(! empty($_POST['password'])){ $pass = $_POST['password'];} else { die("No password specified.\n");}
if(! empty($_POST['start'])){ $start = $_POST['start'];} else { die("No start time specified.\n");}
if(! empty($_POST['end'])){ $end = $_POST['end'];} else { die("No end time specified.\n");}

// start MySQL connection
$conn = mysql_connect()   or die("ERROR: ".$i18n_strings["signOnWarnings"]["noDBconnect"].$errorCancel);
mysql_select_db($dbName) or die ("ERROR: ".$i18n_strings["signOnWarnings"]["noDBselect"].$errorCancel);

// get the shiftID
$shiftID = shiftNameToID(strtolower($shift));

// make a timestamp for this calendar
$timestamp = strtotime($year."-".$month."-01");
$monthName = date("F", $timestamp); // the full textual name of the month

$mainDate = $timestamp;

// THIS IS THE BEGINNING OF THE HTML
echo '<html>';
echo '<head>';
echo '<meta http-equiv="refresh" content="180">';
echo '<link rel="stylesheet" href="php_ems.css" type="text/css">'; // the location of the CSS file for the schedule
echo '<link rel="stylesheet" href="massSignon.css" type="text/css">'; // the location of the CSS file for the schedule
echo '<title>'.$orgName." Schedule Mass Signons RESULTS - ".$monthName." ".$year." ".$shift.'</title>';
echo '<script type="text/javascript" src="php-ems-tools.js"> </script>';
echo '</head>';
echo '<body>';

// output the header
echo '<h3 align=center>'.$shortName.' Mass Signon RESULTS for '.$monthName.' '.$year.' - '.$shift.'</h3>';

// navigation links
echo '<p align=center>';
echo '<a href="schedule.php?year='.$year.'&month='.$month.'&shift='.$shift.'">Schedule</a>';
echo '</p>';

echo '<p><strong>Results of Mass Signon Request:</strong></p>'."\n";

$fatalError = false;

// validate start and end times
$temp_ts_ary = makeTimestampsFromTimes(time(), $start, $end);
$start_ts = $temp_ts_ary['start'];
$end_ts = $temp_ts_ary['end'];
if($start_ts >= $end_ts)
{
    echo '<p><strong>ERROR:</strong> Start time must be before end time.</p>'."\n";
    $fatalError = true;
}

// validate the login credentials
if(! idInDB($EMTid))
{
    echo '<p><strong>ERROR:</strong> '.$EMTid.' is not a valid EMTid.</p>'."\n";
    $fatalError = true;
}
if(! schedAuth($EMTid, md5($pass)))
{
    echo '<p><strong>ERROR:</strong> Invalid password for EMTid '.$EMTid.'.</p>'."\n";
    $fatalError = true;
}

foreach($_POST as $key => $val)
{
    if($fatalError){ break;}

    if(substr($key, 0, 7) != "signon_"){ continue;}
    $parts = explode("_", $key);
    $ts = $parts[1];
    $signon_date = $parts[2];

    $year = date("Y", $ts);
    $month = date("m", $ts);
    $date = date("d", $ts);
    $shift = tsToShiftName($ts);

    $temp_ts_ary = makeTimestampsFromTimes($ts, $start, $end);
    $start_ts = $temp_ts_ary['start'];
    $end_ts = $temp_ts_ary['end'];

    echo '<p><strong>'.date("D m-d", $ts).'</strong> - ';

    if($start_ts >= $end_ts)
    {
	echo '<span class="errorText"><strong>ERROR:</strong> Start time must be before end time.</span></p>';
	continue;
    }

    if(memberIsOnDutyInInterval($EMTid, $start_ts, $end_ts-1))
    {
	echo '<span class="errorText"><strong>ERROR:</strong> Member '.$EMTid.' is already signed on between '.date("Y-m-d H:i", $start_ts).' and '.date("Y-m-d H:i", $end_ts).'</span></p>';
	continue;
    }

    if(isProbie($EMTid) && probieIsOnDutyInInterval($start_ts, $end_ts)) // if other probie is on duty
    {
	echo '<span class="errorText"><strong>ERROR:</strong> Another probie is already signed on between '.date("Y-m-d H:i", $start_ts).' and '.date("Y-m-d H:i", $end_ts).'</span></p>';
	continue;
    }

    // - add the schedule entry
    $query = 'INSERT INTO '.$config_sched_table.' SET EMTid="'.mysql_real_escape_string($EMTid).'",start_ts='.$start_ts.',end_ts='.$end_ts.',sched_year='.$year.',sched_month='.$month.',sched_date='.$date.',sched_shift_id='.shiftNameToID($shift).' ;';
    if($debug){ error_log("doMassSignon.php - NEW SIGNON. Query: ".$query."\n");}
    $result = mysql_query($query) or die ("ERROR: INSERT Query Error");
    $newID = mysql_insert_id();
    // - add a schedule_changes entry
    logMassSignonForm(null, $newID, null, false, "doMassSignon.php", "signon", $query);
    echo "Successfully signed on (".$newID.")</p>\n";
}

/*
DESCRIBE schedule; DESCRIBE schedule_changes;
+----------------+-------------+------+-----+---------+----------------+
| Field          | Type        | Null | Key | Default | Extra          |
+----------------+-------------+------+-----+---------+----------------+
| sched_entry_id | int(11)     | NO   | PRI | NULL    | auto_increment | 
| EMTid          | varchar(10) | YES  |     | NULL    |                | 
| start_ts       | int(11)     | YES  |     | NULL    |                | 
| end_ts         | int(11)     | YES  |     | NULL    |                | 
| sched_year     | smallint(6) | YES  |     | NULL    |                | 
| sched_month    | tinyint(4)  | YES  |     | NULL    |                | 
| sched_date     | tinyint(4)  | YES  |     | NULL    |                | 
| sched_shift_id | tinyint(4)  | YES  |     | NULL    |                | 
| deprecated     | tinyint(4)  | YES  |     | 0       |                | 
+----------------+-------------+------+-----+---------+----------------+
9 rows in set (0.01 sec)

+------------------------+------------------+------+-----+---------+----------------+
| Field                  | Type             | Null | Key | Default | Extra          |
+------------------------+------------------+------+-----+---------+----------------+
| sched_change_ID        | int(10) unsigned | NO   | PRI | NULL    | auto_increment | 
| deprecated_sched_ID    | int(11)          | YES  |     | NULL    |                | 
| deprecated_by_sched_ID | int(11)          | YES  |     | NULL    |                | 
| change_ts              | int(11)          | YES  |     | NULL    |                | 
| admin_username         | varchar(50)      | YES  |     | NULL    |                | 
| remote_host            | varchar(50)      | YES  |     | NULL    |                | 
| php_auth_username      | varchar(50)      | YES  |     | NULL    |                | 
| form                   | varchar(20)      | YES  |     | NULL    |                | 
| queries                | text             | YES  |     | NULL    |                | 
| admin_auth_success     | tinyint(1)       | YES  |     | 0       |                | 
| auth_type              | varchar(50)      | YES  |     | NULL    |                | 
| action                 | varchar(50)      | YES  |     | NULL    |                | 
+------------------------+------------------+------+-----+---------+----------------+
12 rows in set (0.01 sec)
*/

// END DEBUG

echo "<table class='cal'>\n";
showCurrentMonth(true);
echo "</table>\n";


?>

</body>
</html>