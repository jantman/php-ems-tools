<?php
//
// isOnDuty.php
//
// Version 0.1 as of Time-stamp: "2007-03-20 15:50:30 jantman"
//
// This file is part of the php-ems-tools package
// available at 
//
// (C) 2006 Jason Antman.
// This package is licensed under the terms of the
// GNU General Public License (GPL)
//

// 
// DO NOT MAKE CHANGES
// UNLESS YOU KNOW WHAT YOU ARE DOING.
// 

// isOnDuty.php
// function to determine whether a given member is on duty at a given time
 
function isOnDuty($EMTid, $timestamp)
{
    global $debug;
    global $dayFirstHour;
    global $nightFirstHour;

    if($debug){ echo "Begin isOnDuty... EMTid=".$EMTid." timestamp=".$timestamp."=".date("Y-m-d H:i:s", $start)."<br>";}

    if((date("H", $timestamp) >= $nightFirstHour) || (date("H", $timestamp) < $dayFirstHour))
    {
	if($debug){ echo "calling isOnDutyNight...<br>";}
	$memberOn = isOnDutyNight($EMTid, $timestamp);
    }
    else
    {
	if($debug){ echo "calling isOnDutyDay...<br>";}
	$memberOn = isOnDutyDay($EMTid, $timestamp);
    }

    return $memberOn;
}

function isOnDutyDay($EMTid, $timestamp)
{ 
    global $dbName;
    global $debug;
    mysql_connect() or die("I'm sorry but I cannot connect to the MySQL server. Error: ".mysql_error());
    global $dbName;
    mysql_select_db($dbName) or die("I'm sorry but there was an error selecting the DB. Error: ".mysql_error());
    $query = "SELECT * FROM ".getTableName($timestamp)." WHERE Date='".getScheduleDate($timestamp)."';";
    if($debug){echo "dbName=".$dbName." QUERY=".$query."<br><br>";}
    $result = mysql_query($query) or die("MySQL Query Error: ".mysql_error());
    $arr = mysql_fetch_array($result);
    $memberOn = false;
    for($i = 1; $i < 7; $i++)
    {
	if($arr[$i."ID"] == $EMTid)
	{
	    $start = strtotime(date("Y-m-d", $timestamp)." ".$arr[$i."Start"]);
	    $end = strtotime(date("Y-m-d", $timestamp)." ".$arr[$i."End"]);
	    // DEBUG
	    echo "ACTUAL times:<br>";
	    echo "start=".$start."=".date("Y-m-d H:i:s", $start).'<br>';
	    echo "end=".$end."=".date("Y-m-d H:i:s", $end).'<br>';
	    // END DEBUG
	    if($timestamp >= $start && $timestamp <= $end)
	    {
		if($debug){ echo "Timestamp is in range for slot ".$i."<br>";}
		$memberOn = true;
	    }
	}
    }
    mysql_free_result();
    return $memberOn;
}

function isOnDutyNight($EMTid, $timestamp)
{
    global $dbName;
    global $debug;
    global $dayFirstHour;

    mysql_connect() or die("I'm sorry but I cannot connect to the MySQL server. Error: ".mysql_error());
    global $dbName;
    mysql_select_db($dbName) or die("I'm sorry but there was an error selecting the DB. Error: ".mysql_error());
    $date = getScheduleDate($timestamp);
    $query = "SELECT * FROM ".getTableName($timestamp)." WHERE Date='".$date."';";
    if($debug){echo "dbName=".$dbName." QUERY=".$query."<br><br>";}
    $result = mysql_query($query) or die("MySQL Query Error: ".mysql_error());
    $arr = mysql_fetch_array($result);
    $memberOn = false;
    for($i = 1; $i < 7; $i++)
    {
	if($arr[$i."ID"] == $EMTid)
	{
	    $start = strtotime(date("Y-m", $timestamp)."-".$date." ".$arr[$i."Start"]);
	    if(date("H", $start) < $dayFirstHour)
	    {
		if($debug){ echo "startDate +<br>";}
		$start = $start + 86400;
	    }
	    $end =  strtotime(date("Y-m", $timestamp)."-".$date." ".$arr[$i."End"]);
	    if(date("H", $end) <= $dayFirstHour)
	    {
		if($debug){ echo "endDate +<br>";}
		$end = $end + 86400;
	    }
/*
	    if(date("H", $end) <= $dayFirstHour)
	    { 
		$end = $end + 86400; // subtract a day 
	    }
*/

	    // DEBUG
	    echo "ACTUAL times:<br>";
	    echo "start=".$start."=".date("Y-m-d H:i:s", $start).'<br>';
	    echo "end=".$end."=".date("Y-m-d H:i:s", $end).'<br>';
	    // END DEBUG

	    if($timestamp >= $start && $timestamp <= $end)
	    {
		if($debug){ echo "Timestamp is in range for slot ".$i."<br>";}
		$memberOn = true;
	    }
	}
    }
    mysql_free_result();
    return $memberOn;
}

function getTableName($timestamp)
{
    global $debug;
    global $dayFirstHour;
    global $nightFirstHour;
    //figure out the month, shift, date, etc.

    $date = date("d", $timestamp);

    if(date("H", $timestamp) < $dayFirstHour)
    {
	$shift = 'night';
	$date--;
    }
    elseif(date("H", $timestamp) >= $nightFirstHour)
    {
	$shift = 'night';
    }
    else
    {
	$shift = 'day';
    }

    if($debug)
    {
	echo "START DEBUG OUTPUT:<br>";
	echo "timestamp=".$timestamp."<br>";
	echo "Y-m-d H:i:s=".date("Y-m-d H:i:s", $timestamp)."<br>";
	echo "Calculated Shift=".$shift."<br>";
	echo "Calculated schedule date=".$date."<br>";
	echo "END DEBUG OUTPUT:<br>";
    }


    $tblName = "schedule_".date("Y", $timestamp)."_".date("m", $timestamp)."_".$shift;
    return $tblName;
}

function getScheduleDate($timestamp)
{
    global $debug;
    global $dayFirstHour;
    global $nightFirstHour;
    //figure out the month, shift, date, etc.

    $date = date("d", $timestamp);

    if(date("H", $timestamp) < $dayFirstHour)
    {
	$shift = 'night';
	$date--;
    }
    elseif(date("H", $timestamp) >= $nightFirstHour)
    {
	$shift = 'night';
    }
    else
    {
	$shift = 'day';
    }

    if($debug)
    {
	echo "START DEBUG OUTPUT:<br>";
	echo "timestamp=".$timestamp."<br>";
	echo "Y-m-d H:i:s=".date("Y-m-d H:i:s", $timestamp)."<br>";
	echo "Calculated Shift=".$shift."<br>";
	echo "Calculated schedule date=".$date."<br>";
	echo "END DEBUG OUTPUT:<br>";
    }

    return $date;
}
?>