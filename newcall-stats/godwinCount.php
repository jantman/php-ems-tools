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
require_once('../newcall/inc/newcall.php.inc');
require_once('../newcall/inc/runNum.php');

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


echo '<h3>Midland Park Ambulance Corps - 301 Godwin Ave Calls Count for '.$year.'</h3>';
echo '<p><a href="kentshireCalls.php?year='.($year-1).'">'.($year-1).'</a>&nbsp;&nbsp;&nbsp;<a href="kentshireCalls.php?year='.($year+1).'">'.($year+1).'</a></p>';

echo '<p><em>NOTE: This will only show calls from 1/1/2010 on.</em></p>';

echo '<p><strong>Please check that all addresses are correct before using the final count!</strong></p>';
	
echo '<table border=1>';
$count = 0;
echo '<tr>';
echo '<th>Run #</b></td>';
echo '<th>Date</th>';
echo '<th>Disp. Time</th>';
echo '<th>Call Type</th>';
echo '<th>Outcome</th>';
echo '<th>Pt Address</th>';
echo '<th>Call Location</th>';
echo '<th>Pt Location</th>';
echo '</tr>';

mysql_connect() or die("I'm sorry but I cannot connect to the MySQL server. Error: ".mysql_error());
mysql_select_db($dbName) or die("I'm sorry but there was an error selecting the DB. Error: ".mysql_error());
$query = "SELECT c.RunNumber,c.date_date,c.outcome,c.call_type,cl.AptNumber,cl.Street,cl.StreetNumber,cl.City,c.pt_loc_at_scene,ct.dispatched,p.City AS ptCity,p.Street AS ptStreet,p.StreetNumber AS ptStreetNumber,p.AptNumber AS ptAptNumber,p.State AS ptState FROM calls AS c LEFT JOIN calls_locations AS cl ON c.call_loc_id=cl.call_loc_id LEFT JOIN calls_times AS ct ON c.RunNumber=ct.RunNumber LEFT JOIN patients AS p ON p.Pkey=c.patient_pkey WHERE c.is_deprecated=0 AND cl.is_deprecated=0 AND ct.is_deprecated=0 AND cl.City='Midland Park' AND cl.Street='Godwin Ave' AND cl.StreetNumber='301' AND YEAR(c.date_date)=$year;";
$result = mysql_query($query) or die("MySQL Query Error: ".mysql_error());
while($r = mysql_fetch_array($result))
{
    $count++;
    echo '<tr>';
    echo '<td><a href="/newcall/newcall.php?RunNumber='.$r['RunNumber'].'">'.formatRunNum($r['RunNumber']).'</a></td>';
    echo '<td>'.$r['date_date'].'</td>';
    echo '<td>'.date("H:i:s", $r['dispatched']).'</td>';
    echo '<td>'.$r['call_type'].'</td>';
    echo '<td>'.$r['outcome'].'</td>';
    echo '<td>'.makeAddress($r['ptStreetNumber'], $r['ptStreet'], $r['ptAptNumber'], $r['ptCity'], $r['ptState']).'</td>';
    echo '<td>'.makeAddress($r['StreetNumber'], $r['Street'], $r['AptNumber'], $r['City']).'</td>';
    echo '<td>'.$r['pt_loc_at_scene'].'</td>';
    echo '</tr>';
}

echo '</table>';
echo '<p><h3>'.$count.' calls shown above. Please confirm that all are actually to 187 Paterson Ave.</h3></p>';

?>
</body>
</html>