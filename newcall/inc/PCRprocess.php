<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-23 23:42:05 jantman"                                                              |
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
 | $LastChangedRevision:: 66                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/inc/PCRprocess.php                                     $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * Functions to process new PCR form - put to database.
 *
 * @package MPAC-NewCall
 */

/**
 * Puts the complete PCR to the database
 *
 * NOTE - everything MUST be transaction-safe: any error rolls back everything, returns a message, and spits back the form.
 *
 * @return boolean|string boolean true or a string contaning an error message to be displayed to the user
 */
function process_new_PCR_form()
{
    $DEBUG = false;
    $DEBUG_NOCOMMIT = false;
    $CREW_DEBUG = false;
    $errors = false;
    trans_start();

    // TODO - how to make sure nobody has stolen our RunNumber since we first loaded the form???
    $RunNumber = ((int)$_POST['RunNumber']);

    // Make sure it isn't already here.
    $query = "SELECT * FROM calls WHERE RunNumber=$RunNumber;";
    $result = mysql_query($query) or db_error($query, mysql_error());
    if(mysql_num_rows($result) > 0)
    {
	die("ERROR: this RunNumber already exists in the database. apparently something went wrong. Please call Jay at (201)906-7347.");
    }

    // CALLS
    $query = "INSERT INTO calls SET RunNumber=".$RunNumber;
    if(trim($_POST['Date']) != ""){ $query .= ",date_date='".date("Y-m-d", strtotime($_POST['Date']))."'";}
    $query .= ",date_ts=".$_POST['ts_Date'];
    $query .= ",submit_ts=".time();
    if(trim($_POST['patientNum']) != ""){ $query .= ",pt_num='".((int)$_POST['patientNum'])."'";}
    if(trim($_POST['patientOf']) != ""){ $query .= ",pt_total='".((int)$_POST['patientOf'])."'";}
    if(isset($_POST['ptpkey'])){ $query .= ",patient_pkey=".((int)$_POST['ptpkey']);}
    if(trim($_POST['age']) != ""){ $query .= ",pt_age=".((int)$_POST['age']);}
    if(trim($_POST['PtLocation']) != ""){ $query .= ",pt_loc_at_scene='".mysql_real_escape_string(trim($_POST['PtLocation']))."'";}
    if(trim($_POST['PtPhysician']) != ""){ $query .= ",pt_physician='".mysql_real_escape_string(trim($_POST['PtPhysician']))."'";}
    if(trim($_POST['CallLoc']) == "Home"){ $query .= ",call_loc_id=0";}
       else { $query .= ",call_loc_id=".((int)$_POST['calllocid']);}
    $query .= ",outcome='".mysql_real_escape_string(trim($_POST['OC']))."'";
    $query .= ",call_type='".mysql_real_escape_string(trim($_POST['CallType']))."'";
    $query .= ",signature_ID='".mysql_real_escape_string(trim($_POST['signature_EMTid']))."'";
    // TODO - is signature authenticated?
    // if yes:
    //$query .= ",signature_auth=1";

    if(trim($_POST['PtTransTo']) != ""){ $query .= ",PtTransferredTo='".mysql_real_escape_string(trim($_POST['PtTransTo']))."'";}
    if(trim($_POST['Passengers']) != ""){ $query .= ",Passengers='".mysql_real_escape_string(trim($_POST['Passengers']))."'";}
    if(trim($_POST['EquipLeft']) != ""){ $query .= ",EquipmentLeft='".mysql_real_escape_string(trim($_POST['EquipLeft']))."'";}

    // figure out overall duty/general stuff, as well as duty/general for each member
    $duty_status = getDutyCrewByTS($_POST["ts_time_disp"]);
    $probies = getProbiesByEMTid();
    $num = (int)$_POST['nextCrewRow'];
    if($CREW_DEBUG){ echo "BEGIN Duty/Gen Calculation. nextCrewRow=$num<br />duty_status: ".$duty_status."<br />";}
    $is_duty = true;
    for($i = 0; $i < $num; $i++)
    {
	$blam = ""; // for $CREW_DEBUG
	if(trim($_POST['crew_id_'.$i]) == ""){ continue;}
	if(in_array(trim($_POST['crew_id_'.$i]), $duty_status))
	{
	    $_POST['crew_genDuty_'.$i] = "Duty";
	    if($CREW_DEBUG){ echo "EMTid=".$_POST['crew_id_'.$i]." DUTY. is_duty=".($is_duty ? "true" : "false")."<br />";}
	}
	else
	{
	    $_POST['crew_genDuty_'.$i] = "Gen";
	    if($CREW_DEBUG){ echo "EMTid=".$_POST['crew_id_'.$i]." on_scene=".$_POST['crew_onscene'.$i]." GENERAL. is_duty=".($is_duty ? "true" : "false")."<br />";}
	    // only set not duty crew if not probie and not on scene
	    if(! in_array(trim($_POST['crew_id_'.$i]), $probies) && ($_POST['crew_onscene'.$i] != "on"))
	    {
		$is_duty = false;
		if($CREW_DEBUG){ echo "EMTid=".$_POST['crew_id_'.$i]." GENERAL. Not probie or on scene. is_duty=".($is_duty ? "true" : "false")."<br />";}
	    }
	}
    }
    
    if($num == 0){ $is_duty = false;} // 2010-04-19 jantman - if we have no crew, it's a general.

    if($CREW_DEBUG){ echo "FINISHED CREW - is_duty=".($is_duty ? "true" : "false")."<br />";}

    if($is_duty){ $query .= ",is_duty_call=1";}
    elseif(isset($_POST['is_second_rig']) && $_POST['is_second_rig'] == "yes"){ $query .= ",is_second_rig=1";}

    $query .= ";";
    if($DEBUG)
    {
	echo '<pre>'.$query.'</pre>'."\n";
    }
    else
    {
	$bar = trans_safe_query($query);
	if(! $bar){ $errors = true;}
    }
    
    // UNIT
    if(isset($_POST['unit']) && trim($_POST['unit']) != "" && trim($_POST['unit']) != "N/A")
    {
	$query = "INSERT INTO calls_units SET RunNumber=".$RunNumber;
	$query .= ",unit='".mysql_real_escape_string(trim($_POST['unit']))."'";
	$query .= ",end_mileage=".((int)$_POST['mileage']);
	$query .= ";";
	if($DEBUG)
	{
	    echo '<pre>'.$query.'</pre>'."\n";
	}
	else
	{
	    $bar = trans_safe_query($query);
	    if(! $bar){ $errors = true;}
	}
    }

    // MUTUAL AID
    if(isset($_POST['MAcheck']) && trim($_POST['MAcheck']) == "on")
    {
	$query = "INSERT INTO calls_MA SET RunNumber=".$RunNumber;
	if(trim($_POST['MA_town']) == "--Other")
	{
	    $query .= ",City='".mysql_real_escape_string(trim($_POST['MA_town_other']))."'";
	}
	else
	{
	    $query .= ",City='".mysql_real_escape_string(trim($_POST['MA_town']))."'";
	}
	$query .= ";";
	if($DEBUG)
	{
	    echo '<pre>'.$query.'</pre>'."\n";
	}
	else
	{
	    $bar = trans_safe_query($query);
	    if(! $bar){ $errors = true;}
	}

    }

    // TIMES
    $query = "INSERT INTO calls_times SET RunNumber=".$RunNumber;
    if(isset($_POST['ts_time_disp'])){ $query .= ",dispatched=".$_POST['ts_time_disp'];}
    if(isset($_POST["ts_time_insvc"])){ $query .= ",inservice=".$_POST['ts_time_insvc'];}
    if(isset($_POST['ts_time_onscene'])){ $query .= ",onscene=".$_POST['ts_time_onscene'];}
    if(isset($_POST['ts_time_enroute'])){ $query .= ",enroute=".$_POST['ts_time_enroute'];}
    if(isset($_POST['ts_time_arrived'])){ $query .= ",arrived=".$_POST['ts_time_arrived'];}
    if(isset($_POST['ts_time_avail'])){ $query .= ",available=".$_POST['ts_time_avail'];}
    if(isset($_POST['ts_time_out'])){ $query .= ",outservice=".$_POST['ts_time_out'];}
    $query .= ";";
    if($DEBUG)
    {
        echo '<pre>'.$query.'</pre>'."\n";
    }
    else
    {
        $bar = trans_safe_query($query);
        if(! $bar){ $errors = true;}
    }


    // ALS
    $query = "INSERT INTO calls_als SET RunNumber=".$RunNumber;
    $query .= ",als_status='".mysql_real_escape_string(trim($_POST['ALS']))."'";
    if(trim($_POST['ALSunit']) != ""){ $query .= ",als_unit='".mysql_real_escape_string(trim($_POST['ALSunit']))."'";}
    $query .= ";";
    if($DEBUG)
    {
        echo '<pre>'.$query.'</pre>'."\n";
    }
    else
    {
        $bar = trans_safe_query($query);
        if(! $bar){ $errors = true;}
    }


    // TX
    if(trim($_POST['tx']) != "")
    {
	$foo = explode(",", $_POST['tx']);
	foreach($foo as $val)
	{
	    $val = trim($val);
	    if($val == ""){ continue;}
	    $query = "INSERT INTO calls_tx SET RunNumber=".$RunNumber.",treatment='".mysql_real_escape_string(trim($val))."';";
	    if($DEBUG)
	    {
		echo '<pre>'.$query.'</pre>'."\n";
	    }
	    else
	    {
		$bar = trans_safe_query($query);
		if(! $bar){ $errors = true;}
	    }
	}
    }

    // Injured Area
    if(trim($_POST['injured_area']) != "")
    {
	$foo = explode(",", $_POST['injured_area']);
	foreach($foo as $val)
	{
	    $val = trim($val);
	    if($val == ""){ continue;}
	    $query = "INSERT INTO calls_injured_area SET RunNumber=".$RunNumber.",name='".mysql_real_escape_string(trim($val))."';";
	    if($DEBUG)
	    {
		echo '<pre>'.$query.'</pre>'."\n";
	    }
	    else
	    {
		$bar = trans_safe_query($query);
		if(! $bar){ $errors = true;}
	    }
	}
    }



    // CREW
    $num = (int)$_POST['nextCrewRow'];
    for($i = 0; $i < $num; $i++)
    {
	if(trim($_POST['crew_id_'.$i]) == ""){ continue;}
	$query = "INSERT INTO calls_crew SET RunNumber=".$RunNumber;
	$query .= ",EMTid='".mysql_real_escape_string(trim($_POST['crew_id_'.$i]))."'";
	if(isset($_POST['crew_driver_scene']) && $_POST['crew_driver_scene'] == $i){ $query .= ",is_driver_to_scene=1";}
	if(isset($_POST['crew_driver_bldg']) && $_POST['crew_driver_bldg'] == $i){ $query .= ",is_driver_to_bldg=1";}
	if(isset($_POST['crew_driver_hosp']) && $_POST['crew_driver_hosp'] == $i){ $query .= ",is_driver_to_hosp=1";}
	if($_POST['crew_onscene'.$i] == "on"){ $query .= ",is_on_scene=1";}
	if(trim($_POST['crew_genDuty_'.$i]) == "Duty"){ $query .= ",is_on_duty=1";}
	$query .= ";";
	if($DEBUG)
	{
	    echo '<pre>'.$query.'</pre>'."\n";
	}
	else
	{
	    $bar = trans_safe_query($query);
	    if(! $bar){ $errors = true;}
	}
    }
    // TODO - should also record the overall duty/general status here.
    // do we have to do an update to the call? should we have done this before?

    // VITALS
    $num = (int)$_POST['nextVitalsRow'];
    for($i = 0; $i < $num; $i++)
    {
	$query = "INSERT INTO calls_vitals SET RunNumber=".$RunNumber.",vitals_set_number=".$i;
	$foo = $query;
	if(trim($_POST['Vitals_'.$i.'_time']) != ""){ $query .= ",time='".mysql_real_escape_string(trim($_POST['Vitals_'.$i.'_time']))."'";}
	if(trim($_POST['Vitals_'.$i.'_bp']) != ""){ $query .= ",bp='".mysql_real_escape_string(trim($_POST['Vitals_'.$i.'_bp']))."'";}
	if(trim($_POST['Vitals_'.$i.'_pulse']) != ""){ $query .= ",pulse='".mysql_real_escape_string(trim($_POST['Vitals_'.$i.'_pulse']))."'";}
	if(trim($_POST['Vitals_'.$i.'_resp']) != ""){ $query .= ",resps='".mysql_real_escape_string(trim($_POST['Vitals_'.$i.'_resp']))."'";}
	if(trim($_POST['Vitals_'.$i.'_lungSounds']) != ""){ $query .= ",lung_sounds='".mysql_real_escape_string(trim($_POST['Vitals_'.$i.'_lungSounds']))."'";}
	if(trim($_POST['Vitals_'.$i.'_pupilL']) != "NONE"){ $query .= ",pupils_left='".mysql_real_escape_string(trim($_POST['Vitals_'.$i.'_pupilL']))."'";}
	if(trim($_POST['Vitals_'.$i.'_pupilR']) != "NONE"){ $query .= ",pupils_right='".mysql_real_escape_string(trim($_POST['Vitals_'.$i.'_pupilR']))."'";}
	if(trim($_POST['Vitals_'.$i.'_skinTemp']) != ""){ $query .= ",skin_temp='".mysql_real_escape_string(trim($_POST['Vitals_'.$i.'_skinTemp']))."'";}
	if(trim($_POST['Vitals_'.$i.'_skinColor']) != ""){ $query .= ",skin_color='".mysql_real_escape_string(trim($_POST['Vitals_'.$i.'_skinColor']))."'";}
	if(trim($_POST['Vitals_'.$i.'_skinMoisture']) != ""){ $query .= ",skin_moisture='".mysql_real_escape_string(trim($_POST['Vitals_'.$i.'_skinMoisture']))."'";}
	if(trim($_POST['Vitals_'.$i.'_spo2']) != ""){ $query .= ",spo2='".mysql_real_escape_string(trim($_POST['Vitals_'.$i.'_spo2']))."'";}

	if($foo == $query){ continue;} // everything was empty, we didn't add anything.

	if(trim($_POST['Vitals_'.$i.'_consciousness']) != ""){ $query .= ",consciousness='".mysql_real_escape_string(trim($_POST['Vitals_'.$i.'_consciousness']))."'";}
	$query .= ";";
	if($DEBUG)
	{
	    echo '<pre>'.$query.'</pre>'."\n";
	}
	else
	{
	    $bar = trans_safe_query($query);
	    if(! $bar){ $errors = true;}
	}
    }

    // CLINICAL
    $query = "INSERT INTO calls_clinical SET RunNumber=".$RunNumber;
    if(trim($_POST['chief_complaint']) != ""){ $query .= ",chief_complaint='".mysql_real_escape_string(trim($_POST['chief_complaint']))."'";}
    if(trim($_POST['time_onset']) != ""){ $query .= ",time_of_onset='".mysql_real_escape_string(trim($_POST['time_onset']))."'";}

    if(trim($_POST['AidGiven']) != "")
    {
	$query .= ",aid_given='".mysql_real_escape_string(trim($_POST['AidGiven']))."'";
	// add each GivenBy field value to a temp variable
	$foo = "";
	if(isset($_POST["AidGivenBy_PD"]) && trim($_POST["AidGivenBy_PD"]) == "on") { $foo .= "PD, "; }
	if(isset($_POST["AidGivenBy_Family"]) && trim($_POST["AidGivenBy_Family"]) == "on") { $foo .= "Family, "; }
	if(isset($_POST["AidGivenBy_Bystander"]) && trim($_POST["AidGivenBy_Bystander"]) == "on") { $foo .= "Bystander, "; }
	if(isset($_POST["AidGivenBy_Other"]) && trim($_POST["AidGivenBy_Other"]) == "on")
	{
	    $foo .= "Other: ".trim($_POST["AidGivenBy_Other_Text"]).", ";
	}
	
	// if the temp variable isn't empty, clean it up and put it to the DB
	if(trim($foo != ""))
	{
	    $foo = trim($foo, ", ");
	    $query .= ",aid_given_by='".mysql_real_escape_string($foo)."'";
	}
    }

    if(trim($_POST['Allergies']) != ""){ $query .= ",allergies='".mysql_real_escape_string(trim($_POST['Allergies']))."'";}
    if(trim($_POST['Medications']) != ""){ $query .= ",medications='".mysql_real_escape_string(trim($_POST['Medications']))."'";}
    if(trim($_POST['hx']) != ""){ $query .= ",pt_hx='".mysql_real_escape_string(trim($_POST['hx']))."'";}
    if(trim($_POST['remarks']) != ""){ $query .= ",remarks='".mysql_real_escape_string(trim($_POST['remarks']))."'";}
    if(isset($_POST['loc']) && trim($_POST['loc']) == "yes"){ $query .= ",has_loss_of_consc=1";}
    $query .= ";";
    if($DEBUG)
    {
        echo '<pre>'.$query.'</pre>'."\n";
    }
    else
    {
        $bar = trans_safe_query($query);
        if(! $bar){ $errors = true;}
    }

    if($DEBUG_NOCOMMIT){ trans_rollback(); return false;}

    // DEBUG
    if($errors == true)
    {
	trans_rollback();
	return false;
    }
    else
    {
	trans_commit();
	return true;
    }
}
?>