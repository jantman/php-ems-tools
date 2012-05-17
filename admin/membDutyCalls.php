<html>
<head>
<meta http-equiv="refresh" content="180">

<?php

//
// membDutyCalls.php
//
// tool to view calls missed by members on duty
// should be accessible by admins only
//
// Version 1.0 as of Time-stamp: "2007-02-05 00:19:18 jantman"
//
// This file is part of the php-ems-tools package
// available at http://www.php-ems-tools.com 
//
// (C) 2006 Jason Antman.
// This package is licensed under the terms of the
// GNU General Public License (GPL)
//

// 
// DO NOT MAKE CHANGES
// UNLESS YOU KNOW WHAT YOU ARE DOING.
// 
require('../custom.php');

global $dbName;

if(! empty($_GET['year']))
{
    $year = $_GET['year'];
}
else
{
    $year = date("Y");
}

if(! empty($_GET['month']))
{
    $month = $_GET['month'];
}
else
{
    $month = date("m");
}

if(! empty($_GET['EMTid']))
{
    $EMTid = $_GET['EMTid'];
}
// DB Connection
global $shortName;
global $serverWebRoot;
echo "<title>".$shortName." Check Member Duty Calls</title></head><body>";
echo "<h3>".$shortName." Check Member Duty Calls as of ".date("Y-m-d H:i:s")." for ".$EMTid." schedule ".$year."-".$month."</h3>";
echo '<link rel="stylesheet" href="'.$serverWebRoot.'php_ems.css" type="text/css">';


$conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
mysql_select_db($dbName) or die ('Unable to select database!');

$query = "SELECT RunNumber,Date,TimeDisp,DriverToScene,DriverToBldg,DriverToHosp,crew1,crew2,crew3,crew4,crew5,crew6,UNIX_TIMESTAMP(CONCAT(Date, ' ', TimeDisp)) AS ts FROM calls WHERE YEAR(Date)=".$year." AND MONTH(Date)=".$month.";";
$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());

// iterate over all calls for the month
while($row = mysql_fetch_assoc($result))
{
    echo '<p>';
    echo "RunNumber=".$row['RunNumber']." - ".$row['Date']." ".$row['TimeDisp']." ";
    $query2 = "SELECT sched_entry_id,EMTid,start_ts,end_ts FROM schedule WHERE deprecated=0 AND start_ts <= ".($row['ts'] - 900)." AND end_ts >= ".($row['ts'] + 900)." AND EMTid='".mysql_real_escape_string($EMTid)."';";
    $result2 = mysql_query($query2) or die ("Error in query: $query2. " . mysql_error());
    if(mysql_num_rows($result2) < 1)
    {
	echo "NOT ON DUTY.";
    }
    else
    {
	$row2 = mysql_fetch_assoc($result2);
	if($row['DriverToScene'] != $EMTid && $row['DriverToBldg'] != $EMTid && $row['DriverToHosp'] != $EMTid && $row['crew1'] != $EMTid && $row['crew2'] != $EMTid && $row['crew3'] != $EMTid && $row['crew4'] != $EMTid && $row['crew5'] != $EMTid && $row['crew6'] != $EMTid)
	{
	    echo "<strong>ON DUTY BUT NOT ON CALL.</strong>";
	    echo " Duty ".date("Y-m-d H:i:s", $row2['start_ts'])." to ".date("Y-m-d H:i:s", $row2['end_ts'])." (".$row2['sched_entry_id'].")";
	}
	else
	{
	    echo '<strong>Took Call.</strong>';
	}
    }
    echo '</p>';
}

/*
$query = "SELECT RunNumber,Date,TimeDisp,DriverToScene,DriverToBldg,DriverToHosp,crew1,crew2,crew3,crew4,crew5,crew6 FROM calls WHERE YEAR(Date)=".((int)$year)." AND MONTH(Date)=".((int)$month).";";

$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());

while($row = mysql_fetch_assoc($result))
{
    $ts = strtotime($row['Date']." ".$row['TimeDisp']);
    echo '<p>';
    echo $row['RunNumber']."         ";
    echo "Date=".$row['Date']." Time=".$row['TimeDisp'];
    echo " TS=".$ts." ===== ".date("Y-m-d H:i:s", $ts);
    echo '</p>';
}
*/
mysql_free_result($result);
mysql_close($conn);


?>
</body>
</html>