<?php
require_once('../../custom.php');
require_once('inc/common.php');

$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

$euid = (int)$_GET['euid'];

$query = "SELECT * FROM eso_uploads WHERE eu_id=$euid;";
$result = mysql_query($query) or die("Error in query.");
if(mysql_num_rows($result) < 1){ die("Invalid eu_id.");}

$row = mysql_fetch_assoc($result);

header("Content-type: ".$row['eu_mime_type']);
header("Content-Type: ".$row['eu_mime_type']);
header("Content-Length: ".$row['eu_size_b']);
header('Content-Disposition: attachment; filename="'.$row['eu_name'].'"');
header("Content-Transfer-Encoding: binary\n");
echo $row['eu_content'];

?>

