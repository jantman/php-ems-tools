<html>
<?php
// Mutual Aid Log (per-year)
// Time-stamp: "2008-04-05 01:04:33 jantman"
// $Id: mutual_aid_log.php,v 1.1 2008/04/19 01:33:58 jantman Exp jantman $

require_once('custom.php'); // include config stuff
// need $dbName

// connect to DB
mysql_connect() or die("I'm sorry but I cannot connect to the MySQL server. Error: ".mysql_error());
mysql_select_db($dbName) or die("I'm sorry but there was an error selecting the DB. Error: ".mysql_error());

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
$query = "SELECT RunNumber,Date,TimeDisp,Unit,MA,MAto,CallType,OC FROM calls WHERE MA=1 AND YEAR(Date)=".$year.";";
$result = mysql_query($query) or die("MySQL Query Error: ".mysql_error());
$num_results = mysql_num_rows($result);
?>
<head>
<title>MPAC Mutual Aid Log</title>
<link rel="stylesheet" href="mutual_aid.css" type="text/css">
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
<th>Rig</th>
</tr>
<?php

while($row = mysql_fetch_assoc($result))
{
    echo '<tr>';
    echo '<td>'.$row['RunNumber'].'</td>';
    echo '<td>'.$row['Date'].'</td>';
    echo '<td>'.$row['TimeDisp'].'</td>';
    echo '<td>'.$row['MAto'].'</td>';
    echo '<td>'.$row['CallType'].'</td>';
    // canceled
    if($row['OC'] == "Cancelled"){ echo '<td><font color="red">YES</font></td>';} else { echo "<td>No</td>";}
    // crew
    $crewArr = getMembersOnCall($row['RunNumber']);
    ksort($crewArr);
    $crewStr = "";
    foreach($crewArr as $id){ $crewStr .= $id." ";}
    echo '<td>'.$crewStr.'</td>';

    // outcome
    switch ($row['OC'])
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
        case "Cancelled":
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
    // rig
    echo '<td>'.$row['Unit'].'</td>';
    echo '</tr>'."\n";
}
mysql_free_result($result);
// done with the table, close it
?>
</table>
<?php
// utility functions
function getMembersOnCall($runNum)
{
    global $dbName;

    $query2 = "SELECT * FROM calls WHERE RunNumber='".$runNum."';";

    $res = mysql_query($query2) or die("MySQL Query Error: ".mysql_error());
    $r = mysql_fetch_array($res);
    
    $memb[$r['DriverToScene']] = $r['DriverToScene'];
    $memb[$r['DriverToHosp']] = $r['DriverToHosp'];
    $memb[$r['DriverToBldg']] = $r['DriverToBldg'];
    $memb[$r['crew1']] = $r['crew1'];
    $memb[$r['crew2']] = $r['crew2'];
    $memb[$r['crew3']] = $r['crew3'];
    $memb[$r['crew4']] = $r['crew4'];
    $memb[$r['crew5']] = $r['crew5'];
    $memb[$r['crew6']] = $r['crew6'];

    mysql_free_result($result);

    return $memb;
}
?>
</body>
</html>