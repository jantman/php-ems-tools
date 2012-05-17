<?php
// massSignon.php.inc
//
// Simple form to allow members to sign on for multiple shifts at once.
// second version, 2010-07-20 for second schedule version
//
// +----------------------------------------------------------------------+
// | PHP EMS Tools      http://www.php-ems-tools.com                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006-2010 Jason Antman.                                |
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
//      $Id: massSignOns.php,v 1.4 2007/09/20 00:00:40 jantman Exp $

function showCurrentMonth($viewOnly = false)
{
    global $month, $year, $dayFirstHour, $nightFirstHour, $mainDate, $shift;
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

    // TODO - BEGIN shift timestamp hack
    // TODO - this is a hack, it should be implemented in the date GET variable
    if($shift == "Day")
    {
	$startTimeStr = $dayFirstHour.":00:00";
    }
    else
    {
	$startTimeStr = $nightFirstHour.":00:00";
    }
    // TODO - end hack
    // TODO - END shift timestamp hack

    // BEGIN CALENDAR

    // last days of last month that are in same week as the first of this month
    $lastday = mktime(0, 0, 0, $month, 0, $year); // last day of last month
    $firstDate = strtotime(date("Y-m-", $lastday).(date("d", $lastday)-$weekday));
    for($f=0;$f<=$weekday;$f++)
    {
	$mydate = strtotime(date("Y-m-", $firstDate).(date("d", $firstDate)+$f)." ".$startTimeStr);
	showMassSignonDay($mydate, $mainDate, $viewOnly);
    }

    // first week of this month
    for($f=0;$f<=$daysleft;$f++)
    {
	$thisdate = mktime(substr($startTimeStr, 0, 2), 0, 0, $month, $counter, $year);
	showMassSignonDay($thisdate, $mainDate, $viewOnly);
	$counter++;
    }

    echo "</tr>\n";

    // other weeks of this month, including full last week of month (and any days of next month in same week)
    for($i=1;$i<=($numrows-1);$i++)
    {
	echo "<tr>\n";
	for($a=0;$a<=($cols-1);$a++)
	{
	    $thisdate = mktime(substr($startTimeStr, 0, 2), 0, 0, $month, $counter, $year);
	    showMassSignonDay($thisdate, $mainDate, $viewOnly);
	    $counter++;
	}
	echo "</tr>\n";
    }
}

function showMassSignonDay($ts, $monthTS, $viewOnly = false)
{
    $final = "";
    $final .= getMassSignonCellHeader($ts, $monthTS);
    $final .= getMassSignonCellContent($ts, $monthTS, $viewOnly);

    // close the DIV and TD
    $final .= '</div> <!-- END todayDay/day/otherDay DIV -->'."\n";
    $final .= "</td>\n";
    echo $final;
}

function getMassSignonCellHeader($ts, $monthTS)
{
    // this function outputs the table cell contents for a day
    // $ts is the timestamp for that day, $monthTS is the timestamp for a day in the current month

    global $shift;
    $shiftID = shiftNameToID(strtolower(tsToShiftName($ts)));

    // generate the opening DIV code
    $final = "";
    $final .= "<td id='date_".$ts."'>";
    $displayStr = ""; // the string to actually display for the date
    $displayStr = date("d", $ts);
    if(substr($displayStr, 0, 1) == 0)
    {
	$displayStr = substr($displayStr, 1);
    }
    $message = getDayMessage($ts, $shiftID); // returns an array with "message" and "id"
    $messageID = -1; // send -1 to JS function if we don't have a message
    if(isset($message['message']))
    {
	$displayStr .= $message['message'];
	$messageID = $message['id'];
    }
    // TODO: daily message form should have better arguments
    if(date("Y-m", $ts) != date("Y-m", $monthTS))
    {
	// not in the currently viewed month
	$final .= '<div class="otherDate">'.date("M", $ts)." ".$displayStr.'</div>'."\n";
	$final .= '<div class="otherDay" id="day_'.$ts.'">'."\n";
    }
    elseif(date("Y-m-d", $ts) == date("Y-m-d"))
    {
	// TODAY
	$final .= '<div class="todayDate" id="message_'.$ts.'">'.$displayStr.'</div>'."\n";
	$final .= '<div class="todayDay" id="day_'.$ts.'">'."\n";
    }
    elseif(strtotime(date("Y-m-d", $ts)) < time())
    {
	// this month, in past
	$final .= '<div class="pastDate" id="message_'.$ts.'">'.$displayStr.'</div>'."\n";
	$final .= '<div class="pastDay" id="day_'.$ts.'"">'."\n";
    }
    else
    {
	// this month, in future
	$final .= '<div class="date" id="message_'.$ts.'">'.$displayStr.'</div>'."\n";
	$final .= '<div class="day" id="day_'.$ts.'">'."\n";
    }
    return $final;
}

