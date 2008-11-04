<?php
//
// schedule.php
//
// this is the main schedule page
//
// Time-stamp: "2008-11-04 14:08:40 jantman"
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

// this file will import the user's customization
require_once('./config/config.php');
require_once('./config/scheduleConfig.php'); // schedule configuration
require_once('inc/sched.php');

// figure out what today is, and calculate what else is needed - what month do we show?
if(isset($_GET['date']))
{
    $mainDate = ((int)$_GET['date']);
    $month = date("m", ((int)$_GET['date']));
    $year = date("Y", ((int)$_GET['date']));
    if(isset($_GET['shift'])){ $shift = $_GET['shift'];} // figure out what shift to show
}
else
{
    $mainDate = time();
    $month = date("m");
    $year = date("Y");
    // figure out what shift to show
    if(isset($_GET['shift']))
    {
	$shift = $_GET['shift'];
    }
    else
    {
	if(date("H") < 6 || date("H") >=18)
	{
	    $shift = "Night";
	}
	else
	{
	    $shift = "Day";
	}
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $orgName." Schedule - ".date("M Y", $mainDate)." ".$shift;?></title>

<link rel="stylesheet" type="text/css" href="schedule.css" media="screen" />
<link rel="stylesheet" type="text/css" href="schedule_print.css" media="print" />
<link rel="stylesheet" type="text/css" href="scheduleForm.css" />
<!-- TODO: print stylesheet, force landscape? -->
<script language="javascript" type="text/javascript" src="inc/scheduleDHTML.js"></script>
<!-- members array for form validation -->
<script language="javascript" type="text/javascript">
<?php
// generate the JS code for the members array
echo 'var memberIDs = new Array();'."\n";

// find out which member types can pull duty
require_once('config/rosterConfig.php'); // for member types
$typesThatCanPullDuty = array();
for($i=0; $i < count($memberTypes); $i++)
{
    if($memberTypes[$i]['canPullDuty'])
    {
	$typesThatCanPullDuty[] = $memberTypes[$i]['name'];
    }
}

// now find out which members can pull duty
$conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed.");
mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!");
$query = 'SELECT EMTid,status FROM roster;';
$result = mysql_query($query) or die ("Auth Query Error");
$JSarrayCount = 0;
while($row = mysql_fetch_array($result))
{
    if(in_array($row['status'], $typesThatCanPullDuty))
    {
	echo 'memberIDs['.$JSarrayCount.'] = "'.$row['EMTid'].'";'."\n";
	$JSarrayCount++;
    }
}
mysql_free_result($result);
mysql_close($conn);
?>
</script>
<!-- end members array -->
<script language="javascript" type="text/javascript" src="inc/schedForms.js"></script>
<script language="javascript" type="text/javascript" src="inc/grayout.js"></script>
<!--
<script language="javascript" type="text/javascript" src="inc/eventForm.js"></script>
-->

</head>

<body>

<?php echo '<div class="monthControlLeft"><a href="schedule.php?date='.lastMonthDate($mainDate).'&shift='.$shift.'">&lt;&lt; '.date("F", lastMonthDate($mainDate)).'</a></div>'."\n"; ?>
<?php echo '<div class="monthControlRight"><a href="schedule.php?date='.nextMonthDate($mainDate).'&shift='.$shift.'">'.date("F", nextMonthDate($mainDate)).' &gt;&gt;</a></div>'."\n"; ?>
<div id="header">
<h1><?php echo $orgName." Schedule - ".date("M Y", $mainDate)." ".$shift;?></h1>
</div> <!-- END header DIV -->

<div id="calhead">
<?php
echo '<div class="headerPart">';
if($shift == "Day"){ echo '<strong><a href="schedule.php?date='.$mainDate.'&shift=Night">Nights</a></strong>';} else { echo '<strong><a href="schedule.php?date='.$mainDate.'&shift=Day">Days</a></strong>';}
echo '</div>';
echo '<div class="headerPart"><a href="#">Mass Signon</a></div>'; // TODO: implement this
echo '<div class="headerPart"><a href="countHours.php">Count Hours</a></div>';
echo '<div class="headerPart"><strong><a href="schedule.php">Current Shift</a></strong></div>';
echo '<div class="headerPart"><a href="#">Help</a></div>'; // TODO: implement this
?>
</div> <!-- END calhead DIV -->

<div id="calheadPrint">
<?php
echo 'as of '.date("D, M Y j H:i:s");
?>
</div> <!-- END calhead DIV -->

<div id="caldiv">
<?php
// heading


showMonthCalendarTable($mainDate);
?>
</div> <!-- END caldiv DIV -->

<div id="popup" class="popup">
<div id="popuptitleArea">
<div id="popuptitle"></div>
<div id="popupCloseBox" onClick="hidePopup()">X</div>
<div id="clearing"></div>
</div> <!-- END popuptitleArea DIV -->
<div id="popupbody">
</div> <!-- END popupbody DIV -->
</div> <!-- END popup DIV -->

</body>

</html>
