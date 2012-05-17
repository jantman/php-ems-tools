<?php

//
// generals.php
//
// Version 1.0 as of Time-stamp: "2008-11-30 22:02:33 jantman"
//
// This file is part of the php-ems-tools package
// available at 
//
// (C) 2006 Jason Antman.
// This package is licensed under the terms of the
// GNU General Public License (GPL)
//

// generals.php - code to handle general calls

require_once('custom.php');

function getMembersOnCall($runNum)
{
    global $dbName;

    mysql_connect() or die("I'm sorry but I cannot connect to the MySQL server. Error: ".mysql_error());
    mysql_select_db($dbName) or die("I'm sorry but there was an error selecting the DB. Error: ".mysql_error());
    $query = "SELECT * FROM calls WHERE RunNumber='".$runNum."';";

    $result = mysql_query($query) or die("MySQL Query Error: ".mysql_error());
    $r = mysql_fetch_array($result);
    
    $memb[$r['DriverToScene']] = $r['DriverToScene'];
    $memb[$r['DriverToHosp']] = $r['DriverToHosp'];
    $memb[$r['DriverToBldg']] = $r['DriverToBldg'];
    $memb[$r['crew1']] = $r['crew1'];
    $memb[$r['crew2']] = $r['crew2'];
    $memb[$r['crew3']] = $r['crew3'];
    $memb[$r['crew4']] = $r['crew4'];
    $memb[$r['crew5']] = $r['crew5'];
    $memb[$r['crew6']] = $r['crew6'];

    mysql_free_result($result);

    return $memb;
}

function isOnDuty($EMTid, $timestamp)
{
    global $dbName;

    mysql_connect() or die("I'm sorry but I cannot connect to the MySQL server. Error: ".mysql_error());
    mysql_select_db($dbName) or die("I'm sorry but there was an error selecting the DB. Error: ".mysql_error());
    $query = "SELECT * FROM schedule WHERE start_ts <=".$timestamp." AND end_ts >= ".$timestamp." AND deprecated=0 AND EMTid='".$EMTid."';";

    if($debug){ echo "<br>isOnDuty running on timestamp ".$timestamp." = ".date("Y-m-d H:i:s", $timestamp)." shift ".$shift." ID=".$EMTid."<br>".$query.'<br>';}

    $result = mysql_query($query) or die("MySQL Query Error: ".mysql_error());
    if(mysql_num_rows($result) >= 1)
    {
	return true;
    }
    mysql_free_result($result);
    return false;
}

function getSchedDate($timestamp)
{
    $h = date("H", $timestamp);

    // set date back one if night shift between 00:00-06:00
    if($h < 6)
    {
	$timestamp -= 86400;
    }

    // figure out shift
    if($h >= 18 || $h < 6)
    {
	$shift = "night";
    }
    else
    {
	$shift = "day";
    }

    $retVal = array();
    $retVal['year'] = date("Y", $timestamp);
    $retVal['month'] = date("m", $timestamp);
    $retVal['date'] = date("d", $timestamp);
    $retVal['shift'] = $shift;
    return $retVal;
}

function getSchedString($timestamp)
{
    $d = getSchedDate($timestamp);
    $s = $d['year']."_".$d['month']."_".$d['shift'];
    return $s;
}

function getSchedTS($timestamp)
{
    // converts a real timestamp to a timestamp using the proper SCHEDULING date
    if(date("h", $timestamp) < 6)
    {
	$timestamp -= 86400;
    }
    return $timestamp;
}

function getRealTS($schedTime, $schedTS, $type)
{
    // takes schedTime (a schedule time formatted like 18:00:00)
    //    and a schedule timestamp (schedTS)
    // returns an actual timestamp

    $timeA = explode(":", $schedTime);

    if($timeA[0] <= 6 && $type == "end"){ $schedTS += 86400;}

    $dateSched = date("Y-m-d", $schedTS);
    $realTS = strtotime($dateSched . $schedTime);
    return $realTS;
}

function intToHour($i)
{
    if(strlen($i)==1)
    {
	return "0".$i.":00";
    }
    else
    {
	return $i.":00";
    }
}



?>