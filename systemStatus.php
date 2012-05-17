<?php
// Systems status for MPAC critical Systems
// Time-stamp: "2009-01-30 21:31:33 jantman"
// this is a CUSTOM script for MPAC ONLY - everything is HARD CODED
require_once("Net/Ping.php"); // PEAR Net_ping package
$uptime_threshold = 360000; // 1 hour in 1/100sec
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>MPAC System Status</title>
<link rel="stylesheet" type="text/css" href="systemStatus.css" />
</head>

<body>

<?php

if(isset($_POST['type']) && ($_POST['type'] == "webcheck"))
{
    require_once('/srv/www/htdocs/inc/serviceCheckFuncs.php');
    echo '<p><a href="systemStatus.php">Main Status View</a></p>'."\n";
    echo '<h1>MPAC System Status - Web-Based Check (Real-Time)</h1>'."\n";
    echo '<h2>as of '.date("Y-m-d H:i:s").'</h2>'."\n";
    echo '<table class="status">'."\n";
    echo '<tr><th>Service</th><th>Status</th><th>Detail</th></tr>'."\n";
    echo '<tr><th colspan="3">Internet Connection</th></tr>'."\n";
    echo '<tr><th colspan="3">PCRserv</th></tr>'."\n";
    echo '<tr><th colspan="3">VoIP1</th></tr>'."\n";
    // Ping - iLO
    $host = '192.168.1.111';
    echo '<tr><td>Ping iLO</td>'.ping_td('192.168.1.110').'</tr>';
    echo '<tr><td>Ping eth0</td>'.ping_td($host).'</tr>';
    echo '<tr><td>Ping eth1 (ext)</td>'.ping_td('192.168.1.112').'</tr>';
    echo '<tr><td>Uptime</td>';
    $uptime = get_snmp_plain($host, 'public', 'HOST-RESOURCES-MIB::hrSystemUptime.0');
    if($uptime >= $uptime_threshold){ echo '<td class="statusOK">OK</td><td class="detailOK">'.format_timeticks($uptime).'</td>';}
    else{ echo '<td class="statusNG">FAIL</td><td class="detailNG">'.format_timeticks($uptime).'</td>';}
    echo '</tr>';
    echo '<tr><th colspan="3">Wireless (MPAC-WAP1)</th></tr>'."\n";
    echo '</table>'."\n";
    // DEBUG
    echo '<p><b>DEBUG</b></p>';
    echo '<p><b>END DEBUG</b></p>';
    // END DEBUG
}
else
{
    echo '<h1>MPAC Network Status</h1>'."\n";
    echo '<h2>as of '.date("Y-m-d H:i:s").'</h2>'."\n";
    echo '<h2>(Green is good. Not green is bad.)</h2>'."\n";
    require_once('/srv/www/htdocs/inc/parseNagiosXML.php');
    echo "\n".'<div class="manualCheckLink">';
    echo '<p>You can also do a manual check of certain critical services.</p><p><strong>Only</strong> do this if there\'s nothing meaningful shown higher on this page.</p><p>A manual check will take <i>quite some time</i>.</p>';
    echo '<p><form name="manualCheck" action="systemStatus.php" method="POST"><input name="type" type="hidden" value="webcheck" /><input name="buttonGroup[btnSubmit]" value="Manual Check" type="submit" /></form></p>'."\n";
    echo '</div>'."\n";
}
?>

</body>

</html>

<?php
//
// FUNCTIONS HERE
//
function ping_td($host)
{
    // returns 2 formatted <td> elements for the ping result, status then detail
    //   'status' and 'detail'
    $ping = do_ping($host);
    $temp = array();
    if($ping['loss'] < 20)
    {
	// green
	 return '<td class="statusOK">OK</td><td class="detailOK">'.$ping['loss']."% loss ".$ping['time'].'sec</td>';
    }
    else
    {
	// red
	return '<td class="statusNG">FAIL</td><td class="detailNG">'.$ping['loss']."% loss ".$ping['time'].'sec</td>';
    }
}

?>
