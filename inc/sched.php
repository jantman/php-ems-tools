<?php

//
// inc/sched.php
//
// Functions to generate the schedule
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

    // generate the opening DIV code
    $final = "";
    $final .= "<td id='date_".$ts."'>";
    $displayStr = ""; // the string to actually display for the date
    $displayStr = date("d", $ts);
    if(substr($displayStr, 0, 1) == 0)
    {
	$displayStr = substr($displayStr, 1);
    }
    if(date("Y-m", $ts) != date("Y-m", $monthTS))
    {
	// not in the currently viewed month
	$final .= '<div class="otherDate">'.date("M", $ts)." ".$displayStr.'</div>'."\n";
	$final .= '<div class="otherDay" id="day_'.$ts.'">'."\n";
    }
    elseif(date("Y-m-d", $ts) == date("Y-m-d"))
    {
	// TODAY
	$final .= '<div class="todayDate">'.$displayStr.'</div>'."\n";
	$final .= '<div class="todayDay" id="day_'.$ts.'" onClick="showNewForm('.$ts.','.$monthTS.')">'."\n";
    }
    else
    {
	$final .= '<div class="date">'.$displayStr.'</div>'."\n";
	$final .= '<div class="day" id="day_'.$ts.'" onClick="showNewForm('.$ts.','.$monthTS.')">'."\n";
    }
    return $final;
}

function getCellContent($ts, $monthTS)
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


    // TODO: support sorting by start of member signon and probationary status

    // TODO - JS popup - year, month, shift, date, signonSlot (1-6)

    $final = "";
    if($row['1ID'] != null)
    {
	    $final.= '<div class="calSignon"> <a href="javascript:showMemberForm('.$year.', '.$month.', '.$shift.', '.$date.', 1)">';
	    $final .= memberString($row['1ID'], $row['1Start'], $row['1End']);
	    $final .= '</a></div>'."\n";
    }
    if($row['2ID'] != null)
    {
	    $final.= '<div class="calSignon"> <a href="javascript:showMemberForm('.$year.', '.$month.', '.$shift.', '.$date.', 2)">';
	    $final .= memberString($row['2ID'], $row['2Start'], $row['2End']);
	    $final .= '</a></div>'."\n";
    }
    if($row['3ID'] != null)
    {
	    $final.= '<div class="calSignon"> <a href="javascript:showMemberForm('.$year.', '.$month.', '.$shift.', '.$date.', 3)">';
	    $final .= memberString($row['3ID'], $row['3Start'], $row['3End']);
	    $final .= '</a></div>'."\n";
    }
    if($row['4ID'] != null)
    {
	    $final.= '<div class="calSignon"> <a href="javascript:showMemberForm('.$year.', '.$month.', '.$shift.', '.$date.', 4)">';
	    $final .= memberString($row['4ID'], $row['4Start'], $row['4End']);
	    $final .= '</a></div>'."\n";
    }
    if($row['5ID'] != null)
    {
	    $final.= '<div class="calSignon"> <a href="javascript:showMemberForm('.$year.', '.$month.', '.$shift.', '.$date.', 5)">';
	    $final .= memberString($row['5ID'], $row['5Start'], $row['5End']);
	    $final .= '</a></div>'."\n";
    }
    if($row['6ID'] != null)
    {
	    $final.= '<div class="calSignon"> <a href="javascript:showMemberForm('.$year.', '.$month.', '.$shift.', '.$date.', 6)">';
	    $final .= memberString($row['6ID'], $row['6Start'], $row['6End']);
	    $final .= '</a></div>'."\n";
    }
    return $final;
}

function memberString($IDstr, $startTime, $endTime)
{
    global $showNames;
    // this will generate the string for the day's table
    if($IDstr == null)
    {
	return '&nbsp;';
    }
    else
    {
    global $dbName;
    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed.".$errorMsg);
    mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!".$errorMsg);
    $query =  'SELECT EMTid,shownAs,status FROM roster WHERE EMTid="'.$IDstr.'";';
    $result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query: ".$query."<br><br>" . mysql_error().'<br><br>'.$errorMsg);
    mysql_close($conn); 
    $row = mysql_fetch_array($result);
    mysql_free_result($result); 
    $string = '';
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
    if($row['status'] == 'Probie')
    {
	$string = '<font color="blue"><b>'.$string.'</b></font>';
    }
    return $string;
    }
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

?>