<?php
// Time-stamp: "2009-12-09 10:08:19 jantman"
// $Id: checkRoster.php,v 1.1 2008/10/22 20:55:19 jantman Exp $
// Check Roster - checks for members with missing information

$conn = mysql_connect();
mysql_select_db("pcr") or die("Unable to select database.");

echo '<h1>Roster Information Missing</h1><h2>For Active Members</h2>'."\n";

echo '<table border="1"><tr><th>EMTid</th><th>Name</th><th>Missing</th>';

$query = "SELECT * FROM roster WHERE status='Senior' OR status='Driver' OR status='Probie' ORDER BY lpad(EMTid,10,'0');";
$result = mysql_query($query) or die("Error in query: ".$query."\nError: ".mysql_error());
while($row = mysql_fetch_assoc($result))
{
    $missing = array();
    $toCheck = array('Address', 'HomePhone', 'CellPhone', 'Email', 'textEmail', 'cellProvider');
    foreach($toCheck as $item)
    {
	if($row[$item] == null || trim($row[$item]) == "")
	{
	    $missing[] = $item;
	}
    }
    if(count($missing) > 0)
    {
	echo '<tr><td>'.$row['EMTid']."</td><td>".$row['LastName'].", ".$row['FirstName'].'</td><td>';
	foreach($missing as $thing)
	{
	    echo " ".$thing.",";
	}
	echo "</td>";
    }
}
echo '</table>';

?>