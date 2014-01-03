<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-11-30 11:43:56 jantman"                                                              |
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
 | $LastChangedRevision:: 72                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/inc/PCRpopulate.php                                    $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * Functions to populate PCR form with data for a given RunNumber
 *
 * @package MPAC-NewCall-PHP
 */

// this function pulls a run from the DB and puts it into a $vals array
// to populate the form
function getRunFromDB($RunNum)
{
    $vals = array();
    $vals['RunNumber'] = $RunNum;
    
    // call
    $query = "SELECT * FROM calls WHERE RunNumber=$RunNum AND is_deprecated=0;";
    $result = mysql_query($query) or db_error($query, mysql_error());
    $row = mysql_fetch_assoc($result);
    $vals['Date'] = date("m/d/Y", $row['date_ts']);
    $vals['patientNum'] = $row['pt_num'];
    $vals['patientOf'] = $row['pt_total'];
    if(isset($row['patient_pkey']) && $row['patient_pkey'] != null){ $vals['ptid'] = $row['patient_pkey']; $vals['ptpkey'] = $row['patient_pkey'];}
    $vals['age'] = $row['pt_age'];
    $vals['PtLocation'] = $row['pt_loc_at_scene'];
    $vals['PtPhysician'] = $row['pt_physician'];
    $vals['calllocid'] = $row['call_loc_id'];
    $vals['OC'] = $row['outcome'];
    $vals['CallType'] = $row['call_type'];
    $vals['signature_EMTid'] = $row['signature_ID'];
    $vals['PtTransTo'] = $row['PtTransferredTo'];
    $vals['Passengers'] = $row['Passengers'];
    $vals['EquipLeft'] = $row['EquipmentLeft'];
    if($row['is_second_rig'] != 0){ $vals['is_second_rig'] = $row['is_second_rig'];}
    $vals['is_duty_call'] = $row['is_duty_call'];

    // times
    $query = "SELECT * FROM calls_times WHERE RunNumber=$RunNum AND is_deprecated=0;";
    $result = mysql_query($query) or db_error($query, mysql_error());
    $row = mysql_fetch_assoc($result);
    if($row['dispatched'] != null){ $vals['time_disp'] = date("H:i", $row['dispatched']);}
    if($row['inservice'] != null){ $vals['time_insvc'] = date("H:i", $row['inservice']);}
    if($row['onscene'] != null){ $vals['time_onscene'] = date("H:i", $row['onscene']);}
    if($row['enroute'] != null){ $vals['time_enroute'] = date("H:i", $row['enroute']);}
    if($row['arrived'] != null){ $vals['time_arrived'] = date("H:i", $row['arrived']);}
    if($row['available'] != null){ $vals['time_avail'] = date("H:i", $row['available']);}
    if($row['outservice'] != null){ $vals['time_out'] = date("H:i", $row['outservice']);}

    // clinical
    $query = "SELECT * FROM calls_clinical WHERE RunNumber=$RunNum AND is_deprecated=0;";
    $result = mysql_query($query) or db_error($query, mysql_error());
    $row = mysql_fetch_assoc($result);
    $vals['chief_complaint'] = $row['chief_complaint'];
    $vals['time_onset'] = $row['time_of_onset'];
    $vals['AidGiven'] = $row['aid_given'];
    if($row['aid_given_by'] != null)
    {
	if(strstr($row['aid_given_by'], ","))
	{
	    $foo = explode(",", $row['aid_given_by']);
	}
	else
	{
	    $foo = array($row['aid_given_by'], "");
	}
	foreach($foo as $s)
	{
	    if(trim($s) == "PD"){ $vals['AidGivenBy_PD'] = "on";}
	    if(trim($s) == "Family"){ $vals['AidGivenBy_Family'] = "on";}
	    if(trim($s) == "Bystander"){ $vals['AidGivenBy_Bystander'] = "on";}
	    if(strstr(trim($s), "Other:"))
	    {
		$vals['AidGivenBy_Other'] = "on";
		$vals["AidGivenBy_Other_Text"] = substr($s, strpos($s, ":"));
	    }
	}
    }
    $vals['Allergies'] = $row['allergies'];
    $vals['Medications'] = $row['medications'];
    $vals['hx'] = $row['pt_hx'];
    $vals['remarks'] = $row['remarks'];
    if($row['has_loss_of_consc'] == 1){ $vals['loc'] = "yes";} else { $vals['loc'] = "no";}

    // patient
    if(isset($vals['ptid']))
    {
	$query = "SELECT * FROM patients WHERE Pkey=".$vals['ptid'].";";
	$result = mysql_query($query) or db_error($query, mysql_error());
	$row = mysql_fetch_assoc($result);
	$vals['DOB'] = date("m/d/Y", strtotime($row['DOB']));
	$vals['NameFirst'] = $row['FirstName'];
	$vals['NameMiddle'] = $row['MiddleName'];
	$vals['NameLast'] = $row['LastName'];
	$vals['sex'] = $row['Sex'];
	$vals['Address'] = makeAddress($row['StreetNumber'], $row['Street'], $row['AptNumber'], $row['City'], $row['State']);
	$vals['fdf_Address'] = makeAddress($row['StreetNumber'], $row['Street'], $row['AptNumber']);
	$vals['fdf_Town'] = $row['City'].", ".$row['State'];
    }
    
    // unit
    $query = "SELECT * FROM calls_units WHERE RunNumber=$RunNum AND is_deprecated=0;";
    $result = mysql_query($query) or db_error($query, mysql_error());
    if(mysql_num_rows($result) > 0)
    {
	$row = mysql_fetch_assoc($result);
	$vals['unit'] = $row['unit'];
	$vals['mileage'] = (int)$row['end_mileage'];
    }

    // Mutual Aid
    $query = "SELECT * FROM calls_MA WHERE RunNumber=$RunNum AND is_deprecated=0;";
    $result = mysql_query($query) or db_error($query, mysql_error());
    if(mysql_num_rows($result) > 0)
    {
	$row = mysql_fetch_assoc($result);
	$MA = $row['City'];
	$query = "SELECT oC_id,oC_state,oC_City FROM opt_Cities WHERE oC_is_MA=1 AND oC_City='".$MA."';";
	$result = mysql_query($query) or db_error($query, mysql_error());
	if(mysql_num_rows($result) > 0)
	{
	    // this is in our drop-down
	    $vals['MA_town'] = $MA;
	    $vals['MAcheck'] = "on";
	}
	else
	{
	    // not in drop-down
	    $vals['MAcheck'] = "on";
	    $vals['MA_town'] = "--Other";
	    $vals['MA_town_other'] = $MA;
	}
    }

    // ALS
    $query = "SELECT * FROM calls_als WHERE RunNumber=$RunNum AND is_deprecated=0;";
    $result = mysql_query($query) or db_error($query, mysql_error());
    if(mysql_num_rows($result) > 0)
    {
	$row = mysql_fetch_assoc($result);
	$vals['ALS'] = $row['als_status'];
	$vals['ALSunit'] = $row['als_unit'];
    }

    // call location
    if($vals['calllocid'] == 0)
    {
	$vals['CallLoc'] = "Home";
    }
    else
    {
	$vals['CallLoc'] = "Other";
	$vals['call_loc_other'] = makeCallLocAddress($vals['calllocid']);
    }

    // injured area
    $query = "SELECT * FROM calls_injured_area WHERE RunNumber=$RunNum AND is_deprecated=0;";
    $result = mysql_query($query) or db_error($query, mysql_error());
    if(mysql_num_rows($result) > 0)
    {
	$foo = "";
	while($row = mysql_fetch_assoc($result))
	{
	    $foo .= $row['name'].", ";
	}
	$foo = trim($foo, ", ");
	$vals['injured_area'] = $foo;
    }

    // treatments
    $query = "SELECT * FROM calls_tx WHERE RunNumber=$RunNum AND is_deprecated=0;";
    $result = mysql_query($query) or db_error($query, mysql_error());
    if(mysql_num_rows($result) > 0)
    {
	$foo = "";
	while($row = mysql_fetch_assoc($result))
	{
	    $foo .= $row['treatment'].", ";
	}
	$foo = trim($foo, ", ");
	$vals['tx'] = $foo;
    }

    // crew
    $query = "SELECT * FROM calls_crew WHERE RunNumber=$RunNum AND is_deprecated=0;";
    $result = mysql_query($query) or db_error($query, mysql_error());
    $i = 0;
    $vals['nextCrewRow'] = mysql_num_rows($result)+1;
    while($row = mysql_fetch_assoc($result))
    {
	$vals['crew_id_'.$i] = $row['EMTid'];
	if($row['is_driver_to_scene'] == 1){ $vals['crew_driver_scene'] = $i;}
	if($row['is_driver_to_hosp'] == 1){ $vals['crew_driver_hosp'] = $i;}
	if($row['is_driver_to_bldg'] == 1){ $vals['crew_driver_bldg'] = $i;}
	if($row['is_on_scene'] == 1){ $vals['crew_onscene'.$i] = "on";}
	if($row['is_on_duty'] == 1){ $vals['crew_genDuty_'.$i] = "Duty";}
	$i++;
    }

    // vitals
    $query = "SELECT * FROM calls_vitals WHERE RunNumber=$RunNum AND is_deprecated=0;";
    $result = mysql_query($query) or db_error($query, mysql_error());
    $vals['nextVitalsRow'] = mysql_num_rows($result)+1;
    while($row = mysql_fetch_assoc($result))
    {
	$i = $row['vitals_set_number'];
	$vals['Vitals_'.$i.'_time'] = $row['time'];
	$vals['Vitals_'.$i.'_bp'] = $row['bp'];
	$vals['Vitals_'.$i.'_pulse'] = $row['pulse'];
	$vals['Vitals_'.$i.'_resp'] = $row['resps'];
	$vals['Vitals_'.$i.'_lungSounds'] = $row['lung_sounds'];
	$vals['Vitals_'.$i.'_consciousness'] = $row['consciousness'];
	$vals['Vitals_'.$i.'_pupilL'] = $row['pupils_left'];
	$vals['Vitals_'.$i.'_pupilR'] = $row['pupils_right'];
	$vals['Vitals_'.$i.'_spo2'] = $row['spo2'];
	$vals['Vitals_'.$i.'_skinTemp'] = $row['skin_temp'];
	$vals['Vitals_'.$i.'_skinColor'] = $row['skin_color'];
	$vals['Vitals_'.$i.'_skinMoisture'] = $row['skin_moisture'];
    }

    return $vals;
}

?>