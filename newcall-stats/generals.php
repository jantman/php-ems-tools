<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-01-29 16:01:16 jantman"                                                              |
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
 | $HeadURL:: http://svn.jasonantman.com/newcall/stats/generals.php                                     $ |
 +--------------------------------------------------------------------------------------------------------+
*/

function getMembersOnCall($runNum)
{
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