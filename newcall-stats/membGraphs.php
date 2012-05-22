<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2011-02-01 11:44:40 jantman"                                                              |
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
 | $HeadURL:: http://svn.jasonantman.com/newcall/stats/membCalls.php                                    $ |
 +--------------------------------------------------------------------------------------------------------+
*/

include("libchart/libchart.php");
require_once("../newcall/inc/antman.php");
require_once('../newcall/inc/newcall.php.inc');
require_once('../newcall/inc/runNum.php');

require_once('../inc/global.php');

$id = $_GET['EMTid'];

//Connect to MySQL
$conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
mysql_select_db('pcr') or die ('Unable to select database!');

$name = getNameByEMTid($id);

echo '<title>Call Stats for Member '.$id.' ('.$name.') as of '.date('l M j Y H:i').'</title><body>'."\n";
echo '<h3>Call Stats for Member '.$id.' ('.$name.') as of '.date('l M j Y H:i').'</h3><br>'."\n"; 

$id = mysql_real_escape_string($id);
	
$a = array();

$years = array();

// OLD call report system
$query  = "SELECT COUNT(*) AS count,DATE_FORMAT(Date, '%Y-%m') AS date_formatted FROM OLDcalls WHERE DriverToScene='".$id."' || DriverToHosp='".$id."' || DriverToBldg='".$id."' || crew1='".$id."' || crew2='".$id."' || crew3='".$id."' || crew4='".$id."' || crew5='".$id."' || crew6='".$id."' || onScene1='".$id."' || onScene2='".$id."' || onScene3='".$id."' || onScene4='".$id."' || onScene5='".$id."' || onScene6='".$id."'";
$query .= " GROUP BY date_formatted";
$query .= ";";
$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());  
while ($row = mysql_fetch_array($result)) 
{
    $a[$row['date_formatted']] = $row['count'];
    if(! isset($years[substr($row['date_formatted'], 0, strpos($row['date_formatted'], "-"))])) { $years[substr($row['date_formatted'], 0, strpos($row['date_formatted'], "-"))] = 0;}
    $years[substr($row['date_formatted'], 0, strpos($row['date_formatted'], "-"))]+= $row['count'];
}
mysql_free_result($result);

// current call report system
$q = "SELECT count(*) AS count,DATE_FORMAT(date_date, '%Y-%m') AS date FROM calls AS c LEFT JOIN calls_crew AS cc ON c.RunNumber=cc.RunNumber WHERE c.is_deprecated=0 AND cc.is_deprecated=0 AND cc.EMTid='$id' GROUP BY date;";
$res = mysql_query($q) or die ("Error in query: $q. " . mysql_error());  
while($r = mysql_fetch_assoc($res))
{
    $a[$r['date']] = $r['count'];
    if(! isset($years[substr($row['date_formatted'], 0, strpos($row['date_formatted'], "-"))])) { $years[substr($row['date_formatted'], 0, strpos($row['date_formatted'], "-"))] = 0;}
    $years[substr($r['date'], 0, strpos($r['date'], "-"))]+= $r['count'];
}
mysql_free_result($res);

$chart = new VerticalChart(900, 400);
foreach($a as $month => $count)
{
    $chart->addPoint(new Point($month, $count));
}
$chart->setTitle("Calls by Month For Member $id");
$chart->render("generated/membGraphCalls.png");
echo '<img src="generated/membGraphCalls.png"><br><br>';

// yearly count
echo '<table border="1">'."\n";
echo '<tr><th colspan="2">Calls By Year</th></tr>'."\n";
echo '<tr><th>Year</th><th>Count</th></tr>'."\n";
foreach($years as $year => $count)
{
    if($year != 0) { echo "<tr><td>$year</td><td>$count</td></tr>\n";}
}
echo '</table>'."\n";


// SCHEDULE - HOURS
$h = array();
$query = "SELECT DATE_FORMAT(FROM_UNIXTIME(start_ts), '%Y-%m') AS date_formatted,start_ts,end_ts FROM schedule WHERE EMTid='$id' AND deprecated=0;";
$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());  
while ($row = mysql_fetch_array($result)) 
{
    $secs = $row['end_ts'] - $row['start_ts'];
    $hours = $secs / 3600;
    if(! isset($h[$row['date_formatted']])){ $h[$row['date_formatted']] = 0;}
    $h[$row['date_formatted']] += $hours;
}
mysql_free_result($result);

ksort($h);

$chart = new VerticalChart(900, 400);
foreach($h as $month => $hours)
{
    $chart->addPoint(new Point($month, $hours));
}
$chart->setTitle("Duty Hours by Month For Member $id");
$chart->render("generated/membGraphHours.png");
echo '<img src="generated/membGraphHours.png"><br><br>';

?>