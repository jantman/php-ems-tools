<html>
<head>
<?php
//(C) 2006 Jason Antman. All Rights Reserved.
// with questions, go to www.jasonantman.com
// or email jason AT jasonantman DOT com
// Time-stamp: "2007-09-13 16:18:18 jantman"

//This software may not be copied, altered, or distributed in any way, shape, form, or means.
// version: 2.0 as of 2006-10-3

// rigCheck.php
// page to do rig checks
// see custom.php for more information - specifically rigCheckData variable


require('./config/config.php');
global $shortName;
echo '<title>'.$shortName.' Rig Check</title>';
echo '<link rel="stylesheet" href="'.$serverWebRoot.'php_ems.css" type="text/css">'; // the location of the CSS file for the schedule

?>
</head>
<body>

<?php
if(! empty($_GET['index']))
{
    $rigIndex = $_GET['index'];
}

global $rigChecks;
$rigCheckData = $rigChecks[$rigIndex]['data'];
$table2start = $rigChecks[$rigIndex]['table2start'];
$table3start = $rigChecks[$rigIndex]['table3start'];
$rigNum = $rigChecks[$rigIndex]['name'];

echo '<h3 align=center>'.$shortName.' Rig Check - '.$rigNum.'</h3>';


showTable($rigCheckData, $table2start, $table3start, $rigNum);

function showTable($rigCheckData, $table2start, $table3start, $rigNum)
{
    echo '<form method="post" action="doRigCheck.php">';
    echo '<DIV align="center"><b>Crew: </b><input type="text" name="crew1" size=3> <input type="text" name="crew2" size=3> <input type="text" name="crew3" size=3> <input type="text" name="crew4" size=3>';
    echo '<b>&nbsp;&nbsp;Rig:&nbsp;</b>';
    echo '<input type=hidden name="rig" value="'.$rigNum.'">';
    echo $rigNum."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    echo '<b>&nbsp;&nbsp;Mileage:&nbsp;</b>';
    echo '<input type="text" name="mileage" size=6>';
    echo '</DIV><br>';
    echo '<table border=1 align=center>';
    echo '<tr>';
    echo '<td>';
    // show table 1
    echo '<table>';
    echo '<tr><td>&nbsp;</td><td>OK</td><td>NG</td></tr>';
    for($i = 0; $i < $table2start; $i++)
    {
	echo '<tr><td><b>'.$rigCheckData[$i]['name'].'</b></td></tr>';
	$items = $rigCheckData[$i]['items'];
	for($c = 0; $c < count($items); $c++)
	{
	    echo '<tr><td>&nbsp;&nbsp;&nbsp; '.$items[$c].'</td>';
	    echo '<td><input type="radio" name="check['.$i.']['.$c.']" value="OK"></td>'; // OK
	    echo '<td><input type="radio" name="check['.$i.']['.$c.']" value="NG" checked="yes"></td>'; // NG
	    echo '<tr>';
	}
    }
    echo '</table>';

    echo '</td><td>';
    // show table 2
    echo '<table>';
    echo '<tr><td>&nbsp;</td><td>OK</td><td>NG</td></tr>';
    for($i = $table2start; $i < $table3start; $i++)
    {
	echo '<tr><td><b>'.$rigCheckData[$i]['name'].'</b></td></tr>';
	$items = $rigCheckData[$i]['items'];
	for($c = 0; $c < count($items); $c++)
	{
	    echo '<tr><td>&nbsp;&nbsp;&nbsp; '.$items[$c].'</td>';
	    echo '<td><input type="radio" name="check['.$i.']['.$c.']" value="OK"></td>'; // OK
	    echo '<td><input type="radio" name="check['.$i.']['.$c.']" value="NG" checked="yes"></td>'; // NG
	    echo '<tr>';
	}
    }
    echo '</table>';
    echo '</td><td>';
    // show table 3
    echo '<table>';
    echo '<tr><td>&nbsp;</td><td>OK</td><td>NG</td></tr>';
    for($i = $table3start; $i < count($rigCheckData); $i++)
    {
	echo '<tr><td><b>'.$rigCheckData[$i]['name'].'</b></td></tr>';
	$items = $rigCheckData[$i]['items'];
	for($c = 0; $c < count($items); $c++)
	{
	    echo '<tr><td>&nbsp;&nbsp;&nbsp; '.$items[$c].'</td>';
	    echo '<td><input type="radio" name="check['.$i.']['.$c.']" value="OK"></td>'; // OK
	    echo '<td><input type="radio" name="check['.$i.']['.$c.']" value="NG" checked="yes"></td>'; // NG
	    echo '<tr>';
	}
    }
    echo '</table>';

    echo '</td></tr></table>';
    echo '<br>';
    echo '<DIV align="center">';
    echo '<b>Comments / Items Replaced:</b><br>';
    echo '<textarea name="comments" rows="4" cols="70"></textarea><br>';
    echo '<b>Items Still Broken / Items Un-Replaceable:</b><br>';
    echo '<textarea name="stillBroken" rows="4" cols="70"></textarea><br>';
    echo '<br>';
    echo '<b>Signature ID: </b><input type="text" name="sigID" size=3>';
    echo '</DIV>';
    echo '<p>';
    echo '<input type="submit" /><input type="reset" />';
    echo '</p>';

    echo '</form>';
}

?>