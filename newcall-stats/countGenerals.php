<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-04-07 13:31:47 jantman"                                                              |
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
 | $HeadURL:: http://svn.jasonantman.com/newcall/stats/countGenerals.php                                $ |
 +--------------------------------------------------------------------------------------------------------+
*/
require_once('../custom.php');
require_once('../inc/sched.php');
require_once('generals.php');
require_once('../newcall/inc/newcall.php.inc');

$debug = false;

if(! empty($_GET['style']))
{
    //style monthly or yearly
    $style = $_GET['style'];
}
else
{
    $style = "monthly";
}

if(! empty($_GET['sort']))
{
    $sort = $_GET['sort'];
}
else
{
    $sort = "EMTid";
}

if($sort == 'EMTid')
{
    $sort = 'lpad(EMTid,10,"0")';
}

if(isset($_GET['showall']))
{
    $showAllMembers = true;
}
else
{
    $showAllMembers = false;
}

if(! empty($_GET['start']))
{
    $start = (int)$_GET['start'];
}
else
{
    $start = strtotime(date("Y-m")."-01 00:00:00");
}

$year = date("Y", $start);
$month = date("m", $start);

?>
<html>
<head>
<style type="text/css">
table.hours {
    border-width: 1px 1px 1px 1px;
    border-spacing: 0px;
    border-style: solid solid solid solid;
    border-color: black black black black;
    border-collapse: separate;
    background-color: white;
}
table.hours th {
    border-width: 1px 1px 1px 1px;
 padding: 1px 1px 1px 1px;
    border-style: solid solid solid solid;
    border-color: black black black black;
    background-color: white;
    -moz-border-radius: 0px 0px 0px 0px;
}
table.hours td {
    border-width: 1px 1px 1px 1px;
 padding: 1px 1px 1px 1px;
    border-style: solid solid solid solid;
    border-color: black black black black;
    background-color: white;
    -moz-border-radius: 0px 0px 0px 0px;
}
</style>
<?php
$title = $shortName; 
if($style=='monthly')
{
    $title .= ' Monthly General Call Totals for';
    $title .= ' '.date("M Y", $start);
}
else
{
    $title .= ' Yearly General Call Totals for';
    $title .= ' '.$year;
}
echo '<title>'.$title.'</title>'; 
?>
</head>
<body>
<?php

// figure out which member types to show
$types = array();
global $memberTypes;
for($i = 0; $i < count($memberTypes); $i++)
{
    if($memberTypes[$i]['canPullDuty'] == true)
    {
	$types[] = $memberTypes[$i]['name'];
    }
}
$types[] = "Driver";

