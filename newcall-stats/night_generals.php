<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-01-29 15:38:31 jantman"                                                              |
 +--------------------------------------------------------------------------------------------------------+
 | Copyright (c) 2009, 2010 Jason Antman. All rights reserved.                                            |
 |                                                                                                        |
 | This program is free software; you can redistribute it and/or modify                                   |
 | it under the terms of the GNU General Public License as published by                                   |
 | the Free Software Foundation; either version 3 of the License, or                                      |
 | (at your option) any later version.                                                                    |
 |                                                                                                        |
 | This program is distributed in the hope that it will be useful,                                        |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of                                         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                                          |
 | GNU General Public License for more details.                                                           |
 |                                                                                                        |
 | You should have received a copy of the GNU General Public License                                      |
 | along with this program; if not, write to:                                                             |
 |                                                                                                        |
 | Free Software Foundation, Inc.                                                                         |
 | 59 Temple Place - Suite 330                                                                            |
 | Boston, MA 02111-1307, USA.                                                                            |
 +--------------------------------------------------------------------------------------------------------+
 |Please use the above URL for bug reports and feature/support requests.                                  |
 +--------------------------------------------------------------------------------------------------------+
 | Authors: Jason Antman <jason@jasonantman.com>                                                          |
 +--------------------------------------------------------------------------------------------------------+
 | $LastChangedRevision:: 52                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/stats/mutual_aid_log.php                               $ |
 +--------------------------------------------------------------------------------------------------------+
*/

require_once('../custom.php'); // include config stuff
require_once('../newcall/inc/newcall.php.inc');
require_once('../newcall/inc/runNum.php');
require_once('../newcall/inc/stats.php');

// what year to show
if(isset($_GET['year']))
{
    $year = (int)$_GET['year'];
}
else
{
    $year = date("Y");
}

// get and show the data

// query
$query = "SELECT c.RunNumber,c.outcome,c.call_type,c.date_ts,ct.dispatched FROM calls AS c LEFT JOIN calls_times AS ct ON c.RunNumber=ct.RunNumber WHERE c.is_deprecated=0 AND ct.is_deprecated=0 AND is_duty_call=0 AND (HOUR(FROM_UNIXTIME(ct.dispatched)) <= 8 OR HOUR(FROM_UNIXTIME(ct.dispatched)) > 18);";
$result = mysql_query($query) or die("MySQL Query Error: ".mysql_error());
$num_results = mysql_num_rows($result);
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>MPAC Nighttime Generals</title>
<link rel="stylesheet" href="css/mutual_aid.css" type="text/css">
</head>
<body>
<?php
// table layout below
echo '<h1>MPAC Nighttime Generals (1900-0900) for '.$year.' as of '.date("Y-m-d")." ".date("H:i:s").'</h1>'."\n";
echo '<h2>'.$num_results.' calls</h2>'."\n";
?>
<table class="ma_log">
<tr>
<th>Run &#35;</th>
<th>Date</th>
<th>Time Disp</th>
<th>Call Type</th>
<th>Outcome</th>
<th>Crew</th>
</tr>
<?php

$membCalls = array();
$idToName = array();

while($row = mysql_fetch_assoc($result))
{
    echo '<tr>';
    echo '<td><a href="/newcall/newcall.php?RunNumber='.$row['RunNumber'].'">'.formatRunNum($row['RunNumber']).'</a></td>';
    echo '<td>'.date("Y-m-d", $row['date_ts']).'</td>';
    echo '<td>'.date("H:i", $row['dispatched']).'</td>';
    echo '<td>'.$row['call_type'].'</td>';

    // outcome
    switch ($row['outcome'])
    {
	case "Refusal":
	    echo '<td>Refusal</td>';
	    break;
	case "BLS":
	    echo '<td>BLS Transport</td>';
	    break;
	case "ALS":
	    echo '<td>ALS/BLS Transport</td>';
	    break;
	case "Other":
	    echo "<td>Other/Non-Emergency</td>";
	    break;
        case "Canceled":
	    echo "<td>Cancelled</td>";
	    break;
	case "DOA":
	    echo "<td>DOA</td>";
	    break;
	case "NoCrew":
	    echo "<td>No Crew</td>";
	    break;
	case "Air":
	    echo "<td>Air Transport</td>";
	    break;
    } 

    // crew
    $crewArr = getMembersOnCall($row['RunNumber']);
    ksort($crewArr);
    $q2 = "SELECT cc.EMTid,cc.is_on_duty,r.FirstName,r.LastName FROM calls_crew AS cc LEFT JOIN roster AS r ON cc.EMTid=r.EMTid WHERE cc.is_deprecated=0 AND cc.RunNumber=".$row['RunNumber'].";";
    $res2 = mysql_query($q2) or die("MySQL Query Error: ".mysql_error());
    echo '<td>';
    while($r = mysql_fetch_assoc($res2))
    {
	echo '<nobr>';
	if($r['is_on_duty'] != 1){ echo '<strong>';}
	echo $r['EMTid'].' ('.$r['FirstName']." ".$r['LastName'].') ';
	if($r['is_on_duty'] == 1)
	{
	    echo 'duty';
	}
	else
	{
	    echo 'GENERAL</strong>';
	    if(! isset($membCalls[$r['EMTid']])){ $membCalls[$r['EMTid']] = 1;}else{$membCalls[$r['EMTid']]++; }
	    $idToName[$r['EMTid']] = $r['FirstName']." ".$r['LastName'];
	}
	echo '</nobr><br />';
    }
    echo '</td>';


    echo '</tr>'."\n";
}
mysql_free_result($result);
// done with the table, close it
?>
</table>

<h2>Calls By Member (General):</h2>

<table class="ma_log">
<tr>
<th>EMTid</th>
<th>Name</th>
<th># Calls</th>
</tr>

<?php
arsort($membCalls);

include("libchart/libchart.php");
$chart = new VerticalChart(700,400);

foreach($membCalls as $id => $count)
{
    echo '<tr><td>'.$id.'</td><td>'.$idToName[$id].'</td><td>'.$count.'</td></tr>'."\n";
    $chart->addPoint(new Point($idToName[$id], $count));
}
echo '</table>'."\n";


$chart->setTitle("1900-0900 Generals by Member");
$chart->render("generated/nightGenerals.png");
echo '<img src="generated/nightGenerals.png">';
?>
</body>
</html>