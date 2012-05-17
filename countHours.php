<?php
// countHours.php
//
// Page to count up each members' monthly scheduled hours.
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
//      $Id: countHours.php,v 1.4 2007/09/20 00:00:40 jantman Exp $

require_once('./config/config.php'); // main configuration
require_once('./config/scheduleConfig.php'); // schedule configuration
require_once('./config/rosterConfig.php'); // roster configuration
require_once('./inc/global.php'); // global functions
require_once('inc/sched.php');

if(! empty($_GET['style']))
{
    //style monthly or yearly
    $style = $_GET['style'];
}
else
{
    $style = "monthly";
}

if(! empty($_GET['sort']))
{
    $sort = $_GET['sort'];
}
else
{
    $sort = "EMTid";
}

if($sort == 'EMTid')
{
    $sort = 'lpad(EMTid,10,"0")';
}

$debugCLI = false; // DEBUG

if(! empty($_GET['year']))
{
    $year = (int)$_GET['year'];
}
else
{
    $year = date("Y");
}

if(isset($_GET['debug']))
{
    $simpleDebug = true;
    $debugOut = "";
}
else
{
    $simpleDebug = false;
}

$start_ts = (int)$_GET['start'];
$end_ts = (int)$_GET['end'];

if($debugCLI){ $start_ts = 1225533600; $end_ts = 1228129200;}

?>
<html>
<head>
<style type="text/css">
table.hours {
    border-width: 1px 1px 1px 1px;
    border-spacing: 0px;
    border-style: solid solid solid solid;
    border-color: black black black black;
    border-collapse: separate;
    background-color: white;
}
table.hours th {
    border-width: 1px 1px 1px 1px;
 padding: 1px 1px 1px 1px;
    border-style: solid solid solid solid;
    border-color: black black black black;
    background-color: white;
    -moz-border-radius: 0px 0px 0px 0px;
}
table.hours td {
    border-width: 1px 1px 1px 1px;
 padding: 1px 1px 1px 1px;
    border-style: solid solid solid solid;
    border-color: black black black black;
    background-color: white;
    -moz-border-radius: 0px 0px 0px 0px;
}
</style>
<?php
$title = $shortName;
if($style=='monthly')
{
    $title .= ' Monthly Hours Totals for';
    $title .= ' '.date("M Y", $start_ts);
}
else
{
    $title .= ' Yearly Hours Totals for';
    $title .= ' '.$year;
}
echo '<title>'.$title.'</title>';
?>
</head>
<body>
<?php

// figure out which member types to show
$types = array();
global $memberTypes;
for($i = 0; $i < count($memberTypes); $i++)
{
    if($memberTypes[$i]['canPullDuty'] == true)
    {
	$types[] = $memberTypes[$i]['name'];
    }
}

// get shift IDs
$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');
$query = "SELECT * FROM schedule_shifts WHERE deprecated=0;";
$result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());
$shifts = array();
while($row = mysql_fetch_assoc($result))
{
    $shifts[$row['shiftName']] = $row['sched_shift_id'];
}


if($debugCLI){ echo "start=".$start_ts.'='.date("Y-m-d H:i:s", $start_ts)." end=".$end_ts."=".date("Y-m-d H:i:s", $end_ts)."<br />";}

