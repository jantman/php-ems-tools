<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-24 22:36:19 jantman"                                                              |
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
 | $HeadURL:: http://svn.jasonantman.com/newcall/inc/PCRedit.php                                        $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * Functions to edit PCR.
 *
 * @package MPAC-NewCall-PHP
 */

/**
 * Tells if a row from the table is different from an array of new values.
 * @param string $pkey_name the name of the table's primary key column (will be ignored)
 * @param Array $oldRow the row returned by a MySQL query (the current data)
 * @param Array $newArr the new row (like above but with new data)
 * @return boolean true if the arrays are different, false if they are the same
 * @author Jason Antman <jason@jasonantman.com>
 */
function PCR_arrays_differ($pkey_name, $oldRow, $newArr, $ignoreKeys = null)
{
    $DIFF_DEBUG = true; // TODO - disable once we know things are working OK

    if($DIFF_DEBUG){ echo '<pre>'; echo "===orig newArr=== count=".count($newArr)."\n"; echo var_dump($newArr); echo '</pre>';}
    $newArr = array_merge($oldRow, $newArr);
    if($DIFF_DEBUG){ echo '<pre>'; echo "===new newArr=== count=".count($newArr)."\n"; echo var_dump($newArr); echo '</pre>';}

    // strip out the stuff that doesn't matter/should be different
    if(isset($oldRow['Pkey'])){ unset($oldRow['Pkey']);}
    if(isset($newArr['Pkey'])){ unset($newArr['Pkey']);}
    if(isset($oldRow['is_from_old_calls'])){ unset($oldRow['is_from_old_calls']);}
    if(isset($newArr['is_from_old_calls'])){ unset($newArr['is_from_old_calls']);}
    if(isset($oldRow['is_deprecated'])){ unset($oldRow['is_deprecated']);}
    if(isset($newArr['is_deprecated'])){ unset($newArr['is_deprecated']);}
    if(isset($oldRow['deprecated_by_id'])){ unset($oldRow['deprecated_by_id']);}
    if(isset($newArr['deprecated_by_id'])){ unset($newArr['deprecated_by_id']);}
    if(isset($oldRow['deprecates_id'])){ unset($oldRow['deprecates_id']);}
    if(isset($newArr['deprecates_id'])){ unset($newArr['deprecates_id']);}
    if(isset($oldRow['submit_ts'])){ unset($oldRow['submit_ts']);}
    if(isset($newArr['submit_ts'])){ unset($newArr['submit_ts']);}
    if(isset($oldRow['update_ts'])){ unset($oldRow['update_ts']);}
    if(isset($newArr['update_ts'])){ unset($newArr['update_ts']);}
    if(isset($oldRow[$pkey_name])){ unset($oldRow[$pkey_name]);}
    if(isset($newArr[$pkey_name])){ unset($newArr[$pkey_name]);}

    foreach($oldRow as $key => $val)
    {
	if($oldRow[$key] == null && ! isset($newArr[$key])){ unset($oldRow[$key]);}
    }

    foreach($newArr as $key => $val)
    {
	if($newArr[$key] == null && ! isset($oldRow[$key])){ unset($newArr[$key]);}
    }

    if($ignoreKeys != null)
    {
	foreach($ignoreKeys as $key => $val)
	{
	    if(isset($oldRow[$key])){ unset($oldRow[$key]);}
	    if(isset($newArr[$key])){ unset($newArr[$key]);}
	}
    }

    if($DIFF_DEBUG){ echo '<pre>===array_diff_assoc output:\n'; echo var_dump(array_diff_assoc($oldRow, $newArr)); echo "\n======OLD=====\n"; echo var_dump($oldRow); echo "\n=====NEW====\n"; echo var_dump($newArr); echo '</pre>'; echo '<p>count(oldRow)='.count($oldRow).' count(newRow)='.count($newArr).'</p>';}

    if(count(array_diff_assoc($oldRow, $newArr)) > 0){ return true;}

    return false;
}

/**
 * determines whether or not to update a given row
 *
 * This does a query for the specified row of the specified table using
 * WHERE $pkey_name=$pkey_val AND is_deprecated=0
 * and then calls PCR_arrays_differ() to compare the resulting row with
 * the newArr array. If they differ, it returns true, if not it returns false.
 *
 * @param string $table the name of the table the row is found in
 * @param string $pkey_name the name of the primary key field
 * @param int $pkey_val the value of the primary key field for this function
 * @param Array $newArr the array matching the MySQL result row with the (possibly) new values
 * @return boolean true if the rows differ and it needs to be updated, false otherwise
 */
function PCR_row_needs_update($table, $pkey_name, $pkey_val, $newArr, $ignoreKeys = null)
{
    $query = "SELECT * FROM $table WHERE $pkey_name=$pkey_val AND is_deprecated=0;";
    $result = mysql_query($query) or db_error($query, mysql_error());
    $row = mysql_fetch_assoc($result);
    return PCR_arrays_differ($pkey_name, $row, $newArr);
}

