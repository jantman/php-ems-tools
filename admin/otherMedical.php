<?php

//otherMedical.php
//Breakdown of calls typed as "Other Medical"
//(C) 2006 Jason Antman.

//Time-stamp: "2007-05-13 23:04:56 jantman"

require_once "antman.php";  

$conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
mysql_select_db('pcr') or die ('Unable to select database!');
?>

<head><title>MPAC Other Medical Calls Summary</title></head>
<body>
<h2>MPAC Calls Summary - Calls Typed as "Other Medical" for year <?php echo date("Y"); ?>

<table border=1>
<tr>
<td><b>Run #</b></td><td><b>Call Type</b></td><td><b>Chief Complaint</b></td><td><b>Remarks</b></td>
</tr>

<?php
$query = 'SELECT Pkey,RunNumber,Date,CallType,ChiefComplaint,Remarks FROM calls WHERE CallType="Other Medical";';
$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());  
while ($row = mysql_fetch_array($result)) 
{
    $date = substr($row['Date'],0,4);
	if($date == date("Y"))
	{
	    echo '<tr>';
	    echo '<td>'.$row['RunNumber'].'</td>';
	    echo '<td>'.$row['CallType'].'</td>';
	    echo '<td>'.$row['ChiefComplaint'].'</td>';
	    echo '<td>'.$row['Remarks'].'</td>';
	    echo '</tr>';
	}
}
mysql_free_result($result);
?>