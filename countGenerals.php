<?php
//
// countGenerals.php
//
// Version 0.1 as of Time-stamp: "2009-12-31 20:21:23 jantman"
//
// This file is part of the php-ems-tools package
// available at 
//
// (C) 2006 Jason Antman.
// This package is licensed under the terms of the
// GNU General Public License (GPL)
//

require_once('custom.php');
require_once('inc/sched.php');
require_once('generals.php');

$debug = false;

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
if($style=='monthly')
{
    $title .= ' Monthly General Call Totals for';
    $title .= ' '.date("M Y", $start);
}
else
{
    $title .= ' Yearly General Call Totals for';
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
$types[] = "Driver";

//echo "<pre>".var_dump($memberTypes)."</pre>";

echo '<h3>'.$title.'<br>as of '.date("Y-m-d H:i:m").'</h3>';
if($style=='monthly')
{
    // controls for +/- month
    echo '<a href="countGenerals.php?start='.lastMonthDate($start).'&style=monthly"> &lt Month</a>';
    echo '&nbsp;&nbsp;&nbsp;';
    echo '<a href="countGenerals.php?start='.nextMonthDate($start).'&style=monthly">Month &gt</a>';
    echo '<br>';


    echo '<font size="2"><a href="countGenerals.php?year='.$year.'&month='.$month.'&style=yearly">(Go To Yearly Count)</a></font><br>';
    echo '<table class="hours">';
    echo '<td><b><a href="countGenerals.php?start='.$start.'&sort=EMTid">ID</a></b></td>';
    echo '<td><b><a href="countGenerals.php?start='.$start.'&sort=LastName">Last Name</a></b></td>';
    echo '<td><b><a href="countGenerals.php?start='.$start.'&sort=FirstName">First Name</a></b></td>';
    echo '<td><b>Generals</b></td><td><b>Duty</b></td><td><b>Non-Emerg./Events</b></td><td><b>TOTAL CALLS</b></td></tr>';
    // mysql connection
    $connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
    mysql_select_db($dbName) or die ('ERROR: Unable to select database!');
    $query = 'SELECT * FROM roster ORDER BY '.$sort.';';
    $result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());
    while($row = mysql_fetch_array($result))
    {
	if(in_array($row['status'], $types))
	{
	    echo '<tr>';
	    echo '<td><b>'.$row['EMTid'].'</b></td>';
	    echo '<td>'.$row['LastName'].'</td>';
	    echo '<td>'.$row['FirstName'].'</td>';
	    $callA = countMonthGenerals($year, $month, $row['EMTid']);
	    $duty = $callA['duty'];
	    $generals = $callA['generals'];
	    $other = $callA['other'];
	    echo '<td>'.makeString($generals).'</td>';
	    echo '<td>'.makeString($duty).'</td>';
	    echo '<td>'.makeString($other).'</td>';
	    echo '<td>'.makeString($duty + $generals + $other).'</td>';
	    echo '</tr>';
	}
	else
	{
	    // DEBUG
	    //echo $row['EMTid']."<br>";
	}
    }
    mysql_free_result($result);
    mysql_close($connection);
}
else
{
    //style is yearly
    echo '<font size="2"><a href="countGenerals.php?year='.$year.'&month='.$month.'&style=monthly">(Go To Monthly Count)</a></font>&nbsp;&nbsp;&nbsp;<a href="countGenerals.php?year='.$year.'&style=monthly&showall=true">Show All Members</a><br>';
    echo '<table class="hours">';
    echo '<tr>';
    echo '<td><b><a href="countGenerals.php?year='.$year.'&sort=EMTid&style=yearly">ID</a></b></td>';
    echo '<td><b><a href="countGenerals.php?year='.$year.'&sort=LastName&style=yearly">Last Name</a></b></td>';
    echo '<td><b><a href="countGenerals.php?year='.$year.'&sort=FirstName&style=yearly">First Name</a></b></td>';
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
		$generals = countMonthGenerals($year, $i, $row['EMTid']);
		$generals = $generals['generals'];
		echo '<td>'.makeString($generals).'</td>';
		$total += ($generals);
	    }
	    echo '<td>'.makeString($total).'</td>';
	    echo '</tr>';
	}
	elseif($showAllMembers == true)
	{
	    echo '<tr>';
	    echo '<td><b>'.$row['EMTid'].'</b></td>';
	    echo '<td>'.$row['LastName'].'</td>';
	    echo '<td>'.$row['FirstName'].'</td>';
	    $total = 0;
	    for($i=1; $i<13; $i++)
	    {
		$generals = countMonthGenerals($year, $i, $row['EMTid']);
		$generals = $generals['generals'];
		echo '<td>'.makeString($generals).'</td>';
		$total += ($generals);
	    }
	    echo '<td>'.makeString($total).'</td>';
	    echo '</tr>';
	}
    }
    mysql_free_result($result);
    mysql_close($connection);
}

//FUNCTIONS

