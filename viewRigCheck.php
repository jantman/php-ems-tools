<html>
<head>
<?php
//(C) 2006 Jason Antman. All Rights Reserved.
// with questions, go to www.jasonantman.com
// or email jason AT jasonantman DOT com
// Time-stamp: "2006-11-27 20:46:28 jantman"

//This software may not be copied, altered, or distributed in any way, shape, form, or means.
// version: 2.0 as of 2006-10-3

// doRigCheck.php
// page to do rig checks
// see custom.php for more information - specifically rigCheckData variable


require('custom.php');
require('global.php');
global $shortName;
$key = $_GET['pKey'];
echo '<title>'.$shortName.' Rig Check '.$key.'</title>';
echo '<link rel="stylesheet" href="'.$serverWebRoot.'php_ems.css" type="text/css">'; // the location of the CSS file for the schedule

global $dbName;
$conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.".$errorMsg);
mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!".$errorMsg);

$query = 'SELECT * FROM rigCheck WHERE pKey='.$key.';';

$result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query.<br><br>" . mysql_error().'<br><br>'.$errorMsg);
$row = mysql_fetch_array($result);
mysql_free_result($result);
mysql_close($conn); 

$OKa = explode(',', $row['OK']);
$NGa = explode(',', $row['NG']);

$check = makeArray($OKa, $NGa);

//echo '<pre>';
//echo var_dump($check);
//echo '</pre>';

echo '</head>';
echo '<body>';

echo '<h3 align=center>'.$shortName.' Rig Check '.$key.'</h3>';

$time = $row['time'];

global $rigCheckData;
global $table2start;
global $table3start;

showTable($rigCheckData, $table2start, $table3start, $check, $row);

function showTable($rigCheckData, $table2start, $table3start, $check, $row)
{
    global $crewStr;
    $crewStr = '&nbsp;&nbsp;'.$row['crew1']." ".$row['crew2']." ".$row['crew3']." ".$row['crew4'].'&nbsp;';
    echo '<DIV align="center"><b>Crew: </b>'.$crewStr;
    echo '<b>&nbsp;&nbsp;Rig:&nbsp;</b>';
    echo $row['rig'];
    echo '<b>&nbsp;&nbsp;Mileage:&nbsp;</b>';
    echo $row['mileage'];
    echo '<b>&nbsp;Date:&nbsp;</b>';
    $time = $row['timeStamp'];
    echo date("Y-m-d", $time);
    echo '&nbsp;&nbsp;<b>Time:</b>&nbsp;'.date("H:i", $time);
    echo '</DIV><br>';
    echo '<table border=1 align=center>';
    echo '<tr>';
    echo '<td>';
    // show table 1
    echo '<table>';
    for($i = 0; $i < $table2start; $i++)
    {
	echo '<tr><td><b>'.$rigCheckData[$i]['name'].'</b></td></tr>';
	$items = $rigCheckData[$i]['items'];
	for($c = 0; $c < count($items); $c++)
	{
	    echo '<tr><td>&nbsp;&nbsp;&nbsp; '.$items[$c].'</td>';
	    echo '<td>';
	    if($check[$i][$c] == "NG")
	    {
		echo '<b><u>NG</b></u>';
	    }
	    else
	    {
		echo 'OK';
	    }
	    echo '</td>';
	    echo '<tr>';
	}
    }
    echo '</table>';

    echo '</td><td>';
    // show table 2
    echo '<table>';
    for($i = $table2start; $i < $table3start; $i++)
    {
	echo '<tr><td><b>'.$rigCheckData[$i]['name'].'</b></td></tr>';
	$items = $rigCheckData[$i]['items'];
	for($c = 0; $c < count($items); $c++)
	{
	    echo '<tr><td>&nbsp;&nbsp;&nbsp; '.$items[$c].'</td>';
	    echo '<td>';
	    if($check[$i][$c] == "NG")
	    {
		echo '<b><u>NG</b></u>';
	    }
	    else
	    {
		echo 'OK';
	    }
	    echo '</td>';
	    echo '<tr>';
	}
    }
    echo '</table>';
    echo '</td><td>';
    // show table 3
    echo '<table>';
    for($i = $table3start; $i < count($rigCheckData); $i++)
    {
	echo '<tr><td><b>'.$rigCheckData[$i]['name'].'</b></td></tr>';
	$items = $rigCheckData[$i]['items'];
	for($c = 0; $c < count($items); $c++)
	{
	    echo '<tr><td>&nbsp;&nbsp;&nbsp; '.$items[$c].'</td>';
	    echo '<td>';
	    echo '<td>';
	    if($check[$i][$c] == "NG")
	    {
		echo '<b><u>NG</b></u>';
	    }
	    else
	    {
		echo 'OK';
	    }
	    echo '</td>';
	    echo '<tr>';
	}
    }
    echo '</table>';

    echo '</td></tr></table>';
    echo '<br>';
    echo '<DIV align="center">';
    echo '<b>Comments / Items Replaced:</b><br>';
    echo $row['comments'];
    echo '<br>';
    echo '<b>Items Still Broken / Items Un-Replaceable:</b><br>';
    echo $row['stillBroken'];
    echo '<br><br>';
    echo '<p>';
    echo '<b>Signautre: _______________________________ ID:</b>&nbsp;'.$row['sigID'];
    echo '</p>';
    echo '</DIV>';
    echo '</form>';
}

function makeArray($OKa, $NGa)
{
    $check = array();

    // break down into array
    global $rigCheckData;
    for($i = 0; $i < count($rigCheckData); $i++);
    {
	$temp = array();
	for($c = 0; $c < count($rigCheckData[$i]['items']); $c++)
	{
	    $temp[$c] = "";
	}
	$check[$i] = $temp;
    }
    // we now have an empty array that's ready.

    for($n = 0; $n < count($OKa); $n++)
    {
	if($OKa[$n] <> null)
	{
	    $str = $OKa[$n];
	    // get the $i and $c from the string
	    $i = substr($str, strpos($str, '[') + 1, (strpos($str, ']') - strpos($str, '[') - 1));
	    $c = substr($str, strrpos($str, '[') + 1, (strrpos($str, ']') - strrpos($str, '[') - 1));

	    // DEBUG
	    //echo 'i='.$i.' c='.$c.'<br>';
	    //echo $OKa[$n];
	    // END DEBUG

	    $check[$i][$c] = "OK";
	}
    }

    for($n = 0; $n < count($NGa); $n++)
    {
	if($NGa[$n] <> null)
	{
	    $str = $NGa[$n];
	    // get the $i and $c from the string
	    $i = substr($str, strpos($str, '[') + 1, (strpos($str, ']') - strpos($str, '[') - 1));
	    $c = substr($str, strrpos($str, '[') + 1, (strrpos($str, ']') - strrpos($str, '[') - 1));

	    // DEBUG
	    //echo 'i='.$i.' c='.$c.'<br>';
	    //echo $OKa[$n];
	    // END DEBUG

	    $check[$i][$c] = "NG";
	}
    }

    return $check;
}

?>
</body>
</html>