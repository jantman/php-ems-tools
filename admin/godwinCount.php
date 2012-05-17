<html>
<head><title>301 Godwin Call Count</title></head>
<body>
<?php

//
// ketshireCount.php
//
// Version 1.0 as of Time-stamp: "2007-06-07 11:50:58 jantman"
//
// This file is part of the php-ems-tools package
// available at 
//
// (C) 2006 Jason Antman.
// This package is licensed under the terms of the
// GNU General Public License (GPL)
//

// generals.php - code to handle general calls

require_once('../custom.php');


    global $dbName;

if(! empty($_GET['year']))
{
    //style monthly or yearly
    $year = $_GET['year'];
}
else
{
    $year = date("Y");
}

    mysql_connect() or die("I'm sorry but I cannot connect to the MySQL server. Error: ".mysql_error());
    mysql_select_db($dbName) or die("I'm sorry but there was an error selecting the DB. Error: ".mysql_error());
$query = "SELECT * FROM calls WHERE YEAR(Date)=".$year.";";

    $result = mysql_query($query) or die("MySQL Query Error: ".mysql_error());

echo '<h3>Midland Park Ambulance Corps - 301 Godwin Ave Calls Count for '.$year.'</h3><br>';
echo '<a href="kentshireCalls.php?year='.($year-1).'">'.($year-1).'</a>&nbsp;&nbsp;&nbsp;<a href="kentshireCalls.php?year='.($year+1).'">'.($year+1).'</a><br>';
echo '<b>Please check that all addresses are correct before using the final count!</b><br>';
	
echo '<table border=1>';
$count = 0;
echo '<tr>';
echo '<td><b>Run #</b></td>';
echo '<td><b>Date</b></td>';
echo '<td><b>Disp. Time</b></td>';
echo '<td><b>Call Type</b></td>';
echo '<td><b>Pt. Address</b></td>';
echo '<td><b>Call Location</b></td>';
echo '</tr>';
while($r = mysql_fetch_array($result))
{
    $add = $r['PtAddress'];
    $loc = $r['CallLoc'];
    if(stristr($loc, "godwin") && stristr($loc, "301"))
    {
	$count++;
	echo '<tr>';
	echo '<td>'.$r['RunNumber'].'</td>';
	echo '<td>'.$r['Date'].'</td>';
	echo '<td>'.$r['TimeDisp'].'</td>';
	echo '<td>'.$r['CallType'].'</td>';
	echo '<td>'.$r['PtAddress'].'</td>';
	if($loc != "Home")
	{
	    echo '<td><b>'.$r['CallLoc'].'</b></td>';
	}
	else
	{
	    echo '<td>'.$r['CallLoc'].'</td>';
	}
	echo '</tr>';
    }
}

echo '</table>';
echo '<p><h3>'.$count.' calls shown above. Please confirm that all are actually to 187 Paterson Ave.</h3></p>';

?>
</body>
</html>