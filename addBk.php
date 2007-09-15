<?php
// addBk.php
//
// Page to view your organization's address book.
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

if(! empty($_GET['sort']))
{
    $sort = $_GET['sort'];
}
else
{
    $sort = "company";
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
echo '<title>'.$shortName.' - View Address Book</title>';
echo '<script type="text/javascript" src="php-ems-tools.js"> </script>';
echo '</head>';
echo '<body>';
echo '<table class="roster">';
// END OF PAGE TOP HTML

//Finish setting up the table
if($adminView==1)
{ $colspan = 11;}
else
{ $colspan = 10;}
echo "\n"; // linefeed
echo '<td align=center colspan="'.$colspan.'"><b>'.$orgName.' Address Book</b><br> (as of '.date("M d Y").')';
//echo '<a href="javascript:helpPopUp('."'docs/roster_help.php'".')">HELP</a>';
if($adminView==1)
{
    echo '<br><a href="javascript:rosterPopUp('."'addBkEdit.php?action=new'".')">Add New Entry</a>';
    echo '&nbsp; &nbsp; &nbsp; <a href="addBk.php?adminView=0&sort='.$sort.'">Standard View</a>';
}
echo '</td>';
echo "\n"; // linefeed

//CONNECT TO THE DB
$connection = mysql_connect() or die ("I'm sorry, but I was unable to connect! (MySQL error: unable to connect).".$errorMsg);
//SELECT pcr
mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!".$errorMsg);
//QUERY
if($sort=="pKey")
{
    $query =  "SELECT * FROM addBk ORDER BY pKey;";
}
else
{
    $query  = "SELECT * FROM addBk ORDER BY ".$sort.";";
}
$result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query: ".$query."<br><br>" . mysql_error().'<br><br>'.$errorMsg);
mysql_close($connection); 

//setup the table

echo '<tr>';
echo '<td><a href="addBk.php?sort=company';
if($adminView==1){ echo '&adminView=1';}
echo '">Company</a></td>';
if($adminView==1)
{
    echo '<td>Edit</td>';
}
echo '<td><a href="addBk.php?sort=description';
if($adminView==1){ echo '&adminView=1';}
echo '">Description</a></td>';
echo '<td><a href="addBk.php?sort=contact';
if($adminView==1){ echo '&adminView=1';}
echo '">Contact</a></td>';
echo '<td><a href="addBk.php?sort=address';
if($adminView==1){ echo '&adminView=1';}
echo '">Address</a></td>';
echo '<td><a href="addBk.php?sort=phone1';
if($adminView==1){ echo '&adminView=1';}
echo '">Phone1</a></td>';
echo '<td><a href="addBk.php?sort=phone2';
if($adminView==1){ echo '&adminView=1';}
echo '">Phone2</a></td>';
echo '<td><a href="addBk.php?sort=fax';
if($adminView==1){ echo '&adminView=1';}
echo '">Fax</a></td>';
echo '<td><a href="addBk.php?sort=email';
if($adminView==1){ echo '&adminView=1';}
echo '">Email</a></td>';
echo '<td><a href="addBk.php?sort=web';
if($adminView==1){ echo '&adminView=1';}
echo '">Web Site</a></td>';
echo '<td><a href="addBk.php?sort=notes';
if($adminView==1){ echo '&adminView=1';}
echo '">Notes</a></td>';
echo '</tr>';

//loop through the members and call the showMember function
while ($row = mysql_fetch_array($result))  
{
    // figure out the member type
    global $adminView;
    showEntry($row);
}
mysql_free_result($result); 

//this function will display a row for a member
function showEntry($r)
{
    global $adminView;
    
    echo '<tr>';
    if(! empty($r['company']))
    {
	echo '<td>'.$r['company'].'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }


    if($adminView==1)
    {
	echo '<td><a href="javascript:rosterPopUp('."'addBkEdit.php?pKey=".$r['pKey']."&action=edit'".')">EDIT</a></td>';
    }
    if(! empty($r['description']))
    {
	echo '<td>'.$r['description'].'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['contact']))
    {
	echo '<td>'.$r['contact'].'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['address']))
    {
	echo '<td>'.$r['address'].'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['phone1']))
    {
	echo '<td>'.$r['phone1'].'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['phone2']))
    {
	echo '<td>'.$r['phone2'].'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['fax']))
    {
	echo '<td>'.$r['fax'].'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['email']))
    {
	echo '<td><a href="mailto:'.$r['email'].'">'.$r['email'].'</a></td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['web']))
    {
	echo '<td><a href="'.$r['web'].'">'.$r['web'].'</a></td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['notes']))
    {
	echo '<td>'.$r['notes'].'</td>';
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