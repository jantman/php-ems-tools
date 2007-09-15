<?php
// external/auth/isOnDuty.php
//
// Include file to handle calculations of whether a member is on duty or not
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
//      $Id$
 
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