echo '<h3>'.$title.'<br>as of '.date("Y-m-d H:i:m").'</h3>';
if($style=='monthly')
{
    // controls for +/- month
    echo '<a href="countGenerals.php?start='.lastMonthDate($start).'&style=monthly"> &lt Month</a>';
    echo '&nbsp;&nbsp;&nbsp;';
    echo '<a href="countGenerals.php?start='.nextMonthDate($start).'&style=monthly">Month &gt</a>';
    echo '<br>';


    echo '<font size="2"><a href="countGenerals.php?year='.$year.'&month='.$month.'&style=yearly">(Go To Yearly Count)</a></font><br>';
    echo '<table class="hours">';
    echo '<td><b><a href="countGenerals.php?start='.$start.'&sort=EMTid">ID</a></b></td>';
    echo '<td><b><a href="countGenerals.php?start='.$start.'&sort=LastName">Last Name</a></b></td>';
    echo '<td><b><a href="countGenerals.php?start='.$start.'&sort=FirstName">First Name</a></b></td>';
    echo '<td><b>Generals</b></td><td><b>Duty</b></td><td><b>Non-Emerg./Events</b></td><td><b>TOTAL CALLS</b></td></tr>';
    $query = 'SELECT * FROM roster ORDER BY '.$sort.';';
    $result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());
    while($row = mysql_fetch_array($result))
    {
	if(in_array($row['status'], $types))
	{
	    echo '<tr>';
	    echo '<td><b>'.$row['EMTid'].'</b></td>';
	    echo '<td>'.$row['LastName'].'</td>';
	    echo '<td>'.$row['FirstName'].'</td>';
	    $callA = countMonthGenerals($year, $month, $row['EMTid']);
	    $duty = $callA['duty'];
	    $generals = $callA['generals'];
	    $other = $callA['other'];
	    echo '<td>'.makeString($generals).'</td>';
	    echo '<td>'.makeString($duty).'</td>';
	    echo '<td>'.makeString($other).'</td>';
	    echo '<td>'.makeString($duty + $generals + $other).'</td>';
	    echo '</tr>';
	}
	else
	{
	    // DEBUG
	    //echo $row['EMTid']."<br>";
	}
    }
    mysql_free_result($result);
}
else
{
    //style is yearly
    echo '<font size="2"><a href="countGenerals.php?year='.$year.'&month='.$month.'&style=monthly">(Go To Monthly Count)</a></font>&nbsp;&nbsp;&nbsp;<a href="countGenerals.php?year='.$year.'&style=monthly&showall=true">Show All Members</a><br>';
    echo '<table class="hours">';
    echo '<tr>';
    echo '<td><b><a href="countGenerals.php?year='.$year.'&sort=EMTid&style=yearly">ID</a></b></td>';
    echo '<td><b><a href="countGenerals.php?year='.$year.'&sort=LastName&style=yearly">Last Name</a></b></td>';
    echo '<td><b><a href="countGenerals.php?year='.$year.'&sort=FirstName&style=yearly">First Name</a></b></td>';
    echo '<td><b>Jan</b></td><td><b>Feb</b></td><td><b>Mar</b></td><td><b>Apr</b></td><td><b>May</b></td><td><b>Jun</b></td><td><b>Jul</b></td><td><b>Aug</b></td><td><b>Sep</b></td><td><b>Oct</b></td><td><b>Nov</b></td><td><b>Dec</b></td><td><b>TOTAL</b></td></b></tr>';
    $query = 'SELECT EMTid,status,LastName,FirstName FROM roster ORDER BY '.$sort.';';
    $result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());
    while($row = mysql_fetch_array($result))
    {
	if(in_array($row['status'], $types))
	{
	    echo '<tr>';
	    echo '<td><b>'.$row['EMTid'].'</b></td>';
	    echo '<td>'.$row['LastName'].'</td>';
	    echo '<td>'.$row['FirstName'].'</td>';
	    $total = 0;
	    for($i=1; $i<13; $i++)
	    {
		$generals = countMonthGenerals($year, $i, $row['EMTid']);
		$generals = $generals['generals'];
		echo '<td>'.makeString($generals).'</td>';
		$total += ($generals);
	    }
	    echo '<td>'.makeString($total).'</td>';
	    echo '</tr>';
	}
	elseif($showAllMembers == true)
	{
	    echo '<tr>';
	    echo '<td><b>'.$row['EMTid'].'</b></td>';
	    echo '<td>'.$row['LastName'].'</td>';
	    echo '<td>'.$row['FirstName'].'</td>';
	    $total = 0;
	    for($i=1; $i<13; $i++)
	    {
		$generals = countMonthGenerals($year, $i, $row['EMTid']);
		$generals = $generals['generals'];
		echo '<td>'.makeString($generals).'</td>';
		$total += ($generals);
	    }
	    echo '<td>'.makeString($total).'</td>';
	    echo '</tr>';
	}
    }
    mysql_free_result($result);
}

//FUNCTIONS

function countMonthGenerals($year, $month, $ID)
{
    $retArray = array();
    $query = "SELECT cc.EMTid,cc.is_on_duty,cc.is_on_scene,c.outcome,c.date_ts FROM calls_crew AS cc LEFT JOIN calls AS c ON cc.RunNumber=c.RunNumber WHERE cc.is_deprecated=0 AND c.is_deprecated=0 AND cc.EMTid='$ID' AND MONTH(c.date_date)=$month AND YEAR(c.date_date)=$year;";
    $result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());
    $genCount = 0;
    $dutyCount = 0;
    $otherCount = 0;
    while($row = mysql_fetch_array($result))
    {
	$timestamp = $row['date_ts'];

	if($row['outcome'] == "Other")
	{
	    // non-emerg. or other call type
	    $otherCount++;
	}
	elseif($row['is_on_duty'] == 1)
	{
	    $dutyCount++;
	}
	else
	{
	    $genCount++;
	}
    }
    mysql_free_result($result);

    $retArray['duty'] = $dutyCount;
    $retArray['generals'] = $genCount;
    $retArray['other'] = $otherCount;

    return $retArray;
}

function GetMonthString($n)
{
    $timestamp = mktime(0, 0, 0, $n, 1, 2005);
    
    return date("M", $timestamp);
}


function makeString($n)
{
    // how to show a string in the table
    if($n == 0)
    {
	return '&nbsp;';
    }
    else
    {
	return $n;
    }
}

?>
</body>
</html>