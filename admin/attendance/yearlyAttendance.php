<?php
$dbName = "pcr";
$conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed.");
mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!");
if(isset($_GET['year']))
{
    $year = (int)$_GET['year'];
}
else
{
    $year = date("Y");
}

$att = array(1 => array("Meeting" => array(), "Drill" => array()), 2 => array("Meeting" => array(), "Drill" => array()), 3 => array("Meeting" => array(), "Drill" => array()), 4 => array("Meeting" => array(), "Drill" => array()), 5 => array("Meeting" => array(), "Drill" => array()), 6 => array("Meeting" => array(), "Drill" => array()), 7 => array("Meeting" => array(), "Drill" => array()), 8 => array("Meeting" => array(), "Drill" => array()), 9 => array("Meeting" => array(), "Drill" => array()), 10 => array("Meeting" => array(), "Drill" => array()), 11 => array("Meeting" => array(), "Drill" => array()), 12 => array("Meeting" => array(), "Drill" => array()));

$query = "SELECT * FROM attendance WHERE YEAR(FROM_UNIXTIME(date_ts))=".$year.";";
$result = mysql_query($query) or die ("Query Error");
while($row = mysql_fetch_array($result))
{
    $att[date("n", $row['date_ts'])][$row['type']][$row['EMTid']] = $row['status'];
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
<h2>For year <?php echo $year;?></h2>
<p>As of <?php echo date("Y-m-d H:i:s");?></p>

<table class="attendance">
<tr><th rowspan="2">Name</th><th rowspan="2">ID</th>
<?php
for($i = 1; $i < 13; $i++)
{
    echo '<th colspan="2">'.date("F", strtotime("2010-".$i."-01 01:00:00")).'</th>';
}
?>
</tr>
<tr>
<?php
for($i = 1; $i < 13; $i++)
{
    echo '<th>Mtng</th><th>Drill</th>';
}
?>
</tr>

<?php

$query = "SELECT EMTid,FirstName,LastName FROM roster WHERE status='Senior' OR status='Driver' OR status='Probie' ORDER BY LastName,FirstName;";
$result = mysql_query($query) or die ("Query Error");
while($row = mysql_fetch_array($result))
{
    $name = $row['LastName'].", ".$row['FirstName'];
    $id = $row['EMTid'];
    echo '<tr>';
    echo '<td><nobr>'.$name.'</nobr></td>';
    echo '<td>'.$id.'</td>';

    for($i = 1; $i < 13; $i++)
    {
	if(isset($att[$i]['Meeting'][$id]))
	{
	    echo '<td style="text-align: center;">'.substr($att[$i]['Meeting'][$id], 0, 1).'</td>';
	}
	else
	{
	    echo '<td>&nbsp;</td>';
	}

	if(isset($att[$i]['Drill'][$id]))
	{
	    echo '<td style="text-align: center;">'.substr($att[$i]['Drill'][$id], 0, 1).'</td>';
	}
	else
	{
	    echo '<td>&nbsp;</td>';
	}
    }

    echo '</tr>'."\n";
}

?>

</table>

</body>

</html>
