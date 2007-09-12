<?php
//
// emailList.php
//
// Version 0.1 as of Time-stamp: "2006-12-14 12:59:40 jantman"
//
// This file is part of the php-ems-tools package
// available at 
//
// (C) 2006 Jason Antman.
// This package is licensed under the terms of the
// GNU General Public License (GPL)
//

require_once('custom.php');

global $serverWebRoot;
global $shortName;

echo '<head>';
echo '<meta http-equiv="refresh" content="180">';
echo '<link rel="stylesheet" href="'.$serverWebRoot.'php_ems.css" type="text/css">'; // the location of the CSS file for the schedule
echo '<title>'.$shortName.' - Email List</title>';
echo '</head>';
echo '<body>';
echo '<h3>'.$shortName.' Email List</h3>';
//CONNECT TO THE DB
$connection = mysql_connect() or die ("I'm sorry, but I was unable to connect! (MySQL error: unable to connect).".$errorMsg);
//SELECT pcr
mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!".$errorMsg);
//QUERY
$query =  "SELECT status,EMTid,FirstName,LastName,Email FROM roster ORDER BY lpad(EMTid,10,'0');";
$result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query: ".$query."<br><br>" . mysql_error().'<br><br>'.$errorMsg);
mysql_close($connection); 

$emails = "";

echo '<table border=1>';
//loop through the members and call the showMember function
while ($row = mysql_fetch_array($result))  
{
    if($row['status'] <> "Resigned")
    {
	echo '<tr>';
	echo '<td>'.$row['EMTid'].'</td><td>'.$row['FirstName'].'&nbsp;'.$row['LastName'].'</td><td>'.$row['Email'].'</td>';
	echo '</tr>';
	if($row['Email'] <> null && $row['Email'] <> "")
	{
	    $emails .= $row['Email'].", ";
	}
    }
}
echo '</table>';

echo '<br><br><b>Email List:</b><br>'.$emails;
mysql_free_result($result); 

?>  
</table>
</body>
</html>