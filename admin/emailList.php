<?php
// emailList.php
//
// Quick page to output a list of all members' email addresses
//
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
//      $Id: emailList.php,v 1.3 2007/09/15 00:08:30 jantman Exp $

require_once('../config/config.php');

global $serverWebRoot;
global $shortName;

echo '<head>';
echo '<meta http-equiv="refresh" content="180">';
echo '<link rel="stylesheet" href="php_ems.css" type="text/css">'; // the location of the CSS file for the schedule
echo '<title>'.$shortName.' - Email List</title>';
echo '</head>';
echo '<body>';
echo '<h3>'.$shortName.' Email List</h3>';
//CONNECT TO THE DB
$connection = mysql_connect() or die ("I'm sorry, but I was unable to connect! (MySQL error: unable to connect).".$errorMsg);
//SELECT pcr
mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!".$errorMsg);
//QUERY
$query =  "SELECT status,EMTid,FirstName,LastName,Email FROM roster ORDER BY lpad(EMTid,10,'0');";
$result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query: ".$query."<br><br>" . mysql_error().'<br><br>'.$errorMsg);
mysql_close($connection); 

$emails = "";

echo '<p><strong>To send e-mail to all members, please send a message to <a href="mailto:members@midlandparkambulance.com">members@midlandparkambulance.com</a>. It will automatically be sent to all active members.</strong></p>';

echo '<table border=1>';
//loop through the members and call the showMember function
while ($row = mysql_fetch_array($result))  
{
    if($row['status'] != "Resigned" && $row['status'] != 'Inactive Life')
    {
	echo '<tr>';
	echo '<td>'.$row['EMTid'].'</td><td>'.$row['FirstName'].'&nbsp;'.$row['LastName'].'</td><td>'.$row['Email'].'</td>';
	echo '</tr>';
	if($row['Email'] <> null && $row['Email'] <> "")
	{
	    $emails .= $row['FirstName']." ".$row['LastName']." &lt;".$row['Email']."&gt;, ";
	}
    }
}
echo '</table>';

mysql_free_result($result); 

echo '<p><strong>For messages over 40kb (which the list won\'t accept), or non-corps-business, here\'s a list of the above email addresses, ready to copy into your email client:</strong></p>';

$emails = trim($emails, " ,");
echo '<p>'.$emails.'</p>';

?>  
</table>
</body>
</html>