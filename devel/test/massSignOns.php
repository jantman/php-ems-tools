<?php
// massSignOns.php
//
// Simple form to allow members to sign on for multiple shifts at once.
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
//      $Id: massSignOns.php 149 2008-11-26 02:45:38Z jantman $


// this file will import the user's customization
require_once('./config/config.php');

require_once('./config/scheduleConfig.php'); // schedule configuration

// DEBUG
definedUsedInOtherFile();
// END DEBUG

// this script views the current schedule from the DB

// get the URL variables
if(! empty($_GET['year']))
{
    $year = $_GET['year'];
}
else
{
    $year = date('Y');
}
if(! empty($_GET['month']))
{
    $month = $_GET['month'];
}
else
{
    $month = date('m');
}
if(! empty($_GET['shift']))
{
    $shift = $_GET['shift'];
}
else
{
    if((date('H')<6) || (date('H')>18))
    {
	$shift = "night";
    }
       else
    {
	$shift = "day";
    }
}

// find the relative URL for this page
$uri = $_SERVER['REQUEST_URI'];
$pageURL = substr($uri, strrpos($uri, "/") + 1, strlen($uri) - (strrpos($uri, "/") + 1));

// make the textual shift name
if($shift=="day")
{
    $shiftName = "Days";
    $otherShift = "night";
    $otherShiftName = "Nights";
}
if($shift=="night")
{
    $shiftName = "Nights";
    $otherShift = "day";
    $otherShiftName = "Days";
}

// make a timestamp for this calendar
$timestamp = strtotime($year."-".$month."-01");
$monthName = date("F", $timestamp); // the full textual name of the month

// THIS IS THE BEGINNING OF THE HTML
echo '<html>';
echo '<head>';
echo '<meta http-equiv="refresh" content="180">';
echo '<link rel="stylesheet" href="'.$serverWebRoot.'php_ems.css" type="text/css">'; // the location of the CSS file for the schedule
echo '<title>'.$orgName." Schedule Mass Signons- ".$monthName." ".$year." ".$shiftName.'</title>';
echo '<script type="text/javascript" src="php-ems-tools.js"> </script>';
echo '</head>';
echo '<body>';


// output the header
echo '<h3 align=center>'.$shortName.' Mass Signon for '.$monthName.' '.$year.' - '.$shiftName.'</h3>';

// navigation links
echo '<p align=center>';
if($month==1)
{
    echo '<a href="massSignOns.php?year='.($year-1).'&month=12&shift='.$shift.'"> << Month </a>';
}
else
{
    echo '<a href="massSignOns.php?year='.$year.'&month='.($month-1).'&shift='.$shift.'"> << Month </a>';
}
echo '&nbsp;&nbsp;';
echo '<a href="massSignOns.php?year='.$year.'&month='.$month.'&shift='.$otherShift.'"> '.$otherShiftName.' </a>';
echo '&nbsp;&nbsp;';
echo '<a href="massSignOns.php"> Current Shift </a>';
echo '&nbsp;&nbsp;';
echo '<a href="javascript:helpPopUp('."'docs/massSignOns_help.html'".')">HELP</a>';
echo '&nbsp;&nbsp;';
if($month==12)
{
    echo '<a href="massSignOns.php?year='.($year+1).'&month=1&shift='.$shift.'">Month >> </a>';
}
else
{
    echo '<a href="massSignOns.php?year='.$year.'&month='.($month+1).'&shift='.$shift.'">Month >> </a>';
}
echo '</p>';

echo '<form method="post" action="doMassSignon.php">';

echo '<table width="100%" class="roster">';

showCurrentMonth();

echo '</table>';

function showCurrentMonth()
{
    // use these variables with a global scope
    global $month;
    global $year;
    global $dbName;
    global $shift;
    // make sure the table exists
    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed.".$errorMsg);
    mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!".$errorMsg);
    $query = 'SHOW TABLES LIKE "schedule_'.$year.'_'.$month.'_'.$shift.'";';
    $result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query<br>".$query."<br>" . mysql_error().'<br><br>'.$errorMsg);
    if(mysql_num_rows($result)<1)
    {
	$query =  'CREATE TABLE IF NOT EXISTS schedule_'.$year.'_'.$month.'_'.$shift.' SELECT * FROM schedule_template;';
	$result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query<br>".$query."<br>" . mysql_error().'<br><br>'.$errorMsg);
    }
    mysql_close($conn); 
    // find out the day number of the first of the month
    // 1 is sunday 7 is saturday
    $startDay = date("w" , strtotime($year.'-'.$month.'-01')) + 1; 
    // find out the number of days in the month
    $numDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

    echo '<input name="numDays" type="hidden" value="'.$numDays.'" />';
    echo '<input name="month" type="hidden" value="'.$month.'" />';
    echo '<input name="year" type="hidden" value="'.$year.'" />';
    echo '<input name="shift" type="hidden" value="'.$shift.'" />';

    dayNames(); // prints a table row with all of the day names

    showWeek($startDay, 1, $numDays); // show the first week

    $nextDay = 7 - $startDay + 2; // should be the start date of the second week.
    while($nextDay <= $numDays)
    {
	// display the rest of the weeks
	showWeek(1, $nextDay, $numDays);
	$nextDay += 7;
    }
}

