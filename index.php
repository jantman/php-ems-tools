<html>
<!-- Time-stamp: "2012-01-24 21:22:43 jantman" -->
<!-- php-ems-tools index -->
<head>

<?php
require_once('custom.php');
require_once('inc/web_session.php.inc');
require_once('inc/common.php');
$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

echo '<title>'.$shortName.' - PHP EMS Tools Index</title>'."\n";
echo '<link rel="stylesheet" href="php_ems.css" type="text/css">'."\n"; // the location of the CSS file for the schedule
//figure out the month and year
$month = date("m", time());
$year = date("Y", time());
?>

</head>
<body>

<div style="float: right;"> <!-- BEGIN firefox button right-float div -->
<a href="http://affiliates.mozilla.org/link/banner/6335"><img src="http://affiliates.mozilla.org/media/uploads/banners/download-small-blue-EN.png" alt="Firefox" /></a>
<!-- <a href="http://www.spreadfirefox.com/node&amp;id=238326&amp;t=308"><img alt="Firefox 3" title="Firefox 3" src="http://sfx-images.mozilla.org/affiliates/Buttons/Firefox3.5/200x32_all_orange.png" border="0"></a> -->
</div> <!-- END firefox button right-float div -->

<?php
if($config_show_login_links)
{
    echo '<div class="loginlink">'."\n";
    if(php_ems_validSession())
    {
	// we have a valid session
	echo '<a href="handlers/login.php?action=logout">Logout '.$_SESSION['membername'].'</a>';
    }
    else
    {
	echo '<a href="login.php?action=login&request='.urlencode("../index.php").'">Login</a>';
    }
    echo '</div> <!-- closes LOGINLINK div -->'."\n";
}
echo '<h3>'.$shortName.' - PHP EMS Tools Index</h3>';
?>

<p class="respLink">
<?php echoLink("responding.php", "Responding Members &amp; Current Crew"); echo '&nbsp;&nbsp;&nbsp;&nbsp;'; echoLink("responding-WAP.php", "(WAP)"); echo '&nbsp;&nbsp;&nbsp;&nbsp;'; echoLink("respondingHx.php", "(History)"); ?>
</p>

<ul>
<li><strong><?php echoLink("newcall/", "Call Reports"); ?></strong>(only available at MPAC)</li>
<li><strong><?php echoLink("schedule.php", "Schedule"); ?></strong>
<?php
if($_SERVER["REQUEST_URI"] == "/auth/index.php" || $_SERVER["REQUEST_URI"] == "/auth/")
{
    echo '&nbsp;&nbsp;&nbsp;&nbsp;';
    echoLink('WAP/index.php', '(WAP)');
}
?>
  <ul>
    <li><?php echoLink("saturdaySchedule.php", 'Saturday Night Schedule'); ?></li>
  </ul>
</li>
<li><strong>Roster</strong>
  <ul>
    <li><?php echoLink("roster.php", 'Roster'); ?></li>
    <li><?php echoLink("rosterPositions.php", 'Roster - Officers, Positions, and Committee'); ?></li>
    <li><?php echoLink("committees.php", 'Committee List'); ?></li>
    <li><?php echoLink("rosterCerts.php", 'Roster - Certifications');?></li>
  </ul>
</li>
<li><strong>Statistics</strong>
  <ul>
    <li><?php echoLink("newcall-stats/", 'Call Stats');?> (<?php echoLink("stats.php", 'Call Stats prior to 1/1/2010');?>) </li>
    <li><?php echoLink("newcall-stats/countGenerals.php", 'Generals Count'); ?></li>
    <li><?php echoLink("newcall-stats/mutual_aid_log.php", 'Mutual Aid Log (yearly)'); ?></li>
    <li><?php echoLink("grantStats.php", 'Grant Statistics'); ?></li>
  </ul>
</li>
<li><strong>Rig Checks</strong>
  <ul>
    <li><strong>Fill In</strong> Rig Check: <?php echoLink("rigCheck.php?unit=588", '588'); echo '&nbsp;&nbsp;&nbsp;'; echoLink("rigCheck.php?unit=589", '589'); ?></li>
    <li><strong>Print Blank</strong> Rig Check: <?php echoLink("blankRigCheck.php?unit=588", '588'); echo '&nbsp;&nbsp;&nbsp;'; echoLink("blankRigCheck.php?unit=589", '589'); ?></li>
    <li><?php echoLink("viewRigChecks.php", 'View Rig Checks'); ?></li>
  </ul>
