<?php

//
// inc/sched.php
//
// Functions to generate the schedule
//
// Time-stamp: "2008-07-01 16:15:16 jantman"
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

require_once('config/config.php');
require_once('config/scheduleConfig.php');

function showMonthCalendarTable($mainDate)
{
    // the master function to handle calendar generation
    // it generates a calendar table with one cell per day
    // and calls showDayCell() with the timestamp of the day to fill in the cell.
    $month = date("m", $mainDate);
    $year = date("Y", $mainDate);

    echo "<table class='cal'>\n";
    echo "<tr>";
    echo "<th>Sunday</th>";
    echo "<th>Monday</th>";
    echo "<th>Tuesday</th>";
    echo "<th>Wednesday</th>";
    echo "<th>Thursday</th>";
    echo "<th>Friday</th>";
    echo "<th>Saturday</th>";
    echo "</tr>\n";

    // stuff for calculating the calendar layout
    $cols = 7;
    $weekday = date("w", mktime(0, 0, 0, $month, 1, $year));
    $numdays = date("t", mktime(0, 0, 0, $month, 1, $year));
    $numrows = ceil(($numdays + $weekday) / $cols);
    $counter = 1;
    $newcounter = 1;

    echo "<tr>"."\n";
    $daysleft = 6 - $weekday--;

    // BEGIN CALENDAR

    // last days of last month that are in same week as the first of this month
    $lastday = mktime(0, 0, 0, $month, 0, $year); // last day of last month
    $firstDate = strtotime(date("Y-m-", $lastday).(date("d", $lastday)-$weekday));
    for($f=0;$f<=$weekday;$f++)
    {
	$mydate = strtotime(date("Y-m-", $firstDate).(date("d", $firstDate)+$f));
	showDayCell($mydate, $mainDate);
    }

    // first week of this month
    for($f=0;$f<=$daysleft;$f++)
    {
	$thisdate = mktime(0, 0, 0, $month, $counter, $year);
	showDayCell($thisdate, $mainDate);
	$counter++;
    }

    echo "</tr>\n";

    // other weeks of this month, including full last week of month (and any days of next month in same week)
    for($i=1;$i<=($numrows-1);$i++)
    {
	echo "<tr>\n";
	for($a=0;$a<=($cols-1);$a++)
	{
	    $thisdate = mktime(0, 0, 0, $month, $counter, $year);
	    showDayCell($thisdate, $mainDate);
	    $counter++;
	}
	echo "</tr>\n";
    }

    echo "</table>\n";
}

function showDayCell($ts, $monthTS)
{
    echo getFullCell($ts, $monthTS);
}

function getFullCell($ts, $monthTS)
{
    $final = "";
    $final .= getCellHeader($ts, $monthTS);
    $final .= getCellContent($ts, $monthTS);

    // close the DIV and TD
    $final .= '</div> <!-- END todayDay/day/otherDay DIV -->'."\n";
    $final .= "</td>\n";
    return $final;
}

function getCellHeader($ts, $monthTS)
{
    // this function outputs the table cell contents for a day
    // $ts is the timestamp for that day, $monthTS is the timestamp for a day in the current month

    global $shift;

    // generate the opening DIV code
    $final = "";
    $final .= "<td id='date_".$ts."'>";
    $displayStr = ""; // the string to actually display for the date
    $displayStr = date("d", $ts);
    if(substr($displayStr, 0, 1) == 0)
    {
	$displayStr = substr($displayStr, 1);
    }
    $displayStr .= getDayMessage($ts, $shift);
    if(date("Y-m", $ts) != date("Y-m", $monthTS))
    {
	// not in the currently viewed month
	$final .= '<div class="otherDate">'.date("M", $ts)." ".$displayStr.'</div>'."\n";
	$final .= '<div class="otherDay" id="day_'.$ts.'">'."\n";
    }
    elseif(date("Y-m-d", $ts) == date("Y-m-d"))
    {
	// TODAY
	$final .= '<div class="todayDate" id="date_'.$ts.'" onClick="showMessageForm('.$ts.','.$monthTS.')">'.$displayStr.'</div>'."\n";
	$final .= '<div class="todayDay" id="day_'.$ts.'" onClick="showSignonForm('.$ts.','.$monthTS.')">'."\n";
    }
    else
    {
	$final .= '<div class="date" id="date_'.$ts.'" onClick="showMessageForm('.$ts.','.$monthTS.')">'.$displayStr.'</div>'."\n";
	$final .= '<div class="day" id="day_'.$ts.'" onClick="showSignonForm('.$ts.','.$monthTS.')">'."\n";
    }
    return $final;
}

