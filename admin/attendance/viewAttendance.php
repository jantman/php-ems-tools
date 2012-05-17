<?php
$dbName = "pcr";
$conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed.");
mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!");
if(isset($_GET['date']) && isset($_GET['type']))
{
    $date = (int)$_GET['date'];
    $type = $_GET['type'];
}
else
{
    die("You must specify a date (TS) and type.");
}

$att = array();
$query = "SELECT EMTid,status FROM attendance WHERE date_ts=".$date." AND type='".mysql_real_escape_string($type)."';";
$result = mysql_query($query) or die ("Query Error");
while($row = mysql_fetch_array($result))
{
    $att[$row['EMTid']] = $row['status'];
}

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

<p>
<strong>Date:</strong> <?php echo date("Y-m-d", $date);?>
&nbsp;&nbsp;&nbsp;
<strong>Event Type:</strong> <?php echo $type;?>
</p>

<table class="attendance">
<tr><th>Name</th><th>ID</th><th>Attendance</th></tr>

<?php
$query = "SELECT EMTid,FirstName,LastName FROM roster WHERE status='Senior' OR status='Driver' OR status='Probie' ORDER BY LastName,FirstName;";
$result = mysql_query($query) or die ("Query Error");
$count = 0;
while($row = mysql_fetch_array($result))
{
    $name = $row['LastName'].", ".$row['FirstName'];
    $id = $row['EMTid'];
    echo '<tr>';
    echo '<td>'.$name.'</td>';
    echo '<td>'.$id.'</td>';
    if(isset($att[$id]))
    {
	echo '<td>'.$att[$id].'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    echo '</tr>'."\n";
    $count++;
}

?>

</table>

</body>

</html>
