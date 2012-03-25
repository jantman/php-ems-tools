<?php
//
// rosterPositions.php
//
// This page views the roster of members, positions, and committees
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
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/rosterPosition#$ |
// +----------------------------------------------------------------------+

require_once('./config/config.php'); // main configuration
require_once('./config/rosterConfig.php'); // roster configuration

// this script views the roster from the DB
if(! empty($_GET['adminView']))
{
    $adminView = $_GET['adminView'];
}
else
{
    $adminView = 0;
}
if(! empty($_GET['sort']))
{
    $sort = $_GET['sort'];
}
else
{
    $sort = "EMTid";
}

echo '<head>';
echo '<meta http-equiv="refresh" content="180">';
echo '<link rel="stylesheet" href="'.$serverWebRoot.'php_ems.css" type="text/css">'; // the location of the CSS file for the schedule
echo '<title>'.$shortName.' - Roster - Positions</title>';
echo '<script type="text/javascript" src="php-ems-tools.js"> </script>';
echo '</head>';
echo '<body>';
echo '<table class="roster">';
// END OF PAGE TOP HTML

//Finish setting up the table
$colspan = 11;

// extended positions option
if($useExtdTypes)
{
    $colspan = $colspan + 1;
}

echo "\n"; // linefeed
echo '<td align=center colspan="'.$colspan.'"><b>'.$orgName.' Roster - Positions</b><br> (as of '.date("M d Y").')';
if($adminView==1)
{
    echo '<br><a href="committees.php?adminView=1">List of Committee Membership</a>';
    echo '&nbsp; &nbsp; &nbsp; <a href="rosterPositions.php?adminView=0&sort='.$sort.'">Standard View</a>';
}
else
{
    echo '<br><a href="committees.php">List of Committee Membership</a>';
}
echo '&nbsp; &nbsp; &nbsp; <a href="javascript:helpPopUp('."'docs/roster_help.php'".')">HELP</a>';
echo '</td>';
echo "\n"; // linefeed

//CONNECT TO THE DB
$connection = mysql_connect() or die ("I'm sorry, but I was unable to connect! (MySQL error: unable to connect).".$errorMsg);
//SELECT pcr
mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!".$errorMsg);
//QUERY
if($sort=="EMTid")
{
    $query =  "SELECT * FROM roster ORDER BY lpad(EMTid,10,'0');";
}
else
{
    $query  = "SELECT * FROM roster ORDER BY case when ".$sort." = 'None' then 'ZZZZZZZZZZZZZZZZZZZZZZZZ' else ".$sort." end;";

}
$result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query: ".$query."<br><br>" . mysql_error().'<br><br>'.$errorMsg);
mysql_close($connection); 

//setup the table
echo '<tr>';
echo '<td><a href="rosterPositions.php?adminView='.$adminView.'&sort=EMTid">ID</a></td>';
if($adminView==1)
{
    echo '<td>Edit</td>';
}
echo '<td><a href="rosterPositions.php?adminView='.$adminView.'&sort=LastName">Last Name</a></td>';
echo '<td><a href="rosterPositions.php?adminView='.$adminView.'&sort=FirstName">First Name</a></td>';
echo '<td><a href="rosterPositions.php?adminView='.$adminView.'&sort=officer">Officer</a></td>';
echo '<td><a href="rosterPositions.php?adminView='.$adminView.'&sort=position">Position</a></td>';
echo '<td>Committees</td>';
echo '<td><a href="rosterPositions.php?adminView='.$adminView.'&sort=trustee">Trustee</a></td>';
if($useExtdTypes)
{
    echo '<td>Other Types</td>'; // extended member types
}
echo '</tr>';

//loop through the members and call the showMember function
while ($row = mysql_fetch_array($result))  
{
    showMember($row);
}
mysql_free_result($result); 

//this function will display a row for a member
function showMember($r)
{
    global $adminView;
    global $useExtdTypes;
    echo '<tr>';
    //get the roster view of the status/memberType
    if($r['unitID']<>"")
    {
	echo '<td>'.$r['unitID'].'</td>';
    }
    else
    {
	echo '<td>'.$r['EMTid'].'</td>';
    }
    if($adminView==1)
    {
	echo '<td><a href="javascript:rosterPopUp('."'rosterPosEdit.php?EMTid=".$r['EMTid']."'".')">EDIT</a></td>';

    }
    if(! empty($r['LastName']))
    {
	echo '<td>'.$r['LastName'].'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['FirstName']))
    {
	echo '<td>'.$r['FirstName'].'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(isEmpty($r['officer']))
    {
	echo '<td>&nbsp;</td>';
    }
    else
    {
	echo '<td>'.$r['officer'].'</td>';
    }
    if(isEmpty($r['position']))
    {
	echo '<td>&nbsp;</td>';
    }
    else
    {
	echo '<td>'.$r['position'].'</td>';
    }
    $membStr = getCommitteeString($r['EMTid']);
    if($membStr == "")
    {
	echo '<td>&nbsp;</td>';
    }
    else
    {
	echo '<td>'.$membStr.'</td>';
    }
    if(isEmpty($r['trustee']))
    {
	echo '<td>&nbsp;</td>';
    }
    else
    {
	echo '<td>Yes</td>';
    }
    // extended member types
    if($useExtdTypes)
    {
	if(isEmpty($r['OtherPositions']))
	{
	    echo '<td>&nbsp;</td>';
	}
	else
	{
	    echo '<td>'.$r['OtherPositions'].'</td>';
	}
    }

    echo '</tr>';


}

function isEmpty($string)
{
    if($string == null)
    {
	return true;
    }
    if($string == "")
    {
	return true;
    }
    if($string == "None")
    {
	return true;
    }
    return false;

}

function getCommitteeString($EMTid)
{
    global $dbName;
    mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!".$errorMsg);
    // for a given EMTid, returns a string listing the committees the person is on and their position on the committees.
    $query = "SELECT mc.EMTid,mc.appointed_ts,c.comm_name,p.comm_pos_name FROM members_committees AS mc LEFT JOIN committees AS c ON mc.comm_id=c.comm_id LEFT JOIN committee_positions AS p ON mc.pos_id=p.comm_pos_id WHERE removed_ts IS NULL AND mc.EMTid='".$EMTid."';";
    $result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query: ".$query."<br><br>" . mysql_error().'<br><br>'.$errorMsg);
    $final = "";
    while($row = mysql_fetch_assoc($result))
    {
	$final .= $row['comm_name'].' ('.$row['comm_pos_name'].'), ';
    }
    $final = trim($final); // trim any trailing whitespace
    $final = trim($final, ","); // trim trailing comma
    return $final;
}

?>  
</table>
</body>
</html>