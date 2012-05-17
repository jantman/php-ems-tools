<?php
// Time-stamp: "2010-01-16 21:19:22 jantman"
// page to show current schedule and responding members
// $Id$
require_once('/srv/www/htdocs/inc/responding.php.inc'); // this provides the functions for everything
require_once('/srv/www/htdocs/inc/respondingNotice.php');
// TODO: redo all of this with a timed DHTML refresh of the DIVs so the page itself doesn't reload
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php echo '<title>'.$shortName.' Crew & Responding Members</title>'; ?>
<link rel="stylesheet" type="text/css" href="responding.css" />
<script language="javascript" type="text/javascript" src="inc/responding.js"></script>
<meta http-equiv="refresh" content="8">
</head>

<body>

<?php

if(isset($_POST['action']) && $_POST['action'] == "callin")
{
    processCallin();
}

echo getRespondingNotice();

echo '<div class="signonLink"><a href="schedule.php">Schedule / Sign On</a></div>'."\n";
echo '<div class="helpLink"><a href="responding_help.php">Help!</a></div>'."\n";

require_once('/srv/www/htdocs/inc/maintenance.php');
if($maint_warn_html != "")
{
    echo '<div style="width: 100%;">&nbsp;</div>'."\n";
    echo '<div class="maintWarning">'.$maint_warn_html.'</div>'."\n";
    echo '<div class="clearing"></div>'."\n";
}

echo '<div class="mainTitle">'.$shortName.' Current Crew &amp; Responding Members</div>'."\n";
echo '<div class="timeDiv" id="timeDiv">'.genTimeDiv().'</div>'."\n";

// tones cell
echo '<div class="tonesDiv" id="tonesDiv">'.genTonesDiv().'</div>'."\n";

//responding cell
if($rmt_conn)
{
    echo '<div>'."\n";
    echo '<div class="spacer">&nbsp;</div>'."\n";
    echo '<div class="respTitle">Currently Responding Members</div>'."\n";
    echo '<div class="clearResp"><a href="javascript:clearResponding()">Clear Responding</a></div>'."\n";
    echo '<div class="clearing"></div>'."\n";
    echo '</div>'."\n";
    echo '<div class="respDiv" id="respDiv">'.genRespDiv().'</div>';
}
//crew cell
echo '<div class="crewTitle">Current Duty Crew</div>'."\n";
echo '<div class="crewDiv" id="crewDiv">'.genCrewDiv().'</div>'."\n";

// CALL-IN AREA
if(isset($_SERVER["PHP_AUTH_USER"]) && $_SERVER["PHP_AUTH_USER"] == "440")
{
    // display nothing
}
elseif(isset($_SERVER["PHP_AUTH_USER"]))
{
    // pre-fill the EMTid
    echo '<div class="callinTitle">Call In Now</div>'."\n";
    echo '<div class="callinDiv" id="callinDiv">'."\n";
    echo '<form name="callin" method="POST">'."\n";
    echo '<input type="hidden" name="action" value="callin" />'."\n";
    echo '<label for="EMTid"><strong>EMTid: </strong></label><input type="text" name="EMTid" value="'.$_SERVER["PHP_AUTH_USER"].'" size="5" />';
    echo '<select name="DTMF"><option value="1">To Building</option><option value="2">To Scene</option><option value="3">At HQ</option><option value="4">Cancel Response</option></select>';
    echo '<input type="submit" value="Call In Now" />'."\n";
    echo '</form>'."\n";
    echo '</div>'."\n";
}
elseif(! isset($_SERVER["PHP_AUTH_USER"]) && substr($_SERVER["REMOTE_ADDR"], 0, 10) == "192.168.1.")
{
    echo '<div class="callinTitle">Call In Now</div>'."\n";
    echo '<div class="callinDiv" id="callinDiv">'."\n";
    echo '<form name="callin" method="POST">'."\n";
    echo '<input type="hidden" name="action" value="callin" />'."\n";
    echo '<label for="EMTid"><strong>EMTid: </strong></label><input type="text" name="EMTid" value="" size="5" />';
    echo '<label for="password"><strong>Password: </strong></label><input type="password" name="password" size="8" />';
    echo '<select name="DTMF"><option value="3">At HQ</option><option value="1">To Building</option><option value="2">To Scene</option><option value="4">Cancel Response</option></select>';
    echo '<input type="submit" value="Call In Now" />'."\n";
    echo '</form>'."\n";
    echo '</div>'."\n";
}

//todays schedule
echo '<div class="schedTitle">Today\'s Schedule</div>'."\n";
echo '<div class="schedDiv" id="schedDiv">'.genSchedDiv().'</div>'."\n";

// reminders
echo '<div class="reminderDiv" id="reminderDiv">'.genReminderDiv().'</div>'."\n";

echo '<div class="footer" id="footer">';
echo 'best viewed with <a href="http://www.spreadfirefox.com/node&id=238326&t=305">Mozilla Firefox</a> 3.0 or better at 1024x768 or larger.';
echo '<br />&copy; 2009 Jason Antman. part of <a href="http://www.php-ems-tools.com">php ems tools</a>';
echo '</div>'."\n";
?>

</body>

</html>


<?php

function processCallin()
{
    global $rmt_conn, $local_conn;

    if(! isset($_SERVER["PHP_AUTH_USER"]))
    {
	if(! isset($_POST['EMTid']) || ! isset($_POST['password']))
	{
	    // no full credentials
	    return false;
	}

	// check auth
	$query = "SELECT status FROM roster WHERE EMTid='".mysql_real_escape_string($_POST['EMTid'])."' AND password='".mysql_real_escape_string($_POST['password'])."';";
	$result = mysql_query($query, $local_conn) or die("Error in Query: ".$query."\n Error: ".mysql_error($local_conn)."\n");
	if(mysql_num_rows($result) < 1){ return;} // auth failed
	$row = mysql_fetch_assoc($result);
	if($row['status'] != "Senior" && $row['status'] != "Driver" && $row['status'] != "Probie")
	{
	    // can't authenticate
	    return;
	}
    }
    
    $query = "SELECT status FROM roster WHERE EMTid='".mysql_real_escape_string($_POST['EMTid'])."';";
    $result = mysql_query($query, $local_conn) or die("Error in Query: ".$query."\n Error: ".mysql_error($local_conn)."\n");
    if(mysql_num_rows($result) < 1){ return;} // auth failed
    $row = mysql_fetch_assoc($result);
    $status = mysql_real_escape_string($row['status']);

    $query = "INSERT INTO callins SET start_ts=".time().",end_ts=".time().",cid='2014443838',DTMF_select=".((int)$_POST['DTMF']).",EMTid='".mysql_real_escape_string($_POST['EMTid'])."',manual_entry=1,status='".$status."';";
    $rmt_result = mysql_query($query, $rmt_conn) or die("Error in Query: ".$query."\n Error: ".mysql_error($rmt_conn)."\n");
    

}

?>