function getCellContent($ts, $monthTS)
{
    global $config_sorted_entries;
    if($config_sorted_entries == true)
    {
	return getSortedCellContent($ts, $monthTS);
    }

    // this function outputs the table cell contents for a day
    // $ts is the timestamp for that day, $monthTS is the timestamp for a day in the current month

    global $dbName;

    // figure out the month, year, date, and shift we want
    global $shift;
    $year = date('Y', $ts);
    $month = date('m', $ts);
    $date = date('d', $ts);

    // get the info from the DB
    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed.");
    mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!");
    $query =  'SELECT * FROM schedule_'.$year.'_'.$month.'_'.strtolower($shift).' WHERE Date='.$date.';';
    $result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query.<br><br>" . mysql_error());
    mysql_close($conn); 
    $row = mysql_fetch_array($result);
    mysql_free_result($result); 

    // TODO - JS popup - year, month, shift, date, signonSlot (1-6)

    $final = "";
    for($i = 1; $i < 7; $i++)
    {
	if($row['1ID'] != null)
	{
	    if(date("Y-m", $ts) != date("Y-m"))
	    {
		// another month, don't show link to edit
		$linkLoc = "";
		$final.= '<div class="calSignon">"'.memberString($row[$i.'ID'], $row[$i.'Start'], $row[$i.'End'], $linkLoc).'</div>'."\n";
	    }
	    else
	    {
		$final .= '<div class="calSignon">';
		$linkLoc = 'javascript:showEditForm('.$year.','.$month.',\''.$shift.'\','.$date.','.$i.')';
		$final .= memberString($row[$i.'ID'], $row[$i.'Start'], $row[$i.'End'], $linkLoc);
		$final .= '</a></div>'."\n";
	    }
	}
    }
    return $final;
}

