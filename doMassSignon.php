<?php
// doMassSignon.php
//
// Script to handle the output from the massSignon page, and put it in the DB.
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
//      $Id: doMassSignon.php 101 2008-07-01 01:34:35Z jantman $
?>
<html>
<!-- Time-stamp: "2007-09-13 18:16:49 jantman" -->
<!-- php-ems-tools index -->
<head>

<?php
require_once('./config/config.php'); // main configuration

require_once('./config/scheduleConfig.php'); // schedule configuration

echo '<title>Schedule Mass Signon Results</title>';
echo '<link rel="stylesheet" href="'.$serverWebRoot.'php_ems.css" type="text/css">'; // the location of the CSS file for the schedule
//figure out the month and year
?>

</head>
<body>
<?php

// import values
require_once('./inc/global.php');
global $dbName;

// get the preliminary stuff
$year = $_REQUEST['year'];
$month = $_REQUEST['month'];
$shift= $_REQUEST['shift'];
$EMTid = $_REQUEST['EMTid'];
$start = $_REQUEST['start'];
$end = $_REQUEST['end'];

if($shift == 'day')
{
    $shiftName = 'Days';
}
else
{
    $shiftName = "Nights";
}

echo '<p align="center"><h3>Schedule Mass Signon For: '.GetMonthString($month).' '.$year.' '.$shiftName.'</h3></p>';
echo '<p align="center"><h3>Results:</h3></p>';

// check validity of ID
if(! canPullDuty($EMTid))
{
    die("I'm sorry, but the specified EMT ID cannot sign on for duty.");
}

global $dbName;
$conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.".$errorMsg);
mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!".$errorMsg);

for($i = 1; $i <= $_REQUEST['numDays']; $i++)
{
    $day = true;
    if($_REQUEST[$i] == 'on') // checked
    {
	if(($month == date("m")) && ($year == date("Y")) && ($i < date('d')))
	{
	    echo "<b>Cannot sign on for ".$month."/".$i."/".$year." - date has already passed.</b><br>";
	    $day = false;
	}   
	// find an empty slot
	$query = 'SELECT * FROM schedule_'.$year.'_'.$month.'_'.$shift.' WHERE Date='.$i.';';
	$result = mysql_query($query) or die ("Auth Query Error");
	$row = mysql_fetch_array($result);
	$slot = 0;
	$signedOn = false;
	for($c = 1; $c < 7; $c++)
	{
	    if($row[$c.'ID'] == null && $slot == 0)
	    {
		$slot = $c;
	    }
	    if($row[$c.'ID'] == $EMTid)
	    {
		$signedOn = true;
	    }
	}
	$fail = false;
	if($slot == 0)
	{
	    // no empty slots
	    echo "<b>Cannot sign on for ".$month."/".$i."/".$year." - there are no empty slots left.</b><br>";
	    $fail = true;
	}
	if($signedOn)
	{
	    // already signed on for this shift
	    echo "<b>Cannot sign on for ".$month."/".$i."/".$year." - you are already signed on for this shift.</b><br>";
	    $fail = true;
	}

	// update record for this member
	$query = 'UPDATE schedule_'.$year.'_'.$month.'_'.$shift.' SET '.$slot.'Start="'.$start.'",'.$slot.'End="'.$end.'",'.$slot.'ID="'.$EMTid.'" WHERE Date='.$i.';';
	if(! $fail)
	{
	    mysql_query($query);
	    $chQuery =  'CREATE TABLE IF NOT EXISTS schedule_'.$year.'_'.$month.'_change LIKE schedule_change_template;';
	    mysql_query($chQuery) or die ("Query Error".mysql_error());
            $address = $_SERVER['REMOTE_ADDR'];
            $host = gethostbyaddr($address);
            $chQuery = 'INSERT INTO schedule_'.$year.'_'.$month.'_change SET timestamp='.time().',EMTid="'.$adminID.'",query="'.make_safe($query).'",host="'.$host.'",address="'.$address.'",form="mass sign on";';
            mysql_query($chQuery) or die ("Query Error".mysql_error()." in query ".$chQuery);
	}

	// issue a confirmation message
	$query = 'SELECT * FROM schedule_'.$year.'_'.$month.'_'.$shift.' WHERE Date='.$i.';';
	$result = mysql_query($query) or die ("Auth Query Error");
	$row = mysql_fetch_array($result);
	if($row[$slot.'ID'] == $EMTid && $slot <> 0 && !$signedOn)
	{
	    echo 'Successfully signed on ID# '.$row[$slot.'ID'].' for '.$month.'/'.$i.'/'.$year.' from '.$row[$slot.'Start'].'-'.$row[$slot.'End'].'<br>';
	}
	elseif($slot == 0)
	{
	    // suppress generic error
	}
	elseif($signedOn)
	{
	    // suppress generic error
	}
	else
	{
	    echo '<b><u>Singon failed for unknown reasons. Please check the above messages and contact the administrator.</u></b><br>';
	}

	mysql_free_result($result);
    }
    //echo $i.' '.$_REQUEST[$i].'<br>';
}

