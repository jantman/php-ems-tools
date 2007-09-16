<?php
// index.php
//
// Main page with navigation links for all components.
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
?>
<html>
<!-- Time-stamp: "2007-09-13 16:21:06 jantman" -->
<!-- php-ems-tools index -->
<head>

<?php
require_once('./config/config.php');
echo '<title>'.$shortName.' - PHP EMS Tools Index</title>';
echo '<link rel="stylesheet" href="'.$serverWebRoot.'php_ems.css" type="text/css">'; // the location of the CSS file for the schedule
//figure out the month and year
$month = date("m", time());
$year = date("Y", time());
?>

</head>
<body>

<?php
echo '<h3>'.$shortName.' - PHP EMS Tools Index</h3>';
?>

<p>
<a href="schedule.php">Schedule</a>
</p>

<p>
<a href="massSignOns.php">Mass Signon</a>
</p>

<?php
if($shortName == "MPAC")
{
    echo '<p><a href="dispatchSchedule.php">Dispatch Schedule</a></p>';
}
?>

<p>
<a href="roster.php">Roster</a>
</p>

<p>
<a href="rosterPositions.php">Roster - Officers, Positions, and Committee</a>
</p>

<p>
<a href="rosterCerts.php">Roster - Certifications</a>
</p>

<p>
<?php
echo '<a href="countHours.php?year='.$year.'&month='.$month.'">Monthly Hour Totals</a>';
?>
</p>

<p>

<?php
echo '<a href="countHours.php?year='.$year.'&style=yearly">Yearly Hour Totals</a>';
?>

</p>

<p>
<a href="addBk.php">Address Book</a>
</p>

<p>
<a href="rigCheckHandler.php">Rig Check</a>
</p>

<?php
include('localLinks.php');
?> 

<p>
<b>Administrative Views / Edit:</b>
</p>
<p>
<a href="roster.php?adminView=1">Roster Administrative View</a>
</p>
<p>
<a href="rosterPositions.php?adminView=1">Roster - Officers, Positions, and Committee - Administrative View</a>
</p>
<p>
<a href="rosterCerts.php?adminView=1">Roster Certifications - Administrative View</a>
</p>
<p>
<a href="addBk.php?adminView=1">Address Book - Administrative View</a>
</p>

<p>
<a href="viewRigChecks.php">View Rig Checks</a>
</p>

<!-- BEGIN RIG CHECK ALERT CODE -->

<hr>
<p>
<?php
global $rigCheckAlertTime;
global $rigCheckAgeAlert;

if($rigCheckAgeAlert)
{
    // if rigCheckAgeAlert is true, calculate alerts
 
    // alerts for last rig check
    $connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
    mysql_select_db($dbName) or die ('ERROR: Unable to select database!');
    $query = 'SELECT * FROM rigCheck ORDER BY timeStamp ASC;';
    $result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());

    // get the rig names
    global $rigChecks;
    $lastRigCheck = array();
    foreach($rigChecks as $valArray)
    {
	$lastRigCheck[$valArray['name']] = 0;
    }

    while($row = mysql_fetch_array($result))
    {
	if($row['timeStamp'] > $lastRigCheck[$row['rig']])
	{
	    $lastRigCheck[$row['rig']] = $row['timeStamp'];
	}
    }

    $now = time();

    foreach($lastRigCheck as $key => $value)
    {
	if(($now - $value) >= $rigCheckAlertTime)
	{
	    if($value == 0)
	    {
		echo '<font color="red">'.$key.' rig check never entered!</font><br>';
	    }
	    else
	    {
		echo '<font color="red">'.$key.' last rig check: '.date("D n-d-Y", $value).'</font><br>';
	    }
	}
    }
}
?>

</p>
<hr>

<!-- END RIG CHECK CODE -->

<p>
For documentation, see <a href="docs/index.html">The documentation</a> or <a href="http://www.php-ems-tools.com">the project homepage</a>.
</p>

<?php
// this code will notify you of new updates at the bottom of the index page.

// this has been re-worked as of version 2.0 to provide a secure method of performing this action

require_once('version.php'); // so we know the current version number

global $verNum; // the current version number

$url = "http://www.php-ems-tools.com/news.php?verNum=".$verNum; // the URL to check for update notices
$head = get_headers($url); // get the HTTP headers returned by the server. current version number is in them.

// if we had an error trying to get the headers, just skip this part
if($head)
{
    $newestVer = ""; // this will store the latest version number

    // parse the version number from the HTTP headers
    foreach($head as $val)
    {
	if(strstr($val, "PHP-EMS-Tools-Current-Version:")) // find the header which has the version info
	{
	    $newestVer = explode(" ", $val); // turn the header string into a space-separated array
	    $newestVer = $newestVer[1]; // get the second element, i.e. the version number
	}
    }

    // if the local version (this program) is a lower number than the latest, show a message
    if($verNum < $newestVer)
    {
	echo '<p><font color="red">An updated version of PHP EMS Tools has been released. It has added features and possibly bug fixes, some of which may be very important. Please see <a href="http://www.php-ems-tools.com">php-ems-tools.com</a> for more information. Please be sure to notify whoever setup this program for you, and ask them to upgrade. If you have any questions, please contact the project developers via the email link at <a href="http://www.php-ems-tools.com">php-ems-tools.com</a>.</font></p>';
    }
}

?>
<hr>
<p>This is free, open-source software. Please help support free software.
</p>
<p>
Thank you for choosing <a href="http://www.php-ems-tools.com">PHP EMS Tools</a>, the *free* suite of tools for Emergency Medical Services.
</p>
</body>
</html>