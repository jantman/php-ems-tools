<?php
// script that handles the back-end logic for responding.php
// Time-stamp: "2011-02-08 10:41:25 jantman"

$positions = array("Senior" => "EMT", "Driver" => "Driver", "Probie" => "Probie", "Resigned" => "Resigned"); // how to show the 'status' from the roster on our page

// ESTABLISH DB CONNECTIONS
require_once('/srv/www/htdocs/config/config.php');
$local_conn = mysql_connect() or die("Error: Unable to establish local MySQL connection.");
mysql_select_db($dbName, $local_conn) or die("Unable to select local DB: '".$dbName."'\n");
require_once('/srv/www/htdocs/config/openEScallin.php');
$rmt_conn = false;
$rmt_conn = mysql_connect($callin_host, $callin_user);
if(! $rmt_conn)
{
    echo '<div class="maintWarning"><h1>Call-in server ('.$callin_host.') appears to be down.</h1><h2>No call-ins will show up here.</h2></div>'."\n";
}
else
{
    mysql_select_db($callin_db, $rmt_conn) or die("Unable to select database '".$callin_db."' on host ".$callin_host."\n");
}

require_once('/srv/www/htdocs/config/scheduleConfig.php'); // schedule configuration

function genTimeDiv()
{
    $final = '<span style="font-size: 1em;">as of '.date("D Y-m-d").'</span>'.'&nbsp;&nbsp;&nbsp;<span style="font-size: 1.5em;">'.date("H:i:s").'</span>';
    return $final;
}

function genBayTimeDiv()
{
    $final = '<span class="timeSpan">'.date("H:i:s").'</span>';
    return $final;
}

function genTonesDiv($probie = false)
{
    // returns the string of HTML for the current crew table
    global $positions, $local_conn, $shortName;

    $probieStr = "";
    if($probie)
    {
	$query = "SELECT r.status FROM schedule AS s LEFT JOIN roster AS r ON s.EMTid=r.EMTid WHERE start_ts <= ".time()." AND end_ts >= ".time()." AND deprecated=0 AND r.status='Probie';";
	$result = mysql_query($query, $local_conn) or die("Error in Query: ".$query."\n Error: ".mysql_error($local_conn)."\n");
	if(mysql_num_rows($result) > 0){ $probieStr = "& Probie";}
    }

    $query = "SELECT r.status FROM schedule AS s LEFT JOIN roster AS r ON s.EMTid=r.EMTid WHERE start_ts <= ".time()." AND end_ts >= ".time()." AND deprecated=0 AND r.status='Senior';";
    $result = mysql_query($query, $local_conn) or die("Error in Query: ".$query."\n Error: ".mysql_error($local_conn)."\n");
    $seniorCount = mysql_num_rows($result);

    // show what tones to send
    if($seniorCount >= 2)
    {
	return '<span class="currentToneType">Duty Crew: 2 (or more) Senior Members '.$probieStr.'</span>';
    }
    elseif($seniorCount == 1)
    {
	return '<span class="currentToneType">Duty Crew: 1 Senior Member '.$probieStr.'</span>';
    }
    // else
    if($probieStr != ""){ return '<span class="currentToneType">No Duty Crew (only probie)</span>';}
    return '<span class="currentToneType">No Duty Crew</span>';
}

