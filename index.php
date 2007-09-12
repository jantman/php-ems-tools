<html>
<!-- Time-stamp: "2006-12-20 20:35:39 jantman" -->
<!-- php-ems-tools index -->
<head>

<?php
require_once('custom.php');
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
<a href="rigCheck.php">Rig Check</a>
</p>

<p>
<a href="blankRigCheck.php">Print Blank Rig Check</a>
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

<p>
For documentation, see <a href="docs/index.html">The documentation</a> or <a href="http://www.php-ems-tools.com">the project homepage</a>.
</p>

<?php

require('version.php');

// PHP EMS Tools News

include("http://www.php-ems-tools.com/news.php?verNum=".$verNum);

?>
<hr>
<p>This is free, open-source software. Please help support free software.
</p>
<p>
Thank you for choosing <a href="http://www.php-ems-tools.com">PHP EMS Tools</a>, the *free* suite of tools for Emergency Medical Services.
</p>
</body>
</html>