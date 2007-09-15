<?php
// dispatchSchedule.php
//
// Page that attempts to generate a weekly schedule of when coverage is needed.
//   This is still a mostly experimental feature.
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

// this turns debugging mode on and off
$debug = false;

// 
//     NOTICE:
// 
// At this time, this is hard-coded to shifts from 0600-1800 and 1800-0600, and to require
// a full crew as two or more "Senior" members.
// 
// Stay tuned for an update that works with other organizations.
//
//


// GLOBALS
require('./config/config.php');
$day = 86400; // seconds in a day


if(! empty($_GET['weekof']))
{
    $weekof = strtotime($_GET['weekof']);
}
else
{
    $weekof = time();
}
// This is a timestamp:
$weekStart = findWeekStart($weekof); // find the first sunday in this week

global $orgName;
echo "<h3 align=center>".$orgName." Dispatch Schedule for week starting on ".date("l, F j, Y", $weekStart)."</h3>";
echo '<center><a href="dispatchSchedule.php?weekof='.date("Y-m-d", $weekStart - (3 * $day)).'"> << Week </a><a href="dispatchSchedule.php?weekof='.date("Y-m-d", $weekStart + (9 * $day)).'"> Week >> </a></center>';

$spacer = "&nbsp;&nbsp;&nbsp;";

$currentDay = $weekStart; 
//make the schedule
for($i = 0; $i < 7; $i++)
{
    $d = $currentDay +  ($i * $day);

    echo '<b>'.date("D m/d", $d).'</b><br>';
    echo $spacer.$spacer.$spacer."<b>Day</b><br>";
    altDay($d);
    if($debug)
    {
	    echo '<b>'.date("D m/d", $d).'</b><br>';
    }
    echo $spacer.$spacer.$spacer."<b>Night</b><br>";
    altNight($d);
}


function findWeekStart($weekof)
{
    $ts = $weekof;
    global $day;

    for($c = 7; $c > 0; $c--)
    {
	$temp = $ts - ($c * $day); // this day's stamp

	if(date("D" ,$temp) == "Sun")
	{
	    // find the sunday that starts this week
	    return $temp;
	}
    }
}

function makeHourTime($timestamp, $time)
{
    // figure out day vs night, return right stamp
    $day = 86400;

    $string = date("Y", $timestamp)."-".date("m", $timestamp)."-".date("d", $timestamp)." ".$time;

    if(substr($time, 0, 2) < 6)
    {
	$stamp = strtotime($string);
	$stamp += $day;
    }
    else
    {
	$stamp = strtotime($string);
    }

    return $stamp;
}

function intToHour($int)
{
    if($int < 10 && strlen($int) == 1)
    {
	return "0".$int.":00:00";
    }
    else
    {
	return $int.":00:00";
    }
}

function makeEmptyShift($array)
{

    foreach($array as $key => $value)
    {
	$array[$key] = 0;
    }
    return $array;
}

function isFullCrew($h, $timestamp, $shift)
{
    global $memberTypes;
    $arr = hourArray($h, $timestamp, $shift);
    $isFull = true;

    for($i = 0; $i < count($memberTypes); $i++)
    {
	if($arr[$memberTypes[$i]['name']] < $memberTypes[$i]['crewRequires'])
	{
	    $isFull = false;
	}
    }
    return $isFull;
}

function isOnDuty($EMTid, $timestamp)
{
    // WARNING:
    // this method DOES NOT work. Needs to be made more like numOnDuty()


    global $dayFirstHour, $dayLastHour, $nightFirstHour, $nightLastHour;
    global $dbName;

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
    
    mysql_connect() or die("I'm sorry but I cannot connect to the MySQL server. Error: ".mysql_error());
    global $dbName;
    mysql_select_db($dbName) or die("I'm sorry but there was an error selecting the DB. Error: ".mysql_error());
    $query = "SELECT * FROM schedule_".date("Y", $timestamp)."_".date("m", $timestamp)."_".$shift." WHERE Date='".$date."';";

    $result = mysql_query($query) or die("MySQL Query Error: ".mysql_error());
	
    while($r = mysql_fetch_array($result))
    {
	for($i = 0; $i < 7; $i++)
	{
	    if($r[$i.'ID'] == $EMTid)
	    {		    
		// this is the right person
		$startHr = explode(":", $r[$i.'Start']);
		$startHr = $startHr[0];
		    $start = strtotime(date("Y-m-d", $timestamp)." ".intToHour($startHr));

		$endHr = explode(":", $r[$i.'End']);
		$endHr = $endHr[0];
		$endHr++; // compensate for the real time -1 in DB

		    $end = strtotime(date("Y-m-d", $timestamp)." ".intToHour($endHr));


		if($start <= $timestamp && $end >= $timestamp)
		{
		    return true;
		}
	    }
	}
    }
	return false;
}



