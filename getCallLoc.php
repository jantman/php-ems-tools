<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-25 22:56:59 jantman"                                                              |
 +--------------------------------------------------------------------------------------------------------+
 | Copyright (c) 2009, 2010 Jason Antman. All rights reserved.                                            |
 |                                                                                                        |
 | This program is free software; you can redistribute it and/or modify                                   |
 | it under the terms of the GNU General Public License as published by                                   |
 | the Free Software Foundation; either version 3 of the License, or                                      |
 | (at your option) any later version.                                                                    |
 |                                                                                                        |
 | This program is distributed in the hope that it will be useful,                                        |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of                                         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                                          |
 | GNU General Public License for more details.                                                           |
 |                                                                                                        |
 | You should have received a copy of the GNU General Public License                                      |
 | along with this program; if not, write to:                                                             |
 |                                                                                                        |
 | Free Software Foundation, Inc.                                                                         |
 | 59 Temple Place - Suite 330                                                                            |
 | Boston, MA 02111-1307, USA.                                                                            |
 +--------------------------------------------------------------------------------------------------------+
 |Please use the above URL for bug reports and feature/support requests.                                  |
 +--------------------------------------------------------------------------------------------------------+
 | Authors: Jason Antman <jason@jasonantman.com>                                                          |
 +--------------------------------------------------------------------------------------------------------+
 | $LastChangedRevision:: 67                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/getCallLoc.php                                         $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * Handler to return the information on a specified call location as JSON.
 *
 * @package MPAC-NewCall-Handlers
 */

require_once('inc/newcall.php.inc');

if(isset($_GET['id']))
{
    $id = (int)$_GET['id'];
    $query = "SELECT Pkey,call_loc_id,place_name,StreetNumber,Street,AptNumber,City,State,Intsct_Street FROM calls_locations WHERE call_loc_id=$id AND is_deprecated=0;";
}
elseif(isset($_GET['pkey']))
{
    $pkey = (int)$_GET['pkey'];
    $query = "SELECT Pkey,call_loc_id,place_name,StreetNumber,Street,AptNumber,City,State,Intsct_Street FROM calls_locations WHERE Pkey=$pkey AND is_deprecated=0;";
}
$result = mysql_query($query) or die("Error in Query: ".$query."<br />ERROR: ".mysql_error()."<br />");

if(mysql_num_rows($result) < 1)
{
    $arr = array("ERROR" => "ERROR: Invalid Call Location ID.");
    echo json_encode($arr);
}

// JUST RETURN id and DisplayAddress

$row = mysql_fetch_assoc($result);
$foo = array();
$foo['id'] = $row['call_loc_id'];

if(isset($row['Intsct_Street']))
{
    $foo['DisplayAddress'] = makeIntsctAddress($row['Street'], $row['Intsct_Street'], $row['City'], $row['State']);
}
else
{
    $foo['DisplayAddress'] = makeAddress($row['StreetNumber'], $row['Street'], $row['AptNumber'], $row['City'], $row['State']);
}

if(trim($row['City']) != "")
{
    $foo['City'] = trim($row['City']);
}

require_once('Services/JSON.php');
$json = new Services_JSON();
echo $json->encode($foo);

// TODO - couldn't use this on PCRserv with old PHP
//echo json_encode($foo);

?>

