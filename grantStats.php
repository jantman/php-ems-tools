<?php

//simpleMonthly.php
//Simple Monthly Stats for PCRpro
//(C) 2006 Jason Antman.

//Updated 2006-6-27 

require_once "antman.php";  

$conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
mysql_select_db('pcr') or die ('Unable to select database!');

echo '<h1>Grant Statistics</h1>'."\n";

$years = array(date("Y")-1, date("Y")-2, date("Y")-3);

echo '<p><strong>Total rig mileage:</strong></p>'."\n";
echo '<table>'."\n";
echo '<tr><th>'.$years[0].'</th><th>'.$years[1].'</th><th>'.$years[2]."</th></tr>\n";

foreach($years as $year)
{
    $min = get_db_field("SELECT MIN(EndMileage) FROM OLDcalls WHERE YEAR(Date)=$year AND Unit='589';");
    $max = get_db_field("SELECT MAX(EndMileage) FROM OLDcalls WHERE YEAR(Date)=$year AND Unit='589';");
    $total = $max - $min;
    $min = get_db_field("SELECT MIN(EndMileage) FROM OLDcalls WHERE YEAR(Date)=$year AND Unit='588';");
    $max = get_db_field("SELECT MAX(EndMileage) FROM OLDcalls WHERE YEAR(Date)=$year AND Unit='588';");
    $total += ($max - $min);
    echo '<td>'.$total.'</td>';
}
echo '</tr></table>'."\n";

echo '<p><strong>Call Stats:</strong></p>'."\n";
echo '<table>'."\n";
echo '<tr><th>&nbsp;</th><th>'.$years[0].'</th><th>'.$years[1].'</th><th>'.$years[2]."</th></tr>\n";
//row
echo '<tr><th>Structure Fire</th>';
foreach($years as $year)
{
    $total = get_db_field("SELECT COUNT(*) FROM OLDcalls WHERE YEAR(Date)=$year AND CallType='Other - Fire Standby';");
    echo '<td>'.$total.'</td>';
}
//row
echo '<tr><th>BLS Response</th>';
foreach($years as $year)
{
    $total = get_db_field("SELECT COUNT(*) FROM OLDcalls WHERE YEAR(Date)=$year AND (OC='BLS' OR OC='Cancelled' OR OC='Refusal') AND CallType!='Other - Transport';");
    echo '<td>'.$total.'</td>';
}
//row
echo '<tr><th>ALS Response</th>';
foreach($years as $year)
{
    $total = get_db_field("SELECT COUNT(*) FROM OLDcalls WHERE YEAR(Date)=$year AND (OC='ALS/BLS' OR OC='Air');");
    echo '<td>'.$total.'</td>';
}
//row
echo '<tr><th>BLS Non-Emergency Transport</th>';
foreach($years as $year)
{
    $total = get_db_field("SELECT COUNT(*) FROM OLDcalls WHERE YEAR(Date)=$year AND CallType='Other - Transport';");
    echo '<td>'.$total.'</td>';
}
//row
echo '<tr><th>Extrication</th>';
foreach($years as $year)
{
    $total = get_db_field("SELECT COUNT(*) FROM OLDcalls WHERE YEAR(Date)=$year AND Tx LIKE '%extric%';");
    echo '<td>'.$total.'</td>';
}
//row
echo '<tr><th>Total Calls</th>';
foreach($years as $year)
{
    $total = get_db_field("SELECT COUNT(*) FROM OLDcalls WHERE YEAR(Date)=$year;");
    echo '<td>'.$total.'</td>';
}
//row
echo '<tr><th>Total Calls with Transport</th>';
foreach($years as $year)
{
    $total = get_db_field("SELECT COUNT(*) FROM OLDcalls WHERE YEAR(Date)=$year AND (OC='ALS/BLS' OR OC='Air' OR OC='BLS');");
    echo '<td>'.$total.'</td>';
}

echo '</tr>'."\n";






function get_db_array($query)
{
    $result = mysql_query($query) or die("<pre>Error in query: $query\n ERROR: ".mysql_error()."</pre>");
    $row = mysql_fetch_assoc($result);
    return $row;
}

function get_db_field($query)
{
    $result = mysql_query($query) or die("<pre>Error in query: $query\n ERROR: ".mysql_error()."</pre>");
    $row = mysql_fetch_array($result, MYSQL_NUM);
    return $row[0];
}

?>