function genRespDiv()
{
    global $local_conn, $rmt_conn, $positions;
    $final = "";
    $final .=  '<table class="responding">'."\n";
    $final .=  '<tr><th>Member</th><th>ID</th><th>Position</th><th>Call Time</th><th>Responding</th></tr>'."\n";
    
    // store local array of EMTid => Name
    $IDtoName = array(); // array of EMTid => LastName, FirstName
    $IDtoUnit = array(); // array of EMTid => unitID

    $local_query = "SELECT EMTid,FirstName,LastName,unitID FROM roster;";
    $local_result = mysql_query($local_query, $local_conn) or die("Error in Query: ".$local_query."\n Error: ".mysql_error($local_conn)."\n");
    while($row = mysql_fetch_assoc($local_result))
    {
	$IDtoName[$row['EMTid']] = $row['LastName'].", ".$row['FirstName'];
	if($row['unitID'] != ""){ $IDtoUnit[$row['EMTid']] = $row['unitID'];}
    }
    
    // connect to remote VoIP1 MySQL

    
    // iterate through all rows for current (non-cleared) members
    $rmt_query = "SELECT c.start_ts,c.DTMF_select,c.EMTid,c.end_ts,c.is_cleared,c.status,d.description FROM callins AS c LEFT JOIN dtmf_options AS d ON c.DTMF_select=d.dtmf_key WHERE c.is_cleared=0;";
    $rmt_result = mysql_query($rmt_query, $rmt_conn) or die("Error in Query: ".$rmt_query."\n Error: ".mysql_error($rmt_conn)."\n");
    
    if(mysql_num_rows($rmt_result) < 1)
    {
	// what to do if no call-ins
	$final .= '<tr><td colspan="5"><span style="font-style: italic;">NO members responding yet.</span></td></tr>';
	
    }
    else
    {
	// we have callins
	while($row = mysql_fetch_assoc($rmt_result))
	{
	    // added 2009-12-14
	    if(! is_local_user() && $positions[$row['status']] == "Probie")
	    {
		continue;
	    }
	    // end added 2009-12-14

	    // for each row, display the pertinent information, using the colors defined in $colors
	    $final .=  '<tr>';
	    $final .=  '<td>'.$IDtoName[$row['EMTid']].'</td>';
	    $final .=  '<td>'.$row['EMTid'];
	    if(isset($IDtoUnit[$row['EMTid']])){ $final .= " (".$IDtoUnit[$row['EMTid']].")";}
	    $final .= '</td>';
	    $final .=  '<td>'.$positions[$row['status']].'</td>';
	    
	    // make sure it's today
	    if(date("Y-m-d", $row['start_ts']) == date("Y-m-d"))
	    {
		$final .=  '<td>'.date("H:i", $row['start_ts']).'</td>';
	    }
	    else
	    {
		$final .=  '<td>'.date("D H:i", $row['start_ts']).'</td>';
	    }
	    
	    if($row['description'] == "CANCELED RESPONSE")
	    {
		$final .=  '<td><span style="font-weight: bold; color: #FF0000;">'.$row['description'].'</span></td>';
	    }
	    else
	    {
		$final .=  '<td>'.$row['description'].'</td>';
	    }
	    $final .=  '</tr>'."\n";
	}
    }
    $final .= '</table>'."\n";
    return $final;
}

function genRespWAPDiv()
{
    global $local_conn, $rmt_conn, $positions;
    
    // store local array of EMTid => Name
    $IDtoName = array(); // array of EMTid => LastName, FirstName
    $IDtoUnit = array(); // array of EMTid => unitID

    $local_query = "SELECT EMTid,FirstName,LastName,unitID FROM roster;";
    $local_result = mysql_query($local_query, $local_conn) or die("Error in Query: ".$local_query."\n Error: ".mysql_error($local_conn)."\n");
    while($row = mysql_fetch_assoc($local_result))
    {
	$IDtoName[$row['EMTid']] = $row['LastName'].", ".$row['FirstName'];
	if($row['unitID'] != ""){ $IDtoUnit[$row['EMTid']] = $row['unitID'];}
    }
    
    // connect to remote VoIP1 MySQL

    
    // iterate through all rows for current (non-cleared) members
    $rmt_query = "SELECT c.start_ts,c.DTMF_select,c.EMTid,c.end_ts,c.is_cleared,c.status,d.description FROM callins AS c LEFT JOIN dtmf_options AS d ON c.DTMF_select=d.dtmf_key WHERE c.is_cleared=0;";
    $rmt_result = mysql_query($rmt_query, $rmt_conn) or die("Error in Query: ".$rmt_query."\n Error: ".mysql_error($rmt_conn)."\n");
    
    if(mysql_num_rows($rmt_result) < 1)
    {
	// what to do if no call-ins
	echo '<h2>No Members Responding Yet</h2>'."\n";
    }
    else
    {
	// we have callins
	echo '<h2>Responding Members:</h2>'."\n";
	while($row = mysql_fetch_assoc($rmt_result))
	{
	    // for each row, display the pertinent information, using the colors defined in $colors
	    echo $IDtoName[$row['EMTid']].' ('.$row['EMTid'];
	    if(isset($IDtoUnit[$row['EMTid']])){ echo " (".$IDtoUnit[$row['EMTid']].")";}
	    echo ') '.$row['description'].'<br />';
	}
    }
}


