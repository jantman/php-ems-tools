<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="php_ems.css" type="text/css">
<title>Schedule SignOn</title>
</head>
<body>
<form action="handlers/signOn.php" method="post" name="signOnForm" id="signOnForm">


<?php 
//
// signOn.php
//
// this is the pop-up signon form for the schedule
// all this does is show the form and allow it to be submitted via JS
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


// TODO - Note - Removed all QuickForm stuff

/*
TODO - DEBUG
What we need to do:
Convert this to a relatively static HTML form with options and values generated by PHP.
The SUBMIT action will call a JS function, which will pull most of the information from DOM and send it to the handler, then evaluate the result and either display an error message, or somehow refresh the calendar.
*/

// this file will import the user's customization
require_once('./config/config.php');

// schedule configuration
require_once('./config/scheduleConfig.php');

// for email notifications:
require_once('./inc/notify.php');
require_once('./inc/global.php');
require_once('./inc/logging.php');

$hiddenItems = ""; // variable to hols the hidden items

// get the URL variables
if(! empty($_GET['ts']))
{
    $ts = ((int)$_GET['ts']);
    $hiddenItems .= '<input name="ts" type="hidden" value="'.$ts.'" />';
}

if(! empty($_GET['id']))
{
    $signonID = ((int)$_GET['id']);
    $hiddenItems .= '<input name="id" type="hidden" value="'.$id.'" />';
}


// parse out the year, month, date, and shift
$year = date("Y", $ts);
$month = date("m", $ts);
$date = date("d", $ts);
$shift = tsToShiftName($ts);

//start working with the form
$conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.".$errorMsg);
mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!".$errorMsg);
$query = 'SELECT * FROM '.$config_sched_table.' WHERE sched_entry_id='.$signonID.';';
$result = mysql_query($query);
if(mysql_num_rows($result) < 1)
{
    $action = "signOn";
    $showDefaults = false;
}
else
{
    $row = mysql_fetch_array($result) or die("Error fetching result for defaults.");
    $showDefaults = true;
}

echo '<div>'."\n";
echo $hiddenItems."\n";
echo '<table border="0">'."\n\n";
echo '	<tr>'."\n";
echo '		<td style="white-space: nowrap; background-color: #CCCCCC;" align="left" valign="top" colspan="2"><b>'.$year."-".$month."-".$date." ".$shift.'</b></td>'."\n".'	</tr>'."\n";

//
// "Action" radio buttons
//
if(isset($_GET['id']))
{
// id is set, default to edit
echo '	<tr>
		<td align="right" valign="top"><b>'.$i18n_strings["signOn"]["Action"].':</b></td>
		<td valign="top" align="left"><input name="action" value="signOn" type="radio" id="action" /><label for="action">'.$i18n_strings["signOn"]["Sign On"].'</label></td>
	</tr>
	<tr>
		<td align="right" valign="top"><b></b></td>
		<td valign="top" align="left"><input name="action" value="edit" type="radio" id="action" checked="checked" /><label for="action">'.$i18n_strings["signOn"]["Edit"].'</label></td>
	</tr>
	<tr>
		<td align="right" valign="top"><b></b></td>
		<td valign="top" align="left"><input name="action" value="remove" type="radio" id="action" /><label for="action">'.$i18n_strings["signOn"]["Remove"].'</label></td>
	</tr>';
}
else
{
    // default to signon
echo '	<tr>
		<td align="right" valign="top"><b>Action:</b></td>
		<td valign="top" align="left"><input name="action" value="signOn" type="radio" id="action" checked="checked" /><label for="action">'.$i18n_strings["signOn"]["Sign On"].'</label></td>
	</tr>
	<tr>
		<td align="right" valign="top"><b></b></td>
		<td valign="top" align="left"><input name="action" value="edit" type="radio" id="action" /><label for="action">'.$i18n_strings["signOn"]["Edit"].'</label></td>
	</tr>
	<tr>
		<td align="right" valign="top"><b></b></td>
		<td valign="top" align="left"><input name="action" value="remove" type="radio" id="action" /><label for="action">'.$i18n_strings["signOn"]["Remove"].'</label></td>
	</tr>';
}

echo '	<tr>
		<td align="right" valign="top"><b>'.$i18n_strings["signOn"]["ID Num"].'</b></td>';
//
// EMTid text box
//
if(isset($row) && isset($row['EMTid']))
{
    echo '		<td valign="top" align="left"><input size="10" maxlength="5" name="EMTid" type="text" value="'.$row['EMTid'].'" /></td>';
}
else
{
    echo '		<td valign="top" align="left"><input size="10" maxlength="5" name="EMTid" type="text" /></td>';
}

//
// TIME SELECTS
//
echo '	</tr>
	<tr>
		<td align="right" valign="top"><b>'.$i18n_strings["signOn"]["Start Time"].':</b></td>
		<td valign="top" align="left"><select name="start">';

for($i = $ts; $i < ($ts + 43200); $i += 1800)
{
    if($i == $ts)
    {
	// first one is default
	echo '	<option value="'.date("H:i:s", $i).'" selected="selected">'.date("H:i:s", $i).'</option>'."\n";
    }
    else
    {
	echo '	<option value="'.date("H:i:s", $i).'">'.date("H:i:s", $i).'</option>'."\n";
    }
}

echo '</select></td>
	</tr>
	<tr>
		<td align="right" valign="top"><b>'.$i18n_strings["signOn"]["End Time"].':</b></td>
		<td valign="top" align="left"><select name="end">';

for($i = $ts + 1800; $i < ($ts + 43200); $i += 1800)
{
    echo '	<option value="'.date("H:i:s", $i).'">'.date("H:i:s", $i).'</option>'."\n";
    $lastTS = $i + 1800;
}
echo '	<option value="'.date("H:i:s", $lastTS).'" selected="selected">'.date("H:i:s", $lastTS).'</option>'."\n";

echo '</select></td>
	</tr>
	<tr>
		<td align="right" valign="top"><b></b></td>';

// BUTTONS
echo '		<td valign="top" align="left"><input name="buttonGroup[btnReset]" value="'.$i18n_strings["signOn"]["Reset"].'" type="reset" />    <input name="buttonGroup[btnSubmit]" value="'.$i18n_strings["signOn"]["Submit"].'" type="submit" /></td>';


echo '	</tr>
	<tr>
		<td align="right" valign="top"><b><br><br></b></td>
		<td valign="top" align="left"></td>
	</tr>
	<tr>
		<td style="white-space: nowrap; background-color: #CCCCCC;" align="left" valign="top" colspan="2"><b>'.$i18n_strings["signOn"]["changingPast"].':</b></td>
	</tr>
	<tr>
		<td align="right" valign="top"><b>'.$i18n_strings["signOn"]["adminID"].'</b></td>
		<td valign="top" align="left"><input size="10" maxlength="5" name="adminID" type="text" /></td>
	</tr>
	<tr>
		<td align="right" valign="top"><b>'.$i18n_strings["signOn"]["adminPW"].'</b></td>
		<td valign="top" align="left"><input size="10" maxlength="10" name="adminPW" type="password" /></td>
	</tr>
</table>
</div>';

?>

</form> 
</body>
</html>