echo '<h3>'.$title.'<br>as of '.date("Y-m-d H:i:m").'</h3>';
echo '<a href="countHours.php?start='.lastMonthDate($start_ts).'&style=monthly"> &lt Month</a>';
echo '&nbsp;&nbsp;&nbsp;';
echo '<a href="countHours.php?start='.nextMonthDate($start_ts).'&style=monthly">Month &gt</a>';
echo '<br>';
if($style=='monthly')
{
    echo '<font size="2"><a href="countHours.php?start='.$start_ts.'&end='.$end_ts.'&style=yearly">(Go To Yearly Count)</a></font><br>'."\n";
    echo '<table class="hours">'."\n";
    echo '<td><b><a href="countHours.php?start='.$start_ts.'&end='.$end_ts.'&sort=EMTid">ID</a></b></td>'."\n";
    echo '<td><b><a href="countHours.php?start='.$start_ts.'&end='.$end_ts.'&sort=LastName">Last Name</a></b></td>'."\n";
    echo '<td><b><a href="countHours.php?start='.$start_ts.'&end='.$end_ts.'&sort=FirstName">First Name</a></b></td>'."\n";
    echo '<td><b>Days</b></td><td><b>Nights</b></td><td><b>TOTAL</b></td></b></tr>'."\n";
    if($sort == "LastName")
    {
	$query = 'SELECT * FROM roster ORDER BY LastName,FirstName;';
    }
    else
    {
	$query = 'SELECT * FROM roster ORDER BY '.$sort.';';
    }
    $result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());
    while($row = mysql_fetch_array($result))
    {
	if(in_array($row['status'], $types))
	{
	    echo '<tr>';
	    echo '<td><b>'.$row['EMTid'].'</b></td>';
	    echo '<td>'.$row['LastName'].'</td>';
	    echo '<td>'.$row['FirstName'].'</td>';
	    $days = countMonthShiftHours($start_ts, $end_ts, $row['EMTid'], "day");
	    $nights = countMonthShiftHours($start_ts, $end_ts, $row['EMTid'], "night");
	    echo '<td>'.tString($days).'</td>';
	    echo '<td>'.tString($nights).'</td>';
	    echo '<td>'.tStringRed($days + $nights).'</td>';
	    echo '</tr>'."\n";
	}
    }
    echo '</table>'."\n";
    if($simpleDebug)
    {
	echo '<h2>Debugging Output:</h2>'."\n";
	echo '<pre>'."\n";
	echo $debugOut;
	echo '</pre>'."\n";
    }
}
else
{
    //style is yearly
    echo '<font size="2"><a href="countHours.php?year='.$year.'&month='.$month.'&style=monthly">(Go To Monthly Count)</a></font><br>';
    echo '<table class="hours">';
    echo '<tr>';
    echo '<td><b><a href="countHours.php?year='.$year.'&sort=EMTid&style=yearly">ID</a></b></td>';
    echo '<td><b><a href="countHours.php?year='.$year.'&sort=LastName&style=yearly">Last Name</a></b></td>';
    echo '<td><b><a href="countHours.php?year='.$year.'&sort=FirstName&style=yearly">First Name</a></b></td>';
    echo '<td><b>Jan</b></td><td><b>Feb</b></td><td><b>Mar</b></td><td><b>Apr</b></td><td><b>May</b></td><td><b>Jun</b></td><td><b>Jul</b></td><td><b>Aug</b></td><td><b>Sep</b></td><td><b>Oct</b></td><td><b>Nov</b></td><td><b>Dec</b></td><td><b>TOTAL</b></td></b></tr>';
    // mysql connection
    $connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
    mysql_select_db($dbName) or die ('ERROR: Unable to select database!');
    $query = 'SELECT EMTid,status,LastName,FirstName FROM roster ORDER BY '.$sort.';';
    $result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());
    while($row = mysql_fetch_array($result))
    {
	if(in_array($row['status'], $types))
	{
	    echo '<tr>';
	    echo '<td><b>'.$row['EMTid'].'</b></td>';
	    echo '<td>'.$row['LastName'].'</td>';
	    echo '<td>'.$row['FirstName'].'</td>';
	    $total = 0;
	    for($i=1; $i<13; $i++)
	    {
		$monthHours = countMonthHours($year, $i, $row['EMTid']);
		echo '<td>'.tString($monthHours).'</td>';
		$total += $monthHours;
	    }
	    echo '<td>'.tString($total).'</td>';
	    echo '</tr>';
	}
    }
    echo '</table>'."\n";
}

//FUNCTIONS