function countMonthGenerals($year, $month, $ID)
{
    global $dbName;
    $retArray = array();
    $query = "SELECT * FROM calls WHERE YEAR(Date)=".$year." AND MONTH(Date)=".$month.";";
    $connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
    mysql_select_db($dbName) or die ('ERROR: Unable to select database!');
    $result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());
    $genCount = 0;
    $dutyCount = 0;
    $otherCount = 0;
    while($row = mysql_fetch_array($result))
    {
	if(($row['DriverToScene'] == $ID) || ($row['DriverToHosp'] == $ID) || ($row['DriverToBldg'] == $ID) || ($row['crew1'] == $ID) || ($row['crew2'] == $ID) || ($row['crew3'] == $ID) || ($row['crew4'] == $ID) || ($row['crew5'] == $ID) || ($row['crew6'] == $ID))
	{

	    // member on this call
	    $timestamp = strtotime($row['Date']." ".$row['TimeDisp']);

	    // DEBUG
	    if($debug){ echo $row['RunNumber']." on ".$row['Date'].' at '.$row['TimeDisp']." ID=".$ID." checking for ".date("Y-m-d H:i:s", $timestamp)." ";}
	    // END DEBUG

	    if(($row['CallType'] == "Other - Misc. Non-Emerg.") || ($row['CallType'] == "Other - Vehicle Maintenance") || ($row['CallType'] == "NOT RECORDED") || ($row['CallType'] == "Other - Transport") || ($row['CallType'] == "Other - Misc. Standby") || ($row['CallType'] == "Other - Event Standby"))
	    {
		// non-emerg. or other call type
		$otherCount++;
		// DEBUG
		if($debug){ echo "Other";}
		// END DEBUG
	    }
	    else
	    {
		if(! isOnDuty($ID, $timestamp))
		{
		    // count this call as GENERAL
		    $genCount++;
		    // DEBUG
		    if($debug){ echo "General";}
		    // END DEBUG
		}
		else
		{
		    // member was on duty
		    $dutyCount++;
		    // DEBUG
		    if($debug){ echo "Duty";}
		    // END DEBUG
		}
	    }
	    // DEBUG
	    if($debug){ echo "<br>";}
	    // END DEBUG
	}
    }
    mysql_free_result($result);
    mysql_close($connection);

    $retArray['duty'] = $dutyCount;
    $retArray['generals'] = $genCount;
    $retArray['other'] = $otherCount;

    return $retArray;
}

function countMonthDays($year, $month, $ID)
{
    global $dbName;
    // mysql connection
    if(strlen($month)==1)
    {
	$month = '0'.$month;
    }
    $connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL! countMonthDays');
    mysql_select_db($dbName) or die ('ERROR: Unable to select database! countMonthDays');
    $table = 'schedule_'.$year.'_'.$month.'_day';
    if(! table_exists($table))
    {
	return 0;
    }
    $query = 'SELECT * FROM '.$table.';';
    $result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());
    $hours = 0;
    while($row = mysql_fetch_array($result))
    {
	for($i=1; $i < 7; $i++)
	{
	    if($row[$i.'ID']==$ID)
	    {
		$start = strtotime($row[$i.'Start']);
		$end = strtotime($row[$i.'End']);
		$time = $end - $start;
		$time = $time - ($time % 3600);
		$time = $time / 3600; // time is now hours
		$hours += $time;
	    }
	}
    }
    mysql_free_result($result);
    mysql_close($connection);
    return $hours;
}

function countMonthNights($year, $month, $ID)
{
    global $dbName;
    if(strlen($month)==1)
    {
	$month = '0'.$month;
    }
    // mysql connection
    $connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL! countMonthNights');
    mysql_select_db($dbName) or die ('ERROR: Unable to select database! countMonthNights');
    $table = 'schedule_'.$year.'_'.$month.'_night';
    if(! table_exists($table))
    {
	return 0;
    }
    $query = 'SELECT * FROM '.$table.';';
    $result = mysql_query($query) or die('Error in query in countMonthNights '.$query." ERROR ".mysql_error());
    $hours = 0;
    while($row = mysql_fetch_array($result))
    {
	for($i=1; $i < 7; $i++)
	{
	    if($row[$i.'ID']==$ID)
	    {
		$s = explode(":", $row[$i.'Start']);
		$s = $s[0];
		$e = explode(":", $row[$i.'End']);
		$e = $e[0];
		if($s > 18 && $e > 18)
		{
		    //both are before midnight
		    $time = $e - $s;
		}
		elseif($s < 18 && $e < 18)
		{
		    //both are after midnight
		    $time = $e - $s;
		}
		else
		{
		    //start before midnight, end after midnight
		    $time = 24 - $s;
		    $time = $time + $e;
		}
		$hours += $time;
	    }
	}
    }
    mysql_free_result($result);
    mysql_close($connection);
    return $hours;
}

function GetMonthString($n)
{
    $timestamp = mktime(0, 0, 0, $n, 1, 2005);
    
    return date("M", $timestamp);
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
    mysql_free_result($result);
    mysql_close($sql);
    return false;
}


?>
</body>
</html>