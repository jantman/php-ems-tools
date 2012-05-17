<table>
<?php
require_once('../../../custom.php');
require_once('../inc/returnForm.php');

$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

echo genReturnForm($_GET['EMTid'], null);

?>