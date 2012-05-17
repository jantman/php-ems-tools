<?php
// +----------------------------------------------------------------------+
// | PHP EMS Tools      http://www.php-ems-tools.com                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006, 2007 Jason Antman.                               |
// |                                                                      |
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation; either version 3 of the License, or    |
// | (at your option) any later version.                                  |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to:                           |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+
// |Please use the above URL for bug reports and feature/support requests.|
// +----------------------------------------------------------------------+
// | Authors: Jason Antman <jason@jasonantman.com>                        |
// +----------------------------------------------------------------------+
//      $Id: roster.php,v 1.5 2007/09/20 00:00:40 jantman Exp $

require_once('./config/config.php'); // main configuration
require_once('./config/rosterConfig.php'); // roster configuration
require_once('./config/scheduleConfig.php'); // roster configuration

if(isset($_GET['year']))
{
    $year = (int)$_GET['year'];
}
else
{
    $year = (int)date("Y");
}

//CONNECT TO THE DB
$connection = mysql_connect() or die ("I'm sorry, but I was unable to connect! (MySQL error: unable to connect).".$errorMsg);
mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!".$errorMsg);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php echo '<title>'.$shortName." - $year Saturday Night Schedule</title>"; ?>
<link rel="stylesheet" href="css/saturdaySchedule.css" type="text/css">
<script type="text/javascript" src="php-ems-tools.js"> </script>
<script language="javascript" type="text/javascript" src="js/reportPrintFormat.js"></script>
</head>

<body>

<?php

echo '<h1>'.$shortName." - $year Saturday Night Schedule</h1>\n";
echo '<h2>as of '.date("Y-m-d H:i:s")."</h2>\n";

echo '<div class="noPrint" style="text-align: center"><a href="javascript:printMe()">Print this Page</a></div>'."\n";

$query = "SELECT s.*,r.LastName,r.FirstName,r.shownAs,r.status FROM schedule AS s LEFT JOIN roster AS r ON s.EMTid=r.EMTid WHERE sched_year=$year AND sched_shift_id=2 AND DAYOFWEEK(FROM_UNIXTIME(start_ts))=7 AND deprecated=0 ORDER BY start_ts ASC,r.status DESC;";
$result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query: ".$query."<br><br>" . mysql_error().'<br><br>'.$errorMsg);

$numRows = mysql_num_rows($result);

$currentDate = "";
$count = 0;
$col = 1;

echo '<div> <!-- container -->'."\n";
echo '<div class="listColumn">'."\n";

while($row = mysql_fetch_assoc($result))
{
    $date = date("F j, Y", $row['start_ts']);

    if($date != $currentDate)
    {
	//if(($count > ($numRows / 3) && $col == 1) || ($count > (($numRows / 3)*2) && $col == 2))
	if(($count > (($numRows / 2)-1) && $col == 1))
	{
	    echo '</div>'."\n".'<div class="listColumn">'."\n";
	    $col++;
	}
	echo '</ul>'."\n";
	echo '<p class="date">'.$date."</p>\n";
	echo '<ul class="scheduleItem">'."\n";
	$currentDate = $date;
    }

    echo '<li>';
    if($row['status'] != 'Senior'){ echo '<em>';}
    echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row['FirstName'].' '.$row['LastName'].' ('.$row['EMTid'].')';
    if((date("H:i", $row['start_ts']) != ($nightFirstHour.":00")) || (date("H:i", $row['end_ts']) != ($dayFirstHour.":00")))
    {
	echo ' '.date("H:i", $row['start_ts']).' - '.date("H:i", $row['end_ts']);
    }
    if($row['status'] != 'Senior'){ echo ' (Probie)</em>';}
    echo '</li>';
    $count++;
}

?>

</div>
</div>

</body>

</html>