</li>
<li><strong>Misc.</strong>
  <ul>
    <li><?php echoLink("addBk.php", 'Address Book'); ?></li>
    <li><?php echoLink("bylaws-out.pdf", 'By-Laws (PDF)'); echo '&nbsp;&nbsp;&nbsp;'; echoLink("rules-out.pdf", 'Rules of Operations (PDF)'); ?></li>
    <li>Light Permit Applications: <?php echoLink("BLC-54.pdf", 'Blue (PDF)'); echo '&nbsp;&nbsp;&nbsp;'; echoLink("BLC-56.pdf", 'Red and Siren (PDF)'); ?> (as of 2009-12-19)</li>
    <li><?php echoLink("checkRequest.pdf", 'Check Request Form'); ?></li>
    <li><?php echoLink("help/newMembers.php", 'Information for New Members'); ?></li>
  </ul>
</li>
<li><strong>Administration</strong>
  <ul>
    <li><?php echoLink("roster.php?adminView=1", 'Roster Administrative View'); ?></li>
    <li><?php echoLink("rosterPositions.php?adminView=1", 'Roster - Officers, Positions, and Committee - Administrative View'); ?></li>
    <li><?php echoLink("rosterCerts.php?adminView=1", 'Roster Certifications - Administrative View'); ?></li>
    <li><?php echoLink("rosterJoined.php", 'Roster by Date Joined'); ?></li>
    <li><?php echoLink("addBk.php?adminView=1", 'Address Book - Administrative View'); ?></li>
    <li><?php echoLink("admin/", 'Administrative Tools'); ?></li>
  </ul>
</li>
<li><strong>Computers</strong>
  <ul>
    <li> <?php echoLink("help/computerPolicy.php", "Computer Use Guidelines"); ?></li>
    <li> <?php echoLink("help/linuxHelp.php", "Linux Help"); ?></li>
    <li> <?php echoLink("help/usernameLookup.php", "Username Lookup"); ?></li>
    <li> <?php echoLink("help/changePass.php", "Password Change"); ?></li>
    <li> <?php echoLink("help/wirelessTools.php", "Wireless Help/Tools"); ?></li>
    <li> <?php echoLink("help/scanner.php", "Scanner/Copier"); ?></li>
    <li> <?php echoLink("help/calendar.php", "MPAC Calendar"); ?></li>
  </ul>
</li>
    <li><strong><?php echoLink('systemStatus.php', 'System Status'); ?> </strong></li>
</ul>
<hr />
<p>
<?php
// alerts for rig checks
$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');
$query = 'SELECT * FROM rigCheck ORDER BY timeStamp ASC;';
$result = mysql_query($query) or die('Error in query in countMonthDays '.$query." ERROR ".mysql_error());
$last89 = 0;
$last88 = 0;
while($row = mysql_fetch_array($result))
{
    if($row['rig'] == "588")
    {
	if($row['timeStamp'] > $last88)
	{
	    $last88 = $row['timeStamp'];
	}
    }
    elseif($row['rig'] == "589")
    {
	if($row['timeStamp'] > $last89)
	{
	    $last89 = $row['timeStamp'];
	}
    }
}

$now = time();

if(($now - $last88) >= 691200)
{
    echo '<font color="red">588 last rig check: '.date("D n-d-Y", $last88).'</font><br>';
}

if(($now - $last89) >= 691200)
{
    echo '<font color="red">589 last rig check: '.date("D n-d-Y", $last89).'</font><br>';
}

?>

</p>
<hr>
<p>
<?php
// DB Connection
$query  = "SELECT RunNumber,date_date FROM calls WHERE YEAR(date_date)=".date("Y").";";
$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
$count = 0;
while($row = mysql_fetch_array($result))
{
	$count++;
}
mysql_free_result($result);
echo "<b>Calls for ".date("Y")." : ".$count."</b><br>";

$query  = "SELECT RunNumber,date_date FROM calls WHERE YEAR(date_date)=".date("Y")." && MONTH(date_date)=".date("n").";";
$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
$count = 0;
while($row = mysql_fetch_array($result))
{
	$count++;
}
mysql_free_result($result);
echo "</p><p>";
echo "<b>Calls for ".date("F Y")." : ".$count."</b><br>";

// LOG THE USER AUTH INFO TO ERROR LOG
error_log("EXTERNAL-AUTH"." ".$_SERVER['REQUEST_TIME']." "." ".$_SERVER['REMOTE_ADDR']." ".$_SERVER['REMOTE_USER']." ".$_SERVER['AUTH_TYPE']." ".$_SERVER["PHP_SELF"], E_NOTICE);

?>
</p>
<hr />
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

<?php echo '<p>'.$_SERVER['REQUEST_URI'].'</p>'; ?>

<p>
Thank you for choosing <a href="http://www.php-ems-tools.com">PHP EMS Tools</a>, the *free* suite of tools for Emergency Medical Services.
</p>
</body>
</html>
