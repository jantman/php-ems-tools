<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-10-27 22:38:19 jantman"                                                              |
 +--------------------------------------------------------------------------------------------------------+
 | Copyright (c) 2009, 2010 Jason Antman. All rights reserved.                                            |
 |                                                                                                        |
 | This program is free software; you can redistribute it and/or modify                                   |
 | it under the terms of the GNU General Public License as published by                                   |
 | the Free Software Foundation; either version 3 of the License, or                                      |
 | (at your option) any later version.                                                                    |
 |                                                                                                        |
 | This program is distributed in the hope that it will be useful,                                        |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of                                         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                                          |
 | GNU General Public License for more details.                                                           |
 |                                                                                                        |
 | You should have received a copy of the GNU General Public License                                      |
 | along with this program; if not, write to:                                                             |
 |                                                                                                        |
 | Free Software Foundation, Inc.                                                                         |
 | 59 Temple Place - Suite 330                                                                            |
 | Boston, MA 02111-1307, USA.                                                                            |
 +--------------------------------------------------------------------------------------------------------+
 |Please use the above URL for bug reports and feature/support requests.                                  |
 +--------------------------------------------------------------------------------------------------------+
 | Authors: Jason Antman <jason@jasonantman.com>                                                          |
 +--------------------------------------------------------------------------------------------------------+
 | $LastChangedRevision:: 52                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/stats/countGenerals.php                                $ |
 +--------------------------------------------------------------------------------------------------------+
*/
require_once('../custom.php');
require_once('../inc/sched.php');
require_once('../newcall-stats/generals.php');
require_once('../newcall/inc/newcall.php.inc');
require_once('../config/config.php'); // main configuration
require_once('../config/scheduleConfig.php'); // schedule configuration
require_once('../config/rosterConfig.php'); // roster configuration
require_once('../inc/global.php'); // global functions


$style = "yearly";

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

if(isset($_GET['showall']))
{
    $showAllMembers = true;
}
else
{
    $showAllMembers = false;
}

if(! empty($_GET['start']))
{
    $start = (int)$_GET['start'];
}
else
{
    $start = strtotime(date("Y-m")."-01 00:00:00");
}

$year = date("Y", $start);
$month = date("m", $start);

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
$title .= ' Sample Clothing Allowance Amounts for';
$title .= ' '.$year;
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
$types[] = "Driver";

echo '<h3>'.$title.'<br>as of '.date("Y-m-d H:i:m").'</h3>';


//style is yearly
echo '<font size="2"><a href="countGenerals.php?year='.$year.'&month='.$month.'&style=monthly">(Go To Monthly Count)</a></font>&nbsp;&nbsp;&nbsp;<a href="countGenerals.php?year='.$year.'&style=monthly&showall=true">Show All Members</a><br>';

$query = 'SELECT EMTid,status,LastName,FirstName FROM roster ORDER BY '.$sort.';';

$members = array();
$totalPoints = 0;

$result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());
while($row = mysql_fetch_array($result))
{
    if(! in_array($row['status'], $types) && ! $showAllMembers){ continue;}

    $arr = array();
    $arr['EMTid'] = $row['EMTid'];
    $arr['LastName'] = $row['LastName'];
    $arr['FirstName'] = $row['FirstName'];
    $total = 0;
    for($i=1; $i<13; $i++)
    {
	$generals = countMonthGenerals($year, $i, $row['EMTid']);
	$generals = $generals['generals'];
	$total += ($generals);
    }
    $arr['generals'] = $total;
    $arr['generalPoints'] = ($total / 2);
    $totalPoints += $arr['generalPoints'];

    $foo = getAttendance($year, $row['EMTid']);
    $arr['meeting'] = $foo['meeting'];
    $totalPoints += $foo['meeting'];
    $arr['drill'] = $foo['drill'];
    $totalPoints += $foo['drill'];

    $total = 0;
    for($i=1; $i<13; $i++)
    {
	$monthHours = countMonthHours($year, $i, $row['EMTid']);
	$total += $monthHours;
    }
    $arr['hours'] = $total;
    $arr['hourPoints'] = $total / 10;
    $totalPoints += $arr['hourPoints'];


    $members[$row['EMTid']] = $arr;
}
mysql_free_result($result);

echo '<table class="hours">';
echo '<tr>';
echo '<th><a href="countGenerals.php?year='.$year.'&sort=EMTid&style=yearly">ID</a></th>';
echo '<th><a href="countGenerals.php?year='.$year.'&sort=LastName&style=yearly">Last Name</a></th>';
echo '<th><a href="countGenerals.php?year='.$year.'&sort=FirstName&style=yearly">First Name</a></th>';
echo '<th>Generals</th><th>General Points</th>';
echo '<th>Hours</th><th>Hour Points</th>';
echo '<th>Meetings</th><th>Drills</th>';
echo '<th>Officer<br />Points</th>';
echo '<th>TOTAL<br />Points</th>';
echo '<th>Point<br />Amount</th>';
echo '<th>Officer<br />Stipend</th>';
echo '<th>TOTAL<br />Amount</th>';
echo '</tr>'."\n";