function showWeek($startDay, $startDate, $numDays)
{
    global $year;
    global $month;
    global $shift;
    global $dbName;

    echo '<tr>';
    
    $currentDate = $startDate;

    for($i = 1; $i<8; $i++)
    {
	// here we output the date and the special message
	$popURL="dailyMessage.php?year=".$year."&month=".$month."&date=".$currentDate."&shift=".$shift;

	if(($i >= $startDay) && ($currentDate <= $numDays))
	{
	    if($currentDate == date("d")) 
	    {
		if($shift == 'day') // today's day shift
		{
		    echo '<td style="cursor: hand; cursor: pointer; background-color: gray;" onclick="popUp('."'".$popURL."'".')">';
		}
		elseif(($shift == 'night') && (date('H') > 6)) // today's night shift
		{
		    echo '<td style="cursor: hand; cursor: pointer; background-color: gray;" onclick="popUp('."'".$popURL."'".')">';
		}
	    }
	       elseif(($shift == 'night') && ($currentDate == date('d') - 1) && (date('G') < 6)) // still last day's shift
	    {
		echo '<td style="cursor: hand; cursor: pointer; background-color: gray;" onclick="popUp('."'".$popURL."'".')">';
	    }
	    else
	    {
		echo '<td style="cursor: hand; cursor: pointer;" onclick="popUp('."'".$popURL."'".')">';
	    }
	    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed.".$errorMsg);
	    mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!".$errorMsg);
	    $query =  'SELECT message FROM schedule_'.$year.'_'.$month.'_'.$shift.' WHERE Date='.$currentDate.';';
	    $result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query.<br>".$query."<br>" . mysql_error().'<br><br>'.$errorMsg);
	    $row = mysql_fetch_array($result);
	    $message = $row['message'];
	    mysql_free_result($result); 
	    mysql_close($conn); 
	    echo '<b>'.($currentDate).'&nbsp;&nbsp;<i>'.$message.'</i></b>';
	    $currentDate++;
	}
	else
	{
	    echo '<td>&nbsp;';
	}
	echo '</td>';
    }
    echo '</tr><tr>';
    $currentDate = $startDate;
    for($i = 1; $i<8; $i++)
    {
	// here we call showDay to output the day's table
	echo '<td>';

	if(($i >= $startDay) && ($currentDate <= $numDays))
	{
	    showDay($currentDate);
	    $currentDate++;
	}
	else
	{
	    echo '&nbsp;';
	}
	echo '</td>';
    }
    echo '</tr>';
}

function showDay($date)
{
    // variables with global scope
    global $year;
    global $month;
    global $shift;
    global $dbName;

    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed.".$errorMsg);
    mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!".$errorMsg);
    $query =  'SELECT * FROM schedule_'.$year.'_'.$month.'_'.$shift.' WHERE Date='.$date.';';
    $result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query.<br><br>" . mysql_error().'<br><br>'.$errorMsg);
    mysql_close($conn); 
    $row = mysql_fetch_array($result);
    mysql_free_result($result); 
    echo '<table width="100%" class="day" >';
    $popURL="signOn.php?year=".$year."&month=".$month."&date=".$date."&shift=".$shift."&slot=";
    if($row['1ID'] <> null)
    {
    echo '<tr><td align=center class="signon" onclick="popUp('."'".$popURL.'1'."'".')">'.memberString($row['1ID'], $row['1Start'], $row['1End']).'</td></tr>';
    }
    if($row['2ID'] <> null)
    {
    echo '<tr><td align=center class="signon" onclick="popUp('."'".$popURL.'2'."'".')">'.memberString($row['2ID'], $row['2Start'], $row['2End']).'</td></tr>';
    }
    if($row['3ID'] <> null)
    {
    echo '<tr><td align=center class="signon" onclick="popUp('."'".$popURL.'3'."'".')">'.memberString($row['3ID'], $row['3Start'], $row['3End']).'</td></tr>';
    }
    if($row['4ID'] <> null)
    {
    echo '<tr><td align=center class="signon" onclick="popUp('."'".$popURL.'4'."'".')">'.memberString($row['4ID'], $row['4Start'], $row['4End']).'</td></tr>';
    }
    if($row['5ID'] <> null)
    {
    echo '<tr><td align=center class="signon" onclick="popUp('."'".$popURL.'5'."'".')">'.memberString($row['5ID'], $row['5Start'], $row['5End']).'</td></tr>';
    }
    if($row['6ID'] <> null)
    {
    echo '<tr><td align=center class="signon" onclick="popUp('."'".$popURL.'6'."'".')">'.memberString($row['6ID'], $row['6Start'], $row['6End']).'</td></tr>';
    }

    // SIGNIN CHECK BOX
    echo '<tr><td align=center class="nocurs">Sign On <input type="checkbox" name="'.$date.'"></td></tr>';
    echo '</table>';
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
	$string = '<font color="blue">'.$string.'</font>';
    }
    return $string;
    }
}

