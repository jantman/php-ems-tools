<?php
// admin/migrate_schedule_v3_to_v4.php
//
// This script migrates your schedule from the multiple tables used in v3 to the single table in v4.
//
// +----------------------------------------------------------------------+
// | PHP EMS Tools	http://www.php-ems-tools.com                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006, 2007, 2008 Jason Antman.	                          |
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
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/admin/migrate_#$ |
// +----------------------------------------------------------------------+

// DEBUG
$dbName = "php_ems_tools_devel";

fwrite(STDOUT, "Are you sure you want to copy your v3 schedule tables to a v4 schedule table? [y/n]\n");
$ans = trim(fgets(STDIN));
if($ans != "y" && $ans != "Y")
{
    fwrite(STDOUT, "Exiting....\n");
    exit();
}

fwrite(STDOUT, "Enter table name for new schedule: [newschedule]\n");
$tblName = trim(fgets(STDIN));
if($tblName == ""){ $tblName = "newschedule";}

// connect and select DB
$conn = mysql_connect() or die("error making connection");
mysql_select_db($dbName) or die("error selecting DB");

// get the shift_id's
$shiftIDs = array();
$query = "SELECT sched_shift_id,shiftName FROM schedule_shifts;";
$result = mysql_query($query) or die("error in query: ".$query."\nERROR:".mysql_error()."\n");
// loop through the rows in the table
while ($row = mysql_fetch_array($result))
{
    $shiftByID[strtolower($row['shiftName'])] = $row['sched_shift_id'];
}

// get a list of old schedule tables
$tablesToProcess = array();
$result = mysql_list_tables($dbName);
while ($row = mysql_fetch_array($result, MYSQL_NUM))
{
    $rowName = $row[0];
    if(substr($rowName, 0, 9) != "schedule_") { continue; } // ignore anything that's not schedule_
    $rowNameArray = explode("_", $rowName);
    if(count($rowNameArray) != 4) { continue; } // ignore anything that's not schedule_YYYY_MM_shift
    if($rowNameArray[3] == "change") { continue; } // ignore anything that *_change
    echo $rowName."\n";
    $tablesToProcess[] = $rowName;
}

// loop through the tables
foreach($tablesToProcess as $tableName)
{
    $tableNameArray = explode("_", $tableName);
    $year = $tableNameArray[1];
    $month = $tableNameArray[2];
    $shift = $tableNameArray[3];

    //echo "tblName=".$tblName." year=".$year." month=".$month." shift=".$shift."\n";

    $query = "SELECT * FROM ".$tableName.";";
    $result = mysql_query($query) or die("error in query: ".$query."\nERROR:".mysql_error()."\n");
    // loop through the rows in the table
    while ($row = mysql_fetch_array($result))
    {
	// loop through members in this row
	for($i = 1; $i < 7; $i++)
	{
	    if(! array_key_exists(($i."ID"), $row))
	    {
		echo "NO COLUMN ".$i."ID. Result:\n";
		echo var_dump($row);
		echo "\nQuery: ".$query."\nNum Rows:".mysql_num_rows($result)."\n";
		exit();
	    }
	    if($row[$i."ID"] != null)
	    {
		// add signon to new table
		addSignon($row[$i.'ID'], $year, $month, $shift, $row['Date'], $row[$i.'Start'], $row[$i.'End']);
	    }
	}
    }

}

// add the signon to the new schedule table
function addSignon($ID, $sched_year, $sched_month, $sched_shift, $sched_date, $startTime, $endTime)
{
    global $tblName;
    global $shiftByID;
    
    $shift_id = $shiftByID[strtolower($sched_shift)];

    // figure out the start and end timestamps
    if(strtolower($sched_shift) == "night")
    {
	// figure out the weird timestamp things with the night shift	
	$shour = explode(":", $startTime); $shour = $shour[0];
	$ehour = explode(":", $endTime); $ehour = $ehour[0];
	if($shour < 7)
	{
	    // we're in the AM of the calendar date after the schedule date
	    $temp = strtotime($sched_year."-".$sched_month."-".$sched_date);
	    $temp = $temp + 86400; // calendar date is the next day
	    $start_ts = strtotime(date("Y-m-d", $temp)." ".$startTime);
	}
	else
	{
	    $start_ts = strtotime($sched_year."-".$sched_month."-".$sched_date." ".$startTime);
	}
	if($ehour < 7)
	{
	    // we're in the AM of the calendar date after the schedule date
	    $temp = strtotime($sched_year."-".$sched_month."-".$sched_date);
	    $temp = $temp + 86400; // calendar date is the next day
	    $end_ts = strtotime(date("Y-m-d", $temp)." ".$endTime);
	}
	else
	{
	    $end_ts = strtotime($sched_year."-".$sched_month."-".$sched_date." ".$endTime);
	}
    }
    else
    {
	$start_ts = strtotime($sched_year."-".$sched_month."-".$sched_date." ".$startTime);
	$end_ts = strtotime($sched_year."-".$sched_month."-".$sched_date." ".$endTime);
    }

    $query = "INSERT INTO ".$tblName." SET sched_year=".$sched_year.",sched_month=".$sched_month.",sched_date=".$sched_date.",EMTid='".$ID."',sched_shift_id=".$shift_id.",start_ts=".$start_ts.",end_ts=".$end_ts.";";
    echo $query."\n";
    $result = mysql_query($query) or die("error in query: ".$query."\nERROR:".mysql_error()."\n");
}

?>