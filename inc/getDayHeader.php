<?php
// inc/getDayHeader.php
//
// script to get the content of one day's header, for refreshing when changing the message
//

$ts = (int)$_GET['ts'];

require_once('../config/config.php');
require_once('../config/scheduleConfig.php');
require_once('sched.php');
require_once('global.php');

$shiftID = shiftNameToID(strtolower(tsToShiftName($ts)));
$displayStr = ""; // the string to actually display for the date
$displayStr = date("d", $ts);
if(substr($displayStr, 0, 1) == 0)
{
    $displayStr = substr($displayStr, 1);
}
$message = getDayMessage($ts, $shiftID); // returns an array with "message" and "id"
$messageID = -1; // send -1 to JS function if we don't have a message
if(isset($message['message']))
{
    $displayStr .= $message['message'];
    $messageID = $message['id'];
}
echo $displayStr;
?>