function genCrewDiv()
{
    // returns the string of HTML for the current crew table
    global $positions, $local_conn, $shortName;
    // who to count as senior members
    $seniors = array("Senior" => "Senior");
    $final = "";
    $query = "SELECT s.*,r.FirstName,r.LastName,r.status,r.unitID FROM schedule AS s LEFT JOIN roster AS r ON s.EMTid=r.EMTid WHERE start_ts <= ".time()." AND end_ts >= ".time()." AND deprecated=0;";
    $result = mysql_query($query, $local_conn) or die("Error in Query: ".$query."\n Error: ".mysql_error($local_conn)."\n");
    if(mysql_num_rows($result) >= 1)
    {
	$final .= '<div class="memberSignons">'."\n";
	$final .= '<table class="signonTable">';
	$final .= '<tr><th>Member</th><th>Position</th><th>On Duty</th></tr>';
	while($row = mysql_fetch_assoc($result))
	{
	    $final .= '<tr>';
	    $final .= '<td>'.$row['LastName'].', '.$row['FirstName'].' ('.$row['EMTid'];
	    if($row['unitID'] != ""){ $final .= " / ".$row['unitID']."";}
	    $final .= ')</td>';
	    $final .= '<td>'.$positions[$row['status']].'</td>';
	    $final .= '<td>'.date("Hi", $row['start_ts']).'-'.date("Hi", $row['end_ts']).'</td>';
	    $final .= '</tr>';
	}
	$final .= '</table>';
	$final .= '</div>'."\n";
    }
    else
    {
	$final .= '<div class="memberSignons">'."\n".'<table class="signonTable">'."\n";
	$final .= '<tr><th>Member</th><th>Position</th><th>On Duty</th></tr>'."\n";
	$final .= '<tr><td colspan="3">No members currently on duty.</td></tr>'."\n";
	$final .= '</table></div>'."\n";
    }
    return $final;
}

function genCrewWAPDiv()
{
    // returns the string of HTML for the current crew table
    global $positions, $local_conn, $shortName;
    // who to count as senior members
    $seniors = array("Senior" => "Senior");
    $query = "SELECT s.*,r.FirstName,r.LastName,r.status,r.unitID FROM schedule AS s LEFT JOIN roster AS r ON s.EMTid=r.EMTid WHERE start_ts <= ".time()." AND end_ts >= ".time()." AND deprecated=0;";
    $result = mysql_query($query, $local_conn) or die("Error in Query: ".$query."\n Error: ".mysql_error($local_conn)."\n");
    if(mysql_num_rows($result) >= 1)
    {
	echo "<h2>Current Crew:</h2>\n";
	while($row = mysql_fetch_assoc($result))
	{
	    echo $row['LastName'].', '.$row['FirstName'].' ('.$row['EMTid'];
	    if($row['unitID'] != ""){ echo " / ".$row['unitID']."";}
	    echo ') '.$positions[$row['status']].' '.date("Hi", $row['start_ts']).'-'.date("Hi", $row['end_ts']).'<br />'."\n";
	}
    }
    else
    {
	echo '<h2>No Duty Crew.</h2>'."\n";
    }
    return $final;
}