function numOnDuty($type, $timestamp)
{
    // tells how many members of specified type are on duty at that time
    global $dayFirstHour, $dayLastHour, $nightFirstHour, $nightLastHour;
    global $dbName;
    global $debug;

    //figure out the month, shift, date, etc.

    $date = date("d", $timestamp);

    if(date("H", $timestamp) < $dayFirstHour)
    {
	$shift = 'night';
	$tmp = $timestamp - 86400;
	$date = date("d", $timestamp);
    }
    elseif(date("H", $timestamp) >= $nightFirstHour)
    {
	$shift = 'night';
    }
    else
    {
	$shift = 'day';
    }

    if($debug){ echo "start numOnDuty timestamp=".date("Y-m-d H:i:s", $timestamp)." date=".$date." shift=".$shift."<br>"; }
    
    mysql_connect() or die("I'm sorry but I cannot connect to the MySQL server. Error: ".mysql_error());
    global $dbName;
    mysql_select_db($dbName) or die("I'm sorry but there was an error selecting the DB. Error: ".mysql_error());

    $query = "SELECT EMTid,status FROM roster;";

    $membersArray = array();

    $result = mysql_query($query) or die("MySQL Query Error: ".mysql_error());
    while($row = mysql_fetch_array($result))
    {
	if($row['status'] == $type)
	{
	    $membersArray[] = $row['EMTid'];
	}
    }


    $count = 0;

    if($debug) { echo "timestamp=".date("Y-m-d H:i:s", $timestamp)." date=".$date."<br>"; }

    $switchedMonths = false;
    $month = date("m", $timestamp);
    $year = date("Y", $timestamp);
    $date = date("d", $timestamp);

    if($date == 1 && $shift=="night" && date("H", $timestamp) < 17)
    {
	if($debug)
	{
	    echo "switch month<br>";
	}
	$t = $timestamp - 86400;
	$month = date("m", $t);
	$year = date("Y", $t);
	$date = date("d", $t);
	$switchedMonths = true;
    }


    if($debug) { echo " ttimestamp=".date("Y-m-d H:i:s", $timestamp)." date=".$date."<br>"; }

    if($shift=='night' && date("H", $timestamp) < $dayFirstHour && (! $switchedMonths))
    {
	$date--;
    }

    $query = "SELECT * FROM schedule_".$year."_".$month."_".$shift." WHERE Date='".$date."';";

    if($debug) {echo "ln754 query=".$query."<br>"; }

    $result = mysql_query($query) or die("MySQL Query Error: ".mysql_error());
	
    $r = mysql_fetch_array($result); // there should only be one row

    for($z = 0; $z < count($membersArray); $z++)
    {
	for($i = 0; $i < 7; $i++)
	{
	    if($r[$i.'ID'] == $membersArray[$z])
	    {		    
		// this is the right person

		$startOrig = $r[$i.'Start'];
		$startHr = explode(":", $r[$i.'Start']);
		$startHr = $startHr[0];
		if($debug) {echo "startOrig=".$startOrig."<br>";}
		if($startOrig == "00:00:00")
		{
		    $start = strtotime(date("Y-m", $timestamp)."-".(date("d", $timestamp) + 1)." ".intToHour($startHr));
		    if($debug) {echo "start if<br>";}
		}
		elseif($startHr <= $dayFirstHour && $shift == 'night')// && date("H", $timestamp) >= ($nightLastHour + 1))
		{
		    if($debug) { echo "start elseif 1 timeStamp=".date("Y-m-d H:i:s", $timestamp)."=".$timestamp."<br>";}
		    if((date("H", $timestamp) >= 0) && (date("H", $timestamp) < $dayFirstHour))
		    {
			$start = strtotime(date("Y-m", $timestamp)."-".(date("d", $timestamp) - 1)." ".intToHour($startHr));
			if($debug) { echo "start elseif 1 if<br>";}
		    }
		    else
		    {
			$start = strtotime(date("Y-m", $timestamp)."-".(date("d", $timestamp))." ".intToHour($startHr));
			if($debug) { echo "start elseif 1 else<br>";}
		    }
		}
		elseif($shift=='night' && date("s", $timestamp) == 59)
		{
		  $start = strtotime(date("Y-m", $timestamp)."-".(date("d", $timestamp))." ".intToHour($startHr));
		  if($debug) {echo "start elseif2<br>";}
		}
		else
		{

		    if($shift== "night" && $startHr > $dayFirstHour && date("H", $timestamp) < $dayFirstHour)
		    {
			// back one day
			$t = $timestamp - 86400;
			$start = strtotime(date("Y-m-d", $t)." ".intToHour($startHr));
		    }
		    elseif($shift == 'night')
		    {
			$start = strtotime(date("Y-m", $timestamp)."-".(date("d", $timestamp))." ".intToHour($startHr));
		    }
		    else
		    {
			$start = strtotime(date("Y-m-d", $timestamp)." ".intToHour($startHr));
		    }

		    if($debug) {echo "start else<br>";}
		}
		$endHr = explode(":", $r[$i.'End']);
		$endOrig = $r[$i.'End'];
		$endHr = $endHr[0];


		if($debug) {echo "endOrig=".$endOrig." date(H, timestamp=".date("H", $timestamp)."<br>";}
		if($endOrig == "00:00:00")
		{
		    $end = strtotime(date("Y-m", $timestamp)."-".(date("d", $timestamp) + 1)." ".intToHour($endHr));
		    if($debug) { echo "end if<br>";}
		}		
		elseif($endHr <= $dayFirstHour && $shift == 'night')// && date("H", $timestamp) >= ($nightLastHour + 1))
		{
		    if($debug) { echo "end elseif 1 timeStamp=".date("Y-m-d H:i:s", $timestamp)."=".$timestamp."endHr=".$endHr."<br>";}
		    if((date("H", $timestamp) >= 0) && (date("H", $timestamp) < $dayFirstHour))
		    {
			$end = strtotime(date("Y-m", $timestamp)."-".(date("d", $timestamp))." ".intToHour($endHr));
			if($debug) { echo "end elseif 1 if<br>";}
		    }
		    else
		    {
			$end = strtotime(date("Y-m", $timestamp)."-".(date("d", $timestamp) + 1)." ".intToHour($endHr));
			if($debug) { echo "end elseif 1 else<br>";}
		    }
		}
		elseif($shift=='night')
		{
		    $end = strtotime(date("Y-m", $timestamp)."-".(date("d", $timestamp))." ".intToHour($endHr));
		    if($debug) { echo "end elseif 2<br>";}
		}
		else
		{
		    $end = strtotime(date("Y-m-d", $timestamp)." ".intToHour($endHr));
		    if($debug) { echo "end else<br>";}
		}

		if($debug) {echo $r[$i.'ID']." start=".date("Y-m-d H:i:s", $start)." timestamp=".date("Y-m-d H:i:s", $timestamp)." end=".date("Y-m-d H:i:s", $end)."<br>";}


		//DEBUG
		//echo 'ID'.$r[$i.'ID'].' startHr='.$startHr.' start='.date("Y-m-d H:i:s", $start).' endHr='.$endHr.'end='.date("Y-m-d H:i:s", $end).' timestamp='.date("Y-m-d H:i:s", $timestamp);
		//END DEBUG

		if($start <= $timestamp && $end >= $timestamp)
		{
		    // DEBUG
		    //echo " true";
		    //END DEBUG
		    if($debug) { echo "count++ EMTid=".$r[$i.'ID']."<br>";}
		    $count++;
		}
		// DEBUG
		//echo '<br>';
		//END DEBUG
	    }
	}
    }
    return $count;

}



