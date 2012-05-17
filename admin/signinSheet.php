<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>MPAC Sign-In Sheet</title>
<link rel="stylesheet" type="text/css" href="signin.css" />
</head>

<body>
<h1>MPAC Sign-In Sheet</h1>

<h2>Event: _____________________________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Date: ___________________</h2>


<table class="signinSheet">
<?php

if(isset($_GET['rcpt']))
{
    echo '<tr><th>ID</th><th>Name</th><th>Signature</th><th>Date</th></tr>';
}
else
{
    echo '<tr><th>ID</th><th>Name</th><th>Time In</th><th>Time Out</th><th>Signature</th><th>Excused?</th></tr>';
}

require_once('../custom.php');
mysql_connect() or die("I'm sorry but I cannot connect to the MySQL server. Error: ".mysql_error());
mysql_select_db($dbName) or die("I'm sorry but there was an error selecting the DB. Error: ".mysql_error());

$query = "SELECT EMTid,LastName,FirstName FROM roster WHERE status='Senior' OR status='Senior' OR status='Driver' OR status='Probie' ORDER BY lpad(EMTid,10,'0');";
$query = "SELECT EMTid,LastName,FirstName FROM roster WHERE status='Senior' OR status='Senior' OR status='Probie' ORDER BY LastName,FirstName;";

$result = mysql_query($query) or die("MySQL Query Error: ".mysql_error());
while($row = mysql_fetch_assoc($result))
{
    echo '<tr>';
    echo '<td>'.$row['EMTid'].'</td>';
    echo '<td>'.$row['LastName'].", ".$row['FirstName'].'</td>';
    if(! isset($_GET['rcpt']))
    {
	echo '<td><nobr>___ ___ : ___ ___</nobr></td>';
	echo '<td><nobr>___ ___ : ___ ___</nobr></td>';
    }
    echo '<td>&nbsp;__________________________________________&nbsp;</td>';

    if(! isset($_GET['rcpt']))
    {
	echo '<td>&nbsp;</td>';
    }
    else
    {
	echo '<td>___________________</td>';
    }

    echo '</tr>'."\n";
}

?>

</table>

</body>

</html>