function getSortedCellContent($ts, $monthTS)
{
    // this function outputs the table cell contents for a day
    // $ts is the timestamp for that day, $monthTS is the timestamp for a day in the current month

    global $dbName;

    // figure out the month, year, date, and shift we want
    global $shift;
    $year = date('Y', $ts);
    $month = date('m', $ts);
    $date = date('d', $ts);

    // get the info from the DB
    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed.");
    mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!");
    $query =  'SELECT * FROM schedule_'.$year.'_'.$month.'_'.strtolower($shift).' WHERE Date='.$date.';';
    $result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query.<br><br>" . mysql_error());
    mysql_close($conn); 
    $row = mysql_fetch_array($result);
    mysql_free_result($result); 

    // make some arrays with the information
    $start = array(); // array of start times like {1-6} => startTime (ts) ({1-6}ID)
    $end = array(); // array of end times like {1-6} => endTime(ts) ({1-6}start)
    $membIDs = array(); // array of EMTids like {1-6} => EMTid ({1-6}end)
    
    // probies
    $Pstart = array(); // array of start times like {1-6} => startTime (ts) ({1-6}ID)
    $Pend = array(); // array of end times like {1-6} => endTime(ts) ({1-6}start)
    $PmembIDs = array(); // array of EMTids like {1-6} => EMTid ({1-6}end)

    for($i = 1; $i < 7; $i++)
    {
	if($row[$i.'ID'] != null && isProbie($row[$i.'ID'])){ $Pstart[$i] = schedTimeToTS($year, $month, $date, $row[$i.'Start']); $Pend[$i] = schedTimeToTS($year, $month, $date, $row[$i.'End']); $PmembIDs[$i] = $row[$i.'ID'];}
	elseif($row[$i.'ID'] != null){ $start[$i] = schedTimeToTS($year, $month, $date, $row[$i.'Start']); $end[$i] = schedTimeToTS($year, $month, $date, $row[$i.'End']); $membIDs[$i] = $row[$i.'ID'];}
    }

    asort($start, SORT_NUMERIC);
    asort($Pstart, SORT_NUMERIC);

    // TODO - JS popup - year, month, shift, date, signonSlot (1-6)

    $final = "";

    foreach($start as $key => $val)
    {
	if(date("Y-m", $ts) != date("Y-m"))
	{
	    // another month, don't show link to edit
	    $final.= '<div class="calSignon"> ';
	    $linkLoc = "";
	    $final .= memberString($membIDs[$key], date("H:i:s", $start[$key]), date("H:i:s", $end[$key]), $linkLoc);
	    $final .= '</div>'."\n";
	}
	else
	{
	    $final.= '<div class="calSignon"> ';
	    $linkLoc = 'javascript:showEditForm('.$year.','.$month.',\''.$shift.'\','.$date.','.$key.')';
	    $final .= memberString($membIDs[$key], date("H:i:s", $start[$key]), date("H:i:s", $end[$key]), $linkLoc);
	    $final .= '</a></div>'."\n";
	}
    }

    foreach($Pstart as $key => $val)
    {
	if(date("Y-m", $ts) != date("Y-m"))
	{
	    // another month, don't show link to edit
	    $final.= '<div class="calSignon"> ';
	    $linkLoc = "";
	    $final .= memberString($PmembIDs[$key], date("H:i:s", $Pstart[$key]), date("H:i:s", $Pend[$key]), $linkLoc);
	    $final .= '</div>'."\n";
	}
	else
	{
	    $final.= '<div class="calSignon"> ';
	    $linkLoc = 'javascript:showEditForm('.$year.','.$month.',\''.$shift.'\','.$date.','.$key.')';
	    $final .= memberString($PmembIDs[$key], date("H:i:s", $Pstart[$key]), date("H:i:s", $Pend[$key]), $linkLoc);
	    $final .= '</a></div>'."\n";
	}
    }

    return $final;
}

function memberString($IDstr, $startTime, $endTime, $linkLocation)
{
    global $showNames;


    // this will generate the string for the day's table
    if($IDstr == null)
    {
	// if we're not given an IDstr string, do nothing.
	return '&nbsp;';
    }

    global $dbName;
    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed.".$errorMsg);
    mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!".$errorMsg);
    $query =  'SELECT EMTid,shownAs,status FROM roster WHERE EMTid="'.$IDstr.'";';
    $result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query: ".$query."<br><br>" . mysql_error().'<br><br>'.$errorMsg);
    mysql_close($conn); 
    $row = mysql_fetch_array($result);
    mysql_free_result($result); 

    $string = ""; // the string we're going to return

    if($linkLocation != "")
    {
	// we need this to be a hyperlink to linkLoc
	$string .= '<a href="'.$linkLocation.'"';
	if($row['status'] == 'Probie')
	{
	    // the member is a probie, so the link needs to be a special class
	    $string .= ' class="probieSignon"';
	}
	$string .= '>';
    }

    if($showNames==1)
    {
	$string .= $row['shownAs'];
    }
    else
    {
	$string .= $IDstr;
    }
    if($row['status'] == 'Probie')
    {
	$string .= '(P)';
    }
    $string .= " ".parseTime($startTime, $endTime);

    if($linkLocation != "")
    {
	// close the link
	$string .= '</a>';
    }

    return $string;
}

function isProbie($EMTid)
{
    global $dbName;
    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed.".$errorMsg);
    mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!".$errorMsg);
    $query =  'SELECT EMTid,shownAs,status FROM roster WHERE EMTid="'.$EMTid.'";';
    $result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query: ".$query."<br><br>" . mysql_error().'<br><br>'.$errorMsg);
    mysql_close($conn); 
    $row = mysql_fetch_array($result);
    mysql_free_result($result); 
    if($row['status'] == 'Probie')
    {
	return true;
    }
    return false;
}