// figure out whether or not the logged in user ia allowed to edit this call
// TODO - implement sessions, have a login/logout link on pages as needed
/**
 * Determines whether the logged in user is allowed to edit this call.
 *
 * First ensures that the user is logged in and authenticated with a valid session. If so, checks that the user
 * (assuming their username is their EMTid) was on the call. If so, allows the edit and returns "Crew". If not,
 * checks if the user is an Admin/Officer. If so, allows the edit and returns "Offier" or "Admin", whichever
 * was matched. Else returns false.
 *
 * @param int $RunNumber the RunNumber for the call the user is attempting to edit.
 * @return boolean|string false if not allowed, otherwise a string describing that matching authorization type (Admin, Officer, Crew)
 *
 */
function PCR_edit_auth($RunNumber)
{
    if(is_valid_session() != 0)
    {
	return 'You must <a href="session.php">Log In</a> to edit a call.';
    }

    // TODO - must be an officer or crew to edit call.

    return "";
}

/**
 * Puts the complete (edited) PCR to the database
 *
 * NOTE - everything MUST be transaction-safe: any error rolls back everything, returns a message, and spits back the form.
 *
 * @param int $RunNumber the RunNumber to edit
 * @return boolean|string boolean true or a string contaning an error message to be displayed to the user
 */
