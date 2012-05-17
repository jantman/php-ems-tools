<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link rel="stylesheet" href="php_ems.css" type="text/css">
<title>Schedule Daily Message</title>
</head>
<body>
<form name="message_form">

<?php 
// dailyMessage.php
//
// Form to add/edit daily messages on the schedule.
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
// | $LastChangedRevision:: 155                                         $ |
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/dailyMessage.p#$ |
// +----------------------------------------------------------------------+



require_once ('./config/config.php'); // main configuration

require_once('./config/scheduleConfig.php'); // schedule configuration

// for i18n
require_once('./inc/'.$config_i18n_filename);

// get the URL variables
$hiddenItems = ""; // variable to hols the hidden items

// get the URL variables
if(! empty($_GET['ts']))
{
    $ts = ((int)$_GET['ts']);
    echo '<input name="ts" type="hidden" value="'.$ts.'" id="ts" />';
}

$message = "";
$shiftID = "";
if(! empty($_GET['id']) && ((int)$_GET['id']) != -1)
{
    $id = ((int)$_GET['id']);
    echo '<input name="id" type="hidden" value="'.$id.'" id="id" />';
    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.");
    mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!");
    $query = 'SELECT message_text FROM '.$config_sched_message_table.' WHERE sched_message_id='.$id.';';
    $result = mysql_query($query);
    $row = mysql_fetch_array($result) or die("Error fetching result for defaults.");

    $message = 'value="'.$row['message_text'].'"';
    if(mysql_num_rows($result) > 0 && $row['sched_shift_id'] == 0){ $shiftID = 'checked="checked"';}
}
else
{
    echo '<input name="id" type="hidden" value="-1" id="id" />';
}

?>
 
<div>
<table border="0">

	<tr>
		<td style="white-space: nowrap; background-color: #CCCCCC;" align="left" valign="top" colspan="2"><b>PHP-EMS-Tools: Edit Daily Message (Admin Only)</b></td>
	</tr>
	<tr>
		<td align="right" valign="top"><b>Administrator ID#</b></td>
		<td valign="top" align="left"><input size="10" maxlength="5" name="adminID" type="text" id="adminID" /></td>
	</tr>
	<tr>
		<td align="right" valign="top"><b>Password</b></td>
		<td valign="top" align="left"><input size="10" maxlength="10" name="adminPW" type="password" id="adminPW" /></td>
	</tr>
	<tr>
		<td style="white-space: nowrap; background-color: #CCCCCC;" align="left" valign="top" colspan="2"><b>PLEASE remember to keep this small!!</b></td>
	</tr>
	<tr>
		<td align="right" valign="top"><b></b></td>
<?php
echo '		<td valign="top" align="left"><input name="action" value="edit" type="radio" id="action" checked="checked" /><label for="action">'.$i18n_strings["signOn"]["Edit"].'</label></td>'."\n";
?>
	</tr>
	<tr>
		<td align="right" valign="top"><b></b></td>
<?php
echo '		<td valign="top" align="left"><input name="action" value="remove" type="radio" id="action" /><label for="action">'.$i18n_strings["signOn"]["Remove"].'</label></td>'."\n";
?>
	</tr>
	<tr>
		<td align="right" valign="top"><b>Daily Message</b></td>
<?php
echo '		<td valign="top" align="left"><input size="30" maxlength="50" name="message_text" type="text" '.$message.' id="message_text" /></td>'."\n";
echo '</tr>'."\n";
echo '<tr>'."\n";
echo '<td align="right" valign="top">&nbsp;</td>'."\n";
echo '		<td valign="top" align="left"><input type="checkbox" name="showAllShifts" value="yes" '.$shiftID.' id="showAllShifts" /> Show message for all shitfs this day.</td>'."\n";
?>
	</tr>
	<tr>
	<td colspan="2" style="text-align: left;">
	<strong>Formatting for Message:</strong><br />
	Text Time @Place<br />
	<strong>Text</strong> is the name of the event<br />
	<strong>Time</strong> is the time of the event <em>(optional)</em><br />
	&nbsp;&nbsp;Like: 6PM or 18:00 or 6P / 8AM or 8A or 08:00<br />
	&nbsp;&nbsp;Or a range of times separated by a dash<br />
	<strong>Place</strong> is the location <em>(optional)</em><br />
	&nbsp;&nbsp;Like: @MPAC or @MPFD<br />
	<strong>Examples:</strong><br />
	Memorial Day<br />
	Monthly Meeting 8PM<br />
	Monthly Drill 19:30<br />
	Bldg Cleanup 8-10A<br />
	BBP Class 10A-11A @MPFD<br />
	BBP Class 7PM @MPFD<br />
	Coin Toss 08:00-12:00
	</td>
	</tr>
	<tr>
		<td align="right" valign="top"><b></b></td>
<?php
echo '		<td valign="top" align="left"><input name="buttonGroup[btnReset]" value="'.$i18n_strings["signOn"]["Reset"].'" type="reset" />    <input name="buttonGroup[btnCancel]" value="'.$i18n_strings["signOn"]["Cancel"].'" onClick="hidePopup(\'popup\')" type="button" />    <input name="buttonGroup[btnSubmit]" value="'.$i18n_strings["signOn"]["Submit"].'" type="button" onClick="submitMessageForm()" />    </td>';
?>
	</tr>
</table>

</div>
</form>
