<?php 
// inc/notify.php
//
// Handles email/SMS notification for schedule changes.
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
//	$Id$

require_once('./config/config.php'); // main configuration

require_once('./config/scheduleConfig.php'); // schedule configuration

function schedule_edit_mail($year, $month, $date, $shift, $EMTid, $start, $end)
{
    global $sched_emailOnEdit;
    global $sched_smsOnEdit;
    global $dbName;
    global $mailfrom;

    //format time times for humans to understand them
    $stA = explode(":", $start);
    $start = $stA[0].":".$stA[1];
    $endA = explode(":", $end);
    $end = $endA[0].":".$endA[1];

    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.".$errorMsg);
    mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!".$errorMsg);

    // get the members on duty
    $query = 'SELECT * FROM schedule_'.$year.'_'.$month.'_'.$shift.' WHERE date='.$date.';';
    $result = mysql_query($query) or die ("Query Error in query: ".$query);
    $row = mysql_fetch_array($result);

    $crew = array(); // array of other members in crew

    if($row['1ID'] <> null && $row['1ID'] <> $EMTid)
    {
	$crew[] = $row['1ID'];
    }
    if($row['2ID'] <> null && $row['2ID'] <> $EMTid)
    {
	$crew[] = $row['2ID'];
    }
    if($row['3ID'] <> null && $row['3ID'] <> $EMTid)
    {
	$crew[] = $row['3ID'];
    }
    if($row['4ID'] <> null && $row['4ID'] <> $EMTid)
    {
	$crew[] = $row['4ID'];
    }
    if($row['5ID'] <> null && $row['5ID'] <> $EMTid)
    {
	$crew[] = $row['5ID'];
    }
    if($row['6ID'] <> null && $row['6ID'] <> $EMTid)
    {
	$crew[] = $row['6ID'];
    }

    global $shortName;

    if($sched_emailOnEdit)
    {
	// setup the message
	$subject = $shortName." shift change for ".$month."/".$date."/".$year." ".$shift;
	$message = "ATTENTION: \n";
	$message .= "This is an automated message generated by PHP EMS Tools for ".$shortName."\n";
	$message .= "\n";
	$message .= "Please be advised that ID# ".$EMTid." has signed on the schedule as follows:\n";
	$message .= $month."/".$date."/".$year."\n";
	$message .= "ON duty from ".$start." to ".$end."\n";
	$message .= "Thank You.";

	// email the other crew members
	foreach ($crew as $id)
	{
	        $query = 'SELECT EMTid,Email FROM roster WHERE EMTid="'.$id.'";';
		$result = mysql_query($query) or die ("Query Error in query:".$query);
		$row = mysql_fetch_array($result);
		if($row['Email'] <> null)
		{
		    // send the email
		    mail($row['Email'], $subject, $message, "From: ".$mailfrom);
		}
	}
    }
    if($sched_smsOnEdit)
    {
	// sms email the other crew members
	// setup the message
	$subject = $shortName." schedule - ID ".$EMTid." signed on ".$month."/".$date."/".$year." ".$shift." from ".$start." to ".$end;
	$message = " ";

	// email the other crew members
	foreach ($crew as $id)
	{
	        $query = 'SELECT EMTid,textEmail FROM roster WHERE EMTid="'.$id.'";';
		$result = mysql_query($query) or die ("Query Error in query:".$query);
		$row = mysql_fetch_array($result);
		if($row['textEmail'] <> null)
		{
		    // send the email
		    mail($row['textEmail'], $subject, $message, "From: ".$mailfrom);

		}
	}
    }
}

function schedule_remove_mail($year, $month, $date, $shift, $EMTid)
{
    global $sched_emailOnRemove;
    global $sched_smsOnRemove;
    global $dbName;
    global $mailfrom;

    //format time times for humans to understand them
    $stA = explode(":", $start);
    $start = $stA[0].":".$stA[1];
    $endA = explode(":", $end);
    $end = $endA[0].":".$endA[1];

    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.".$errorMsg);
    mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!".$errorMsg);

    // get the members on duty
    $query = 'SELECT * FROM schedule_'.$year.'_'.$month.'_'.$shift.' WHERE date='.$date.';';
    $result = mysql_query($query) or die ("Query Error in query: ".$query);
    $row = mysql_fetch_array($result);

    $crew = array(); // array of other members in crew

    if($row['1ID'] <> null && $row['1ID'] <> $EMTid)
    {
	$crew[] = $row['1ID'];
    }
    if($row['2ID'] <> null && $row['2ID'] <> $EMTid)
    {
	$crew[] = $row['2ID'];
    }
    if($row['3ID'] <> null && $row['3ID'] <> $EMTid)
    {
	$crew[] = $row['3ID'];
    }
    if($row['4ID'] <> null && $row['4ID'] <> $EMTid)
    {
	$crew[] = $row['4ID'];
    }
    if($row['5ID'] <> null && $row['5ID'] <> $EMTid)
    {
	$crew[] = $row['5ID'];
    }
    if($row['6ID'] <> null && $row['6ID'] <> $EMTid)
    {
	$crew[] = $row['6ID'];
    }

    global $shortName;

    if($sched_emailOnRemove)
    {
	// setup the message
	$subject = $shortName." shift change for ".$month."/".$date."/".$year." ".$shift;
	$message = "ATTENTION: \n";
	$message .= "This is an automated message generated by PHP EMS Tools for ".$shortName."\n";
	$message .= "\n";
	$message .= "Please be advised that ID# ".$EMTid." IS NO LONGER signed on the schedule on:\n";
	$message .= $month."/".$date."/".$year."\n";
	$message .= "Thank You.";

	// email the other crew members
	foreach ($crew as $id)
	{
	        $query = 'SELECT EMTid,Email FROM roster WHERE EMTid="'.$id.'";';
		$result = mysql_query($query) or die ("Query Error in query:".$query);
		$row = mysql_fetch_array($result);
		if($row['Email'] <> null)
		{
		    // send the email
		    mail($row['Email'], $subject, $message, "From: ".$mailfrom);
		}
	}
    }
    if($sched_smsOnRemove)
    {
	// sms email the other crew members
	// setup the message
	$subject = $shortName." schedule - ID ".$EMTid." signed OFF ".$month."/".$date."/".$year;
	$message = " ";

	// email the other crew members
	foreach ($crew as $id)
	{
	        $query = 'SELECT EMTid,textEmail FROM roster WHERE EMTid="'.$id.'";';
		$result = mysql_query($query) or die ("Query Error in query:".$query);
		$row = mysql_fetch_array($result);
		if($row['textEmail'] <> null)
		{
		    // send the email
		    mail($row['textEmail'], $subject, $message, "From: ".$mailfrom);
		}
	}
    }


}

?>