function parseTime($startTime, $endTime)
{
    // this determines the time string to be added to the member string
    global $shift;
    global $showTimeCompleteShift;
    global $schedTimeFormat;
    if(! $showTimeCompleteShift)
    {
	if(($shift=='day') && ($startTime=='06:00:00') && ($endTime=='18:00:00'))
	{
	    return '';
	}
	elseif(($shift=='night') && ($startTime=='18:00:00') && ($endTime=='06:00:00'))
	{
	    return '';
	}
    }

//time format displayed after names/IDs on schedule
// 1: name 6-18
// 2: name 0600-1800
// 3: name 06:00-18:00
// default is 2
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

function dayNames()
{
    echo '<tr><b><td align=center width="14%">Sunday</td><td align=center width="14%">Monday</td><td align=center width="14%">Tuesday</td><td align=center width="14%">Wednesday</td><td align=center width="14%">Thursday</td><td align=center width="14%">Friday</td><td align=center width="14%">Saturday</td></b></tr>';
    
}

?>
<br>
<p><b>ID #: </b><input size="10" maxlength="5" name="EMTid" type="text" /> <b>Start:</b> <select name="start">
<?php
if($shift=='day')
{
    echo '<option value="06:00:00" selected="selected">06:00:00</option>';
    echo '<option value="07:00:00">07:00:00</option>';
    echo '<option value="08:00:00">08:00:00</option>';
    echo '<option value="09:00:00">09:00:00</option>';
    echo '<option value="10:00:00">10:00:00</option>';
    echo '<option value="11:00:00">11:00:00</option>';
    echo '<option value="12:00:00">12:00:00</option>';
    echo '<option value="13:00:00">13:00:00</option>';
    echo '<option value="14:00:00">14:00:00</option>';
    echo '<option value="15:00:00">15:00:00</option>';
    echo '<option value="16:00:00">16:00:00</option>';
    echo '<option value="17:00:00">17:00:00</option>';
}
else
{
    //night
    echo '<option value="18:00:00" selected="selected">18:00:00</option>';
    echo '<option value="19:00:00">19:00:00</option>';
    echo '<option value="20:00:00">20:00:00</option>';
    echo '<option value="21:00:00">21:00:00</option>';
    echo '<option value="22:00:00">22:00:00</option>';
    echo '<option value="23:00:00">23:00:00</option>';
    echo '<option value="00:00:00">00:00:00</option>';
    echo '<option value="01:00:00">01:00:00</option>';
    echo '<option value="02:00:00">02:00:00</option>';
    echo '<option value="03:00:00">03:00:00</option>';
    echo '<option value="04:00:00">04:00:00</option>';
    echo '<option value="05:00:00">05:00:00</option>';
}
?>

</select>
<b> End: </b>
<select name="end">
<?php
if($shift=='day')
{
    echo '<option value="07:00:00">07:00:00</option>';
    echo '<option value="08:00:00">08:00:00</option>';
    echo '<option value="09:00:00">09:00:00</option>';
    echo '<option value="10:00:00">10:00:00</option>';
    echo '<option value="11:00:00">11:00:00</option>';
    echo '<option value="12:00:00">12:00:00</option>';
    echo '<option value="13:00:00">13:00:00</option>';
    echo '<option value="14:00:00">14:00:00</option>';
    echo '<option value="15:00:00">15:00:00</option>';
    echo '<option value="16:00:00">16:00:00</option>';
    echo '<option value="17:00:00">17:00:00</option>';
    echo '<option value="18:00:00" selected="selected">18:00:00</option>';
}
else
{
    //night
    echo '<option value="19:00:00">19:00:00</option>';
    echo '<option value="20:00:00">20:00:00</option>';
    echo '<option value="21:00:00">21:00:00</option>';
    echo '<option value="22:00:00">22:00:00</option>';
    echo '<option value="23:00:00">23:00:00</option>';
    echo '<option value="00:00:00">00:00:00</option>';
    echo '<option value="01:00:00">01:00:00</option>';
    echo '<option value="02:00:00">02:00:00</option>';
    echo '<option value="03:00:00">03:00:00</option>';
    echo '<option value="04:00:00">04:00:00</option>';
    echo '<option value="05:00:00">05:00:00</option>';
    echo '<option value="06:00:00" selected="selected">06:00:00</option>';
}
?>
</select>
</p>
<p>
<input type="submit" />
<input type="reset" />
</p>
</form>
<a name="bottom"></a>
</body>
</html>