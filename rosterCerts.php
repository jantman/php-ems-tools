<?php
// rosterCerts.php
//
// Page to view roster certification data
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

require_once('./config/config.php'); // main configuration

require_once('./config/rosterConfig.php'); // roster configuration

global $extdCerts; // the extended certifications array

if(! empty($_GET['sort']))
{
    $sort = $_GET['sort'];
}
else
{
    $sort = "EMTid";
}

if(! empty($_GET['adminView']))
{
    $adminView = $_GET['adminView'];
}
else
{
    $adminView = 0;
}

echo '<head>';
echo '<meta http-equiv="refresh" content="180">';
echo '<link rel="stylesheet" href="'.$serverWebRoot.'php_ems.css" type="text/css">'; // the location of the CSS file for the schedule
echo '<title>'.$shortName.' - View Certifications Roster</title>';
echo '<script type="text/javascript" src="php-ems-tools.js"> </script>';
echo '</head>';
echo '<body>';
echo '<table class="roster">';
// END OF PAGE TOP HTML

//Finish setting up the table
if($adminView==1)
{ $colspan = 12;}
else
{ $colspan = 11;}

// compensate colspan for the extended certs array
$colspan = $colspan + sizeof($extdCerts);

echo "\n"; // linefeed
echo '<td align=center colspan="'.$colspan.'"><b>'.$orgName.' Certifications Roster</b><br> (as of '.date("M d Y").')';
echo '<a href="javascript:helpPopUp('."'docs/roster_help.php'".')">HELP</a>';
if($adminView==1)
{
    echo '<br><a href="javascript:rosterPopUp('."'rosterCertsEdit.php?action=new'".')">Add New Member</a>';
    echo '&nbsp; &nbsp; &nbsp; <a href="rosterCerts.php?adminView=0&sort='.$sort.'">Standard View</a>';
}
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
    $query  = "SELECT * FROM roster ORDER BY ".$sort.";";
}
$result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query: ".$query."<br><br>" . mysql_error().'<br><br>'.$errorMsg);
mysql_close($connection); 

//setup the table
    echo '<tr>';
    echo '<td>&nbsp;</td>'; // memberType
    echo '<td><a href="rosterCerts.php?sort=EMTid';
    if($adminView==1){ echo '&adminView=1';}
    echo '">ID</a></td>';
    if($adminView==1)
    {
	echo '<td>Edit</td>';
    }
    echo '<td><a href="rosterCerts.php?sort=LastName';
    if($adminView==1){ echo '&adminView=1';}
    echo '">Last Name</a></td>';
    echo '<td><a href="rosterCerts.php?sort=FirstName';
    if($adminView==1){ echo '&adminView=1';}
    echo '">First Name</a></td>';
    echo '<td><a href="rosterCerts.php?sort=CPR';
    if($adminView==1){ echo '&adminView=1';}
    echo '">CPR</a></td>';
    echo '<td><a href="rosterCerts.php?sort=EMT';
    if($adminView==1){ echo '&adminView=1';}
    echo '">EMT</a></td>';
    echo '<td><a href="rosterCerts.php?sort=FR';
    if($adminView==1){ echo '&adminView=1';}
    echo '">1st Resp.</a></td>';
    echo '<td><a href="rosterCerts.php?sort=HazMat';
    if($adminView==1){ echo '&adminView=1';}
    echo '">HazMat</a></td>';
    echo '<td><a href="rosterCerts.php?sort=BBP';
    if($adminView==1){ echo '&adminView=1';}
    echo '">BBP</a></td>';
    echo '<td><a href="rosterCerts.php?sort=PHTLS';
    if($adminView==1){ echo '&adminView=1';}
    echo '">PHTLS</a></td>';
    echo '<td><a href="rosterCerts.php?sort=NREMT';
    if($adminView==1){ echo '&adminView=1';}
    echo '">NREMT</a></td>';
    foreach($extdCerts as $certName)
    {
	echo '<td>'.$certName.'</td>';
    }
    echo '</tr>';

//loop through the members and call the showMember function
while ($row = mysql_fetch_array($result))  
{
    // figure out the member type
    $memberType = "";
    global $memberTypes;
    global $adminView;
    for($i = 0; $i < count($memberTypes); $i++)
    {
	if($memberTypes[$i]['name'] == $row['status'])
	{
	    $memberType = $memberTypes[$i]['name'];
	}
    }

    // figure out whether we show this member or not
    for($i = 0; $i < count($memberTypes); $i++)
    {
	if($memberTypes[$i]['name'] == $memberType)
	{
	    //this is the right type
	    $shownInShort = $memberTypes[$i]['shownInShort'];
	    $shownInRoster = $memberTypes[$i]['shownInRoster'];
	}
    }
    showMember($row);
}
mysql_free_result($result); 

//this function will display a row for a member
function showMember($r)
{
    global $adminView;
    global $extdCerts; // extended certifications info
    
    // figure out the member type
    $memberType = "";
    global $memberTypes;
    for($i = 0; $i < count($memberTypes); $i++)
    {
	if($memberTypes[$i]['name'] == $r['status'])
	{
	    $memberType = $memberTypes[$i]['rosterName'];
	}
    }

    echo '<tr>';
    //get the roster view of the status/memberType
    
    if($memberType=="")
    {
	$memberType = "&nbsp;";
    }
    echo '<td>'.$memberType.'</td>';

    if($adminView<>1 && $r['unitID']<>"")
    {
	echo '<td>'.$r['unitID'].'</td>';
    }
    else
    {
	echo '<td>'.$r['EMTid'].'</td>';
    }
    if($adminView==1)
    {
	echo '<td><a href="javascript:rosterPopUp('."'rosterCertsEdit.php?EMTid=".$r['EMTid']."&action=edit'".')">EDIT</a></td>';
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
    if(! empty($r['CPR']))
    {
	echo '<td>'.certDate($r['CPR']).'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['EMT']))
    {
	echo '<td>'.certDate($r['EMT']).'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['FR']))
    {
	echo '<td>'.certDate($r['FR']).'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['HazMat']))
    {
	echo '<td>'.certDate($r['HazMat']).'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['BBP']))
    {
	echo '<td>'.certDate($r['BBP']).'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['PHTLS']))
    {
	echo '<td>'.certDate($r['PHTLS']).'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['NREMT']))
    {
	echo '<td>'.certDate($r['NREMT']).'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }

    // get the information for the extended certs from the database
    $otherCerts = $r['OtherCerts']; // other certs CSV list from database
    $otherCertsA = explode(",", $otherCerts); // make an array of the certs
    foreach($extdCerts as $val)
    {
	if(in_array($val, $otherCertsA))
	{
	    // if true, this extd cert (val) is in the member's list (otherCertsA)
	    echo '<td>Yes</td>';
	}
	else
	{
	    echo '<td>&nbsp;</td>';
	}
    }

    echo '</tr>';
}

function certDate($i)
{
    if($i == 1922331600)
    {
	return "&nbsp;";
    }
    else
    {
	if($i - time() < 1296000)
	{
	    // less than 15 days
	    return '<font color="red"><u>'.date("Y-m-d", $i).'</u></font>';
	}

	if($i - time() < 15552000)
	{
	    // less than 6 months
	    return '<font color="orange">'.date("Y-m-d", $i).'</font>';
	}
	return date("Y-m-d", $i);
    }
}

?>  
</table>
</body>
</html>