function altDay($d)
{
    $shift = "day";
    global $dayFirstHour, $dayLastHour, $nightFirstHour, $nightLastHour;
    

    if($shift == 'day')
    {
	$firstH = $dayFirstHour;
	$lastH = $dayLastHour;
	$hour = strtotime(date("Y-m-d", $d)." ".intToHour($firstH));

	$tempHr = $hour + 1;
	$tempNum = numOnDuty("Senior", $hour);

	for($i = $firstH + 1; $i <= ($lastH + 1); $i++)
	{
	    // DEBUG
            //echo "i=".$i."<br>";
	    $hour = strtotime(date("Y-m-d", $d)." ".intToHour($i));
	    $hourMinus = $hour - 1;
	    $hourPlus = $hour + 1;
	    // DEBUG
	    //echo 'i='.$i.'num='.numOnDuty("Senior", $hourPlus).'<br>';
	    // END DEBUG

	    if((numOnDuty("Senior", $tempHr) <> numOnDuty("Senior", $hourPlus)) && ((numOnDuty("Senior", $tempHr) < 2) || ( numOnDuty("Senior", $hourPlus) < 2)))
	    {
	        // we have a change
		// DEBUG
	        //echo "tempHrNum=".numOnDuty("Senior", $tempHr)." tempHr=".date("H", $tempHr)." hourPlusNum=".numOnDuty("Senior", $hourPlus)." hourPlus=".date("H", $hourPlus)."<br>";
		showInterval($tempHr, $hour, $tempNum);

		$tempHr = $hourPlus;
		$tempNum = numOnDuty("Senior", $hourPlus);
	    }

	    elseif($i == ($lastH + 1))
	    {
		showInterval($tempHr, $hour, $tempNum);
	    }

	}

    }

}