$pointValue = 5600 / $totalPoints;
$pointValue = makePointValue($pointValue);

foreach($members as $EMTid => $arr)
{
    $points = 0;
    echo '<tr>';
    echo '<th>'.$arr['EMTid'].'</th>';
    echo '<td>'.$arr['LastName'].'</td>';
    echo '<td>'.$arr['FirstName'].'</td>';
    echo '<td>'.$arr['generals'].'</td>';
    echo '<td>'.$arr['generalPoints']."</td>";
    $points += $arr['generalPoints'];
    echo '<td>'.$arr['hours'].'</td>';
    echo '<td>'.$arr['hourPoints']."</td>";
    $points += $arr['hourPoints'];
    echo '<td>'.$arr['meeting'].'</td>';
    $points += $arr['meeting'];
    echo '<td>'.$arr['drill'].'</td>';
    $points += $arr['drill'];
    echo '<td>'.'&nbsp;'.'</td>'; // officer points
    $points += 0;
    echo '<td>'.$points.'</td>';
    $pointAmt = ($points * $pointValue);
    echo '<td>'.$pointAmt.'</td>';
    $officerAmt = getOfficerStipend($EMTid);
    echo '<td>'.$officerAmt.'</td>';
    echo '<td>'.round(($pointAmt + $officerAmt), 2).'</td>';
    echo '</tr>'."\n";

}

echo '</table>'."\n";

echo '<h2>Total Points for all Corps: '.$totalPoints.'</h2>'."\n";
echo '<h2>Value per point: '.$pointValue.'</h2>'."\n";

//FUNCTIONS

function makePointValue($v)
{
    $int = (int)$v;
    $foo = $v - $int;
    $foo = $foo * 100;
    $foo = (int)$foo;
    $foo = $foo / 100;
    return ($int + $foo);
}

function getOfficerStipend($EMTid)
{
    $amt = 0;
    $query = "SELECT officer FROM roster WHERE EMTid='$EMTid';";
    $result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());
    $row = mysql_fetch_assoc($result);
    if($row['officer'] == "Captain"){ $amt += 350;}
    elseif($row['officer'] == "1st Lieutenant" || $row['officer'] == "2nd Lieutenant"){ $amt += 150;}
    
    $query = "SELECT position FROM roster WHERE EMTid='$EMTid';";
    $result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());
    $row = mysql_fetch_assoc($result);
    if($row['position'] == "President" || $row['position'] == "Secretary" || $row['position'] == "Treasurer" || $row['position'] == "1st VP"){ $amt += 150;}

    return $amt;
}

function getAttendance($year, $EMTid)
{
    $arr = array();
    $query = "SELECT * FROM attendance WHERE EMTid='$EMTid' AND type='Meeting' AND YEAR(FROM_UNIXTIME(date_ts))=$year AND status='Present';";
    $result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());
    $arr['meeting'] = mysql_num_rows($result);
    
    $query = "SELECT * FROM attendance WHERE EMTid='$EMTid' AND type='Drill' AND YEAR(FROM_UNIXTIME(date_ts))=$year AND status='Present';";
    $result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());
    $arr['drill'] = mysql_num_rows($result);
    
    return $arr;
}

function countMonthGenerals($year, $month, $ID)
{
    $retArray = array();
    $query = "SELECT cc.EMTid,cc.is_on_duty,cc.is_on_scene,c.outcome,c.date_ts FROM calls_crew AS cc LEFT JOIN calls AS c ON cc.RunNumber=c.RunNumber WHERE cc.is_deprecated=0 AND c.is_deprecated=0 AND cc.EMTid='$ID' AND MONTH(c.date_date)=$month AND YEAR(c.date_date)=$year;";
    $result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());
    $genCount = 0;
    $dutyCount = 0;
    $otherCount = 0;
    while($row = mysql_fetch_array($result))
    {
	$timestamp = $row['date_ts'];

	if($row['outcome'] == "Other")
	{
	    // non-emerg. or other call type
	    $otherCount++;
	}
	elseif($row['is_on_duty'] == 1)
	{
	    $dutyCount++;
	}
	else
	{
	    $genCount++;
	}
    }
    mysql_free_result($result);

    $retArray['duty'] = $dutyCount;
    $retArray['generals'] = $genCount;
    $retArray['other'] = $otherCount;

    return $retArray;
}


function makeString($n)
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
    $query = 'SELECT * FROM '.$table." WHERE EMTid='".$ID."' AND sched_year=".$year.' AND sched_month='.$month.' AND deprecated=0;';

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


