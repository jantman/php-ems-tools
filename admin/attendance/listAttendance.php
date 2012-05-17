<?php
$dbName = "pcr";
$conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed.");
mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>MPAC Attendance</title>
<link rel="stylesheet" type="text/css" href="attendance.css" />
</head>

<body>

<h1>MPAC Attendance</h1>

<p><a href="attendance.php">Add Attendance</a></p>
<ul>
<?php
$query = "SELECT date_ts,type FROM attendance GROUP BY date_ts,type ORDER BY date_ts DESC;";
$result = mysql_query($query) or die ("Query Error");
while($row = mysql_fetch_array($result))
{
    echo '<li>'.'<a href="viewAttendance.php?date='.$row['date_ts'].'&type='.$row['type'].'">'.date("Y-m-d", $row['date_ts'])." ".$row['type'].'</a></li>';
}

?>
</ul>
</body>

</html>
