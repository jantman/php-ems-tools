<html>
<head>
<meta http-equiv="refresh" content="180">

<?php

//
// changes.php
//
// tool to view schedule change log
// should be accessible by admins only
//
// Version 1.0 as of Time-stamp: "2007-02-05 00:19:18 jantman"
//
// This file is part of the php-ems-tools package
// available at http://www.php-ems-tools.com 
//
// (C) 2006 Jason Antman.
// This package is licensed under the terms of the
// GNU General Public License (GPL)
//

// TODO: sort by EMTid, show only EMTid, sort by date, show only date (both changes made and date of shift for change)

// 
// DO NOT MAKE CHANGES
// UNLESS YOU KNOW WHAT YOU ARE DOING.
// 
require('../custom.php');

global $dbName;

if(! empty($_GET['year']))
{
    $year = $_GET['year'];
}
else
{
    $year = date("Y");
}

if(! empty($_GET['month']))
{
    $month = $_GET['month'];
}
else
{
    $month = date("m");
}
// DB Connection
global $shortName;
global $serverWebRoot;
echo "<title>".$shortName." Schedule Change Log</title></head><body>";
echo "<h3>".$shortName." Schedule Change Log as of ".date("Y-m-d H:i:s")."</h3>";
echo '<link rel="stylesheet" href="'.$serverWebRoot.'php_ems.css" type="text/css">';


$conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
mysql_select_db($dbName) or die ('Unable to select database!');
$query  = "SELECT pKey,timestamp,EMTid,host,address,form,query FROM schedule_".$year."_".$month."_change ORDER BY pKey;";
$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());

echo '<table class="roster">';

echo '<tr><td><b>pKey</b></td><td><b>Time</b></td><td><b>Login EMTid</b></td><td><b>host</b></td><td><b>address</b></td><td><b>form</b></td><td><b>query</b></td>';
while($row = mysql_fetch_assoc($result))
{
    echo '<tr>';
    echo '<td>'.$row['pKey'].'</td>';
    echo '<td>'.date("Y-m-d H:i:s", $row['timestamp']).'</td>';
    echo '<td>'.$row['EMTid'].'</td>';
    echo '<td>'.$row['host'].'</td>';
    echo '<td>'.$row['address'].'</td>';
    echo '<td>'.$row['form'].'</td>'; 
    echo '<td>'.$row['query'].'</td>';
    echo '</tr>';
}
echo '</table></html>';
mysql_free_result($result);


?>