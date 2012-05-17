<?php

//Member Stats
//for PCRpro
//(c) 2006 Jason Antman. 

//As of 2006-6-27

require_once("antman.php"); 

membStats($_GET['year'], $_GET['month']); 


function membStats($year, $month)
{
	//update monthly member stats for year and month 
	
	//select from DB where date contains month and year
	if($month == null)
	{
		$ytd = 1; //do year-to-date stats 
		echo '<title>Year-to-Date Membership Stats for '.$year.' as of '.date('l M j Y H:i').'</title><body>';
		echo '<h3>Year-to-Date Membership Stats for '.$year.' as of '.date('l M j Y H:i').'</h3><br>';
	}
	else
	{ 	
		echo '<title>Month-to-Date Membership Stats for '.textMonth($month).' '.$year.' as of '.date('l M j Y H:i').'</title><body>';
		echo '<h3>Month-to-Date Membership Stats for '.textMonth($month)." ".$year.' as of '.date('l M j Y H:i').'</h3><br>'; 
	}
	if($month != null){ echo 'Please note, at this time the "generals" field is just calls with "general" checked, in the "crew" box.<br>';}
	//layout table: member | <b>Total Calls</b>| generals | duty | Total Hours | duty call hours | gen call hours | Scheduled Time 
	echo '<table border="1">';

	if($ytd) //yearly stats 
	{
		//BEGIN A ROW
		echo '<tr>';
		echo '<td>ID #</td>';
		echo '<td>Last Name</td>';
		echo '<td>First Name</td>';
		for($i=1; $i<13; $i++)
		{
			echo '<td>'.textMonth($i).'</td>';
		}
		echo '<td><b>TOTAL</b></td>';
		echo '</tr>';
		//END ROW
		//Connect to MySQL
		$conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
		mysql_select_db('pcr') or die ('Unable to select database!');
		//get an array of all of the EMT ID's 
		//$query  = "SELECT EMTid FROM roster ORDER BY LastName";
		$query  = "SELECT EMTid FROM roster ORDER BY lpad(EMTid,10,'0')";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		while ($row = mysql_fetch_array($result))
		{
			$EMTidArray[] =& $row['EMTid'];
		}
		mysql_free_result($result); 
	 
		//Now, let's do the stats 
		for($i=0; $i < count($EMTidArray); $i++)
		{  
			$id = $EMTidArray[$i]; 
			$calls = array('1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0, '12' => 0); 
			$total = 0;
			//call totals 
			$query  = "SELECT RunNumber,EXTRACT(MONTH FROM Date) FROM OLDcalls WHERE ";
			$query .= "EXTRACT(YEAR FROM Date)=".$year." && "; 
			$query .= "(DriverToScene='".$id."' || DriverToHosp='".$id."' || DriverToBldg='".$id."' || crew1='".$id."' || crew2='".$id."' || crew3='".$id."' || crew4='".$id."' || crew5='".$id."' || crew6='".$id."' || onScene1='".$id."' || onScene2='".$id."' || onScene3='".$id."' || onScene4='".$id."' || onScene5='".$id."' || onScene6='".$id."')";
			$query .= ";"; 
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());  
			while ($row = mysql_fetch_array($result)) 
			{
				$calls[$row['EXTRACT(MONTH FROM Date)']] = $calls[$row['EXTRACT(MONTH FROM Date)']] + 1;
				$total++;
			}
			mysql_free_result($result);
			//done with call totals
			//GET MEMBER INFO
			$query  = "SELECT EMTid,LastName,FirstName FROM roster WHERE EMTid='".$id."'";
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			while ($row = mysql_fetch_array($result))
			{
				$lastName = $row['LastName'];
				$firstName = $row['FirstName'];
			}
			mysql_free_result($result);
			
			//OUTPUT TO TABLE 
			echo '<tr>';
			echo '<td>'.$id.'</td>';
			echo '<td>'.$lastName.'</td>';
			echo '<td>'.$firstName.'</td>';
			if($calls['1']<>0)
			{
				echo '<td>'.$calls['1'].'</td>';
			}
			else
			{			
				echo '<td>&nbsp;</td>';
			}
			if($calls['2']<>0)
			{
				echo '<td>'.$calls['2'].'</td>';
			}
			else
			{			
				echo '<td>&nbsp;</td>';
			}
			if($calls['3']<>0)
			{
				echo '<td>'.$calls['3'].'</td>';
			}
			else
			{			
				echo '<td>&nbsp;</td>';
			}
			if($calls['4']<>0)
			{
				echo '<td>'.$calls['4'].'</td>';
			}
			else
			{			
				echo '<td>&nbsp;</td>';
			}
			if($calls['5']<>0)
			{
				echo '<td>'.$calls['5'].'</td>';
			}
			else
			{			
				echo '<td>&nbsp;</td>';
			}
			if($calls['6']<>0)
			{
				echo '<td>'.$calls['6'].'</td>';
			}
			else
			{			
				echo '<td>&nbsp;</td>';
			}
			if($calls['7']<>0)
			{
				echo '<td>'.$calls['7'].'</td>';
			}
			else
			{			
				echo '<td>&nbsp;</td>';
			}
			if($calls['8']<>0)
			{
				echo '<td>'.$calls['8'].'</td>';
			}
			else
			{			
				echo '<td>&nbsp;</td>';
			}
			if($calls['9']<>0)
			{
				echo '<td>'.$calls['9'].'</td>';
			}
			else
			{			
				echo '<td>&nbsp;</td>';
			}
			if($calls['10']<>0)
			{
				echo '<td>'.$calls['10'].'</td>';
			}
			else
			{			
				echo '<td>&nbsp;</td>';
			}
			if($calls['11']<>0)
			{
				echo '<td>'.$calls['11'].'</td>';
			}
			else
			{			
				echo '<td>&nbsp;</td>';
			}
			if($calls['12']<>0)
			{
				echo '<td>'.$calls['12'].'</td>';
			}
			else
			{			
				echo '<td>&nbsp;</td>';
			}
			echo '<td><b>'.$total.'</b></td>';
			
			echo '</tr>';
		}
		//BEGIN A ROW
		echo '<tr>';
		echo '<td>ID #</td>';
		echo '<td>Last Name</td>';
		echo '<td>First Name</td>';
		for($i=1; $i<13; $i++)
		{
			echo '<td>'.textMonth($i).'</td>';
		}
		echo '<td><b>TOTAL</b></td>';
		echo '</tr>';
		//END ROW
	}
	else // monthly stats 
	{
		//BEGIN A ROW
		echo '<tr>';
		echo '<td>ID #</td>';
		echo '<td>Last Name</td>';
		echo '<td>First Name</td>';
		echo '<td><b>Total Calls</b></td>';
		echo '<td>Duty Calls</td>';
		echo '<td>General Calls</td>';
		echo '<td><b>Total</b> Call HOURS</td>';
		echo '<td>Duty Call HOURS</td>';
		echo '<td>General Call HOURS</td>';
		//echo '<td>Scheduled<br>Duty Time<br>TOTAL</td>';
		//echo '<td>Scheduled<br>Duty Time<br>(Day)</td>';
		//echo '<td>Scheduled<br>Duty Time<br>(Night)</td>';	
		echo '</tr>';
		//END ROW
		//Connect to MySQL
		$conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
		mysql_select_db('pcr') or die ('Unable to select database!');
		
		//get an array of all of the EMT ID's 
		$query  = "SELECT EMTid FROM roster ORDER BY LastName";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
		while ($row = mysql_fetch_array($result))
		{
			$EMTidArray[] =& $row['EMTid'];
		}
		mysql_free_result($result); 
	 
		
		//Now, let's do the stats 
		for($i=0; $i < count($EMTidArray); $i++)
		{  
			$id = $EMTidArray[$i];
			$dutyCalls = 0;
			$genCalls = 0;
			$genHours = 0;
			$dutyHours = 0; 
			$duty = 0; 
			//call totals 
			$query  = "SELECT RunNumber,CrewType,TimeDisp,TimeOut,Date FROM OLDcalls WHERE ";
			$query .= " EXTRACT(YEAR FROM Date)=".$year;
			$query .= " && EXTRACT(MONTH FROM Date)=".$month;
			$query .= " && (DriverToScene='".$id."' || DriverToHosp='".$id."' || DriverToBldg='".$id."' || crew1='".$id."' || crew2='".$id."' || crew3='".$id."' || crew4='".$id."' || crew5='".$id."' || crew6='".$id."' || onScene1='".$id."' || onScene2='".$id."' || onScene3='".$id."' || onScene4='".$id."' || onScene5='".$id."' || onScene6='".$id."')";

			$query .= ";";
			//TODO: NEED to do YEARLY also. 
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());  
			while ($row = mysql_fetch_array($result)) 
			{
				if($row['CrewType']=="Duty")
				{
					$dutyCalls++; 
				}
				else
				{
					$genCalls++;
				}
				//now we have the count for duty, general, and total calls 
				if($row['TimeDisp']<>NULL)
				{
					if($row['TimeOut']<>NULL)
					{
						//UNsure if this works:
						$len = (strtotime($row['TimeOut']) - strtotime($row['TimeDisp']));
						if($row['CrewType']=="Duty")
						{
							$dutyHours = $dutyHours + $len;
						}
						else
						{
							$genHours = $genHours + $len;  
						}
					}
				}
			}
			mysql_free_result($result);
			//done with call totals
			//GET MEMBER INFO
			$query  = "SELECT EMTid,LastName,FirstName FROM roster WHERE EMTid='".$id."'";
			$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
			while ($row = mysql_fetch_array($result))
			{
				$lastName = $row['LastName'];
				$firstName = $row['FirstName'];
			}
			mysql_free_result($result);
			
			//OUTPUT TO TABLE 
			echo '<tr>';
			echo '<td>'.$id.'</td>';
			echo '<td>'.$lastName.'</td>';
			echo '<td>'.$firstName.'</td>';
			echo '<td>'.($dutyCalls + $genCalls).'</td>'; 
			echo '<td>'.$dutyCalls.'</td>'; 
			echo '<td>'.$genCalls.'</td>';
			echo '<td>'.round(($dutyHours + $genHours) / 3600,3) .'</td>';
			echo '<td>'.round($dutyHours / 3600,3) .'</td>';
			echo '<td>'.round($genHours / 3600,3) .'</td>';
			//echo '<td>Scheduled<br>Duty Time<br>TOTAL</td>';
			//echo '<td>Scheduled<br>Duty Time<br>(Day)</td>';
			//echo '<td>Scheduled<br>Duty Time<br>(Night)</td>';	
			echo '</tr>';
		}
		//BEGIN A ROW
		echo '<tr>';
		echo '<td>ID #</td>';
		echo '<td>Last Name</td>';
		echo '<td>First Name</td>';
		echo '<td><b>Total Calls</b></td>';
		echo '<td>Duty Calls</td>';
		echo '<td>General Calls</td>';
		echo '<td><b>Total</b> Call HOURS</td>';
		echo '<td>Duty Call HOURS</td>';
		echo '<td>General Call HOURS</td>';
		//echo '<td>Scheduled<br>Duty Time<br>TOTAL</td>';
		//echo '<td>Scheduled<br>Duty Time<br>(Day)</td>';
		//echo '<td>Scheduled<br>Duty Time<br>(Night)</td>';	
		echo '</tr>';
		//END ROW
	}
	
	echo '</table>';
	
	
	//algorithm as follows:
	//$temp variables for all columns 
	
	//iterate through roster
		//calls with this member on crew
			//iterate through schedule for this date and dispatch time
				//if this was a second rig, count it as a general. Also, is there any deviation from this? How are standbys counted? 
				//was member on duty? if so, add values to duty temp variables
					//if not, add values to general temp variables
			//add to global (non-duty/general) times 
		//
	//
	
	
}

?>
