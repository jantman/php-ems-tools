<?php
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
//      $Id: roster.php,v 1.5 2007/09/20 00:00:40 jantman Exp $

require_once('../config/config.php'); // main configuration
require_once('../config/rosterConfig.php'); // roster configuration
require_once('../config/scheduleConfig.php'); // roster configuration

if(isset($_GET['year'])){ $year = (int)$_GET['year'];} else { $year = (int)date("Y");}

//CONNECT TO THE DB
$connection = mysql_connect() or die ("I'm sorry, but I was unable to connect! (MySQL error: unable to connect).".$errorMsg);
mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!".$errorMsg);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php echo '<title>'.$shortName.' - Create/Edit '.$year.' Saturday Schedule</title>'; ?>
<link rel="stylesheet" href="css/saturdaySchedule.css" type="text/css">
<script type="text/javascript" src="../php-ems-tools.js"> </script>
</head>

<body>
<?php
echo '<h1 style="text-align: center;">'.$shortName.' - Create/Edit '.$year.' Saturday Schedule</h1>'."\n";
echo '<p style="text-align: center;">Go to: <a href="saturdaySchedule.php?year='.($year-1).'">'.($year-1).'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="saturdaySchedule.php?year='.($year+1).'">'.($year+1).'</a></p>'."\n";

if(isset($_POST['year']))
{
    processForm();
}
else
{
    showForm($year);
}

?>

</body>

</html>

<?php

function processForm()
{
    ksort($_POST);

    $year = (int)$_POST['year'];

    echo "<p><strong>Updaing $year saturday night schedule...</strong></p>\n";

    echo '<pre>';
    foreach($_POST as $key => $val)
    {
	if($val == -1){ continue;}
	if($key == "year"){ continue;}
	$parts = explode("_", $key);
	$date = (int)$parts[1];
	if($parts[0] == "senior")
	{
	    $foo = addMemberToSchedule($date, $val, 2);
	    if($foo != ""){ echo $foo."\n";}
	}
	elseif($parts[0] == "probie")
	{
	    $foo = addMemberToSchedule($date, $val, 2, true);
	    if($foo != ""){ echo $foo."\n";}
	}
    }
    echo '</pre>';


    echo '<pre>';
    echo var_dump($_POST);
    echo '</pre>';
}

// adds a member to the schedule on a given date
// returns true or an error message
function addMemberToSchedule($ts, $EMTid, $shift_id, $is_probie = false)
{
    if($is_probie)
    {
	if(getProbieIdByDate($ts) != "")
	{
	    return "\tERROR: There is already a probie signed on this shift. Not changing. (".date("Y-m-d H:i:s", $ts).", EMTid $EMTid)";
	}
    }
    else
    {
	$ids = getSeniorIdsByDate($ts);
	if(array_key_exists($EMTid, $ids))
	{
	    return "\tERROR: This member already signed on during this shift. Not adding. (".date("Y-m-d H:i:s", $ts).", EMTid $EMTid)";
	}
    }

    $query = "INSERT INTO schedule SET EMTid='".mysql_real_escape_string($EMTid)."',";
    $query .= "start_ts=".$ts.",end_ts=".($ts+43200).",deprecated=0,sched_shift_id=$shift_id,";
    $query .= "sched_year=".date("Y", $ts).",";
    $query .= "sched_month=".date("m", $ts).",";
    $query .= "sched_date=".date("d", $ts).";";
    //echo $query."\n";

    echo "Adding ";
    if($is_probie){ echo "probie";} else { echo "member";}
    echo " $EMTid to schedule on ".date("Y-m-d", $ts)."...";

    $result = mysql_query($query);
    if($result)
    {
	echo "DONE. (sched_entry_id=".mysql_insert_id().").\n";
	return "";
    }
    else
    {
	return "\n\tERROR: ".mysql_error();
    }
}

function showForm($year)
{
    echo '<form name="satSchedule" method="post">'."\n";
    echo '<input type="hidden" name="year" value="'.$year.'" />'."\n";

    $seniors = getSeniorMembers(array(-1 => "--------")); // get an array of all Senior members
    $probies = getProbies(array(-1 => "--------")); // get an array of all Probies

    // figure out the first Saturday of the year
    $date = strtotime($year."-01-01 18:00:00");
    if($year == "2011"){ $date = strtotime($year."-07-17 18:00:00"); }
    while(date("w", $date) != 6)
    {
	$date += 86400;
    }

    // figure out the next saturday after a given date
    if(isset($_GET['start']))
    {
	$date = (int)$_GET['start'];
	$date = strtotime(date("Y-m-d", $date)." 18:00:00");
	while(date("w", $date) != 6)
	{
	    $date += 86400;
	}
    }

    // loop through all saturdays of the year
    while(date("Y", $date) == $year)
    {
	$message = getDailyMessage($date);
	
	echo '<div class="day">';
	echo '<!-- '.$date.' = '.date("D M j, Y H:i:s", $date).'-->';
	echo '<div>'."\n";
	echo '<strong>'.date("F j, Y (D)", $date).'</strong>';
	if($message != ""){ echo ' <em>('.$message.')</em>';}
	echo '</div>'."\n";

	echo '<div class="daySelect">Senior Members: ';
	echo makeSeniorSelects($date, $seniors);
	echo '</div>'."\n";

	echo '<div class="daySelect">Probie: ';
	$bar = getProbieIdByDate($date);
	echo makeSelect("probie_$date", $probies, $bar);
	echo '</div>'."\n";
	$date += 604800;
    }

    echo '<div><input type="submit" value="Submit" /></div>';
    echo '</form>';

    /* DEBUG
    echo '<pre>';
    echo var_dump($seniors);
    echo '</pre>';
    echo '<pre>';
    echo var_dump($probies);
    echo '</pre>';
    */
    // END DEBUG

}