function altNight($d)
{
    global $dayFirstHour, $dayLastHour, $nightFirstHour, $nightLastHour;
    global $debug;

	$firstH = $nightFirstHour;
	$lastH = $nightLastHour;
	$hour = strtotime(date("Y-m-d", $d)." ".intToHour($firstH));

	$tempHr = $hour;
	$tempNum = numOnDuty("Senior", $hour);

	for($i = $firstH + 1; $i <= 23; $i++)
	{
	    if($debug)
	    {
		echo "<b>Hour ".$i."</b><br>";
	    }
	    $hour = strtotime(date("Y-m-d", $d)." ".intToHour($i));
	    $hourMinus = $hour - 1;
	    $hourPlus = $hour + 1;
	    if($debug) { echo "i=".$i." numOnDuty tempHr=".numOnDuty("Senior", $tempHr)." hourMinus=".numOnDuty("Senior", $hourMinus)." hourPlus=".numOnDuty("Senior", $hourPlus)."<br>";}

	    // DEBUG
	    //echo "i=".$i."tempHrNum=".numOnDuty("Senior", $tempHr)." hourMinusNum=".numOnDuty("Senior", $hourMinus)." hourPlusNum=".numOnDuty("Senior", $hourPlus)."<br>";

	    if((numOnDuty("Senior", $tempHr) <> numOnDuty("Senior", $hourPlus)) && ((numOnDuty("Senior", $tempHr) < 2) || ( numOnDuty("Senior", $hourPlus) < 2)))
	    {
	        // we have a change
		showInterval($tempHr, $hour, $tempNum);

		$tempHr = $hourPlus;
		$tempNum = numOnDuty("Senior", $hourPlus);
	    }

	    elseif($i == ($lastH + 1))
	    {
		$tempNum = numOnDuty("Senior", $hourMinus);
		showInterval($tempHr, $hour, $tempNum);
	    }

	}
	$d += (24 * 3600); // add a day
	for($i = 0; $i <= ($lastH + 1); $i++)
	{
	    if($debug)
	    {
		echo "<b>Hour ".$i."</b><br>";
	    }
	    $hour = strtotime(date("Y-m-d", $d)." ".intToHour($i));
	    if($debug) { echo "hour=".$hour."=".date("Y-m-d H:i:s", $hour)."<br>"; }
	    $hourMinus = $hour - 1;
	    $hourPlus = $hour + 1;
	    if($debug) { echo "hour:".$hour." hourMinus=".$hourMinus." hourPlus=".$hourPlus." echo tempHr=".$tempHr."<br>"; }
	    if($debug) { echo "hour:".date("Y-m-d H:i:s", $hour)." hourMinus=".date("Y-m-d H:i:s", $hourMinus)." hourPlus=".date("Y-m-d H:i:s", $hourPlus)." echo tempHr=".date("Y-m-d H:i:s", $tempHr)."<br>"; }
	    if($debug) { echo "i=".$i." numOnDuty tempHr=".numOnDuty("Senior", $tempHr)." hourMinus=".numOnDuty("Senior", $hourMinus)." hourPlus=".numOnDuty("Senior", $hourPlus)."<br>";}

	    if((numOnDuty("Senior", $tempHr) <> numOnDuty("Senior", $hourPlus)) && ((numOnDuty("Senior", $tempHr) < 2) || ( numOnDuty("Senior", $hourPlus) < 2)))
	    {
	        // we have a change
		showInterval($tempHr, $hour, $tempNum);

		$tempHr = $hourPlus;
		$tempNum = numOnDuty("Senior", $hourPlus);
	    }

	    elseif($i == ($lastH + 1))
	    {
		//echo "i=lastH+1<br>";
		$tempNum = numOnDuty("Senior", $hourMinus);
		showInterval($tempHr, $hour, $tempNum);
	    }

	}

}


function showInterval($start, $end, $numMemb)
{
    $memberType = "Senior";
    global $memberTypes;
    $required = 0;
    for($i = 0; $i < count($memberTypes); $i++)
    {
	if($memberTypes[$i]['name'] == $memberType)
	{
	    $required = $memberTypes[$i]['crewRequires'];
	}
    }

    $need = $required - $numMemb;
    if($need > $required)
    {
	$need = $required;
    }
    if($need < 0)
    {
	$need = 0;
    }

    global $spacer;
    echo $spacer.$spacer.$spacer;

    echo date("H:i", $start)."-".date("H:i", $end)." ";
    if($need < 1)
    {
	echo "Duty Crew.";
    }
    else
    {
	echo "<i>General. ".$need." Senior member(s) needed.</i>";
    }
    echo "<br>";
}

?>