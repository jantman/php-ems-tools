#! /usr/bin/php
<?php
require('custom.php');

global $dbName;
$conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.".$errorMsg);
mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!".$errorMsg);

    //AUTHENTICATION
$query = 'SELECT pwdMD5,rightsLevel,EMTid,password FROM roster;';
$result = mysql_query($query) or die ("Query Error");

echo '<table border=1 cellpadding=2>';
while($row = mysql_fetch_array($result))
{
	echo '<tr>';
	echo '<td>'.$row['EMTid'].'</td>';
	echo '<td>'.$row['rightsLevel'].'</td>';
	echo '<td>'.$row['password'].'</td>';	
	echo '<td>'.$row['pwdMD5'].'</td>';
	echo '<td>'.md5($row['password']).'</td>';
	if($row['pwdMD5'] != md5($row['password']))
	{
		echo '<td><b>NO MATCH</b></td>';
	}
	else
	{
		echo '<td>match</td>';
	}
	echo '</tr>';
}
echo '</table>';
?>