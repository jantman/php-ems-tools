<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-04-07 13:27:43 jantman"                                                              |
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
 | $LastChangedRevision:: 55                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/stats/simpleMonthly.php                                $ |
 +--------------------------------------------------------------------------------------------------------+
*/

include("libchart/libchart.php");
require_once("../newcall/inc/antman.php");
require_once('../newcall/inc/newcall.php.inc');
require_once('../newcall/inc/runNum.php');

simpleMonthly($_GET['year'], $_GET['month']); 

//exec("rm /srv/www/htdocs/generated/*.png");

function simpleMonthly($year, $month)
{
    $captain = array();

	if($month == null)
	{
		echo '<title>MPAC Year-to-Date Simple Call Stats for '.$year.' as of '.date('l M j Y H:i').'</title><body>';
		echo '<h3>MPAC Year-to-Date Simple Call Stats for '.$year.' as of '.date('l M j Y H:i').'</h3><br>';
	}
	else
	{ 	
		echo '<title>MPAC Month-to-Date Simple Call Stats for '.textMonth($month).' '.$year.' as of '.date('l M j Y H:i').'</title><body>';
		echo '<h3>MPAC Month-to-Date Simple Call Stats for '.textMonth($month)." ".$year.' as of '.date('l M j Y H:i').'</h3><br>'; 
	}

	$ageArray = array(); 
	for($i=1; $i<120; $i++)
	{
		$ageArray[$i] = 0; 
	}
	// END DEBUG

	$WHERE = "YEAR(c.date_date)=$year";
	if($month != null){ $WHERE .= " AND MONTH(c.date_date)=$month";}

	$query = "SELECT COUNT(*) AS count FROM calls AS c WHERE c.is_deprecated=0 AND $WHERE;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());  
	$row = mysql_fetch_assoc($result);
	echo '<b>Total Calls: '.$row['count'].'</b><br />'."\n";
	$captain['totalcalls'] = $row['count'];

	$query = "SELECT c.RunNumber,c.date_date,cu.unit,cu.end_mileage,((ct.outservice-ct.dispatched)/60) AS lenMin FROM calls AS c LEFT JOIN calls_units AS cu ON c.RunNumber=cu.RunNumber LEFT JOIN calls_times AS ct ON c.RunNumber=ct.RunNumber WHERE c.is_deprecated=0 AND ct.is_deprecated=0 AND cu.is_deprecated=0 AND $WHERE;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());  

	$hours = array();
	$minmiles = array();
	$maxmiles = array();
	$calls = array();
	$numcalls = 0;
	while ($row = mysql_fetch_array($result)) 
	{
	    if(! isset($hours[$row['unit']])){ $hours[$unit] = 0;}
	    if(! isset($minmiles[$row['unit']])){ $minmiles[$row['unit']] = 1000000;}
	    if(! isset($maxmiles[$row['unit']])){ $maxmiles[$row['unit']] = 0;}
	    if(! isset($calls[$row['unit']])){ $calls[$unit] = 0;}

	    if($row['end_mileage'] < $minmiles[$row['unit']]){ $minmiles[$row['unit']] = $row['end_mileage'];}
	    if($row['end_mileage'] > $maxmiles[$row['unit']]){ $maxmiles[$row['unit']] = $row['end_mileage'];}

	    $hours[$row['unit']] += $row['lenMin'];
	    $hours['total'] += $row['lenMin'];
	    $calls[$row['unit']]++;
	    $calls['total']++;

	    $numcalls++;
	}

	//Stats by Rig 
	echo '<b>Stats By Rig:</b><br>'."\n";
	echo '<table border="1" cellpadding="5">'."\n";
	echo '<tr><td>&nbsp;</td><td>588</td><td>589</td><td><b>Total</b></td><td>Avg. Per Call</td></tr>'."\n";
	echo '<tr><td>Hours</td>'."\n";
	echo '<td>'.round($hours['588'] / 60,3).'</td>'."\n";
	$captain['588hrs'] = round($hours['588'] / 60,3);
	echo '<td>'.round($hours['589'] / 60,3).'</td>'."\n";
	$captain['589hrs'] = round($hours['589'] / 60,3);
	echo '<td>'.round($hours['total'] / 60,3).'</td>'."\n";
	echo '<td>'.round((($hours['total'] / 60) / $numcalls),3).'</td></tr>'."\n";
	echo '<tr><td>Miles </td>'."\n";
	echo '<td>'.($maxmiles['588'] - $minmiles['588']).'</td>'."\n";
	$captain['588miles'] = ($maxmiles['588'] - $minmiles['588']);
	echo '<td>'.($maxmiles['589'] - $minmiles['589']).'</td>'."\n";
	$captain['589miles'] = ($maxmiles['589'] - $minmiles['589']);
	$totalMiles = ($maxmiles['588'] - $minmiles['588']) + ($maxmiles['589'] - $minmiles['589']);
	echo '<td>'.$totalMiles.'</td>'."\n";
	echo '<td>'.round($totalMiles / $numcalls,3).'</td>'."\n";
	echo '</tr>'."\n";
	echo '<tr><td>Calls</td><td>'.$calls['588'].'</td><td>'.$calls['589'].'</td><td>'.$numcalls.'</td></tr>'."\n";
	$captain['589calls'] = $calls['589'];
	$captain['588calls'] = $calls['588'];
	echo '</table><br>'."\n";
	
	//Times 
	echo '<br><b>Call Times (minutes):</b><br>';
	echo '<table border="1" cellpadding="5">';
	echo '<tr><td>&nbsp;</td><td><b>Average</b></td><td><b>Minimum</b></td><td><b>Maximum</b></td></tr>'."\n";

	$query = "SELECT MIN((ct.inservice-ct.dispatched)/60) AS minlen,MAX((ct.inservice-ct.dispatched)/60) AS maxlen,AVG((ct.inservice-ct.dispatched)/60) AS avglen FROM calls AS c LEFT JOIN calls_times AS ct ON c.RunNumber=ct.RunNumber WHERE c.is_deprecated=0 AND ct.is_deprecated=0 AND $WHERE;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	$row = mysql_fetch_array($result);
	echo '<tr><td>Dispatch to In Servce</td><td>'.round($row['avglen'],2).'</td><td>'.round($row['minlen'],2).'</td><td>'.round($row['maxlen'],2).'</td></tr>'."\n";

	$query = "SELECT MIN((ct.onscene-ct.inservice)/60) AS minlen,MAX((ct.onscene-ct.inservice)/60) AS maxlen,AVG((ct.onscene-ct.inservice)/60) AS avglen FROM calls AS c LEFT JOIN calls_times AS ct ON c.RunNumber=ct.RunNumber WHERE c.is_deprecated=0 AND ct.is_deprecated=0 AND $WHERE;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	$row = mysql_fetch_array($result);
	echo '<tr><td>In Servce to On Scene</td><td>'.round($row['avglen'],2).'</td><td>'.round($row['minlen'],2).'</td><td>'.round($row['maxlen'],2).'</td></tr>'."\n";

	$query = "SELECT MIN((ct.onscene-ct.dispatched)/60) AS minlen,MAX((ct.onscene-ct.dispatched)/60) AS maxlen,AVG((ct.onscene-ct.dispatched)/60) AS avglen FROM calls AS c LEFT JOIN calls_times AS ct ON c.RunNumber=ct.RunNumber WHERE c.is_deprecated=0 AND ct.is_deprecated=0 AND $WHERE;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	$row = mysql_fetch_array($result);
	echo '<tr><td>Dispatch to On Scene</td><td>'.round($row['avglen'],2).'</td><td>'.round($row['minlen'],2).'</td><td>'.round($row['maxlen'],2).'</td></tr>'."\n";
	$captain['dispToOn'] = round($row['avglen'],2);

	$query = "SELECT MIN((ct.enroute-ct.onscene)/60) AS minlen,MAX((ct.enroute-ct.onscene)/60) AS maxlen,AVG((ct.enroute-ct.onscene)/60) AS avglen FROM calls AS c LEFT JOIN calls_times AS ct ON c.RunNumber=ct.RunNumber WHERE c.is_deprecated=0 AND ct.is_deprecated=0 AND $WHERE;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	$row = mysql_fetch_array($result);
	echo '<tr><td>Time On Scene</td><td>'.round($row['avglen'],2).'</td><td>'.round($row['minlen'],2).'</td><td>'.round($row['maxlen'],2).'</td></tr>'."\n";

	$query = "SELECT MIN((ct.outservice-ct.dispatched)/60) AS minlen,MAX((ct.outservice-ct.dispatched)/60) AS maxlen,AVG((ct.outservice-ct.dispatched)/60) AS avglen FROM calls AS c LEFT JOIN calls_times AS ct ON c.RunNumber=ct.RunNumber WHERE c.is_deprecated=0 AND ct.is_deprecated=0 AND $WHERE;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	$row = mysql_fetch_array($result);
	echo '<tr><td>Total Call Length</td><td>'.round($row['avglen'],2).'</td><td>'.round($row['minlen'],2).'</td><td>'.round($row['maxlen'],2).'</td></tr>'."\n";
	$captain['avgLen'] = round($row['avglen'],2);
	echo '</table><br>';
	
			
	//ALS Interface
	echo '<b>ALS Interface</b><br>';
	echo '<table border="1" cellpadding="5">'."\n";
	$query = "SELECT COUNT(*) AS count,ca.als_status AS status FROM calls AS c LEFT JOIN calls_als AS ca ON c.RunNumber=ca.RunNumber WHERE c.is_deprecated=0 AND ca.is_deprecated=0 AND $WHERE GROUP BY ca.als_status;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	while($row = mysql_fetch_array($result))
	{
	    echo '<tr>';
	    echo '<td>'.$row['status'].'</td>'; 
	    echo '<td>'.$row['count'].'</td>';
	    echo '</tr>'."\n";
	}
	echo '</table><br>'."\n";
	
	//Call Outcomes
	echo '<b>Call Outcomes</b><br>';
	echo '<table border="1" cellpadding="5">';
	$query = "SELECT COUNT(*) AS count,outcome FROM calls AS c WHERE c.is_deprecated=0 AND $WHERE GROUP BY outcome;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	while($row = mysql_fetch_array($result))
	{
	    echo '<tr>';
	    echo '<td>'.$row['outcome'].'</td>'; 
	    echo '<td>'.$row['count'].'</td>';
	    echo '</tr>'."\n";
	}
	echo '</table><br>'; 

	//Mutual Aid
	echo '<b>Mutual Aid (to other towns):</b><br>';
	echo '<table border="1" cellpadding="5">';
	$query = "SELECT COUNT(*) AS count,City FROM calls AS c LEFT JOIN calls_MA AS cMA ON c.RunNumber=cMA.RunNumber WHERE c.is_deprecated=0 AND cMA.is_deprecated=0 AND $WHERE GROUP BY cMA.City;";
	$i = 0;
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	while($row = mysql_fetch_array($result))
	{
	    echo '<tr>';
	    echo '<td>'.$row['City'].'</td>'; 
	    echo '<td>'.$row['count'].'</td>';
	    $i += $row['count'];
	    echo '</tr>'."\n";
	}
	echo '<tr><th>Total</th><td>'.$i.'</td></tr>';
	$captain['tookMA'] = $i;
	echo '</table><br>'; 

	//MA Requested
	$query = "SELECT c.RunNumber,c.date_ts FROM calls AS c WHERE outcome='NoCrew' AND c.is_deprecated=0 AND $WHERE;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	if(mysql_num_rows($result) < 1)
	{
		echo "<b>No known requests for mutual aid/unable to fill crew.</b> There may be some instances for which no call sheet was filled out.<br><br>"."\n";
	}
	else
	{
	    $captain['MAcalled'] = mysql_num_rows($result);
	    echo "<b>Mutual Aid Requested (no crew) at least ".mysql_num_rows($result)." times.</b> There may be other instances for which no call sheet was filled out.<br><br>"."\n";
	    echo '<table>'."\n";
	    echo '<tr><th>Run Number</th><th>Date</th>'."\n";
	    while($row = mysql_fetch_array($result))
	    {
		echo '<tr><td>'.formatRunNum($row['RunNumber']).'</td><td>'.date("Y-m-d", $row['date_ts']).'</td></tr>';
	    }
	    echo '</table>'."\n";
	}
	
	//Crew Type 
	$chart = new PieChart();
	echo '<b>Crew Types:</b><br>';
	echo '<table border="1" cellpadding="5">';

	$query = "SELECT COUNT(*) AS count FROM calls AS c WHERE is_duty_call=1 AND is_second_rig=0 AND c.is_deprecated=0 AND $WHERE;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	$row = mysql_fetch_assoc($result);
	echo '<tr>';
	echo '<th>Duty</th><td>'.$row['count'].'</td></tr>';
	$chart->addPoint(new Point("Duty", $row['count']));
	$captain['duty'] = $row['count'];

	$query = "SELECT COUNT(*) AS count FROM calls AS c WHERE is_duty_call=0 AND is_second_rig=0 AND c.is_deprecated=0 AND $WHERE;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	$row = mysql_fetch_assoc($result);
	echo '<tr>';
	echo '<th>General</th><td>'.$row['count'].'</td></tr>';
	$chart->addPoint(new Point("General", $row['count']));
	$foobarbaz = $captain['duty'] + $row['count'];
	$captain['duty'] = round(($captain['duty'] / $foobarbaz) * 100);
	$captain['general'] = round(($row['count'] / $foobarbaz) * 100);

	$query = "SELECT COUNT(*) AS count FROM calls AS c WHERE is_duty_call=0 AND is_second_rig=1 AND c.is_deprecated=0 AND $WHERE;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	$row = mysql_fetch_assoc($result);
	echo '<tr>';
	echo '<th>Second Rig</th><td>'.$row['count'].'</td></tr>';
	$chart->addPoint(new Point("Second Rig", $row['count']));

	echo '</table><br>'; 
	$chart->setTitle("Crew Types");
	$chart->render("generated/crewTypes.png");
	echo '<img src="generated/crewTypes.png"><br><br>'; 
	
	//CALL TYPES 
	$query = "SELECT COUNT(*) AS count,call_type FROM calls AS c WHERE c.is_deprecated=0 AND $WHERE GROUP BY call_type ORDER BY count;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	$chart = new VerticalChart(700,400);
	echo '<b>Call Types:</b><br>';
	echo '<table border="1" cellpadding="5">';
	$foo1 = -1;
	$foo2 = "";
	while($row = mysql_fetch_array($result))
	{
	    echo '<tr>';
	    echo '<td>'.$row['call_type'].'</td>'; 
	    echo '<td>'.$row['count'].'</td>';
	    echo '</tr>'."\n";
	    $chart->addPoint(new Point($row['call_type'], $row['count']));
	    if($row['count'] > $foo1){ $foo1 = $row['count']; $foo2 = $row['call_type'];}
	}
	echo '</table>';
	$captain['call_type'] = $foo2;
	$captain['call_type_count'] = $foo1;
	$chart->setTitle("Calls by Type");
	$chart->render("generated/callTypes.png");
	echo '<img src="generated/callTypes.png"><br><br>';
	//DONE WITH CALL TYPES 
	
	//Call Volume by day of week
	$dayOfWeek = array("Monday" => 0, "Tuesday" => 0, "Wednesday" => 0, "Thursday" => 0, "Friday" => 0, "Saturday" => 0, "Sunday" => 0);
	$chart = new VerticalChart();
	echo '</table>';
	echo '<b>Calls by Day of Week (Dispatched):</b><br>';
	echo '<table border="1" cellpadding="5">';
	$query = "SELECT COUNT(*) AS count,DAYNAME(date_date) AS day FROM calls AS c WHERE c.is_deprecated=0 AND $WHERE GROUP BY DAYNAME(date_date) ORDER BY DAYOFWEEK(date_date);";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	while($row = mysql_fetch_assoc($result))
	{
	    $dayOfWeek[$row['day']] = $row['count'];
	}
	foreach($dayOfWeek as $day => $count)
	{
	    echo '<tr><th>'.$day.'</th><td>'.$count.'</td></tr>'."\n";
	    $chart->addPoint(new Point($day, $count));
	}
	echo '</table>';
	$chart->setTitle("Calls by Day Dispatched");
	$chart->render("generated/dayDisp.png");
	echo '<img src="generated/dayDisp.png"><br><br>';
	
	//Call Volume by hour of day 
	$hourDisp = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 13 => 0, 14 => 0, 15 => 0, 16 => 0, 17 => 0, 18 => 0, 19 => 0, 20 => 0, 21 => 0, 22 => 0, 23 => 0);
	echo '<b>Calls By Hour of Day (Dispatched):</b><br>';
	$chart = new VerticalChart();
	echo '<table border="1" cellpadding="5">';
	$query = "SELECT COUNT(*) AS count,DAYNAME(date_date) AS day FROM calls AS c WHERE c.is_deprecated=0 AND $WHERE GROUP BY DAYNAME(date_date) ORDER BY DAYOFWEEK(date_date);";
	$query = "SELECT COUNT(*) AS count,HOUR(FROM_UNIXTIME(ct.dispatched)) AS hour FROM calls AS c LEFT JOIN calls_times AS ct ON c.RunNumber=ct.RunNumber WHERE c.is_deprecated=0 AND ct.is_deprecated=0 AND $WHERE GROUP BY HOUR(FROM_UNIXTIME(ct.dispatched)) ORDER BY HOUR(FROM_UNIXTIME(ct.dispatched));";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	while($row = mysql_fetch_assoc($result))
	{
	    $hourDisp[$row['hour']] = $row['count'];
	}
	foreach($hourDisp as $hour => $count)
	{
	    echo '<tr><th>'.$hour.'</th><td>'.$count.'</td></tr>'."\n";
	    $chart->addPoint(new Point($hour, $count));
	}
	echo '</table>';
	$chart->setTitle("Calls by Hour Dispatched");
	$chart->render("generated/hourDisp.png");
	echo '<img src="generated/hourDisp.png">';
	
	//DEMOGRAPHICS 
	$query = "SELECT COUNT(*) AS count FROM calls AS c LEFT JOIN patients AS p ON c.patient_pkey=p.Pkey WHERE c.is_deprecated=0 AND c.patient_pkey != 0 AND $WHERE;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	$row = mysql_fetch_assoc($result);
	$total = $row['count'];
	$query = "SELECT COUNT(*) AS count FROM calls AS c LEFT JOIN patients AS p ON c.patient_pkey=p.Pkey WHERE c.is_deprecated=0 AND c.patient_pkey != 0 AND $WHERE AND p.Sex='Male';";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	$row = mysql_fetch_assoc($result);
	$male = $row['count'];
	$query = "SELECT COUNT(*) AS count FROM calls AS c LEFT JOIN patients AS p ON c.patient_pkey=p.Pkey WHERE c.is_deprecated=0 AND c.patient_pkey != 0 AND $WHERE AND p.Sex='Female';";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	$row = mysql_fetch_assoc($result);
	$female = $row['count'];
	echo '<br><br><h3>Patient Demographics</h3><br>';
	$chart = new PieChart();
	$chart->addPoint(new Point('Male', $male));
	$chart->addPoint(new Point('Female', $female));
	$chart->setTitle("Patient Sex Male=".round(($male/$total)*100,3)."% Female=".round((($total - $male)/$total)*100,3).'%'); 
	$chart->render("generated/ptSex.png");
	echo '</table><img src="generated/ptSex.png"><br>';
	
	// Average Patient Age
	$query = "SELECT AVG(pt_age) AS age FROM calls AS c WHERE c.is_deprecated=0 AND c.patient_pkey != 0 AND $WHERE;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	$row = mysql_fetch_assoc($result);
	echo '<h3>Average Patient Age = '.round($row['age'],0).'</h3>';
	$captain['avgage'] = round($row['age'],0);
	
	$query = "SELECT pt_age AS age,COUNT(*) AS count FROM calls AS c WHERE c.is_deprecated=0 AND c.patient_pkey != 0 AND pt_age IS NOT NULL AND $WHERE GROUP BY pt_age ORDER BY pt_age;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	while($row = mysql_fetch_assoc($result))
	{
	    $ageArray[$row['age']] = $row['count'];
	}

	//$chart = new LineChart(900,400);
	$chart = new VerticalChart(900,400);
	ksort($ageArray); //sort array by keys 
	$max = 0;
	foreach($ageArray as $key => $val)
	{
		$chart->addPoint(new Point($key, $val));
		if($val > $max){ $max = $val;}
	}
	$chart->setUpperBound($max);
	$chart->setTitle("Patient Ages vs. Number of Patients");
	$chart->render("generated/age.png");
	echo '</table><img src="generated/age.png">';

	//Hospitals transported to
	echo '<br>';

	echo '<b>Facilities Transported To:</b><br>';
	echo '<table border="1" cellpadding="5">';
	$chart = new VerticalChart(700,400);

	$query = "SELECT PtTransferredTo,COUNT(*) AS count FROM calls AS c WHERE c.is_deprecated=0 AND c.patient_pkey != 0 AND $WHERE GROUP BY PtTransferredTo ORDER BY PtTransferredTo;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	while($row = mysql_fetch_assoc($result))
	{
	    echo '<tr><th>'.$row['PtTransferredTo'].'</th><td>'.$row['count'].'</td></tr>'."\n";
	    $chart->addPoint(new Point($row['PtTransferredTo'], $row['count']));	
	}	

	echo '</table>';
	$chart->setTitle("Facilities Transported To");
	$chart->render("generated/hosp.png");
	echo '</table><img src="generated/hosp.png"><br><br>';

	echo '<br /><br />'."\n";
	echo '<h2>Captains Report - '.textMonth($month).' '.$year.'</h2>'."\n";
	echo '<ul>'."\n";
	echo '<li>Total Calls - '.$captain['totalcalls'].'</li>';
	echo '<li>589 - '.$captain['589calls'].' calls, '.$captain['589hrs'].' hours, '.$captain['589miles'].' miles</li>';
	echo '<li>588 - '.$captain['588calls'].' calls, '.$captain['588hrs'].' hours, '.$captain['588miles'].' miles</li>';
	echo '<li>Average dispatch to on scene time - '.$captain['dispToOn'].' minutes</li>';
	echo '<li>Average total call length '.$captain['avgLen'].' minutes</li>';
	echo '<li>Took '.$captain['tookMA'].' mutual aid calls</li>';
	if(isset($captain['MAcalled']))
	{
	    echo '<li>requested mutual aid '.$captain['MAcalled'].' time(s).</li>';
	}
	else
	{
	    echo '<li>no recorded calls for mutual aid from other towns.</li>';
	}
	echo '<li>'.$captain['duty'].'% Duty, '.$captain['general'].'% general</li>';
	echo '<li>Most common call type was "'.$captain['call_type'].'" ('.$captain['call_type_count'].' calls)</li>';

	// most common patient ages
	$foobarbaz = "";
	$query = "SELECT pt_age AS age,COUNT(*) AS count FROM calls AS c WHERE c.is_deprecated=0 AND c.patient_pkey != 0 AND pt_age IS NOT NULL AND $WHERE GROUP BY pt_age ORDER BY count DESC LIMIT 3;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	$row = mysql_fetch_assoc($result);
	$foobarbaz = $row['age']." (".$row['count']." patients)";
	$row = mysql_fetch_assoc($result);
	$foobarbaz .= ", ".$row['age']." (".$row['count']." patients)";
	$row = mysql_fetch_assoc($result);
	$foobarbaz .= " and ".$row['age']." (".$row['count']." patients)";

	// median patient age
	$query = "SELECT pt_age FROM calls AS c WHERE c.is_deprecated=0 AND c.patient_pkey != 0 AND pt_age IS NOT NULL AND $WHERE ORDER BY pt_age ASC;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	$count = 0;
	$foo = array();
	while($row = mysql_fetch_assoc($result))
	{
	    $foo[$count] = $row['pt_age'];
	    $count++;
	}

	echo '<li>Average patient age was '.$captain['avgage'].', most common ages were '.$foobarbaz.', median patient age was '.($foo[ceil(count($foo)/2)]).'</li>';
	echo '</ul>'."\n";
}

?>