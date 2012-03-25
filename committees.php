<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
// index.php
//
// Main page with navigation links for all components.
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
// | $LastChangedRevision:: 155                                         $ |
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/committees.php $ |
// +----------------------------------------------------------------------+

if(isset($_GET['adminView']))
{
    $adminView = true;
}
else
{
    $adminView = false;
}

require_once('./config/config.php'); // main configuration
require_once('./config/rosterConfig.php'); // roster configuration

echo '<link rel="stylesheet" href="'.$serverWebRoot.'php_ems.css" type="text/css">'."\n"; // the location of the CSS file for the schedule
echo '<title>'.$shortName.' - Committees</title>'."\n";
echo '<script type="text/javascript" src="php-ems-tools.js"> </script>'."\n";
echo '</head>'."\n";
echo '<body>'."\n";

echo '<table class="roster">'."\n";

echo '<td align=center colspan="3"><b>'.$orgName.' Committees</b><br> (as of '.date("M d Y").')'."\n";
echo '</td>'."\n";

echo '<tr><th>Committee</th><th>Position</th><th>Member</th></tr>'."\n";

$connection = mysql_connect() or die ("I'm sorry, but I was unable to connect! (MySQL error: unable to connect).".$errorMsg);
mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!".$errorMsg);
$query = "SELECT mc.EMTid,mc.appointed_ts,c.comm_name,c.comm_id,p.comm_pos_name,r.FirstName,r.LastName FROM committees AS c LEFT JOIN members_committees AS mc ON mc.comm_id=c.comm_id LEFT JOIN committee_positions AS p ON mc.pos_id=p.comm_pos_id LEFT JOIN roster AS r ON mc.EMTid=r.EMTid WHERE removed_ts IS NULL ORDER BY  c.comm_id,p.comm_pos_id;";
$result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query: ".$query."<br><br>" . mysql_error().'<br><br>'.$errorMsg);
$lastCommittee = "";
while($row = mysql_fetch_assoc($result))
{
    echo '<tr>';
    if($row['comm_name'] == $lastCommittee)
    {
	echo '<td>&nbsp;</td>';
    }
    else
    {
	if($adminView)
	{
	    echo '<td><a href="javascript:rosterPopUp('."'committeeEdit.php?id=".$row['comm_id']."&action=edit'".')">'.$row['comm_name'].'</a></td>';
	}
	else
	{
	    echo '<td>'.$row['comm_name'].'</td>';
	}
	$lastCommittee = $row['comm_name'];
    }
    if($row['comm_pos_name'] != "")
    {
	echo '<td>'.$row['comm_pos_name'].'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if($row['EMTid'] != "")
    {
	echo '<td>'.$row['FirstName'].' '.$row['LastName'].' ('.$row['EMTid'].')</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    echo '</tr>'."\n";
}
?>

</body>

</html>