echo '<br><br>';
echo '<a href="schedule.php?year='.$year.'&month='.$month.'&shift='.$shift.'>View Schedule</a><br>';


global $action;
/*
global $minRightsEdit;
global $minRightsChangePast;
$auth = schedAuth();

       global $requireAuthToSignOn;
       global $requireAuthToEdit;
       global $requireAuthToRemove;
       global $requireAuthToChangePast;

    if($requireAuthToSignOn && ($action=="signOn") && (! $auth))
    {
	die("Either your ID/password is incorrect or you are not authorized to perform this action.");
    }
    if($requireAuthToEdit && ($action=="edit") && ((! $auth) || ($rightsLevel < $minRightsEdit)))
    {
	die("Either your ID/password is incorrect or you are not authorized to perform this action.");
    }
    if($requireAuthToRemove && ($action=="remove") && ((! $auth) || ($rightsLevel < $minRightsEdit)))
    {
	die("Either your ID/password is incorrect or you are not authorized to perform this action.");
    }
    if($requireAuthToChangePast && ((! $auth) || ($rightsLevel < $minRightsChangePast)))
    {
	$changeDay = strtotime($year."-".$month."-".$date);
	$difference = time() - $changeDay;
	if($shift=="night")
	{
	    global $nightLastHour;
	    $lastHr = $nightLastHour;
	}
	else
	{
	    global $dayLastHour;
	    $lastHr = $dayLastHour;
	}
	if((date("Y-m") == $year."-".$month) && (date("j")==($date+1)) && date("G")<$lastHr)
	{
	    $sameShift = true;
	}
	if($difference > 86400 && (! $sameShift))
	{
	    //we are more than a day in the past; fail.
	    die("Either your ID/password is incorrect or you are not authorized to perform this action.");
	}
    }
*/




	$query = 'UPDATE schedule_'.$year.'_'.$month.'_'.$shift.' SET '.$slot.'ID="'.$EMTid.'",'.$slot.'Start="'.$start.'",'.$slot.'End="'.$end.'" WHERE date='.$date.';';
	schedule_edit_mail($year, $month, $date, $shift, $EMTid, $start, $end); // for edit and add

    
    $result = mysql_query($query) or die ("Query Error");

function validateTimes($start, $end)
{
    //let's validate the times
    if($shift=="day")
    {
	if(substr($formItems['start'],0,2) > substr($formItems['end'],0,2))
	{
	    $failed = true;
	}
    }
    if($shift=="night")
    {
	$sS = substr($formItems['start'],0,2);
	$eS = substr($formItems['end'],0,2);
	if($sS < 24 && $sS > 17 && $eS < 24 && $eS > 17) // both between 18-23
	{
	    if($sS > $eS)
	    {
		$failed = true;
	    }
	}
	if($sS < 7 && $eS < 7) // both between 0-6
	{
	    if($sS > $eS)
	    {
		$failed = true;
	    }
	}
	if($eS >= 0 && $eS < 7 && $sS > 17 && $sS < 24) // end 0-6 start 18-23
	{
	    $failed = false;
	}
	if($sS >=0 && $sS < 7 && $eS > 17 && $eS < 24) // start 0-6 end 18-23
	{
	    $failed = true;
	}
	return $failed;
    }
}

?>