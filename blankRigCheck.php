<html>
<head>
<?php
//(C) 2006 Jason Antman. All Rights Reserved.
// with questions, go to www.jasonantman.com
// or email jason AT jasonantman DOT com
// Time-stamp: "2006-11-27 20:46:28 jantman"

//This software may not be copied, altered, or distributed in any way, shape, form, or means.
// version: 2.0 as of 2006-10-3

// rigCheck.php
// page to do rig checks
// see custom.php for more information - specifically rigCheckData variable


require('custom.php');
global $shortName;
echo '<title>'.$shortName.' Rig Check</title>';
echo '<link rel="stylesheet" href="'.$serverWebRoot.'php_ems.css" type="text/css">'; // the location of the CSS file for the schedule
?>
</head>
<body>

<?php
echo '<h3 align=center>'.$shortName.' Rig Check</h3>';


global $rigCheckData;
global $table2start;
global $table3start;

showTable($rigCheckData, $table2start, $table3start);

function showTable($rigCheckData, $table2start, $table3start)
{
    echo '<form method="post" action="doRigCheck.php">';
    echo '<DIV align="center"><b>Crew: </b>____&nbsp;&nbsp;&nbsp;____&nbsp;&nbsp;&nbsp;____&nbsp;&nbsp;&nbsp;____';
    echo '<b>&nbsp;&nbsp;Rig:&nbsp;</b>';
    echo '_______';
    echo '<b>&nbsp;&nbsp;Mileage:&nbsp;</b>';
    echo '__________';
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
     echo '<b>Comments / Items Replaced:</b><br>';
     echo '<br>';
    echo '<b>Items Still Broken / Items Un-Replaceable:</b><br>';
    echo '<br>';
    echo '<br>';
    echo '<b>Signautre: _______________________________ ID:</b>&nbsp;_________';

    echo '</form>';
}

?>