// this is probably not useful anymore
function countMonthHours($year, $month, $ID)
{
    global $dbName;
    global $config_sched_table;
    global $debugCLI;
    global $simpleDebug, $debugOut;
    // mysql connection
    $connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL! countMonthDays');
    mysql_select_db($dbName) or die ('ERROR: Unable to select database! countMonthDays');
    $table = $config_sched_table;
    $query = 'SELECT * FROM '.$table.' WHERE EMTid='.$ID.' AND sched_year='.$year.' AND sched_month='.$month.' AND deprecated=0;';

    if($simpleDebug)
    {
	$debugOut .= "countMonthHours($year, $month, $ID)\n";
    }

    // DEBUG
    //if($month == 1)
    //{
    //echo $query."<br />";
    //}
    // END DEBUG
    $result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());
    $hours = 0;
    while($row = mysql_fetch_array($result))
    {
	$seconds = $row['end_ts'] - $row['start_ts'];
	$myhours = $seconds / 3600;
	if($debugCLI)
	{
	    echo $row['EMTid']." signon=".$row['sched_entry_id']." start=".$row['start_ts'].'='.date("Y-m-d H:i:s", $row['start_ts']).' end='.$row['end_ts'].'='.date("Y-m-d H:i:s", $row['end_ts']).' seconds='.$seconds.' hours='.$myhours.'<br />'."\n";
	}
	if($simpleDebug)
	{
	    $debugOut .= "     signon=".$row['sched_entry_id']." start=".$row['start_ts'].'='.date("Y-m-d H:i:s", $row['start_ts']).' end='.$row['end_ts'].'='.date("Y-m-d H:i:s", $row['end_ts']).' seconds='.$seconds.' hours='.$myhours."\n";
	}
	$hours += $myhours;
    }
    return $hours;
}

function countMonthShiftHours($start, $end, $ID, $shiftName)
{
    global $dbName;
    global $config_sched_table;
    global $debugCLI;
    global $shifts;
    global $simpleDebug, $debugOut;
    // mysql connection
    $connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL! countMonthDays');
    mysql_select_db($dbName) or die ('ERROR: Unable to select database! countMonthDays');
    $table = $config_sched_table;
    $query = 'SELECT * FROM '.$table.' WHERE EMTid='.$ID.' AND start_ts >= '.$start.' AND end_ts <= '.$end.' AND sched_shift_id='.$shifts[$shiftName].' AND deprecated=0;';
    //echo $query."<br />";
    $result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());

    if($simpleDebug)
    {
	$debugOut .= "countMonthShiftHours(start=$start, end=$end, ID=$ID, shiftName=$shiftName)\n";
    }

    $hours = 0;
    while($row = mysql_fetch_array($result))
    {
	$seconds = $row['end_ts'] - $row['start_ts'];
	$myhours = $seconds / 3600;
	if($debugCLI){ echo $row['EMTid']." signon=".$row['sched_entry_id']." start=".$row['start_ts'].'='.date("Y-m-d H:i:s", $row['start_ts']).' end='.$row['end_ts'].'='.date("Y-m-d H:i:s", $row['end_ts']).' seconds='.$seconds.' hours='.$myhours.'<br />'."\n";}
	if($simpleDebug)
	{
	    $debugOut .= "     signon=".$row['sched_entry_id']." start=".$row['start_ts'].'='.date("Y-m-d H:i:s", $row['start_ts']).' end='.$row['end_ts'].'='.date("Y-m-d H:i:s", $row['end_ts']).' seconds='.$seconds.' hours='.$myhours." totalhours=$hours\n";
	}
	$hours += $myhours;
    }
    return $hours;
}

function tStringRed($n)
{
    // how to show a string in the table
    if($n < 30)
    {
	return '<span style="color: red; font-style: italic;">** '.$n.' **</span>';
    }
    else
    {
	return $n;
    }
}

function tString($n)
{
    // how to show a string in the table
    if($n == 0)
    {
	return '&nbsp;';
    }
    else
    {
	return $n;
    }
}

function table_exists($table)
{
    global $dbName;
    $sql = mysql_connect() or die("error in table_exists connect".mysql_error());
    mysql_select_db($dbName) or die("error in table_exists select db".mysql_error());
    $result = mysql_list_tables($dbName) or die("error in table_exists list table".mysql_error());
    while ($row = mysql_fetch_row($result)) 
    {
	if($row[0]==$table)
	{
	    return true;
	}
    }
    return false;
}

?>
</body>
</html>