<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-25 22:55:57 jantman"                                                              |
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
 | $HeadURL:: http://svn.jasonantman.com/newcall/getPt.php                                              $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * Handler to get patient information and return as JSON.
 *
 * @package MPAC-NewCall-Handlers
 */

require_once('inc/newcall.php.inc');

if(isset($_GET['id']))
{
    $id = (int)$_GET['id'];
    $query = "SELECT Pkey,patient_id,FirstName,LastName,MiddleName,Sex,DOB,StreetNumber,Street,AptNumber,City,State FROM patients WHERE patient_id=$id AND is_deprecated=0;";
}
elseif(isset($_GET['pkey']))
{
    $pkey = (int)$_GET['pkey'];
    $query = "SELECT Pkey,patient_id,FirstName,LastName,MiddleName,Sex,DOB,StreetNumber,Street,AptNumber,City,State FROM patients WHERE Pkey=$pkey;";
}
$result = mysql_query($query) or die("Error in Query: ".$query."<br />ERROR: ".mysql_error()."<br />");

if(mysql_num_rows($result) < 1)
{
    $arr = array("ERROR" => "ERROR: Invalid Patient ID.");
    echo json_encode($arr);
}

$row = mysql_fetch_assoc($result);
$foo = array();
$foo['id'] = $row['patient_id'];
$foo['pkey'] = $row['Pkey'];
if(trim($row["DOB"]) != "")
{
    $foo['DOB'] = date("m/d/Y", strtotime($row['DOB']));
    $foo['Age'] = findAgeFromDOB($row['DOB']);
}
if(trim($row['FirstName']) != ""){ $foo['FirstName'] = trim($row['FirstName']);}
if(trim($row['LastName']) != ""){ $foo['LastName'] = trim($row['LastName']);}
if(trim($row['MiddleName']) != ""){ $foo['MiddleName'] = trim($row['MiddleName']);}

$foo['DisplayAddress'] = makeAddress($row['StreetNumber'], $row['Street'], $row['AptNumber'], $row['City'], $row['State']);

if(trim($row['City']) != "")
{
    $foo['City'] = trim($row['City']);
}
if(trim($row['Sex']) != ""){ $foo['Sex'] = trim($row['Sex']);}

require_once('Services/JSON.php');
$json = new Services_JSON();
echo $json->encode($foo);

// TODO - couldn't use this on PCRserv with old PHP
//echo json_encode($foo);

?>

