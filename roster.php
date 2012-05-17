<?php
// roster.php
//
// Main page to view roster
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
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/roster.php     $ |
// +----------------------------------------------------------------------+

require_once('./config/config.php'); // main configuration

require_once('./config/rosterConfig.php'); // roster configuration

// this script views the roster from the DB

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

// shortView determines whether we show type/ID/Names only, or everything
if((! empty($_GET['shortView'])) && $_GET['shortView'] == 1)
{
    $shortView = true;
}
else
{
    $shortView = false;
}

global $serverExtRoot;

echo '<head>';
echo '<meta http-equiv="refresh" content="180">';
echo '<link rel="stylesheet" href="php_ems.css" type="text/css">'; // the location of the CSS file for the schedule
echo '<title>'.$shortName.' - View Roster</title>';
echo '<script type="text/javascript" src="php-ems-tools.js"> </script>';
echo '</head>';
echo '<body>';
echo '<table class="roster">';
// END OF PAGE TOP HTML

//Finish setting up the table
if($adminView==1)
{ $colspan = 12;}
else
{ $colspan = 9;}
echo "\n"; // linefeed
echo '<td align=center colspan="'.$colspan.'"><b>'.$orgName.' Roster</b><br> (as of '.date("M d Y").')';
echo '<a href="javascript:helpPopUp('."'docs/roster_help.php'".')">HELP</a>';
if(!$shortView)
{
    echo '&nbsp;&nbsp;<a href="roster.php?sort='.$sort.'&shortView=1">Short View</a>';
}
else
{
    echo '&nbsp;&nbsp;<a href="roster.php?sort='.$sort.'&shortView=0">Normal View</a>';
}
if($adminView==1)
{
    echo '<br><a href="javascript:rosterPopUp('."'rosterEdit.php?action=new'".')">Add New Member</a>';
    echo '&nbsp; &nbsp; &nbsp; <a href="roster.php?adminView=0&sort='.$sort.'">Standard View</a>';
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
global $shortView;
if($shortView)
{
    echo '<tr>';
    echo '<td>&nbsp;</td>'; // memberType
    echo '<td><a href="roster.php?sort=EMTid&shortView=1';
    echo '">ID</a></td>';
    echo '<td><a href="roster.php?sort=LastName&shortView=1';
    echo '">Last Name</a></td>';
    echo '<td><a href="roster.php?sort=FirstName&shortView=1';
    echo '">First Name</a></td>';
    echo '</tr>';
}
else
{
    echo '<tr>';
    echo '<td>&nbsp;</td>'; // memberType
    echo '<td><a href="roster.php?sort=EMTid';
    if($adminView==1){ echo '&adminView=1';}
    echo '">ID</a></td>';
    if($adminView==1)
    {
	echo '<td>Unit</td>';
	echo '<td>Edit</td>';
	echo '<td>shownAs</td>';
    }
    echo '<td><a href="roster.php?sort=LastName';
    if($adminView==1){ echo '&adminView=1';}
    echo '">Last Name</a></td>';
    echo '<td><a href="roster.php?sort=FirstName';
    if($adminView==1){ echo '&adminView=1';}
    echo '">First Name</a></td>';
    echo '<td><a href="roster.php?sort=SpouseName';
    if($adminView==1){ echo '&adminView=1';}
    echo '">Spouse</a></td>';
    echo '<td><a href="roster.php?sort=Address';
    if($adminView==1){ echo '&adminView=1';}
    echo '">Address</a></td>';
    echo '<td><a href="roster.php?sort=HomePhone';
    if($adminView==1){ echo '&adminView=1';}
    echo '">Home Phone</a></td>';
    echo '<td><a href="roster.php?sort=CellPhone';
    if($adminView==1){ echo '&adminView=1';}
    echo '">Cell Phone</a></td>';
    echo '<td><a href="roster.php?sort=Email';
    if($adminView==1){ echo '&adminView=1';}
    echo '">Email</a></td>';
    echo '</tr>';
}

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
    if($shownInShort && $shortView)
    {
	// short view, but this member type is shown
	showMemberShort($row);
    }
    elseif(! $shortView)
    {
	// not the short view
	if($adminView == 1)
	{
	    // if this is the admin view show the roster
	    showMember($row);
	}
	elseif($shownInRoster)
	{
	    // not admin view or short (is standard view)
	    //    and this member type should be shown
	    showMember($row);
	}
    }
}
mysql_free_result($result); 

//this function will display a row for a member
function showMember($r)
{
    global $adminView;
    
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
	echo '<td>'.$r['unitID']." (".$r['EMTid'].')</td>';
    }
    else
    {
	echo '<td>'.$r['EMTid'].'</td>';
    }
    if($adminView==1)
    {
	if($r['unitID']=="")
	{
	    echo '<td>&nbsp;</td>';
	}
	else
	{
	    echo '<td>'.$r['unitID'].'</td>';
	}
	echo '<td><a href="javascript:rosterPopUp('."'rosterEdit.php?EMTid=".$r['EMTid']."&action=edit'".')">EDIT</a></td>';

	if($r['shownAs']<>"")
	{
	echo '<td>'.$r['shownAs'].'</td>';
	}
	else
	{
	    echo '<td>&nbsp;</td>';
	}
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
    if(! empty($r['SpouseName']))
    {
	echo '<td>'.$r['SpouseName'].'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['Address']))
    {
	echo '<td>'.$r['Address'].'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['HomePhone']))
    {
	echo '<td><a href="tel:'.$r['HomePhone'].'">'.$r['HomePhone'].'</a></td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['CellPhone']))
    {
	echo '<td><a href="tel:'.$r['CellPhone'].'">'.$r['CellPhone'].'</a></td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['Email']))
    {
	echo '<td>'.$r['Email'].'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    echo '</tr>';
}

//this function will display a row for a member
function showMemberShort($r)
{
    global $adminView;

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
	echo '<td>'.$r['unitID']." (".$r['EMTid'].')</td>';
    }
    else
    {
	echo '<td>'.$r['EMTid'].'</td>';
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
    echo '</tr>';
}

?>  
</table>
</body>
</html>