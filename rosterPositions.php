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
//      $Id$

require_once('./config/config.php');

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
echo "\n"; // linefeed
echo '<td align=center colspan="'.$colspan.'"><b>'.$orgName.' Roster - Positions</b><br> (as of '.date("M d Y").')';
if($adminView==1)
{
    echo '<br><a href="javascript:rosterPopUp('."'rosterEdit.php?action=new'".')">Add New Member</a>';
    echo '&nbsp; &nbsp; &nbsp; <a href="rosterPositions.php?adminView=0&sort='.$sort.'">Standard View</a>';
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
echo '<td><a href="rosterPositions.php?adminView='.$adminView.'&sort=comm1">Committee 1</a></td>';
echo '<td><a href="rosterPositions.php?adminView='.$adminView.'&sort=comm1pos">Position</a></td>';
echo '<td><a href="rosterPositions.php?adminView='.$adminView.'&sort=comm2">Committee 2</a></td>';
echo '<td><a href="rosterPositions.php?adminView='.$adminView.'&sort=comm2pos">Position</a></td>';
echo '<td><a href="rosterPositions.php?adminView='.$adminView.'&sort=trustee">Trustee</a></td>';
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
    if(isEmpty($r['comm1']))
    {
	echo '<td>&nbsp;</td>';
    }
    else
    {
	echo '<td>'.$r['comm1'].'</td>';
    }
    if(isEmpty($r['comm1pos']))
    {
	echo '<td>&nbsp;</td>';
    }
    else
    {
	echo '<td>'.$r['comm1pos'].'</td>';
    }
    if(isEMpty($r['comm2']))
    {
	echo '<td>&nbsp;</td>';
    }
    else
    {
	echo '<td>'.$r['comm2'].'</td>';
    }
    if(isEmpty($r['comm2pos']))
    {
	echo '<td>&nbsp;</td>';
    }
    else
    {
	echo '<td>'.$r['comm2pos'].'</td>';
    }
    if(isEmpty($r['trustee']))
    {
	echo '<td>&nbsp;</td>';
    }
    else
    {
	echo '<td>Yes</td>';
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
?>  
</table>
</body>
</html>