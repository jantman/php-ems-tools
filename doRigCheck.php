<html>
<head>
<?php
// doRigCheck.php
//
// Script to handle the output from the rig check form and put it in the DB.
//
// +----------------------------------------------------------------------+
// | PHP EMS Tools      http://www.php-ems-tools.com                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006, 2007 Jason Antman.                               |
// |                                                                      |
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation; either version 3 of the License, or    |
// | (at your option) any later version.                                  |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to:                           |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+
// |Please use the above URL for bug reports and feature/support requests.|
// +----------------------------------------------------------------------+
// | Authors: Jason Antman <jason@jasonantman.com>                        |
// +----------------------------------------------------------------------+
//      $Id$

require('./config/config.php');
require('./inc/global.php');
global $shortName;
echo '<title>'.$shortName.' Rig Check</title>';
echo '<link rel="stylesheet" href="'.$serverWebRoot.'php_ems.css" type="text/css">'; // the location of the CSS file for the schedule

global $dbName;
$conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.".$errorMsg);
mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!".$errorMsg);

if(! idInDB($_REQUEST['crew1']))
{
    die("ERROR: Crew 1 ID is not valid. Please go Back and fix it.");
}
if(($_REQUEST['crew2'] <> null) && (! idInDB($_REQUEST['crew2'])))
{
    die("ERROR: Crew 2 ID is not valid. Please go Back and fix it.");
}
if(($_REQUEST['crew3'] <> null) && (! idInDB($_REQUEST['crew3'])))
{
    die("ERROR: Crew 3 ID is not valid. Please go Back and fix it.");
}
if(($_REQUEST['crew4'] <> null) && (! idInDB($_REQUEST['crew4'])))
{
    die("ERROR: Crew 4 ID is not valid. Please go Back and fix it.");
}
if(($_REQUEST['mileage'] == null) || ($_REQUEST['mileage'] == 0))
{
    die("ERROR: Mileage is not valid. Please go Back and fix it.");
}
if(! idInDB($_REQUEST['sigID']))
{
    die("ERROR: Signature ID is not valid. Please go Back and fix it.");
}

echo '</head>';
echo '<body>';

echo '<h3 align=center>'.$shortName.' Rig Check</h3>';

$time = time();

// code introduced to handle different items for different rigs
$rigNum = $_REQUEST['rig']; // get the rig number
$rigIndex = '';
global $rigChecks;

foreach($rigChecks as $idx => $arr)
{
    if($arr['name'] == $rigNum)
    {
	$rigIndex = $idx;
    }
}

$rigCheckData = $rigChecks[$rigIndex]['data'];
$table2start = $rigChecks[$rigIndex]['table2start'];
$table3start = $rigChecks[$rigIndex]['table3start'];
// DONE with implementing the multi-rig stuff

putToDB();

showTable($rigCheckData, $table2start, $table3start);

function showTable($rigCheckData, $table2start, $table3start)
{
    global $time;
    echo '<DIV align="center"><b>Crew: </b>&nbsp;&nbsp;'.$_REQUEST['crew1']." ".$_REQUEST['crew2']." ".$_REQUEST['crew3']." ".$_REQUEST['crew4'].'&nbsp;';
    echo '<b>&nbsp;&nbsp;Rig:&nbsp;</b>';
    echo $_REQUEST['rig'];
    echo '<b>&nbsp;&nbsp;Mileage:&nbsp;</b>';
    echo $_REQUEST['mileage'];
    echo '<b>&nbsp;Date:&nbsp;</b>';
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
	    if($_REQUEST['check'][$i][$c] == "NG")
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
	    if($_REQUEST['check'][$i][$c] == "NG")
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
	    if($_REQUEST['check'][$i][$c] == "NG")
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
    echo $_REQUEST['comments'];
    echo '<br>';
    echo '<b>Items Still Broken / Items Un-Replaceable:</b><br>';
    echo $_REQUEST['stillBroken'];
    echo '<br><br>';
    echo '<p>';
    echo '<b>Signautre: _______________________________ ID:</b>&nbsp;'.$_REQUEST['sigID'];
    echo '</p>';
    echo '</DIV>';
    echo '</form>';
}

function putToDB()
{
    $query = "INSERT into rigCheck SET ";
    $query .= 'crew1="'.$_REQUEST['crew1'].'",';
    $query .= 'crew2="'.$_REQUEST['crew2'].'",';
    $query .= 'crew3="'.$_REQUEST['crew3'].'",';
    $query .= 'crew4="'.$_REQUEST['crew4'].'",';
    $query .= 'mileage='.$_REQUEST['mileage'].',';
    global $time;
    $query .= 'timeStamp='.$time.',';
    $query .= 'rig="'.$_REQUEST['rig'].'",';
    $query .= 'comments="'.$_REQUEST['comments'].'",';
    $query .= 'stillBroken="'.$_REQUEST['stillBroken'].'",';
    $query .= 'sigID="'.$_REQUEST['sigID'].'",';

    $OK = "";
    $NG = "";

    global $rigCheckData;

    for($i = 0; $i < count($rigCheckData); $i++)
    {
	$items = $rigCheckData[$i]['items'];
	for($c = 0; $c < count($items); $c++)
	{
	    if($_REQUEST['check'][$i][$c] == "NG")
	    {
		$NG .= 'check['.$i.']['.$c.'],';
	    }
	    elseif($_REQUEST['check'][$i][$c] == "OK")
	    {
		$OK .= 'check['.$i.']['.$c.'],';
	    }
	}
    }

    $OK = rtrim($OK, ",");
    $NG = rtrim($NG, ",");

    $query .= 'OK="'.$OK.'",NG="'.$NG.'"';
    $query .= ';';

    $result = mysql_query($query) or die ("Query Error: ".mysql_error());
    mysql_free_result($result);
}

?>
</body>
</html>