function getMassSignonCellContent($ts, $monthTS, $viewOnly = false)
{
    global $config_sorted_entries, $dbName, $config_sched_table, $shift;
    if($config_sorted_entries == true)
    {
	// TODO - this needs to be re-implemented
	//return getSortedCellContent($ts, $monthTS);
    }

    // this function outputs the table cell contents for a day
    // $ts is the timestamp for that day, $monthTS is the timestamp for a day in the current month

    // figure out the month, year, date, and shift we want
    //$shift = tsToShiftName($ts);
    $year = date('Y', $ts);
    $month = date('m', $ts);
    $date = date('d', $ts);

    // get the info from the DB
    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed.");
    mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!");
    // TODO - using the shift name in this schedule is a hack to retain compatibility with old pages
    $query = 'SELECT s.* FROM '.$config_sched_table.' AS s LEFT JOIN schedule_shifts AS ss ON s.sched_shift_id=ss.sched_shift_id WHERE sched_year='.$year.' AND sched_month='.$month.' AND sched_date='.$date.' AND ss.shiftTitle="'.$shift.'" AND s.deprecated=0 ORDER BY s.start_ts;';
    $result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query.<br><br>" . mysql_error()."<br />".$query);

    //echo "\n<!--".$query."-->\n"; // DEBUG

    $final = "";
    while($row = mysql_fetch_array($result))
    {
	// figure out if this signon is this month or not
	if($month != date('m', $monthTS))
	{
	    // this isn't the month being shown, don'tallow an edit link
	    $linkLoc = "";
	    $final.= '<div class="calSignon">'.memberString($row['EMTid'], $row['start_ts'], $row['end_ts'], $linkLoc, $ts, $monthTS).'</div>'."\n";
	}
	else
	{
	    // show an edit link
	    $final .= '<div class="calSignon">';
	    $final .= memberStringMassSignon($row['EMTid'], $row['start_ts'], $row['end_ts']);
	    $final .= '</div>'."\n";
	}
    }

    // we need to have 6 DIVs to fill the box
    $remainingDIVs = 6 - mysql_num_rows($result);
    if($remainingDIVs > 0)
    {
	for($i = 0; $i < $remainingDIVs; $i++)
	{
	    $final .= '<div class="calSignon">&nbsp;</div>'."\n";
	}
    }

    mysql_free_result($result); 

    if($final == "")
    {
	// empty cell - throw in the 6 empty DIVs to keep the background correct
	$final .= '<div class="calSignon">&nbsp;</div>'."\n";
	$final .= '<div class="calSignon">&nbsp;</div>'."\n";
	$final .= '<div class="calSignon">&nbsp;</div>'."\n";
	$final .= '<div class="calSignon">&nbsp;</div>'."\n";
	$final .= '<div class="calSignon">&nbsp;</div>'."\n";
	$final .= '<div class="calSignon">&nbsp;</div>'."\n";
    }

    if(! $viewOnly)
    {
	// only show check box if shift is in future
	if($ts > time())
	{
	    $final .= '<div class="calSignon" style="text-align: center;">'.'<input type="checkbox" name="signon_'.$ts.'_'.$date.'" id="signon_'.$ts.'_'.$date.'">'.'<strong>Sign On '.$month.'-'.$date.'</strong></div> <!-- '.date("Y-m-d H:i:s", $ts).' -->'."\n";
	}
	else
	{
	    $final .= '<div class="calSignon" style="text-align: center;">&nbsp;</div>'."\n";
	}
    }

    return $final;
}


function memberStringMassSignon($IDstr, $start_ts, $end_ts)
{
    global $showNames;


    // this will generate the string for the day's table
    if($IDstr == null)
    {
	// if we're not given an IDstr string, do nothing.
	return '&nbsp;';
    }

    // echo "\n<!-- IDstr=".$IDstr." start_ts=".$start_ts." end_ts=".$end_ts." linkLocation=".$linkLocation." -->\n"; // DEBUG

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
    $string .= " ".parseTime($start_ts, $end_ts);

    if($linkLocation != "")
    {
	// close the link
	$string .= '</a>';
    }

    return $string;
}



?>