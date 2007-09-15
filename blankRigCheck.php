<html>
<head>
<?php
// blankRigCheck.php
//
// Page to display a blank rig check form for printing.
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
    echo '<DIV align="center"><b>Crew: </b>____&nbsp;&nbsp;&nbsp;____&nbsp;&nbsp;&nbsp;____&nbsp;&nbsp;&nbsp;____';
    echo '<b>&nbsp;&nbsp;Rig:&nbsp;</b>';
    echo $rigNum.'&nbsp;&nbsp;&nbsp;&nbsp;';
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