<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-25 22:51:45 jantman"                                                              |
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
 | $HeadURL:: http://svn.jasonantman.com/newcall/updatePtHandler.php                                    $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * Handler for update patient form.
 *
 * @package MPAC-NewCall-Handlers
 */

require_once('inc/newcall.php.inc');
require_once('inc/JAforms.php');
require_once('inc/formFuncs.php');

if(isset($_GET['ptid']) && $_GET['ptid'] != -1)
{
    $id = (int)$_GET['ptid'];
    updatePatient($id);
}
else
{
    updatePatient(-1);
}

function updatePatient($id)
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
	$query = "SELECT MAX(patient_id) AS id FROM patients;";
	$result = mysql_query($query) or db_error($query, mysql_error());
	$row = mysql_fetch_assoc($result);
	$new_id = ((int)$row['id']) + 1;
    }
    
    if(strstr($_GET['pt_NameFirst'], "?") || strstr($_GET['pt_NameLast'], "?"))
    {
	die("ERROR: You can NOT do that. If you didn't physically see a patient, leave ALL patient information blank.");
    }

    if(trim($_GET['pt_AddressStreetNum']) == "St#")
    {
	die("ERROR: You must enter a street number.");
    }

    trans_start();

    $query = "INSERT INTO patients SET ";
    if($id != -1)
    {
	$query .="patient_id=$id";
    }
    else
    {
	$query .="patient_id=$new_id";
    }
    $query .= ",DOB='".date("Y-m-d", strtotime($_GET['pt_DOB']))."'";
    $query .= ",Sex='".$_GET['pt_sex']."'";
    if(isset($_GET['pt_NameFirst']) && trim($_GET['pt_NameFirst']!= "")){ $query .= ",FirstName='".mysql_real_escape_string(trim($_GET['pt_NameFirst']))."'";}
    if(isset($_GET['pt_NameLast']) && trim($_GET['pt_NameLast']!= "")){ $query .= ",LastName='".mysql_real_escape_string(trim($_GET['pt_NameLast']))."'";}
    if(isset($_GET['pt_NameMiddle']) && trim($_GET['pt_NameMiddle']!= "")){ $query .= ",MiddleName='".mysql_real_escape_string(trim($_GET['pt_NameMiddle']))."'";}
    if(isset($_GET['pt_AddressState']) && trim($_GET['pt_AddressState']!= "")){ $query .= ",State='".mysql_real_escape_string(trim($_GET['pt_AddressState']))."'";}

    if(isset($_GET['pt_AddressCity']) && trim($_GET['pt_AddressCity']!= ""))
    {
	if(trim($_GET['pt_AddressCity']) == "--Other")
	{
	    $query .= ",City='".mysql_real_escape_string(trim($_GET['pt_city_other']))."'";
	}
	else
	{
	    $query .= ",City='".mysql_real_escape_string(trim($_GET['pt_AddressCity']))."'";
	}
    }

    if(isset($_GET['pt_AddressStreet']) && trim($_GET['pt_AddressStreet']!= ""))
    {
	if(trim($_GET['pt_AddressCity']) == "Midland Park")
	{
	    $query .= ",Street='".mysql_real_escape_string(trim($_GET['pt_AddressStreetMP']))."'";
	}
	else
	{
	    $query .= ",Street='".mysql_real_escape_string(trim($_GET['pt_AddressStreet']))."'";
	}
    }

    if(isset($_GET['pt_AddressStreetNum']) && trim($_GET['pt_AddressStreetNum']!= "")){ $query .= ",StreetNumber='".mysql_real_escape_string(trim($_GET['pt_AddressStreetNum']))."'";}
    if(isset($_GET['pt_AddressApt']) && trim($_GET['pt_AddressApt'])!= "" && trim($_GET['pt_AddressApt']) != "Apt#"){ $query .= ",AptNumber='".mysql_real_escape_string(trim($_GET['pt_AddressApt']))."'";}
    if($id != -1){ $query .= ",deprecates_id=$old_Pkey;";}
    //echo $query;
    $result = trans_safe_query($query) or db_error($query, mysql_error());
    $new_Pkey = mysql_insert_id();
    
    if($id != -1)
    {
	$query = "UPDATE patients SET deprecated_by_id=$new_Pkey,is_deprecated=1 WHERE Pkey=$old_Pkey;";
	//echo $query;
	$result = trans_safe_query($query) or db_error($query, mysql_error());
    }

    // log the edit
    if($id == -1)
    {
	log_edit("patients", "add", 0, $new_Pkey);
    }
    else
    {
	log_edit("patients", "update", $old_Pkey, $new_Pkey);
    }
    trans_commit();
    echo $new_Pkey;
}

?>