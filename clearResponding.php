<?php
// clears the currently responding members
// Time-stamp: "2009-01-24 22:58:49 jantman"

// ESTABLISH DB CONNECTION
require_once('/srv/www/htdocs/config/openEScallin.php');
$rmt_conn = mysql_connect($callin_host, $callin_user) or die("Unable to connect to callin database on ".$callin_host."\n");
mysql_select_db($callin_db, $rmt_conn) or die("Unable to select database '".$callin_db."' on host ".$callin_host."\n");

// require the utilities functions to send back the new responding div content
require_once('/srv/www/htdocs/inc/responding.php.inc'); // this provides the functions for everything

$cleared_ts = time();

if(trim($_SERVER['PHP_AUTH_USER']) != "")
{
    // use a username and IP address
    $cleared_by = $_SERVER['PHP_AUTH_USER']."@".$_SERVER['REMOTE_ADDR'];
}
else
{
    // no username, so it should be a local IP
    $ip = $_SERVER['REMOTE_ADDR'];
    $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
    if(strpos($hostname, ".midlandparkambulance.com"))
    {
	// we have a valid local hostname
	$hostname = str_replace(".midlandparkambulance.com", "", $hostname);
	$cleared_by = $hostname;
    }
    else
    {
	$cleared_by = $ip;
    }
}


$rmt_query = "UPDATE callins SET cleared_ts=".$cleared_ts.",cleared_by='".$cleared_by."',is_cleared=1 WHERE is_cleared=0;";
$rmt_result = mysql_query($rmt_query, $rmt_conn) or die("Error in Query: ".$rmt_query."\n Error: ".mysql_error($rmt_conn)."\n");
if($rmt_result)
{
    echo genRespDiv();
}

?>