function makeDateStr($start, $end)
{
    $str = date("Hi", $start);
    $str .= "-";
    $str .= date("Hi", $end);
    return $str;
}

function makeTitle($s)
{
    $s = stripslashes($s);
    if(strlen($s) >= 12)
    {
	$s = substr($s, 0, 9)."...";
    }
    $s = "&raquo; ".$s;
    return $s;
}

function lastMonthDate($thisdate)
{
    if(date("m", $thisdate) == 1)
    {
	return strtotime((date("Y", $thisdate)-1)."-12-01");
    }
    return strtotime(date("Y", $thisdate)."-".(date("m", $thisdate)-1)."-01");
}

function nextMonthDate($thisdate)
{
    if(date("m", $thisdate) == 12)
    {
	return strtotime((date("Y", $thisdate)+1)."-01-01");
    }
    return strtotime(date("Y", $thisdate)."-".(date("m", $thisdate)+1)."-01");
}

function parseTime($startTime, $endTime)
{
    // this determines the time string to be added to the member string
    global $shift;
    global $showTimeCompleteShift;
    global $schedTimeFormat;

    if($showTimeCompleteShift == false)
    {
	if(($shift=='Day') && ($startTime=='06:00:00') && ($endTime=='18:00:00'))
	{
	    return '';
	}
	elseif(($shift=='Night') && ($startTime=='18:00:00') && ($endTime=='06:00:00'))
	{
	    return '';
	}
    }

    //time format displayed after names/IDs on schedule
    // 1: name 6-18
    // 2: name 0600-1800
    // 3: name 06:00-18:00
    // default is 3
    if($schedTimeFormat==1)
    {
	$start = explode(":", $startTime);
	$start = $start[0];
	if((strlen($start)==2) && substr($start, 0, 1) == "0")
	{
	    $start = substr($start, 1, 1);
	}
	$end = explode(":", $endTime);
	$end = $end[0];
	if((strlen($end)==2) && substr($end, 0, 1) == "0")
	{
	    $end = substr($end, 1, 1);
	}
    }
    elseif($schedTimeFormat==2)
    {
	$start = explode(":", $startTime);
	$start = $start[0].$start[1];
	$end = explode(":", $endTime);
	$end = $end[0].$end[1];
    }
    elseif($schedTimeFormat==3)
    {
	$start = explode(":", $startTime);
	$start = $start[0].":".$start[1];
	$end = explode(":", $endTime);
	$end = $end[0].":".$end[1];
    }
    return $start."-".$end;
}

function schedTimeToTS($y, $m, $d, $timeStr)
{
    $t = explode(":", $timeStr);
    $h = $t[0];
    $m = $t[1];
    if($h < 6)
    {
	// night on the schedule, schedule date is previous calendar date
	$temp = strtotime($y."-".$m."-".$d." 12:00:00");
	$temp = $temp + 86400;
	$y = date("Y", $temp);
	$m = date("m", $temp);
	$d = date("d", $temp);
    }
    $ts = strtotime($y."-".$m."-".$d." ".$timeStr);
    return $ts;
}

function getDayMessage($ts, $shift)
{
    // gets the daily message for the specified day and shift.
    global $dbName;

    $year = date("Y", $ts);
    $month = date("m", $ts);
    $date = date("d", $ts);
    $shift = strtolower($shift);

    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed.".$errorMsg);
    mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!".$errorMsg);
    $query =  'SELECT message FROM schedule_'.$year.'_'.$month.'_'.$shift.' WHERE Date='.$date.';';
    $result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query.<br>".$query."<br>" . mysql_error().'<br><br>'.$errorMsg);
    $row = mysql_fetch_array($result);    
    mysql_close($conn); 
    if($row['message'] != null)
    {
	return '  <span class="dateMessage">'.$row['message'].'</span>';
    }
    return "";
}

?>