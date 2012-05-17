<?php
require_once('../custom.php');
require_once('../inc/web_session.php.inc');
$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $shortName;?> - Equipment Signout</title>
<link rel="stylesheet" type="text/css" href="doorLogs.css" />
</head>

<body>
<h1><?php echo $shortName;?> - Equipment Signout</h1>

<p><a href="equipSignout.php">Sign Out Equipment</a></p>

</body>

</html>
