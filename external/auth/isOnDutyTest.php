<?php

// isOnDuty test script
// Time-stamp: "2007-03-20 15:47:33 jantman"
 
require('../../custom.php');
require('isOnDuty.php');

$debug = false; // true for debug mode

showHoursSetup();

$tests = array();
$tests[] = "2007-03-13 15:00:00";
$tests[] = "2007-03-13 19:00:00";
$tests[] = "2007-03-14 01:00:00";
$tests[] = "2007-03-14 05:00:00";
$tests[] = "2007-03-14 06:01:00";
$tests[] = "2007-03-14 05:59:59";
$tests[] = "2007-03-14 07:00:00";
$tests[] = "2007-03-14 00:00:00";
$tests[] = "2007-03-14 00:00:01";
$tests[] = "2007-03-14 06:00:00";
$tests[] = "2007-03-13 11:59:59";
$tests[] = "2007-03-13 18:00:00";
$tests[] = "2007-03-14 18:00:01";
$tests[] = "2007-03-14 17:59:59";

echo "Get Table Name Tests:<br>";
for($i = 0; $i < count($tests); $i++)
{
    echo $tests[$i]."&nbsp;&nbsp;".getTableName(strtotime($tests[$i])).'<br>';
    $debug = false;
    echo "Date=".getScheduleDate(strtotime($tests[$i]))."<br>";
    $debug = false; // true for debug mode
    if($debug) { echo "<br><br>"; }
}
echo "Done.<br>";

echo "<br><br><b>Begin isOnDuty TEST:</b><br><br>";
$debug = false; // true for debug mode
for($i = 0; $i < count($tests); $i++)
{
    echo $tests[$i]." EMTid=56".isOnDuty("56", strtotime($tests[$i])).'<br>';
    if($debug) { echo "<br><br>"; }
}

echo "<br><br><br>";

$tests2 = array();
$tests2[] = array("EMTid" => "49", "time" => "2007-03-20 09:00:00");
$tests2[] = array("EMTid" => "41", "time" => "2007-03-19 21:59:59");
$tests2[] = array("EMTid" => "2", "time" => "2007-03-19 18:00:00");
$tests2[] = array("EMTid" => "2", "time" => "2007-03-19 23:59:59");
$tests2[] = array("EMTid" => "2", "time" => "2007-03-19 00:00:00");
$tests2[] = array("EMTid" => "2", "time" => "2007-03-19 00:01:00");
$tests2[] = array("EMTid" => "6", "time" => "2007-03-19 18:00:00");
$tests2[] = array("EMTid" => "6", "time" => "2007-03-19 23:59:59");
$tests2[] = array("EMTid" => "6", "time" => "2007-03-19 00:00:00");
$tests2[] = array("EMTid" => "6", "time" => "2007-03-19 00:01:00");

for($i = 0; $i < count($tests2); $i++)
{
    if(isOnDuty($tests2[$i]["EMTid"], strtotime($tests2[$i]["time"])) == true)
    {
	$res = "TRUE.";
    }
    else 
    {
	$res = "False.";
    }
    echo $tests2[$i]["time"]." EMTid=".$tests2[$i]["EMTid"]." ".$res."<br>";
}

echo "<b>FINISHED with isOnDuty TEST.</b>";
?>