function genSchedDiv()
{
    global $positions, $local_conn, $shortName, $dayFirstHour, $nightFirstHour;
    // get the full crew for this shift
    $final = "";
    if(date("H") < $dayFirstHour)
    {
	// night shift
	$thisShiftStart = strtotime(date("Y-m-d")." ".$nightFirstHour.":00:00")-86400;
	$shiftString = date("m/d", $thisShiftStart)." Night";
	$nextShiftString = date("m/d")." Day";
    }
    elseif(date("H") >= $nightFirstHour)
    {
	// night shift
	$thisShiftStart = strtotime(date("Y-m-d")." ".$nightFirstHour.":00:00");
	$shiftString = date("m/d", $thisShiftStart)." Night";
	$nextShiftString = date("m/d", time()+86400)." Day";
    }
    else
    {
	// day shift
	$thisShiftStart = strtotime(date("Y-m-d")." ".$dayFirstHour.":00:00");
	$shiftString = date("m/d")." Day";
	$nextShiftString = date("m/d")." Night";
    }

    $thisShiftSignons = array();
    $query = "SELECT s.*,r.FirstName,r.LastName,r.status,r.unitID FROM schedule AS s LEFT JOIN roster AS r ON s.EMTid=r.EMTid WHERE start_ts >= ".$thisShiftStart." AND end_ts <= ".($thisShiftStart + 43200)." AND deprecated=0 ORDER BY start_ts ASC;";
    $result = mysql_query($query, $local_conn) or die("Error in Query: ".$query."\n Error: ".mysql_error($local_conn)."\n");
    while($row = mysql_fetch_assoc($result))
    {
	$member = $row['LastName'].", ".$row['FirstName']." (".$row['EMTid'];
	if($row['unitID'] != ""){ $member .= " / ".$row['unitID']."";}	
	$member .= ")";
	$position = $positions[$row['status']];
	$time = date("Hi", $row['start_ts']).'-'.date("Hi", $row['end_ts']);
	$thisShiftSignons[] = array("member" => $member, "position" => $position, "time" => $time);
    }
    
    $nextShiftStart = $thisShiftStart + 43200;
    $nextShiftSignons = array();
    $query = "SELECT s.*,r.FirstName,r.LastName,r.status,r.unitID FROM schedule AS s LEFT JOIN roster AS r ON s.EMTid=r.EMTid WHERE start_ts >= ".$nextShiftStart." AND end_ts <= ".($nextShiftStart + 43200)." AND deprecated=0 ORDER BY start_ts ASC;";
    $result = mysql_query($query, $local_conn) or die("Error in Query: ".$query."\n Error: ".mysql_error($local_conn)."\n");
    while($row = mysql_fetch_assoc($result))
    {
	$member = $row['LastName'].", ".$row['FirstName']." (".$row['EMTid'];
	if($row['unitID'] != ""){ $member .= " / ".$row['unitID']."";}	
	$member .= ")";
	$position = $positions[$row['status']];
	$time = date("Hi", $row['start_ts']).'-'.date("Hi", $row['end_ts']);
	$nextShiftSignons[] = array("member" => $member, "position" => $position, "time" => $time);
    }
    
    // find the nax of the two arrays
    if(count($thisShiftSignons) > count($nextShiftSignons)){ $max = count($thisShiftSignons);} else { $max = count($nextShiftSignons);}
    
    // this shift and the next shift
    $final .= '<table class="allSignons">';
    $final .= '<tr><th colspan="3">'.'This Shift<br />'.$shiftString.'</th><th colspan="3">'.'Next Shift <br />'.$nextShiftString.'</th></tr>';
    $final .= '<tr><th>Member</th><th>Position</th><th>On Duty</th><th>Member</th><th>Position</th><th>On Duty</th></tr>';
    for($i = 0; $i < $max; $i++)
    {
	$final .= '<tr>';
	if(array_key_exists($i, $thisShiftSignons))
	{
	    $final .= '<td>'.$thisShiftSignons[$i]["member"].'</td>';
	    $final .= '<td>'.$thisShiftSignons[$i]["position"].'</td>';
	    $final .= '<td>'.$thisShiftSignons[$i]["time"].'</td>';
	}
	else
	{
	    $final .= '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
	}
	if(array_key_exists($i, $nextShiftSignons))
	{
	    $final .= '<td>'.$nextShiftSignons[$i]["member"].'</td>';
	    $final .= '<td>'.$nextShiftSignons[$i]["position"].'</td>';
	    $final .= '<td>'.$nextShiftSignons[$i]["time"].'</td>';
	}
	else
	{
	    $final .= '<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>';
	}
	$final .= '</tr>'."\n";
    }
    $final .= '</table>';
    return $final;
}

function genReminderDiv()
{
    return 'If you experience any problems, please notify Jason at (201)906-7347.<br /><strong>call <a href="tel:2014787160">201-478-7160</a> to respond or <a href="respond.php">click here</a></strong><br />#1 to building, #2 to scene, #3 at HQ now, #4 cancel response';
}

// added 2009-12-14
function is_local_user()
{
    $foo = $_SERVER['REMOTE_ADDR'];
    if(substr($foo, 0, 10) == "192.168.1.")
    {
	return true;
    }
    return false;
}
// end added 2009-12-14

// added 2010-03-13
function genNextRigDiv()
{
    global $local_conn;
    $query = "SELECT unit,(MAX(end_mileage)-MIN(end_mileage)) AS mileage,COUNT(*) AS count FROM calls_units WHERE is_deprecated=0 GROUP BY unit;";
    $result = mysql_query($query, $local_conn) or die("Error in Query: ".$query."\n Error: ".mysql_error($local_conn)."\n");

    // vars to store data
    $maxMiles = 0;
    $maxMilesUnit = "";
    $minMiles = 1000000; 
    $minMilesUnit = "";
    $maxCalls = 0;
    $maxCallsUnit = "";
    $minCalls = 1000000;
    $minCallsUnit = "";
    while($row = mysql_fetch_assoc($result))
    {
	if($row['mileage'] > $maxMiles){ $maxMiles = $row['mileage']; $maxMilesUnit = $row['unit'];}
	if($row['mileage'] < $minMiles){ $minMiles = $row['mileage']; $minMilesUnit = $row['unit'];}
	if(isset($row['calls']))
	{
	    if($row['calls'] > $maxCalls){ $maxCalls = $row['calls']; $maxCallsUnit = $row['unit'];}
	    if($row['calls'] < $minCalls){ $minCalls = $row['calls']; $minCallsUnit = $row['unit'];}
	}
    }

    $str = "Next out rig: <strong>";
    $str2 = "</strong>";
    
    if($minCallsUnit == $minMilesUnit){ return $str.$minCallsUnit.$str2;}

    // if miles are off by more than 10%
    if($maxMiles >= ($minMiles + ($minMiles * .1))) { return $str.$minMilesUnit.$str2;}
    
    return $str.$minCallsUnit.$str2;
}
// end added 2010-03-13

?>