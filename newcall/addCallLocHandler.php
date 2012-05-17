<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-01-26 16:06:50 jantman"                                                              |
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
 | $LastChangedRevision:: 49                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/addCallLocHandler.php                                  $ |
 +--------------------------------------------------------------------------------------------------------+
*/

require_once('inc/newcall.php.inc');
require_once('inc/JAforms.php');
require_once('inc/formFuncs.php');

/*
if(isset($_GET['id']) && $_GET['id'] != -1)
{
    $id = (int)$_GET['id'];
    updatePatient($id);
}
else
{
    updatePatient(-1);
}
*/

/*
echo "ERROR: ";
echo '<pre>';
echo var_dump($_GET);
echo '</pre>';
die();
*/

addCallLoc(-1);

function addCallLoc($id)
{
    if($id != -1)
    {
	$query = "SELECT Pkey FROM patients WHERE patient_id=$id AND is_deprecated=0;";
	$result = mysql_query($query) or db_error($query, mysql_error());
	$row = mysql_fetch_assoc($result);
	$old_Pkey = $row['Pkey'];
    }
    else
    {
	$query = "SELECT MAX(call_loc_id) AS id FROM calls_locations;";
	$result = mysql_query($query) or db_error($query, mysql_error());
	$row = mysql_fetch_assoc($result);
	$new_id = ((int)$row['id']) + 1;
    }
    
    trans_start();

    $query = "INSERT INTO calls_locations SET ";
    if($id != -1)
    {
	$query .= "call_loc_id=$id";
    }
    else
    {
	$query .= "call_loc_id=$new_id";
    }
    if(isset($_GET['cl_PlaceName']) && trim($_GET['cl_PlaceName']!= "")){ $query .= ",place_name='".mysql_real_escape_string(trim($_GET['cl_PlaceName']))."'";}
    if(isset($_GET['cl_AddressState']) && trim($_GET['cl_AddressState']!= "")){ $query .= ",State='".mysql_real_escape_string(trim($_GET['cl_AddressState']))."'";}

    if(isset($_GET['cl_AddressCity']) && trim($_GET['cl_AddressCity']!= ""))
    {
	if(trim($_GET['cl_AddressCity']) == "--Other")
	{
	    $query .= ",City='".mysql_real_escape_string(trim($_GET['cl_city_other']))."'";
	}
	else
	{
	    $query .= ",City='".mysql_real_escape_string(trim($_GET['cl_AddressCity']))."'";
	}
    }

    if(isset($_GET['cl_intsct']) && trim($_GET['cl_intsct']) == "Yes")
    {
	if(trim($_GET['cl_AddressCity']) == "Midland Park")
	{
	    $query .= ",Street='".mysql_real_escape_string(trim($_GET['cl_AddressIntsctMP1']))."'";
	    $query .= ",Intsct_Street='".mysql_real_escape_string(trim($_GET['cl_AddressIntsctMP2']))."'";
	}
	else
	{
	    $query .= ",Street='".mysql_real_escape_string(trim($_GET['cl_AddressIntsct1']))."'";
	    $query .= ",Intsct_Street='".mysql_real_escape_string(trim($_GET['cl_AddressIntsct2']))."'";
	}
    }
    else
    {
	if(isset($_GET['cl_AddressStreet']) && trim($_GET['cl_AddressStreet']!= ""))
	{
	    if(trim($_GET['cl_AddressCity']) == "Midland Park")
	    {
		$query .= ",Street='".mysql_real_escape_string(trim($_GET['cl_AddressStreetMP']))."'";
	    }
	    else
	    {
		$query .= ",Street='".mysql_real_escape_string(trim($_GET['cl_AddressStreet']))."'";
	    }
	}
	if(isset($_GET['cl_AddressStreetNum']) && trim($_GET['cl_AddressStreetNum']!= "")){ $query .= ",StreetNumber='".mysql_real_escape_string(trim($_GET['cl_AddressStreetNum']))."'";}
	if(isset($_GET['cl_AddressApt']) && trim($_GET['cl_AddressApt'])!= "" && trim($_GET['cl_AddressApt']) != "Apt#"){ $query .= ",AptNumber='".mysql_real_escape_string(trim($_GET['cl_AddressApt']))."'";}
    }

    if($id != -1){ $query .= ",deprecates_id=$old_Pkey;";}
    //echo $query;
    $result = trans_safe_query($query) or db_error($query, mysql_error());
    $new_Pkey = mysql_insert_id();
    
    if($id != -1)
    {
	$query = "UPDATE calls_locations SET deprecated_by_id=$new_Pkey,is_deprecated=1 WHERE Pkey=$old_Pkey;";
	//echo $query;
	$result = trans_safe_query($query) or db_error($query, mysql_error());
    }

    // log the edit
    if($id == -1)
    {
	log_edit("calls_locations", "add", 0, $new_Pkey);
    }
    else
    {
	log_edit("calls_locations", "update", $old_Pkey, $new_Pkey);
    }
    trans_commit();
    echo $new_Pkey;
}

?>