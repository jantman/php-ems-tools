<?php
// inc/global.php
//
// Global functions for PHP EMS Tools.
//
// +----------------------------------------------------------------------+
// | PHP EMS Tools	http://www.php-ems-tools.com                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006, 2007 Jason Antman.	                          |
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
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/inc/global.php $ |
// +----------------------------------------------------------------------+



function GetMonthString($n)
{
    $timestamp = mktime(0, 0, 0, $n, 1, 2005);  
    return date("M", $timestamp);
}

function canPullDuty($EMTid)
{
    //figure out whether this member is eligable to pull duty
    global $dbName;
    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.".$errorMsg);
    mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!".$errorMsg);
    $query = 'SELECT status FROM roster WHERE EMTid="'.$formItems['EMTid'].'";';
    $result = mysql_query($query) or die ("Auth Query Error");
    $row = mysql_fetch_array($result);
    $type = $row['status'];
    global $memberTypes;
    for($i=0; $i < count($memberTypes); $i++)
    {
	if($memberTypes[$i]['name']==$type)
	{
	    if(! $memberTypes[$i]['canPullDuty'])
	    {
		return false;
	    }
	}
    }
    return true;
}

function idInDB($EMTid)
{
    // this function checks with mySQL to see if the EMTid is valid
    global $dbName;
    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.".$errorMsg);
    mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!".$errorMsg);
    $query  = 'SELECT EMTid FROM roster WHERE EMTid="'.$EMTid.'";';
    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
    if(mysql_num_rows($result)==0)
    {
	// the ID given is not in the database
	return false;
    }
    while ($row = mysql_fetch_array($result))
    {
	if($row['EMTid']==$EMTid)
	{
	    // the specified ID actually is in the table
	    return true;
	}
    }
    mysql_free_result($result);
    // just to make sure we didn't miss anything
    return false; 
}

function schedAuth($EMTid, $passMD5)
{
    // authenticates against roster
    // returns rightLevel or -1 for not in roster

    global $dbName;
    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.".$errorMsg);
    mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!".$errorMsg);

    //AUTHENTICATION
    $query = 'SELECT pwdMD5,rightsLevel FROM roster WHERE EMTid="'.$adminID.'";';
    $result = mysql_query($query) or die ("Auth Query Error");
    if(mysql_num_rows() == 0) { return -1; }
    $row = mysql_fetch_array($result);
    $auth = false;
    $rightsLevel = $row['rightsLevel'];
    if($adminPW == $row['pwdMD5'])
    {
	$auth = true;
    }
    if($auth == true)
    {
	return $rightsLevel;
    }
    else
    {
	return -1;
    }
}

function make_safe($str)
{
    // removes SQL keywords and escapes string for putting queries in a db field
    $str = trim($str, ";");
    $str = str_replace("UPDATE", "UPD", $str);
    $str = str_replace("SET", "ST", $str);
    $str = mysql_real_escape_string($str);
    return $str;
}

function tsToShiftName($ts)
{
    // finds the shift name for a timestamp
    global $dayFirstHour;
    global $nightFirstHour;
    $temp = date("Y-m-d", $ts);
    $day = strtotime($temp.$dayFirstHour.":00:00");
    $night = strtotime($temp.$nightFirstHour.":00:00");
    if($ts >= $night)
    {
	return "Night";
    }
    return "Day";
}

function shiftNameToID($name)
{
    // gets the shiftID for a string shift name

    global $dbName;
    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.".$errorMsg);
    mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!".$errorMsg);
    $query = "SELECT sched_shift_id FROM schedule_shifts WHERE shiftName='".mysql_real_escape_string(strtolower($name))."';";
    $result = mysql_query($query);
    if(! $result)
    {
	error_log("PHP-EMS-TOOLS:inc/global.php:shiftNameToID(".$name.") - Query Error - Query: ".$query." - Error: ".mysql_error());
	die();
    }
    if(mysql_num_rows($result) > 0){ $row = mysql_fetch_assoc($result); return $row['sched_shift_id'];}
    return false;
}

function makeTimestampsFromTimes($ts, $start, $end)
{
    global $dayFirstHour;
    // takes the ts argument to signOn.php, and start and end strings (H:m:s) and returns an array with stard and end timestamps
    $dateStr = date("Y-m-d", $ts);
    $startH = (int)substr($start, 0, 2);
    $endH = (int)substr($end, 0, 2);
    if($endH <= $dayFirstHour)
    {
	$end_ts = strtotime(date("Y-m-d", ($ts+86400))." ".$end);
    }
    else
    {
	$end_ts = strtotime(date("Y-m-d", ($ts))." ".$end);
    }
    $start_ts = strtotime(date("Y-m-d", $ts)." ".$start);
    return array("start" => $start_ts, "end" => $end_ts);
}

?>