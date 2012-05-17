<?php

require_once('../custom.php');
mysql_connect() or die("I'm sorry but I cannot connect to the MySQL server. Error: ".mysql_error());
mysql_select_db($dbName) or die("I'm sorry but there was an error selecting the DB. Error: ".mysql_error());

$query = "SELECT LastName,FirstName,Address FROM roster WHERE status='Senior' OR status='Driver' OR status='Probie' ORDER BY LastName,FirstName;";
$result = mysql_query($query) or die("MySQL Query Error: ".mysql_error());

header("Content-Type: text/x-comma-separated-values");
header("Content-Disposition: inline");

echo '"FirstName","LastName","Address"'."\n";

while($row = mysql_fetch_assoc($result))
{
    echo '"'.$row['FirstName'].'","'.$row['LastName'].'","'.$row['Address'].'"'."\n";
}

?>