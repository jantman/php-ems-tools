<html>
<head>
<?php
//(C) 2006 Jason Antman. All Rights Reserved.
// with questions, go to www.jasonantman.com
// or email jason AT jasonantman DOT com
// Time-stamp: "2006-11-27 20:46:28 jantman"

//This software may not be copied, altered, or distributed in any way, shape, form, or means.
// version: 2.0 as of 2006-10-3

// viewRigChecks.php
// page to do rig checks
// see custom.php for more information - specifically rigCheckData variable


require('custom.php');
require('global.php');
global $shortName;
$key = $_GET['pKey'];
echo '<title>'.$shortName.' Rig Checks</title>';
echo '<link rel="stylesheet" href="'.$serverWebRoot.'php_ems.css" type="text/css">'; // the location of the CSS file for the schedule

global $dbName;

echo '</head>';
echo '<body>';

echo '<h3 align=center>'.$shortName.' Rig Checks</h3>';

global $rigCheckData;
global $table2start;
global $table3start;


$conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.".$errorMsg);
mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!".$errorMsg);
$query = 'SELECT * FROM rigCheck;';
$result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query.<br><br>" . mysql_error().'<br><br>'.$errorMsg);

// setup table
?>
<table border=1>
<tr>
<td>Number</td>
<td>Date</td>
<td>Time</td>
<td>Rig</td>
<td>Crew</td>
<td>Signature</td>
</tr>
<?php

while($row = mysql_fetch_array($result))
{
    echo '<tr>';
    echo '<td><a href="viewRigCheck.php?pKey='.$row['pKey'].'">'.$row['pKey'].'</a></td>';
    echo '<td>'.date('Y-m-d', $row['timeStamp']).'</td>';
    echo '<td>'.date('H:i', $row['timeStamp']).'</td>';
    echo '<td>'.$row['rig'].'</td>';
    echo '<td>'.$row['crew1'].'&nbsp;&nbsp;'.$row['crew2'].'&nbsp;&nbsp;'.$row['crew3'].'&nbsp;&nbsp;'.$row['crew4'].'</td>';
    echo '<td>'.$row['sigID'].'</td>';
    echo '</tr>';
}

mysql_free_result($result);
mysql_close($conn); 

echo '</table>';

?>
</body>
</html>