function makeSeniorSelects($ts, $seniors)
{
    $mySeniors = getSeniorIdsByDate($ts);

    // DEBUG
    /*
    if(date("Y-m-d", $ts) == "2009-01-10")
    {
	echo '<pre>';
	echo var_dump($mySeniors);
	echo '</pre>';
    }
    */
    // DEBUG

    $s = "";

    $count = 0;
    foreach($mySeniors as $id => $time)
    {
	if($time != "")
	{
	    $s .= $id." - ".$seniors[$id]." <em>($time)</em>, ";
	}
	else
	{
	    $s .= makeSelect("senior_".$ts."_$count", $seniors, $id).", ";
	}
	$count++;
    }

    for($i = $count; $i < 4; $i++)
    {
	$s .= makeSelect("senior_".$ts."_".$count, $seniors).", ";
	$count++;
    }

    return $s;
}

// get the daily message for a given timestamp (shift start)
function getDailyMessage($ts)
{
    $query = "SELECT * FROM schedule_dailyMessage WHERE sched_year=".date("Y", $ts)." AND sched_month=".date("m", $ts)." AND sched_date=".date("d", $ts)." AND deprecated=0;";
    $result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query: ".$query."<br><br>" . mysql_error().'<br><br>'.$errorMsg);
    if(mysql_num_rows($result) < 1){ return "";}

    $row = mysql_fetch_assoc($result);
    return $row['message_text'];
}

// get an array of all Senior members
function getSeniorMembers($foo = array())
{
    $query = "SELECT EMTid,LastName,FirstName FROM roster WHERE status='Senior' ORDER BY lpad(EMTid,10,'0');";
    $result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query: ".$query."<br><br>" . mysql_error().'<br><br>'.$errorMsg);
    while($row = mysql_fetch_assoc($result))
    {
	$foo[$row['EMTid']] = $row['FirstName'].' '.$row['LastName'];
    }
    return $foo;
}

// get an array of all Probies
function getProbies($foo = array())
{
    $query = "SELECT EMTid,LastName,FirstName FROM roster WHERE status='Probie' ORDER BY lpad(EMTid,10,'0');";
    $result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query: ".$query."<br><br>" . mysql_error().'<br><br>'.$errorMsg);
    while($row = mysql_fetch_assoc($result))
    {
	$foo[$row['EMTid']] = $row['FirstName'].' '.$row['LastName'];
    }
    return $foo;
}

// get the probie signed on duty for a given shift
function getProbieIdByDate($ts)
{
    $query = "SELECT s.*,r.LastName,r.FirstName,r.shownAs,r.status FROM schedule AS s LEFT JOIN roster AS r ON s.EMTid=r.EMTid WHERE sched_year=".date("Y", $ts)." AND sched_month=".date("m", $ts)." AND sched_date=".date("d", $ts)." AND sched_shift_id=2 AND deprecated=0 AND r.status='Probie' ORDER BY start_ts ASC;";
    $result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query: ".$query."<br><br>" . mysql_error().'<br><br>'.$errorMsg);
    if(mysql_num_rows($result) < 1){ return "";}

    $row = mysql_fetch_assoc($result);
    return $row['EMTid'];
}

// get an array of all senior members on duty for a given shift
// array like EMTid=>timeString
// timeString is "" if they're on 1800-0600 or else the times they're on
function getSeniorIdsByDate($ts)
{
    global $nightFirstHour, $dayFirstHour;
    $foo = array();
    $query = "SELECT s.*,r.LastName,r.FirstName,r.shownAs,r.status FROM schedule AS s LEFT JOIN roster AS r ON s.EMTid=r.EMTid WHERE sched_year=".date("Y", $ts)." AND sched_month=".date("m", $ts)." AND sched_date=".date("d", $ts)." AND sched_shift_id=2 AND deprecated=0 AND r.status='Senior' ORDER BY start_ts ASC;";

    $result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query: ".$query."<br><br>" . mysql_error().'<br><br>'.$errorMsg);
    if(mysql_num_rows($result) < 1){ return $foo;}

    while($row = mysql_fetch_assoc($result))
    {
	if(date("H:i", $row['start_ts']) == ($nightFirstHour.":00") && date("H:i", $row['end_ts']) == ($dayFirstHour.":00"))
	{
	    // person is on duty the whole shift
	    $foo[$row['EMTid']] = "";
	}
	else
	{
	    $foo[$row['EMTid']] = date("H:i", $row['start_ts'])." - ".date("H:i", $row['end_ts']);
	}
    }
    return $foo;
}

// make a SELECT input element, with a possible default value
function makeSelect($id, $array, $default = null)
{
    $s = "<select id=\"$id\" name=\"$id\">";
    foreach($array as $key => $val)
    {
	$s .= "<option value=\"$key\"";
	if($key == $default){ $s .= ' selected="selected"';}
	if($key == -1)
	{
	    $s .= ">$val</option>";
	}
	else
	{
	    $s .= ">$key - $val</option>";
	}
    }
    $s .= "</select>";
    return $s;
}

?>