<?php
//script to make shownAs field
require_once("custom.php");

$conn = mysql_connect() or die("error making connection");
mysql_select_db($dbName) or die("error selecting DB");

$query = "SELECT EMTid,shownAs,LastName FROM roster;";
$result = mysql_query($query) or die("error in query.");

while ($row = mysql_fetch_array($result))
{
	$shownAs=$row['LastName'];
	if($row['shownAs']=='')
	{
		mysql_query('UPDATE roster SET shownAs="'.$shownAs.'" WHERE EMTid="'.$row['EMTid'].'";') or die("query error");
		echo "UPDATED ".$row['EMTid'].' shownAs='.$shownAs.'<br>';
	}
	else
	{
		echo "IGNORED ".$row['EMTid'].'<br>';
	}
}
echo '<br><br>DONE.';

?>
