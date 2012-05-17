<?php

//Member Calls 
//for PCRpro
//(c) 2006 Jason Antman. 

//As of 2006-6-27

include "libchart/libchart.php";

$id = $_GET['EMTid'];

//exec("rm /srv/www/htdocs/generated/*.png");

membStats($id);

function membStats($id)
{	
	//Connect to MySQL
	$conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
	mysql_select_db('pcr') or die ('Unable to select database!');
	
	$query  = "SELECT EMTid,LastName,FirstName FROM roster WHERE EMTid='".$id."';";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	while ($row = mysql_fetch_array($result))
	{
		$name= $row['FirstName']." ".$row['LastName'];
	}
	mysql_free_result($result);
	
	//Begin the table 
	echo '<title>Call Stats for Member '.$id.' ('.$name.') as of '.date('l M j Y H:i').'</title><body>';
	echo '<h3>Call Stats for Member '.$id.' ('.$name.') as of '.date('l M j Y H:i').'</h3><br>'; 
	echo '<table border="1">';
	//BEGIN A ROW
	echo '<tr>';
	echo '<td><b>Run #</b></td>'; 
	echo '<td><b>Date</b></td>';
	echo '<td><b>Day of Week</b></td>';
	echo '<td><b>Time Disp.</b></td>';
	echo '<td><b>Time Out Svc.</b></td>';
	echo '<td><b>Location</b></td>';
	echo '<td><b>Pt. Age</b></td>';
	echo '<td><b>Pt. Sex</b></td>';
	echo '<td><b>Call Type</b></td>';
	echo '<td><b>Outcome</b></td>';
//	echo '<td><b>Crew Type</b></td>';
	echo '<td><b>Crew</b></td>';	
	echo '<td>CrewType</td>';
	echo '</tr>';
	//END ROW
	

	$query  = "SELECT DriverToScene,DriverToBldg,DriverToHosp,crew1,crew2,crew3,RunNumber,PtAddress,CallLoc,PtSex,Age,CrewType,DATE_FORMAT(TimeDisp,'%H:%i'),DATE_FORMAT(TimeOut,'%H:%i'),Date,DAYNAME(Date),CallType,OC FROM OLDcalls WHERE DriverToScene='".$id."' || DriverToHosp='".$id."' || DriverToBldg='".$id."' || crew1='".$id."' || crew2='".$id."' || crew3='".$id."' || crew4='".$id."' || crew5='".$id."' || crew6='".$id."' || onScene1='".$id."' || onScene2='".$id."' || onScene3='".$id."' || onScene4='".$id."' || onScene5='".$id."' || onScene6='".$id."'";
	//$query .= " && EXTRACT(YEAR FROM Date)=".$year;
	//$query .= " && EXTRACT(MONTH FROM Date)=".$month;
	$query .= " ORDER BY YEAR(Date), RunNumber";
	$query .= ";";
	//TODO: NEED to do YEARLY also. 
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());  
	while ($row = mysql_fetch_array($result)) 
	{
		echo '<tr>';
		echo '<td>'.$row['RunNumber'].'</td>'; 
		echo '<td>'.$row['Date'].'</td>';
		echo '<td>'.$row['DAYNAME(Date)'].'</td>';
		echo '<td>'.$row["DATE_FORMAT(TimeDisp,'%H:%i')"].'</td>';
		echo '<td>'.$row["DATE_FORMAT(TimeOut,'%H:%i')"].'</td>';
		echo '<td>';
		if($row['CallLoc']=='Home')
		{
			echo $row['PtAddress'];
		}
		else
		{
			echo $row['CallLoc'];
		}
		echo '</td>';
		echo '<td>'.$row['Age'].'</td>';
		echo '<td>'.$row['PtSex'].'</td>';
		echo '<td>'.$row['CallType'].'</td>';
		echo '<td>'.$row['OC'].'</td>';
//		echo '<td>'.$row['CrewType'].'</td>';
		$crew = '';
		$crew .= $row['DriverToScene'].', '.$row['DriverToHosp'].', '.$row['DriverToBldg'];
		$crew .= ','.$row['crew1'].', '.$row['crew2'].', '.$row['crew3'];
		echo '<td>'.$crew.'</td>';
		echo '<td>'.$row['CrewType'].'</td>';
		echo '</tr>';
	}
	mysql_free_result($result);
	echo '</table><br>';
	//QUANTITATIVE ANALYSIS
	
	$hourDisp = array(0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0, 11 => 0, 12 => 0, 13 => 0, 14 => 0, 15 => 0, 16 => 0, 17 => 0, 18 => 0, 19 => 0, 20 => 0, 21 => 0, 22 => 0, 23 => 0);
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
	$callTypes = array();
	$driverToScene = 0;
	$driverToHosp = 0;
	$driverToBldg = 0; 
	$driver = 0; 
	$driverOnly = 0; 
	$count = 0; 
	$unit88 = 0;
	$unit89 = 0; 
	
	$query  = "SELECT TimeDisp,Unit,TimeInSvc,TimeOnScene,TimeInRoute,TimeOut,DriverToScene,DriverToBldg,DriverToHosp,RunNumber,PtAddress,CallLoc,PtSex,Age,CrewType,DATE_FORMAT(TimeOut,'%H:%i'),Date,DAYNAME(Date),CallType,OC,DATE_FORMAT(TimeDisp,'%k') FROM OLDcalls WHERE DriverToScene='".$id."' || DriverToHosp='".$id."' || DriverToBldg='".$id."' || crew1='".$id."' || crew2='".$id."' || crew3='".$id."' || crew4='".$id."' || crew5='".$id."' || crew6='".$id."' || onScene1='".$id."' || onScene2='".$id."' || onScene3='".$id."' || onScene4='".$id."' || onScene5='".$id."' || onScene6='".$id."'";
	//$query .= " && EXTRACT(YEAR FROM Date)=".$year;
	//$query .= " && EXTRACT(MONTH FROM Date)=".$month;
	$query .= " ORDER BY RunNumber";
	$query .= ";";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());  
	while ($row = mysql_fetch_array($result)) 
	{
		$count++;
		$callTypes[$row['CallType']] = $callTypes[$row['CallType']] + 1;
		$dayDisp[$row['DAYNAME(Date)']] = $dayDisp[$row['DAYNAME(Date)']] + 1; 
		$outcome[$row['OC']] = $outcome[$row['OC']] + 1;
		$alsStatus[$row['ALSstatus']] = $alsStatus[$row['ALSstatus']] + 1;
		$crewTypes[$row['CrewType']] = $crewTypes[$row['CrewType']] + 1;
		if($row['DriverToScene'] == $id)
		{
			$driverToScene++;
		}
		if(($row['DriverToScene'] == $id) || ($row['DriverToHosp'] == $id) || ($row['DriverToBldg'] == $id))
		{
			$driver++; 
		}
		if(($row['DriverToScene'] == $id || $row['DriverToScene'] == NULL) && ($row['DriverToHosp'] == $id || $row['DriverToHosp'] == NULL) && ($row['DriverToBldg'] == $id || $row['DriverToBldg'] == NULL))
		{
			$driverOnly++; 
		}
		if($row['DriverToHosp'] == $id)
		{
			$driverToHosp++;
		}
		if($row['DriverToBldg'] == $id)
		{
			$driverToBldg++;
		}
		if($row['Unit'] == "588")
		{
			$unit88++;
		}
		elseif($row['Unit'] == "589")
		{
			$unit89++;
		}
		if($row['TimeDisp']<>NULL)
		{
			$hourDisp[$row["DATE_FORMAT(TimeDisp,'%k')"]] = $hourDisp[$row["DATE_FORMAT(TimeDisp,'%k')"]] + 1;
			if($row['TimeOut']<>NULL)
			{
				$len = strtotime($row['TimeOut']) - strtotime($row['TimeDisp']);
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
				}
				if($onToInRtMax < $len)
				{
					$onToInRtMax = $len;
				}
				$inRtCount++; 
			}
		} 
		//more 
		
	}
	mysql_free_result($result);
	
	echo '<h3>Total Calls: '.$count.'</h3><br>';
	
	echo '<br><b>Call Times (minutes):</b><br>';
	echo '<table border="1" cellpadding="5">';
	echo '<tr><td>&nbsp;</td><td><b>Average</b></td><td><b>Minimum</b></td><td><b>Maximum</b></td></tr>';
	echo '<tr><td>Dispatch to In Servce</td><td>'.(($dispToIn / $inSvcCount)/60).'</td><td>'.($dispToInMin/60).'</td><td>'.($dispToInMax/60).'</td></tr>';
	echo '<tr><td>In Servce To On Scene</td><td>'.round((($inToOn / $onSceneCount)/60),3).'</td><td>'.($inToOnMin/60).'</td><td>'.($inToOnMax/60).'</td></tr>';
	echo '<tr><td><i>Dispatch to On Scene</i></td><td>'.round(((($dispToIn + $inToOn) / $onSceneCount)/60),3).'</td><td>'.(($inToOnMin + $dispToInMin)/60).'</td><td>'.(($dispToInMax + $inToOnMax)/60).'</td></tr>';
	echo '<tr><td>Time On Scene</td><td>'.(($onToInRt / $inRtCount)/60).'</td><td>'.($onToInRtMin/60).'</td><td>'.($onToInRtMax/60).'</td></tr>';
	echo '<tr><td><b>Total Call Length</b></td><td>'.(($callLen/60)/$count).'</td><td>'.($callLenMin/60).'</td><td>'.($callLenMax/60).'</td></tr>';
	echo '</table><br>';
	
	//Call Volume by day of week
	$chart = new VerticalChart();
	echo '</table>';
	echo '<b>Calls by Day of Week (Dispatched):  </b><i>(Note: This is the calendar day (00:00-23:59), not the schedule day.)</i><br>';
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
	//DRIVER 
	echo '<br><br>';
	$chart = new PieChart();
	$chart->addPoint(new Point('Driver', $driver));
	$chart->addPoint(new Point('Not Driver', $count - $driver));
	$chart->setTitle("Calls with ".$id." as a driver - ".round($driver/$count*100,3)."% or ".$driver.' out of '.$count);
	$chart->render("generated/driveNoDrive.png");
	echo '</table><img src="generated/driveNoDrive.png">';
	//DRIVER ONLY 
	echo '<br><br>';
	$chart = new PieChart();
	$chart->addPoint(new Point('Driver Only', $driverOnly));
	$chart->addPoint(new Point('Not Driver Only', $count - $driverOnly));
	$chart->setTitle("Calls with ".$id." as a driver only - ".round($driverOnly/$count*100,3)."% or ".$driverOnly.' out of '.$count);
	$chart->render("generated/driveOnly.png");
	echo '</table><img src="generated/driveOnly.png">';
	//DRIVER To SCENE
	echo '<br><br>';
	$chart = new PieChart();
	$chart->addPoint(new Point('Driver To Scene', $driverToScene));
	$chart->addPoint(new Point('Not Driver To Scene', $count - $driverToScene));
	$chart->setTitle("Calls with ".$id." as a driver to the scene - ".round($driverToScene/$count*100,3)."% or ".$driverToScene.' out of '.$count);
	$chart->render("generated/driverToScene.png");
	echo '</table><img src="generated/driverToScene.png">';
	//DRIVER To HOSPITAL
	echo '<br><br>';
	$chart = new PieChart();
	$chart->addPoint(new Point('Driver To Hosptial', $driverToHosp));
	$chart->addPoint(new Point('Not Driver To Hospital', $count - $driverToHosp));
	$chart->setTitle("Calls with ".$id." as a driver to the hospital - ".round($driverToHosp/$count*100,3)."% or ".$driverToHosp.' out of '.$count);
	$chart->render("generated/driverToHosp.png");
	echo '</table><img src="generated/driverToHosp.png">';
	//DRIVER To BUILDING
	echo '<br><br>';
	$chart = new PieChart();
	$chart->addPoint(new Point('Driver To Building', $driverToBldg));
	$chart->addPoint(new Point('Not Driver To Building', $count - $driverToBldg));
	$chart->setTitle("Calls with ".$id." as a driver to the building - ".round($driverToBldg/$count*100,3)."% or ".$driverToBldg.' out of '.$count);
	$chart->render("generated/driverToBldg.png");
	echo '</table><img src="generated/driverToBldg.png">';
	//Units used
	echo '<br><br>';
	$chart = new PieChart();
	$chart->addPoint(new Point('588', $unit88));
	$chart->addPoint(new Point('589', $unit89));
	$chart->setTitle("Unit used on calls with ".$id." as a crew member. 588 ".round($unit88/$count*100,3)."% 589 ".round($unit89/$count*100,3).'%');
	$chart->render("generated/unit.png");
	echo '</table><img src="generated/unit.png"><br>';
	
	//Call Outcomes
	echo '<b>Call Outcomes</b><br>';
	echo '<table border="1" cellpadding="5">';
	ksort($outcome);//sorts array by keys
	$chart = new VerticalChart();
	for($i=0; $i < count($outcome); $i = $i + 4)
	{ 
		echo '<tr>';
		echo '<td>'.key($outcome).'</td>'; 
		echo '<td>'.current($outcome).'</td>';	
		$chart->addPoint(new Point(key($outcome), current($outcome)));
		next($outcome);
		echo '<td>'.key($outcome).'</td>';
		echo '<td>'.current($outcome).'</td>';
		$chart->addPoint(new Point(key($outcome), current($outcome)));
		next($outcome); 
		echo '<td>'.key($outcome).'</td>';
		echo '<td>'.current($outcome).'</td>'; 
		$chart->addPoint(new Point(key($outcome), current($outcome)));
		next($outcome); 
		echo '<td>'.key($outcome).'</td>';
		echo '<td>'.current($outcome).'</td>';
		$chart->addPoint(new Point(key($outcome), current($outcome)));
		next($outcome);  
		echo '</tr>';
	}
	echo '</table><br>'; 
	$chart->setTitle("Calls by Outcome for Member ".$id);
	$chart->render("generated/outcome.png");
	echo '</table><img src="generated/outcome.png"><br><br>';
	
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
	$chart->setTitle("Calls by Type for Member ".$id);
	$chart->render("generated/callTypes.png");
	echo '</table><img src="generated/callTypes.png"><br><br>';
	//DONE WITH CALL TYPES 
}

?>
