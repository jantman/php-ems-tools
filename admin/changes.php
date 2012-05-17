<html>
<head>
<meta http-equiv="refresh" content="180">

<?php
// admin/changes.php
//
// Page to view schedule change history.
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
// | $LastChangedRevision:: 155                                         $ |
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/admin/changes.#$ |
// +----------------------------------------------------------------------+

// TODO: sort by EMTid, show only EMTid, sort by date, show only date (both changes made and date of shift for change)

// 
// DO NOT MAKE CHANGES
// UNLESS YOU KNOW WHAT YOU ARE DOING.
// 
require('../custom.php');

global $dbName;

if(! empty($_GET['year']))
{
    $year = $_GET['year'];
}
else
{
    $year = date("Y");
}

if(! empty($_GET['month']))
{
    $month = $_GET['month'];
}
else
{
    $month = date("m");
}
// DB Connection
global $shortName;
global $serverWebRoot;
echo '<link rel="stylesheet" href="../php_ems.css" type="text/css">';
echo "<title>".$shortName." Schedule Change Log</title></head><body>";
echo "<h3>".$shortName." Schedule Change Log as of ".date("Y-m-d H:i:s")." for ".$year."-".$month."</h3>";

echo '<form name="changes" method="POST"><label for="EMTid"><strong>Search by ID:</strong></label><input type="text" name="EMTid" id="EMTid"';
if(isset($_POST['EMTid'])){ echo ' value="'.$_POST['EMTid'].'"';}
echo ' /><input type="submit" value="Search" /></form>'."\n\n";

echo '<form name="changes" method="POST"><label for="signonID"><strong>Search by Signon ID:</strong></label><input type="text" name="signonID" id="signonID"';
if(isset($_POST['signonID'])){ echo ' value="'.$_POST['signonID'].'"';}
echo ' /><input type="submit" value="Search" /></form>'."\n\n";

$conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
mysql_select_db($dbName) or die ('Unable to select database!');

// get shifts
$shifts = array();
$query = "SELECT sched_shift_id,shiftTitle FROM schedule_shifts;";
$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
while($row = mysql_fetch_assoc($result))
{
    $shifts[$row['sched_shift_id']] = $row['shiftTitle'];
}


$query = "SELECT * FROM changes_view WHERE ";
if(isset($_POST['EMTid']))
{
    $query .= "(new_year=".$year." OR old_year=".$year." ) ";
    $query .= "AND (old_EMTid='".mysql_real_escape_string($_POST['EMTid'])."' OR new_EMTid='".mysql_real_escape_string($_POST['EMTid'])."') ";
}
elseif(isset($_POST['signonID']))
{
    $foo = (int)$_POST['signonID'];
    $query .= "sched_change_ID=$foo OR deprecated_sched_ID=$foo OR deprecated_by_sched_ID=$foo ";
}
else
{
    $query .= "((new_year=".$year." AND new_month=".$month.") OR (old_year=".$year." AND old_month=".$month.")) ";
}
$query .= "ORDER BY change_ts DESC;";
//echo "<p>".$query."</p>\n";
$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());

echo '<p><strong>'.mysql_num_rows($result).' results:</strong></p>';

echo '<table class="roster">';

echo '<tr><th rowspan="2">ID</th><th rowspan="2">Time</th><th rowspan="2">Host</th><th rowspan="2">Admin</th><th rowspan="2">User</th><th rowspan="2">Action</th><th rowspan="2">Form</th><th colspan="8">Old</th><th colspan="8">New</th></tr>'."\n";
echo '<tr><th>ID</th><th>EMTid</th><th>Start</th><th>End</th><th>Year</th><th>Month</th><th>Date</th><th>Shift</th><th>ID</th><th>EMTid</th><th>Start</th><th>End</th><th>Year</th><th>Month</th><th>Date</th><th>Shift</th></tr>'."\n";
while($row = mysql_fetch_assoc($result))
{
    echo '<tr>';
    echo '<td>'.$row['sched_change_ID'].'</td>';
    echo '<td>'.date("Y-m-d H:i:s", $row['change_ts']).'</td>';
    echo '<td>'.$row['remote_host'].'</td>';
    echo '<td>'.$row['admin_username'].'</td>';
    echo '<td>'.$row['php_auth_username'].'</td>';
    echo '<td>'.$row['action'].'</td>';
    echo '<td>'.$row['form'].'</td>';

    if($row['deprecated_sched_ID'] != null) {echo '<td>'.$row['deprecated_sched_ID'].'</td>';} else { echo '<td>&nbsp;</td>';}
    if($row['old_EMTid'] != null) { echo '<td>'.$row['old_EMTid'].'</td>';} else { echo '<td>&nbsp;</td>';}
    if($row['old_start_ts'] != null) { echo '<td>'.date("Y-m-d H:i", $row['old_start_ts']).'</td>';} else { echo '<td>&nbsp;</td>';}
    if($row['old_end_ts'] != null) { echo '<td>'.date("Y-m-d H:i", $row['old_end_ts']).'</td>';} else { echo '<td>&nbsp;</td>';}
    if($row['old_year'] != null) { echo '<td>'.$row['old_year'].'</td>';} else { echo '<td>&nbsp;</td>';}
    if($row['old_month'] != null) { echo '<td>'.$row['old_month'].'</td>';} else { echo '<td>&nbsp;</td>';}
    if($row['old_date'] != null) { echo '<td>'.$row['old_date'].'</td>';} else { echo '<td>&nbsp;</td>';}
    if($row['old_shift_id'] != null) { echo '<td>'.$shifts[$row['old_shift_id']].'</td>';} else { echo '<td>&nbsp;</td>';}

    if($row['deprecated_by_sched_ID'] != null) {echo '<td>'.$row['deprecated_by_sched_ID'].'</td>';} else { echo '<td>&nbsp;</td>';}
    if($row['new_EMTid'] != null) { echo '<td>'.$row['new_EMTid'].'</td>';} else { echo '<td>&nbsp;</td>';}
    if($row['new_start_ts'] != null) { echo '<td>'.date("Y-m-d H:i", $row['new_start_ts']).'</td>';} else { echo '<td>&nbsp;</td>';}
    if($row['new_end_ts'] != null) { echo '<td>'.date("Y-m-d H:i", $row['new_end_ts']).'</td>';} else { echo '<td>&nbsp;</td>';}
    if($row['new_year'] != null) { echo '<td>'.$row['new_year'].'</td>';} else { echo '<td>&nbsp;</td>';}
    if($row['new_month'] != null) { echo '<td>'.$row['new_month'].'</td>';} else { echo '<td>&nbsp;</td>';}
    if($row['new_date'] != null) { echo '<td>'.$row['new_date'].'</td>';} else { echo '<td>&nbsp;</td>';}
    if($row['new_shift_id'] != null) { echo '<td>'.$shifts[$row['new_shift_id']].'</td>';} else { echo '<td>&nbsp;</td>';}

    echo '</tr>'."\n";
}
echo '</table></html>';
mysql_free_result($result);
mysql_close($conn);
die();

?>