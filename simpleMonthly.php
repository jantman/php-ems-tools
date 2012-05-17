<?php

//simpleMonthly.php
//Simple Monthly Stats for PCRpro
//(C) 2006 Jason Antman.

//Updated 2006-6-27 

include "libchart/libchart/libchart.php";
require_once "antman.php";  

simpleMonthly($_GET['year'], $_GET['month']); 

//exec("rm /srv/www/htdocs/generated/*.png");

function simpleMonthly($year, $month)
{
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
	//echo 'This is a preliminary version. If the charts do not appear correct, please press F5.<br><br>';
	//Connect to MySQL
	$conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
	mysql_select_db('pcr') or die ('Unable to select database!');
	//VARIABLES for each call 
	$callTypes = array(); 
	$crewTypes = array();
	$MAtotal = 0;
	$hours88 = 0;
	$hours89 = 0;
	$hoursOtr = 0; 
	$minMiles588 = 1000000;
	$maxMiles588 = 0;
	$minMiles589 = 1000000; 
	$maxMiles589 = 0; 
	$calls88 = 0; 
	$calls89 = 0; 
	$callsTotal = 0; 
	if($month == null)
	{
	    $callsTotal = 1;
	}
	$generals = 0;
	$duty = 0;
	$secondRig = 0;
	$hourDisp = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 13 => 0, 14 => 0, 15 => 0, 16 => 0, 17 => 0, 18 => 0, 19 => 0, 20 => 0, 21 => 0, 22 => 0, 23 => 0);
	$dayDisp = array();
	$dispToIn = 0;
	$inToOn = 0; 
	$onToInRt = 0; 
	$dispToInMin = 1000000000;
	$inToOnMin = 1000000000; 
	$onToInRtMin = 1000000000;
	$dispToInMax = 0;
	$inToOnMax = 0; 
	$onToInRtMax = 0; 
	$inSvcCount = 0;
	$onSceneCount = 0; 
	$inRtCOunt = 0;
	$callLen = 0;
	$callLenMin = 1000000;
	$callLenMax = 0; 
	$outcome = array();
	$alsStatus = array();
	$countM = 0;
	$countF = 0;
	$countAge = 0;
	$Age = 0; 
	$ageArray = array(); 
	$hospArray = array();
	for($i=1; $i<120; $i++)
	{
		$ageArray[$i] = 0; 
	}
	
	//SQL STUFF. Get info from all calls this month 
	$query  = "SELECT PtSex,Age,ToHosp,ALSstatus,OC,RunNumber,CrewType,TimeDisp,TimeInSvc,TimeOnScene,TimeInRoute,TimeOut,Date,Unit,CallType,EndMileage,MA,DAYNAME(Date),DATE_FORMAT(TimeDisp,'%k') FROM OLDcalls WHERE";
	$query .= " EXTRACT(YEAR FROM Date)=".$year;
	if($month <> NULL)
	{
		$query .= " && EXTRACT(MONTH FROM Date)=".$month; // we will do only the specified month. 
	}
	$query .= ";";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());  
	while ($row = mysql_fetch_array($result)) 
	{
		//CALL Type 
		$callTypes[$row['CallType']] = $callTypes[$row['CallType']] + 1;
		$dayDisp[$row['DAYNAME(Date)']] = $dayDisp[$row['DAYNAME(Date)']] + 1; 
		$outcome[$row['OC']] = $outcome[$row['OC']] + 1;
		if($row['MA']==1)
		{
			$MAtotal++;
		}
		$alsStatus[$row['ALSstatus']] = $alsStatus[$row['ALSstatus']] + 1;
		$crewTypes[$row['CrewType']] = $crewTypes[$row['CrewType']] + 1;
		$ageArray[$row['Age']] = $ageArray[$row['Age']] + 1; 
		if($row['ToHosp']<>"None." && $row['ToHosp']<>"")
		{
			$hospArray[$row['ToHosp']] = $hospArray[$row['ToHosp']] + 1; 
		}
		//Demographics
		if($row['PtSex'] <> NULL)
		{
			if($row['PtSex']=='Male')
			{
				$countM++;	
			}
			elseif($row['PtSex']=='Female')
			{
				$countF++;
			}
		}
		if($row['Age']<> NULL && $row['Age']<>0)
		{
			$countAge++;
			$Age = $Age + $row['Age'];
		}
		//HOURS 
		if($row['TimeDisp']<$row['TimeOut'])
		{
		if($row['TimeDisp']<>NULL)
		{
			$hourDisp[$row["DATE_FORMAT(TimeDisp,'%k')"]] = $hourDisp[$row["DATE_FORMAT(TimeDisp,'%k')"]] + 1;
			if($row['TimeOut']<>NULL)
			{
				$len = strtotime($row['TimeOut']) - strtotime($row['TimeDisp']);
				if($row['Unit'] == '588')
				{
					$hours88 += $len;
				}
				elseif($row['Unit'] == '589')
				{ 
					$hours89 += $len; 
				}
				else
				{
					$hoursOtr += $len; 
				}
				$callLen += $len;
				if($callLenMin > $len)
				{
					$callLenMin = $len;
				}
				if($callLenMax < $len)
				{
					$callLenMax = $len;
				}
			}
			if($row['TimeInSvc']<>NULL)
			{
				$len = strtotime($row['TimeInSvc']) - strtotime($row['TimeDisp']);
				$dispToIn += $len;
				if($len < $dispToInMin)
				{
					$dispToInMin = $len;
				}
				if($len > $dispToInMax)
				{
					$dispToInMax = $len;
				}
				$inSvcCount++; 
			}
			if($row['TimeOnScene']<>NULL && $row['TimeInSvc']<> NULL)
			{
				$len = strtotime($row['TimeOnScene']) - strtotime($row['TimeInSvc']);
				$inToOn += $len; 
				if($inToOnMin > $len)
				{
					$inToOnMin = $len;
				}
				if($inToOnMax < $len)
				{
					$inToOnMax = $len;
				}
				$onSceneCount++; 
			}
			if($row['TimeOnScene']<>NULL && $row['TimeInRoute'] <> NULL)
			{
				$len = strtotime($row['TimeInRoute']) - strtotime($row['TimeOnScene']);
				$onToInRt += $len;
				if($onToInRtMin > $len && $len <> 0)
				{
					$onToInRtMin = $len;
//					echo "Run ".$row['RunNumber'].' dispToInMin = '.$len.'<br />';
				}
				if($onToInRtMax < $len)
				{
					$onToInRtMax = $len;
				}
				$inRtCount++; 
			}
		} 
		}
		//MILES
		if($row['Unit']=="588" && $row['EndMileage'] <> NULL)
		{
			if($row['EndMileage'] < $minMiles588)
			{
				$minMiles588 = $row['EndMileage'];
			}
			if($row['EndMileage'] > $maxMiles588)
			{
				$maxMiles588 = $row['EndMileage'];
			}
		}
		elseif($row['Unit']=="589" && $row['EndMileage'] <> NULL)
		{
			if($row['EndMileage'] < $minMiles589)
			{
				$minMiles589 = $row['EndMileage'];
			}
			if($row['EndMileage'] > $maxMiles589)
			{
				$maxMiles589 = $row['EndMileage'];
			}
		}
		//CALLS
		if($row['Unit']=="588")
		{
			$calls588++;
		}
		elseif($row['Unit']=="589")
		{
			$calls589++;
		}
		$callsTotal++;
		//Call Type 
		if($row['CrewType']=='General')
		{
			$generals++;
		}
		elseif($row['CrewType']=='Duty')
		{
			$duty++;
		}
		else
		{
			$secondRig++;
		}
		//response times 
	}
	mysql_free_result($result); 

	//fix the minimum miles
	if($minMiles588==1000000)
	{
		$minMiles588 = 0;
	}
	if($minMiles589==1000000)
	{
		$minMiles589 = 0;
	}

	//Stats by Rig 
	echo chr(13);
	echo '<b>Stats By Rig:</b><br>';
	echo chr(13);
	echo '<table border="1" cellpadding="5">';
	echo chr(13);
	echo '<tr><td>&nbsp;</td><td>588</td><td>589</td><td><b>Total</b></td><td>Avg. Per Call</td></tr>';
	echo chr(13);
	echo '<tr><td>Hours</td>';
	echo chr(13);
	echo '<td>'.round($hours88 / 3600,3).'</td>';
	echo chr(13);
	echo '<td>'.round($hours89 / 3600,3).'</td>';
	echo chr(13);
	echo '<td>'.round(($hours88 + $hours89 + $hoursOtr) / 3600,3).'</td>';
	echo chr(13);
	echo '<td>'.round((($hours88 + $hours89 + $hoursOtr)/ $callsTotal) / 3600,3).'</td></tr>';
	echo chr(13);
	echo chr(13);
	echo '<tr><td>Miles </td>';
	echo chr(13);
	echo '<td>'.($maxMiles588 - $minMiles588).'</td>';
	echo chr(13);
	echo '<td>'.($maxMiles589 - $minMiles589).'</td>';
	echo chr(13);
	echo '<td>'.(($maxMiles588 - $minMiles588)+ ($maxMiles589 - $minMiles589)).'</td>';
	echo chr(13);
	echo '<td>'.round((($maxMiles588 - $minMiles588)+ ($maxMiles589 - $minMiles589))/ $callsTotal,3).'</td>';
	echo chr(13);
	echo '</tr>';
	echo chr(13);
	echo '<tr><td>Calls</td><td>'.$calls588.'</td><td>'.$calls589.'</td><td>'.$callsTotal.'</td></tr>';
	echo chr(13);
	echo '</table><br>';
	
	//Times 
	echo '<br><b>Call Times (minutes):</b><br>';
	echo '<table border="1" cellpadding="5">';
	echo '<tr><td>&nbsp;</td><td><b>Average</b></td><td><b>Minimum</b></td><td><b>Maximum</b></td></tr>';
	echo '<tr><td>Dispatch to In Servce</td><td>'.(($dispToIn / $inSvcCount)/60).'</td><td>'.($dispToInMin/60).'</td><td>'.($dispToInMax/60).'</td></tr>';
	echo '<tr><td>In Servce To On Scene</td><td>'.round((($inToOn / $onSceneCount)/60),3).'</td><td>'.($inToOnMin/60).'</td><td>'.($inToOnMax/60).'</td></tr>';
	echo '<tr><td><i>Dispatch to On Scene</i></td><td>'.round(((($dispToIn + $inToOn) / $onSceneCount)/60),3).'</td><td>'.(($inToOnMin + $dispToInMin)/60).'</td><td>'.(($dispToInMax + $inToOnMax)/60).'</td></tr>';
	echo '<tr><td>Time On Scene</td><td>'.(($onToInRt / $inRtCount)/60).'</td><td>'.($onToInRtMin/60).'</td><td>'.($onToInRtMax/60).'</td></tr>';
	echo '<tr><td><b>Total Call Length</b></td><td>'.(($callLen / $callsTotal)/60).'</td><td>'.($callLenMin/60).'</td><td>'.($callLenMax/60).'</td></tr>';
	echo '</table><br>';
	
			
	//ALS Interface
	echo '<b>ALS Interface</b><br>';
	echo '<table border="1" cellpadding="5">';
	for($i=0; $i < count($alsStatus); $i = $i + 4)
	{
		echo '<tr>';
		echo '<td>'.key($alsStatus).'</td>'; 
		echo '<td>'.current($alsStatus).'&nbsp;'.'</td>';	
		next($alsStatus);
		echo '<td>'.key($alsStatus).'</td>';
		echo '<td>'.current($alsStatus).'&nbsp;'.'</td>';
		next($alsStatus); 
		echo '<td>'.key($alsStatus).'</td>';
		echo '<td>'.current($alsStatus).'&nbsp;'.'</td>';
		next($alsStatus); 
		echo '<td>'.key($alsStatus).'</td>';
		echo '<td>'.current($alsStatus).'&nbsp;'.'</td>';
		next($alsStatus);  
		echo '</tr>';
	}
	echo '</table><br>';
	
	//Call Outcomes
	echo '<b>Call Outcomes</b><br>';
	echo '<table border="1" cellpadding="5">';
	ksort($outcome);//sorts array by keys
	for($i=0; $i < count($outcome); $i = $i + 4)
	{ 
		echo '<tr>';
		echo '<td>'.key($outcome).'</td>'; 
		echo '<td>'.current($outcome).'</td>';	
		next($outcome);
		echo '<td>'.key($outcome).'</td>';
		echo '<td>'.current($outcome).'</td>';
		next($outcome); 
		echo '<td>'.key($outcome).'</td>';
		echo '<td>'.current($outcome).'</td>'; 
		next($outcome); 
		echo '<td>'.key($outcome).'</td>';
		echo '<td>'.current($outcome).'</td>';
		next($outcome);  
		echo '</tr>';
	}
	echo '</table><br>'; 

	//Mutual Aid
	echo '<b>Mutual Aid (to other towns):</b><br>';
	echo '<table border="1" cellpadding="5">';
	echo '<tr><td>Mutual Aid Calls</td>';
	echo '<td>'.$MAtotal.'</td></tr></table><br>';

	//MA Requested
	if($outcome["No Crew"] > 0)
	{
		echo "<b>Mutual Aid Requested (no crew) at least ".$outcome["No Crew"]." times.</b> There may be other instances for which no call sheet was filled out.<br><br>";
	}
	else
	{
		echo "<b>No known requests for mutual aid/unable to fill crew.</b> There may be some instances for which no call sheet was filled out.<br><br>";
	}
	
	//Crew Type 
	echo '<b>Crew Types:</b><br>';
	echo '<table border="1" cellpadding="5">';
	ksort($crewTypes);//sorts array by keys
	$chart = new PieChart();
	echo '<tr>';
	echo '<td>'.key($crewTypes).'</td>'; 
	echo '<td>'.current($crewTypes).'</td>';
	$chart->addPoint(new Point(key($crewTypes), current($crewTypes)));
	next($crewTypes);
	echo '<td>'.key($crewTypes).'</td>';
	echo '<td>'.current($crewTypes).'</td>';
	$chart->addPoint(new Point(key($crewTypes), current($crewTypes)));
	next($crewTypes);  
	echo '<td>'.key($crewTypes).'</td>';
	echo '<td>'.current($crewTypes).'</td>';
	$chart->addPoint(new Point(key($crewTypes), current($crewTypes)));
	next($crewTypes);  
	echo '</tr>';
	echo '</table><br>'; 
	$chart->setTitle("Crew Types (as specified on PCR) for Member ".$id);
	$chart->render("generated/crewTypes.png");
	echo '</table><img src="generated/crewTypes.png"><br><br>'; 
	
	//CALL TYPES 
	echo '<b>Call Types:</b><br>';
	echo '<table border="1" cellpadding="5">';
	ksort($callTypes);//sorts array by keys  
	//OUTPUT TO TABLE 
	$chart = new VerticalChart(700,400);
	for($i=0; $i < count($callTypes); $i = $i + 4)
	{
		echo '<tr>';
		echo '<td>'.key($callTypes).'</td>';
		echo '<td>'.current($callTypes).'</td>';
		$chart->addPoint(new Point(key($callTypes), current($callTypes)));	
		next($callTypes);
		echo '<td>'.key($callTypes).'</td>';
		echo '<td>'.current($callTypes).'</td>';
		$chart->addPoint(new Point(key($callTypes), current($callTypes)));
		next($callTypes); 
		echo '<td>'.key($callTypes).'</td>';
		echo '<td>'.current($callTypes).'</td>';
		$chart->addPoint(new Point(key($callTypes), current($callTypes)));
		next($callTypes); 
		echo '<td>'.key($callTypes).'</td>';
		echo '<td>'.current($callTypes).'</td>';
		$chart->addPoint(new Point(key($callTypes), current($callTypes)));
		next($callTypes); 
		echo '</tr>';
	}
	echo '</table>';
	$chart->setTitle("Calls by Type");
	$chart->render("generated/callTypes.png");
	echo '</table><img src="generated/callTypes.png"><br><br>';
	//DONE WITH CALL TYPES 
	
	//Call Volume by day of week
	$chart = new VerticalChart();
	echo '</table>';
	echo '<b>Calls by Day of Week (Dispatched):</b><br>';
	echo '<table border="1" cellpadding="5">';
	echo '<tr><td><b>Sunday</b></td><td><b>Monday</b></td><td><b>Tuesday</b></td><td><b>Wednesday</b></td><td><b>Thursday</b></td><td><b>Friday</b></td><td><b>Saturday</b></td></tr>';
	echo '<tr>';
	echo '<td>'.$dayDisp['Sunday'].'</td>'; 
	$chart->addPoint(new Point('Sunday', $dayDisp['Sunday']));
	echo '<td>'.$dayDisp['Monday'].'</td>'; 
	$chart->addPoint(new Point('Monday', $dayDisp['Monday']));
	echo '<td>'.$dayDisp['Tuesday'].'</td>'; 
	$chart->addPoint(new Point('Tuesday', $dayDisp['Tuesday']));
	echo '<td>'.$dayDisp['Wednesday'].'</td>'; 
	$chart->addPoint(new Point('Wednesday', $dayDisp['Wednesday']));
	echo '<td>'.$dayDisp['Thursday'].'</td>'; 
	$chart->addPoint(new Point('Thursday', $dayDisp['Thursday']));
	echo '<td>'.$dayDisp['Friday'].'</td>'; 
	$chart->addPoint(new Point('Friday', $dayDisp['Friday']));
	echo '<td>'.$dayDisp['Saturday'].'</td>'; 
	$chart->addPoint(new Point('Saturday', $dayDisp['Saturday']));
	echo '</tr>';
	echo '</table>';
	$chart->setTitle("Calls by Day Dispatched");
	$chart->render("generated/dayDisp.png");
	echo '</table><img src="generated/dayDisp.png"><br><br>';
	
	//Call Volume by hour of day 
	echo '<b>Calls By Hour of Day (Dispatched):</b><br>';
	echo '<table border="1" cellpadding="5">';
	ksort($hourDisp);//sorts array by keys  
	//OUTPUT TO TABLE 
	echo '<table border="1" cellpadding="5">';
	echo '<tr>';
	echo '<td><b>00</b></td><td>'.$hourDisp[0].'</td>';
	echo '<td><b>06</b></td><td>'.$hourDisp[6].'</td>';
	echo '<td><b>12</b></td><td>'.$hourDisp[12].'</td>';
	echo '<td><b>18</b></td><td>'.$hourDisp[18].'</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td><b>01</b></td><td>'.$hourDisp[1].'</td>';
	echo '<td><b>07</b></td><td>'.$hourDisp[7].'</td>';
	echo '<td><b>13</b></td><td>'.$hourDisp[13].'</td>';
	echo '<td><b>19</b></td><td>'.$hourDisp[19].'</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td><b>02</b></td><td>'.$hourDisp[2].'</td>';
	echo '<td><b>08</b></td><td>'.$hourDisp[8].'</td>';
	echo '<td><b>14</b></td><td>'.$hourDisp[14].'</td>';
	echo '<td><b>20</b></td><td>'.$hourDisp[20].'</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td><b>03</b></td><td>'.$hourDisp[3].'</td>';
	echo '<td><b>09</b></td><td>'.$hourDisp[9].'</td>';
	echo '<td><b>15</b></td><td>'.$hourDisp[15].'</td>';
	echo '<td><b>21</b></td><td>'.$hourDisp[21].'</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td><b>04</b></td><td>'.$hourDisp[4].'</td>';
	echo '<td><b>10</b></td><td>'.$hourDisp[10].'</td>';
	echo '<td><b>16</b></td><td>'.$hourDisp[16].'</td>';
	echo '<td><b>22</b></td><td>'.$hourDisp[22].'</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td><b>05</b></td><td>'.$hourDisp[5].'</td>';
	echo '<td><b>11</b></td><td>'.$hourDisp[11].'</td>';
	echo '<td><b>17</b></td><td>'.$hourDisp[17].'</td>';
	echo '<td><b>23</b></td><td>'.$hourDisp[23].'</td>';
	echo '</tr>';
	$chart = new VerticalChart();
	for($i=0; $i <24; $i++)
	{
		$chart->addPoint(new Point($i, $hourDisp[$i]));
	}
	$chart->setTitle("Calls by Hour Dispatched");
	$chart->render("generated/hourDisp.png");
	echo '</table><img src="generated/hourDisp.png">';
	
	//DEMOGRAPHICS 
	echo '<br><br><h3>Patient Demographics</h3><br>';
	$chart = new PieChart();
	$chart->addPoint(new Point('Male', $countM));
	$chart->addPoint(new Point('Female', $countF));
	$chart->setTitle("Patient Sex Male=".round($countM/($countM + $countF)*100,3)."% Female=".round($countF/($countM + $countF)*100,3).'%'); 
	$chart->render("generated/ptSex.png");
	echo '</table><img src="generated/ptSex.png"><br>';
	
	echo '<h3>Average Patient Age = '.round($Age/$countAge,0).'</h3>';
	
	$chart = new LineChart(900,400);
	ksort($ageArray); //sort array by keys 
	for($i=1; $i < count($ageArray); $i++)
	{
		$chart->addPoint(new Point(key($ageArray), current($ageArray)));
		next($ageArray);
	}
	$chart->setUpperBound(30);
	$chart->setTitle("Patient Ages vs. Number of Patients");
	$chart->render("generated/age.png");
	echo '</table><img src="generated/age.png">';

	//Hospitals transported to
	echo '<br>';

	echo '<b>Facilities Transported To:</b><br>';
	echo '<table border="1" cellpadding="5">';
	ksort($hospArray);//sorts array by keys 

	$chart = new VerticalChart(700,400);
	for($i=0; $i < count($hospArray); $i = $i + 4)
	{
		echo '<tr>';
		echo '<td>'.key($hospArray).'</td>';
		echo '<td>'.current($hospArray).'</td>';
		$chart->addPoint(new Point(key($hospArray), current($hospArray)));	
		next($hospArray);
		echo '<td>'.key($hospArray).'</td>';
		echo '<td>'.current($hospArray).'</td>';
		$chart->addPoint(new Point(key($hospArray), current($hospArray)));
		next($hospArray); 
		echo '<td>'.key($hospArray).'</td>';
		echo '<td>'.current($hospArray).'</td>';
		$chart->addPoint(new Point(key($hospArray), current($hospArray)));
		next($hospArray); 
		echo '<td>'.key($hospArray).'</td>';
		echo '<td>'.current($hospArray).'</td>';
		$chart->addPoint(new Point(key($hospArray), current($hospArray)));
		next($hospArray); 
		echo '</tr>';
	}
	echo '</table>';
	$chart->setTitle("Facilities Transported To");
	$chart->render("generated/hosp.png");
	echo '</table><img src="generated/hosp.png"><br><br>';
}
?>
