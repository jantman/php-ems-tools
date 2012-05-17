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
 | $LastChangedRevision:: 57                                                                            $ |
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
$query = "SELECT c.RunNumber,c.date_ts,ct.dispatched,ma.City,ma.State,c.call_type,c.outcome FROM calls AS c LEFT JOIN calls_times AS ct ON c.RunNumber=ct.RunNumber LEFT JOIN calls_MA AS ma ON c.RunNumber=ma.RunNumber WHERE c.is_deprecated=0 AND ct.is_deprecated=0 AND ma.is_deprecated=0 AND YEAR(c.date_date)=".$year.";";
$result = mysql_query($query) or die("MySQL Query Error: ".mysql_error());
$num_results = mysql_num_rows($result);
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>MPAC Mutual Aid Log</title>
<link rel="stylesheet" href="css/mutual_aid.css" type="text/css">
</head>
<body>
<?php
// table layout below
echo '<h1>MPAC Mutual Aid Log for '.$year.' as of '.date("Y-m-d")." ".date("H:i:s").'</h1>'."\n";
echo '<h2>'.$num_results.' calls</h2>'."\n";
?>
<table class="ma_log">
<tr>
<th>Run &#35;</th>
<th>Date</th>
<th>Time</th>
<th>Town</th>
<th>Call Type</th>
<th>Cancelled?</th>
<th>Crew</th>
<th>Outcome</th>
</tr>
<?php

while($row = mysql_fetch_assoc($result))
{
    echo '<tr>';
    echo '<td><a href="/newcall/newcall.php?RunNumber='.$row['RunNumber'].'">'.formatRunNum($row['RunNumber']).'</a></td>';
    echo '<td>'.date("Y-m-d", $row['date_ts']).'</td>';
    echo '<td>'.date("H:i", $row['dispatched']).'</td>';
    echo '<td>'.$row['City'].'</td>';
    echo '<td>'.$row['call_type'].'</td>';
    // canceled
    if($row['outcome'] == "Canceled"){ echo '<td><font color="red">YES</font></td>';} else { echo "<td>No</td>";}

    // crew
    $crewArr = getMembersOnCall($row['RunNumber']);
    ksort($crewArr);
    $crewStr = "";
    foreach($crewArr as $id){ $crewStr .= $id." ";}
    echo '<td>'.$crewStr.'</td>';

    // outcome
    switch ($row['outcome'])
    {
	case "Refusal":
	    echo '<td>Refusal</td>';
	    break;
	case "BLS":
	    echo '<td>BLS Transport</td>';
	    break;
	case "ALS/BLS":
	    echo '<td>BLS/ALS Transport</td>';
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
	case "No Crew":
	    echo "<td>No Crew</td>";
	    break;
	case "Air":
	    echo "<td>Air Transport</td>";
	    break;
    } 
    echo '</tr>'."\n";
}
mysql_free_result($result);
// done with the table, close it
?>
</table>
</body>
</html>