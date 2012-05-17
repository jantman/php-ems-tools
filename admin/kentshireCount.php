<html>
<head><title>Kentshire Call Count</title></head>
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

$yearStart = strtotime($year."-05-01");
$yearEnd = strtotime(((int)$year+1)."-04-31");

mysql_connect() or die("I'm sorry but I cannot connect to the MySQL server. Error: ".mysql_error());
mysql_select_db($dbName) or die("I'm sorry but there was an error selecting the DB. Error: ".mysql_error());
$query = "SELECT * FROM OLDcalls WHERE YEAR(Date)=".$year." OR YEAR(Date)=".((int)$year+1).";";

$result = mysql_query($query) or die("MySQL Query Error: ".mysql_error());

echo '<h3>Midland Park Ambulance Corps - Kentshire Calls Count for '.$year.'<br>'.date("Y-m-d", $yearStart)." through ".date("Y-m-d", $yearEnd).'</h3><br>';
echo '<a href="kentshireCount.php?year='.($year-1).'">'.($year-1).'</a>&nbsp;&nbsp;&nbsp;<a href="kentshireCount.php?year='.($year+1).'">'.($year+1).'</a><br>';
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
    if(validDate($r['Date']))
    {
	$add = $r['PtAddress'];
	$loc = $r['CallLoc'];
	if(stristr($add, "Kents") || (stristr($add, "187") && stristr($add, "Pat")) || stristr($loc, "Kents") || (stristr($loc, "187") && stristr($loc, "Pat")))
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
}

echo '</table>';
echo '<p><h3>'.$count.' calls shown above. Please confirm that all are actually to 187 Paterson Ave.</h3></p>';

function validDate($dateStr)
{
    $dateTS = strtotime($dateStr);
    global $yearStart;
    global $yearEnd;
    if($yearStart <= $dateTS && $yearEnd >= $dateTS)
    {
	return true;
    }
    else
    {
	return false;
    }
}

?>
</body>
</html>