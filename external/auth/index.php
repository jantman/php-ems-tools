<html>
<head>
<title>MPAC Internet Schedule View</title>
</head>
<body>
<h2>Midland Park Ambulance Corps - Internet Schedule View</h2>
<p>
<a href="schedule.php">View Schedule</a>
</p><p>
<a href="dispatchSchedule.php">Dispatch Schedule</a>
</p><p>
<a href="saturdays.html">Saturday Night Schedule 2006 - January - June</a>
</p><p>
<a href="roster.php">Roster</a>
</p><p>
<?php
// DB Connection
$conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
mysql_select_db('pcr') or die ('Unable to select database!');
$query  = "SELECT RunNumber,Date FROM calls WHERE YEAR(Date)=".date("Y").";";
$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
$count = 0;
while($row = mysql_fetch_array($result))
{
	$count++;
}
mysql_free_result($result);
echo "<b>Calls for ".date("Y")." : ".$count."</b><br>";

$query  = "SELECT RunNumber,Date FROM calls WHERE YEAR(Date)=".date("Y")." && MONTH(Date)=".date("n").";";
$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
$count = 0;
while($row = mysql_fetch_array($result))
{
	$count++;
}
mysql_free_result($result);
echo "</p><p>";
echo "<b>Calls for ".date("F Y")." : ".$count."</b><br>";
?>
</p>
</body>
</html>