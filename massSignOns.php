<?php
// massSignOns.php
//
// Simple form to allow members to sign on for multiple shifts at once.
// second version, 2010-07-20 for second schedule version
//
// +----------------------------------------------------------------------+
// | PHP EMS Tools      http://www.php-ems-tools.com                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006-2010 Jason Antman.                                |
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
//      $Id: massSignOns.php,v 1.4 2007/09/20 00:00:40 jantman Exp $

// this file will import the user's customization
require_once('./config/config.php');
require_once('./config/scheduleConfig.php'); // schedule configuration
require_once('./inc/sched.php');
require_once('./inc/global.php');
require_once('./inc/massSignon.php.inc');

// get the URL variables
if(! empty($_GET['year']))
{
    $year = $_GET['year'];
}
else
{
    $year = date('Y');
}
if(! empty($_GET['month']))
{
    $month = $_GET['month'];
}
else
{
    $month = date('m');
}
if(! empty($_GET['shift']))
{
    $shift = $_GET['shift'];
}
else
{
    if((date('H')<6) || (date('H')>18))
    {
	$shift = "Night";
    }
       else
    {
	$shift = "Day";
    }
}

// get the shiftID
$shiftID = shiftNameToID(strtolower(tsToShiftName($ts)));

// make a timestamp for this calendar
$timestamp = strtotime($year."-".$month."-01");
$monthName = date("F", $timestamp); // the full textual name of the month

$mainDate = $timestamp;

// THIS IS THE BEGINNING OF THE HTML
echo '<html>';
echo '<head>';
echo '<meta http-equiv="refresh" content="180">';
echo '<link rel="stylesheet" href="php_ems.css" type="text/css">'; // the location of the CSS file for the schedule
echo '<link rel="stylesheet" href="massSignon.css" type="text/css">'; // the location of the CSS file for the schedule
echo '<title>'.$orgName." Schedule Mass Signons- ".$monthName." ".$year." ".$shift.'</title>';
echo '<script type="text/javascript" src="php-ems-tools.js"> </script>';
echo '</head>';
echo '<body>';


// output the header
echo '<h3 align=center>'.$shortName.' Mass Signon for '.$monthName.' '.$year.' - '.$shift.'</h3>';

// navigation links
echo '<p align=center>';
echo '<a href="schedule.php?year='.$year.'&month='.$Month.'&shift='.$shift.'">Schedule</a>';
echo '</p>';

echo '<form method="post" action="doMassSignon.php">';

echo '<input type="hidden" name="year" value="'.$year.'">'."\n";
echo '<input type="hidden" name="month" value="'.$month.'">'."\n";
echo '<input type="hidden" name="shift" value="'.$shift.'">'."\n";

echo "<table class='cal'>\n";
showCurrentMonth(false);
echo "</table>\n";


?>
<br>
<p><b>ID #: </b><input size="10" maxlength="5" name="EMTid" type="text" />
<b>Password: </b><input size="10" maxlength="20" name="password" type="password" />
 <b>Start:</b> <select name="start">
<?php
if($shift=='Day')
{
    echo '<option value="06:00:00" selected="selected">06:00:00</option>';
    echo '<option value="07:00:00">07:00:00</option>';
    echo '<option value="08:00:00">08:00:00</option>';
    echo '<option value="09:00:00">09:00:00</option>';
    echo '<option value="10:00:00">10:00:00</option>';
    echo '<option value="11:00:00">11:00:00</option>';
    echo '<option value="12:00:00">12:00:00</option>';
    echo '<option value="13:00:00">13:00:00</option>';
    echo '<option value="14:00:00">14:00:00</option>';
    echo '<option value="15:00:00">15:00:00</option>';
    echo '<option value="16:00:00">16:00:00</option>';
    echo '<option value="17:00:00">17:00:00</option>';
}
else
{
    //night
    echo '<option value="18:00:00" selected="selected">18:00:00</option>';
    echo '<option value="19:00:00">19:00:00</option>';
    echo '<option value="20:00:00">20:00:00</option>';
    echo '<option value="21:00:00">21:00:00</option>';
    echo '<option value="22:00:00">22:00:00</option>';
    echo '<option value="23:00:00">23:00:00</option>';
    echo '<option value="00:00:00">00:00:00</option>';
    echo '<option value="01:00:00">01:00:00</option>';
    echo '<option value="02:00:00">02:00:00</option>';
    echo '<option value="03:00:00">03:00:00</option>';
    echo '<option value="04:00:00">04:00:00</option>';
    echo '<option value="05:00:00">05:00:00</option>';
}
?>

</select>
<b> End: </b>
<select name="end">
<?php
if($shift=='Day')
{
    echo '<option value="07:00:00">07:00:00</option>';
    echo '<option value="08:00:00">08:00:00</option>';
    echo '<option value="09:00:00">09:00:00</option>';
    echo '<option value="10:00:00">10:00:00</option>';
    echo '<option value="11:00:00">11:00:00</option>';
    echo '<option value="12:00:00">12:00:00</option>';
    echo '<option value="13:00:00">13:00:00</option>';
    echo '<option value="14:00:00">14:00:00</option>';
    echo '<option value="15:00:00">15:00:00</option>';
    echo '<option value="16:00:00">16:00:00</option>';
    echo '<option value="17:00:00">17:00:00</option>';
    echo '<option value="18:00:00" selected="selected">18:00:00</option>';
}
else
{
    //night
    echo '<option value="19:00:00">19:00:00</option>';
    echo '<option value="20:00:00">20:00:00</option>';
    echo '<option value="21:00:00">21:00:00</option>';
    echo '<option value="22:00:00">22:00:00</option>';
    echo '<option value="23:00:00">23:00:00</option>';
    echo '<option value="00:00:00">00:00:00</option>';
    echo '<option value="01:00:00">01:00:00</option>';
    echo '<option value="02:00:00">02:00:00</option>';
    echo '<option value="03:00:00">03:00:00</option>';
    echo '<option value="04:00:00">04:00:00</option>';
    echo '<option value="05:00:00">05:00:00</option>';
    echo '<option value="06:00:00" selected="selected">06:00:00</option>';
}
?>
</select>
</p>
<p>
<input type="submit" />
<input type="reset" />
</p>
</form>
<a name="bottom"></a>
</body>
</html>