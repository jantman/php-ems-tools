<?php
//
// countHours.php
//
// Version 0.1 as of Time-stamp: "2007-04-02 22:58:14 jantman"
//
// This file is part of the php-ems-tools package
// available at 
//
// (C) 2006 Jason Antman.
// This package is licensed under the terms of the
// GNU General Public License (GPL)
//
require_once('custom.php');
if(! empty($_GET['style']))
{
    //style monthly or yearly
    $style = $_GET['style'];
}
else
{
    $style = "monthly";
}

if(! empty($_GET['year']))
{
    //style monthly or yearly
    $year = $_GET['year'];
}

if(! empty($_GET['month']))
{
    //style monthly or yearly
    $month = $_GET['month'];
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
    $title .= ' '.GetMonthString($month).' '.$year;
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

echo '<h3>'.$title.'<br>as of '.date("Y-m-d H:i:m").'</h3>';
if($style=='monthly')
{
    echo '<font size="2"><a href="countHours.php?year='.$year.'&month='.$month.'&style=yearly">(Go To Yearly Count)</a></font><br>';
    echo '<table class="hours">';
    echo '<td><b><a href="countHours.php?year='.$year.'&month='.$month.'&sort=EMTid">ID</a></b></td>';
    echo '<td><b><a href="countHours.php?year='.$year.'&month='.$month.'&sort=LastName">Last Name</a></b></td>';
    echo '<td><b><a href="countHours.php?year='.$year.'&month='.$month.'&sort=FirstName">First Name</a></b></td>';
    echo '<td><b>Days</b></td><td><b>Nights</b></td><td><b>TOTAL</b></td></b></tr>';
    // mysql connection
    $connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
    mysql_select_db($dbName) or die ('ERROR: Unable to select database!');
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
	    $days = countMonthDays($year, $month, $row['EMTid']);
	    $nights = countMonthNights($year, $month, $row['EMTid']);
	    echo '<td>'.tString($days).'</td>';
	    echo '<td>'.tString($nights).'</td>';
	    echo '<td>'.tStringRed($days + $nights).'</td>';
	    echo '</tr>';
	}
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
		$days = countMonthDays($year, $i, $row['EMTid']);
		$nights = countMonthNights($year, $i, $row['EMTid']);
		echo '<td>'.tString($days + $nights).'</td>';
		$total += ($days + $nights);
	    }
	    echo '<td>'.tString($total).'</td>';
	    echo '</tr>';
	}
    }

}

//FUNCTIONS

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
		// thanks to Gabe Kooreman for debugging this next line
		// without even being asked.
		if($s >= 18 && $e > 18)
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
    return $hours;
}

function GetMonthString($n)
{
    $timestamp = mktime(0, 0, 0, $n, 1, 2005);
    
    return date("M", $timestamp);
}

function tStringRed($n)
{
    // how to show a string in the table
    if($n == 0)
    {
	return '<font color="red">0</font>';
    }
    elseif($n < 30)
    {
	return '<font color="red">'.$n.'</font>';
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