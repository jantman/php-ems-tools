<html>
<head>
<?php
// viewRigChecks.php
//
// Page to view lost of completed rig checks
//
// +----------------------------------------------------------------------+
// | PHP EMS Tools	http://www.php-ems-tools.com                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006, 2007 Jason Antman.	                          |
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
// | $LastChangedRevision:: 155                                         $ |
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/viewRigChecks.#$ |
// +----------------------------------------------------------------------+


require_once('./config/config.php'); // master configuration
require_once('./config/rigCheckData.php'); // rig check configration
require_once('./inc/global.php');
global $shortName;
$key = $_GET['pKey'];
echo '<title>'.$shortName.' Rig Checks</title>';
echo '<link rel="stylesheet" href="'.$serverWebRoot.'php_ems.css" type="text/css">'; // the location of the CSS file for the schedule

global $dbName;

echo '</head>';
echo '<body>';

echo '<h3 align=center>'.$shortName.' Rig Checks</h3>';

$conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.".$errorMsg);
mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!".$errorMsg);
$query = 'SELECT * FROM rigCheck;';
$result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query.<br><br>" . mysql_error().'<br><br>'.$errorMsg);

// setup table
?>
<table border=1>
<tr>
<td>Number</td>
<td>Date</td>
<td>Time</td>
<td>Rig</td>
<td>Crew</td>
<td>Signature</td>
</tr>
<?php

while($row = mysql_fetch_array($result))
{
    echo '<tr>';
    echo '<td><a href="viewRigCheck.php?pKey='.$row['pKey'].'">'.$row['pKey'].'</a></td>';
    echo '<td>'.date('Y-m-d', $row['timeStamp']).'</td>';
    echo '<td>'.date('H:i', $row['timeStamp']).'</td>';
    echo '<td>'.$row['rig'].'</td>';
    echo '<td>'.$row['crew1'].'&nbsp;&nbsp;'.$row['crew2'].'&nbsp;&nbsp;'.$row['crew3'].'&nbsp;&nbsp;'.$row['crew4'].'</td>';
    echo '<td>'.$row['sigID'].'</td>';
    echo '</tr>';
}

mysql_free_result($result);
mysql_close($conn); 

echo '</table>';

?>
</body>
</html>