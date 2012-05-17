<?php
//
// rosterJoined.php
//
// Version 0.1 as of Time-stamp: "2009-10-26 11:47:17 jantman"
//
// This file is part of the php-ems-tools package
// available at 
//
// (C) 2006 Jason Antman.
// This package is licensed under the terms of the
// GNU General Public License (GPL)
//

require_once('custom.php');

// this script views the roster from the DB

if(! empty($_GET['sort']))
{
    $sort = $_GET['sort'];
}
else
{
    $sort = "EMTid";
}

echo '<head>';
echo '<meta http-equiv="refresh" content="180">';
echo '<link rel="stylesheet" href="php_ems.css" type="text/css">'; // the location of the CSS file for the schedule
echo '<title>'.$shortName.' - Roster by Date Joined</title>';
echo '<script type="text/javascript" src="php-ems-tools.js"> </script>';
echo '</head>';
echo '<body>';
echo '<table class="roster">';
// END OF PAGE TOP HTML

//Finish setting up the table
echo "\n"; // linefeed
echo '<td align=center colspan="8"><b>'.$orgName.' Roster by Date Joined</b><br> (as of '.date("M d Y").')';
//echo '<a href="javascript:helpPopUp('."'docs/roster_help.php'".')">HELP</a>';
echo '</td>';
echo "\n"; // linefeed

//CONNECT TO THE DB
$connection = mysql_connect() or die ("I'm sorry, but I was unable to connect! (MySQL error: unable to connect).".$errorMsg);
//SELECT pcr
mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!".$errorMsg);
//QUERY
if($sort=="EMTid")
{
    $query =  "SELECT * FROM roster WHERE status!='Resigned' ORDER BY lpad(EMTid,10,'0');";
}
else
{
    $query  = "SELECT * FROM roster WHERE status!='Resigned' ORDER BY ".$sort.";";
}
$result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query: ".$query."<br><br>" . mysql_error().'<br><br>'.$errorMsg);
mysql_close($connection); 

//setup the table
echo '<tr>';
echo '<td><a href="rosterJoined.php?sort=EMTid">ID</a></td>';
echo '<td>Edit</td>';
echo '<td><a href="rosterJoined.php?sort=LastName">Last Name</a></td>';
echo '<td><a href="rosterJoined.php?sort=FirstName">First Name</a></td>';
echo '<td><a href="rosterJoined.php?sort=dateJoined_ts">Date Joined</a></td>';
echo '<td>Years Since<br />Joined</td>';
echo '<td><a href="rosterJoined.php?sort=dateActive_ts">Date Active</a></td>';
echo '<td>Years Since<br />Active</td>';
echo '</tr>'."\n";

//loop through the members and call the showMember function
while ($row = mysql_fetch_array($result))  
{
    echo '<tr>';
    echo '<td>'.$row['EMTid'].'</td>';
    echo '<td><a href="javascript:rosterPopUp('."'rosterJoinedEdit.php?EMTid=".$row['EMTid']."&action=edit'".')">EDIT</a></td>';
    echo '<td>'.$row['LastName'].'</td>';
    echo '<td>'.$row['FirstName'].'</td>';
    if($row['dateJoined_ts'] != NULL)
    {
	echo '<td>'.date("Y-m-d", $row['dateJoined_ts']).'</td>';
	$years = (time() - $row['dateJoined_ts']) / 31536000;
	$years = round($years, 1);
	echo '<td>'.$years.'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td><td>&nbsp;</td>';
    }
    if($row['dateActive_ts'] != NULL)
    {
	echo '<td>'.date("Y-m-d", $row['dateActive_ts']).'</td>';
	$years = (time() - $row['dateActive_ts']) / 31536000;
	$years = round($years, 1);
	echo '<td>'.$years.'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td><td>&nbsp;</td>';
    }
    echo '</tr>'."\n";
}
mysql_free_result($result); 


?>  
</table>
</body>
</html>