function process_edited_PCR_form($RunNumber)
{
    $DEBUG = true;
    $DEBUG_NOCOMMIT = true;
    $errors = false;
    global $DIFF_DEBUG;
    $DIFF_DEBUG = true;
    trans_start();

    // DEBUG - TODO - remove this
    echo '<pre>';
    echo var_dump($_POST);
    echo '</pre>';
    // END DEBUG

    // get current Pkey for this run
    $query = "SELECT Pkey FROM calls WHERE RunNumber=$RunNumber AND is_deprecated=0;";
    $result = mysql_query($query) or error_log("process_edited_PCR_form: get Pkey query error: Query: $query ERROR: ".mysql_error());
    $row = mysql_fetch_assoc($result);
    $orig_Pkey = $row['Pkey'];

    // auth
    $foo = PCR_edit_auth($RunNumber);
    if($foo != ""){ $errors['auth'] = $foo;}
    

    // CALLS
    $query = "INSERT INTO calls SET RunNumber=".$RunNumber;
    $newArr = array("RunNumber" => $RunNumber);
    if(trim($_POST['Date']) != ""){ $query .= ",date_date='".date("Y-m-d", strtotime($_POST['Date']))."'"; $newArr['date_date'] = date("Y-m-d", strtotime($_POST['Date']));}
    $query .= ",date_ts=".$_POST['ts_Date'];
    $newArr['date_ts'] = $_POST['ts_Date'];
    // TODO - we'll have to somehow ignore this in our array diff
    $query .= ",submit_ts=".time();
    if(trim($_POST['patientNum']) != ""){ $query .= ",pt_num='".((int)$_POST['patientNum'])."'"; $newArr['pt_num'] = ((int)$_POST['patientNum']);}
    if(trim($_POST['patientOf']) != ""){ $query .= ",pt_total='".((int)$_POST['patientOf'])."'"; $newArr['pt_total'] = ((int)$_POST['patientOf']);}
    if(isset($_POST['ptpkey'])){ $query .= ",patient_pkey=".((int)$_POST['ptpkey']); $newArr['patient_pkey'] = ((int)$_POST['ptpkey']);}
    if(trim($_POST['age']) != ""){ $query .= ",pt_age=".((int)$_POST['age']); $newArr['pt_age'] = ((int)$_POST['age']);}
    if(trim($_POST['PtLocation']) != ""){ $query .= ",pt_loc_at_scene='".mysql_real_escape_string(trim($_POST['PtLocation']))."'"; $newArr['pt_loc_at_scene'] = mysql_real_escape_string(trim($_POST['PtLocation']));}
    if(trim($_POST['PtPhysician']) != ""){ $query .= ",pt_physician='".mysql_real_escape_string(trim($_POST['PtPhysician']))."'"; $newArr['pt_physician'] = mysql_real_escape_string(trim($_POST['PtPhysician']));}
    if(trim($_POST['CallLoc']) == "Home"){ $query .= ",call_loc_id=0"; $newArr['call_loc_id'] = 0;}
       else { $query .= ",call_loc_id=".((int)$_POST['calllocid']); $newArr['call_loc_id'] = ((int)$_POST['calllocid']);}
    $query .= ",outcome='".mysql_real_escape_string(trim($_POST['OC']))."'";
    $newArr['outcome'] = mysql_real_escape_string(trim($_POST['OC']));
    $query .= ",call_type='".mysql_real_escape_string(trim($_POST['CallType']))."'";
    $newArr['call_type'] = mysql_real_escape_string(trim($_POST['CallType']));
    $query .= ",signature_ID='".mysql_real_escape_string(trim($_POST['signature_EMTid']))."'";
    $newArr['signature_ID'] = mysql_real_escape_string(trim($_POST['signature_EMTid']));
    $query .= ",signature_auth=1";

    if(trim($_POST['PtTransTo']) != ""){ $query .= ",PtTransferredTo='".mysql_real_escape_string(trim($_POST['PtTransTo']))."'"; $newArr['PtTransTo'] = mysql_real_escape_string(trim($_POST['PtTransTo']));}
    if(trim($_POST['Passengers']) != ""){ $query .= ",Passengers='".mysql_real_escape_string(trim($_POST['Passengers']))."'"; $newArr['Passengers'] = mysql_real_escape_string(trim($_POST['Passengers']));}
    if(trim($_POST['EquipLeft']) != ""){ $query .= ",EquipmentLeft='".mysql_real_escape_string(trim($_POST['EquipLeft']))."'"; $newArr['EquipmentLeft'] = mysql_real_escape_string(trim($_POST['EquipLeft']));}

    // figure out overall duty/general stuff, as well as duty/general for each member
    $duty_status = getDutyCrewByTS($_POST["ts_time_disp"]);
    $num = (int)$_POST['nextCrewRow'];
    $is_duty = true;
    for($i = 0; $i < $num; $i++)
    {
	if(trim($_POST['crew_id_'.$i]) == ""){ continue;}
	if(in_array(trim($_POST['crew_id_'.$i]), $duty_status))
	{
	    $_POST['crew_genDuty_'.$i] = "Duty";
	}
	else
	{
	    $_POST['crew_genDuty_'.$i] = "Gen";
	    $is_duty = false;
	}
    }
    
    if($is_duty){ $query .= ",is_duty_call=1"; $newArr['is_duty_call'] = 1;}
    elseif(isset($_POST['is_second_rig']) && $_POST['is_second_rig'] == "yes"){ $query .= ",is_second_rig=1"; $newArr['is_second_rig'] = 1;}

    if(PCR_row_needs_update("calls", "Pkey", $orig_Pkey, $newArr))
    {
	if($DEBUG){ echo "<p>PCR_row_needs_update returned true for 'calls' record.</p>";}
	$query .= ",deprecates_id=$orig_Pkey;";
	if($DEBUG)
	{
	    echo '<pre>'.$query.'</pre>'."\n";
	    $new_pkey = -1;
	}
	else
	{
	    $bar = trans_safe_query($query);
	    $new_pkey = mysql_insert_id();
	    if(! $bar){ $errors = true;}
	}

	$query = "UPDATE calls SET deprecated_by_id=$new_pkey,is_deprecated=1 WHERE Pkey=$orig_Pkey;";
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
    else
    {
	if($DEBUG){ echo "<p>PCR_row_needs_update returned false for 'calls' record.</p>";}
    }
    
    // UNIT
    // get current Pkey for this run
    $query = "SELECT * FROM calls_units WHERE RunNumber=$RunNumber AND is_deprecated=0;";
    $result = mysql_query($query) or error_log("process_edited_PCR_form: get Pkey query error: Query: $query ERROR: ".mysql_error());
    if(mysql_num_rows($result) < 1)
    {
	// no unit in DB, see if we need one
	if(isset($_POST['unit']) && trim($_POST['unit']) != "" && trim($_POST['unit']) != "N/A")
	{
	    // no unit in DB, but we're adding one
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
	// else no unit in DB, no unit on call sheet, leave as is
    }
    else
    {
	// we have a unit in the DB
	$row = mysql_fetch_assoc($result);
	$orig_Pkey = $row['cu_id'];
	$newArr = array();
	if(isset($_POST['unit']) && trim($_POST['unit']) != "" && trim($_POST['unit']) != "N/A")
	{
	    // we have a unit on the form too - diff, update if needed
	    $query = "INSERT INTO calls_units SET RunNumber=".$RunNumber;
	    $query .= ",unit='".mysql_real_escape_string(trim($_POST['unit']))."'";
	    $newArr['unit'] = mysql_real_escape_string(trim($_POST['unit']));
	    $query .= ",end_mileage=".((int)$_POST['mileage']);
	    $newArr['end_mileage'] = ((int)$_POST['mileage']);
	    $query .= ";";

	    if(PCR_row_needs_update("calls_units", "cu_id", $orig_Pkey, $newArr))
	    {
		if($DEBUG){ echo "<p>PCR_row_needs_update returned true for 'calls' record.</p>";}
		$query .= ",deprecates_id=$orig_Pkey;";
		if($DEBUG)
		{
		    echo '<pre>'.$query.'</pre>'."\n";
		    $new_pkey = -1;
		}
		else
		{
		    $bar = trans_safe_query($query);
		    $new_pkey = mysql_insert_id();
		    if(! $bar){ $errors = true;}
		}

		$query = "UPDATE calls SET deprecated_by_id=$new_pkey,is_deprecated=1 WHERE Pkey=$orig_Pkey;";
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
	    else
	    {
		if($DEBUG){ echo "<p>PCR_row_needs_update returned false for 'calls' record.</p>";}
	    }
	}
	else
	{
	    // no unit on the form - remove unit
	    $query = "UPDATE calls_units SET is_deprecated=1 WHERE cu_id=$orig_Pkey;";
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

    // LEFT OFF HERE I THINK - 2010-08-23

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