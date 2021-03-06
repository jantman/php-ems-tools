<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-24 22:36:03 jantman"                                                              |
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
 | $HeadURL:: http://svn.jasonantman.com/newcall/inc/PCRvalidation.php                                  $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * PCR form validation functions.
 *
 * @package MPAC-NewCall-PHP
 */

// this function handles all validation of the PCR form.
// it should return an array that's empty or includes one or more of the following arrays:
//  'errors'
//  'warnings'
// each array is another array, like InputFieldName => message
function validate_PCR_form()
{
    global $PCRconfig_date_format_regex, $PCRconfig_time_format_regex;
    $time_regex = $PCRconfig_time_format_regex;

    $ret = array('errors' => array(), 'warnings' => array());

    //$ret['errors']['Foo'] = "TODO - DEBUG - get rid of this error.";
    //$ret['warnings']['Foo'] = "TODO - DEBUG - get rid of this warning.";

    //
    // BEGIN VALIDATION
    //

    // DATE
    if(trim($_POST['Date']) == "" || ! preg_match($PCRconfig_date_format_regex, $_POST['Date']) || ! strtotime($_POST['Date']))
    {
	$ret['errors']['Date'] = "Date - Invalid date format.";
    }
    else
    {
	$_POST['ts_Date'] = strtotime($_POST['Date']);
    }

    if($_POST['ts_Date'] > strtotime(date("Y-m-d ", time())." 00:00:00"))
    {
	$ret['errors']['Date'] = "Date cannot be in the future.";
    }


    // TIMES
    // dispatch time - must be present and OK
    $times_ts = array(); // for checking that times are in sequence
    $min_time_so_far = ""; // the minimum time (not TS) found so far.
    $changed_date = false; // did we have to change the date?
    if(! isset($_POST['time_disp']) || trim($_POST['time_disp']) == "" || ! preg_match($time_regex, $_POST['time_disp']))
    {
	$ret['errors']['time_disp'] = "Dispatch Time - invalid time format or missing. <em>(if there was no actual dispatch, just use the in service time here as well.)</em>";
    }
    else
    {
	$_POST['ts_time_disp'] = strtotime($_POST['Date']." ".$_POST['time_disp']);
	$times_ts[] = $_POST['ts_time_disp'];
	$min_time_so_far = $_POST['time_disp'];
    }

    // out of service time - must be present and OK
    if(! isset($_POST['time_out']) || trim($_POST['time_out']) == "" || ! preg_match($time_regex, $_POST['time_out']))
    {
	$ret['errors']['time_out'] = "Out of Service Time - invalid time format or missing <em>(if you were dispatched to a second call, use the dispatch time for the second call as the out of service time for this call.)</em>.";
    }

    // rest of times - check validity and make TS/add as appropriate
    if(trim($_POST['time_insvc']) != "" || ! call_NoHosp()) { doTime("time_insvc", "In Service", $time_regex, $ret, $times_ts, $min_time_so_far, $changed_date);}
    if(trim($_POST['time_onscene']) != "") { doTime("time_onscene", "On Scene", $time_regex, $ret, $times_ts, $min_time_so_far, $changed_date);}
    if(call_transport()){ doTime("time_enroute", "EnRoute", $time_regex, $ret, $times_ts, $min_time_so_far, $changed_date);}
    if(call_transport()){ doTime("time_arrived", "Arrived", $time_regex, $ret, $times_ts, $min_time_so_far, $changed_date);}
    if(trim($_POST['time_onscene']) != "") { doTime("time_avail", "Available", $time_regex, $ret, $times_ts, $min_time_so_far, $changed_date);}
    doTime("time_out", "Out of Service", $time_regex, $ret, $times_ts, $min_time_so_far, $changed_date);

    // check that the times are in order
    $foo = false;
    for($i = 1; $i < count($times_ts); $i++)
    {
	if($times_ts[$i] < $times_ts[$i-1])
	{
	    $foo = true;
	}
    }
    if($foo)
    {
	$ret['errors']['times'] = "oops! Your times seem to be out of order.";
    }

    // patient number
    if(trim($_POST["patientNum"]) != "" && trim($_POST['patientOf']) != "" && (((int)$_POST["patientNum"]) > ((int)$_POST['patientOf'])))
    {
	$ret['errors']['patientNum'] = "You can not have a patient number higher than the total number of patients.";
    }

    // call type
    if(trim($_POST['CallType']) == "")
    {
	$ret['errors']['CallType'] = "You must select a call type.";
    }

    // call location
    if(trim($_POST["CallLoc"]) == "Other" && (! isset($_POST["calllocid"]) || trim($_POST["calllocid"]) == ""))
    {
	$ret['errors']['CallLoc'] = "If you selected 'Other' for a call location, you must select a location.";
    }

    // make sure no driver to hospital if we didn't go to a hospital
    if(isset($_POST["crew_driver_hosp"]) && call_NoHosp())
    {
	$ret['warnings']['crew_driver_hosp'] = "You cannot have a driver to the Hospital on a call without a patient transported. This has been automatically corrected.";
	unset($_POST['crew_driver_hosp']);
    }

    // check that there are no drivers on a call with no rig
    if(isset($_POST["crew_driver_hosp"]) && call_NoRig())
    {
	$ret['warnings']['crew_driver_hosp'] = "You cannot have a driver to the hospital on a call where no ambulance was used. This has been automatically corrected.";
	unset($_POST['crew_driver_hosp']);
    }
    if(isset($_POST["crew_driver_bldg"]) && call_NoRig())
    {
	$ret['warnings']['crew_driver_bldg'] = "You cannot have a driver to the building on a call where no ambulance was used. This has been automatically corrected.";
	unset($_POST['crew_driver_bldg']);
    }
    if(isset($_POST["crew_driver_scene"]) && call_NoRig())
    {
	$ret['warnings']['crew_driver_scene'] = "You cannot have a driver to the scene on a call where no ambulance was used. This has been automatically corrected.";
	unset($_POST['crew_driver_scene']);
    }

    // equipment left
    if(strtolower(trim($_POST["EquipLeft"])) == "none")
    {
	$ret['warnings']["EquipLeft"] = "If you left no equipment at the hospital, please leave the field blank. This has been automatically corrected.";
	$_POST["EquipLeft"] = "";
    }

    // passengers
    if(strtolower(trim($_POST["Passengers"])) == "none")
    {
	$ret['warnings']["Passengers"] = "If you transported no passengers, please leave the field blank. This has been automatically corrected.";
	$_POST["Passengers"] = "";
    }

    // remarks
    if(strlen(trim($_POST['remarks'])) < 8)
    {
	$ret['errors']['remarks'] = "You must enter a narrative for Remarks.";
    }

    // other call type - OC
    if(call_type_other() && trim($_POST['OC']) != "Other" && trim($_POST['OC']) != "NoCrew" && trim($_POST['OC']) != "Canceled")
    {
	$ret['errors']['OC'] = "You selected a call type that can only have an outcome of Other, No Crew, or Canceled.";
    }

    // NoCrew but unit
    if(trim($_POST['OC']) == "NoCrew" && trim($_POST['unit']) != "" && trim($_POST['unit']) != "N/A")
    {
	$ret['errors']['unit'] = "You cannot have a unit selected for a call with no crew.";
    }

    // check mileage
    if(trim($_POST['unit']) != "" && trim($_POST['unit']) != "N/A")
    {
	$foo = checkMileage(trim($_POST['unit']), trim($_POST['mileage']));
	if($foo == "TOOHIGH"){ $ret['warnings']['mileage'] = "The mileage entered is 30+ miles more than the last recorded mileage for this rig. Are you sure it is correct? Perhaps you entered an incorrect mileage, or selected the wrong rig.";}
	elseif($foo == "TOOLOW"){ $ret['errors']['mileage'] = "The mileage entered is less than the last recorded mileage for this rig. This is not possible. Please check the mileage and <strong>that you have selected the correct rig.</strong>";}
    }

    // mileage but no unit
    if(trim($_POST['mileage']) != "" && (trim($_POST['unit']) == "" || trim($_POST['unit']) == "N/A"))
    {
	$ret['errors']['mileage'] = "You cannot have a mileage for a call with no unit.";
    }

    // unit but no mileage
    if(trim($_POST['mileage']) == "" && trim($_POST['unit']) != "" && trim($_POST['unit']) != "N/A")
    {
	$ret['errors']['mileage'] = "You must enter a mileage for a call with a unit selected.";
    }

    // unit but no driver
    if((! isset($_POST['crew_driver_scene']) || ! isset($_POST['crew_driver_bldg'])) && trim($_POST['unit']) != "" && trim($_POST['unit']) != "N/A")
    {
	$ret['errors']['crew_driver_scene'] = "If you selected a unit, you must at least have a driver to the scene and building.";
    }

    // signature
    check_signature($ret);

    // check crew
    checkCrew($ret);

    // OC or ALS implies transport, but no Transferred To
    if(call_transport() && trim($_POST["PtTransTo"]) == "None")
    {
	$ret['errors']['PtTransTo'] = "You selected an Outcome or ALS status that implies transport, but failed to select a Transferred To location.";
    }

    // ALS status implies a unit, but no unit specified
    if(call_ALS() && trim($_POST['ALSunit']) == "")
    {
	$ret['errors']['ALSunit'] = "You selected an ALS status that should have an associated ALS unit, but you did not specify an ALS unit. <em>(hint: it's usually 402 in MP.)</em>";
    }

    // transport but no interventions
    if(call_transport() && trim($_POST["tx"]) == "")
    {
	$ret['errors']['tx'] = "You transported a patient. <em>Something</em> must be checked off for Treatments/Interventions. <em>(hint: Regular Stretcher)</em>.";
    }

    // call type other but patient selected
    if(call_type_other() && call_has_patient())
    {
	$ret['errors']['CallType'] = "A call type of Other cannot have a patient.";
    }

    // call type other but patients home as location
    if(call_type_other() && trim($_POST["CallLoc"]) == "Home")
    {
	$ret['errors']['CallLoc'] = "A call type of Other cannot have a call location of Patients Home.";
    }

    // TODO - keep adding in validation rules here.

    // TODO - validate mileage against last known for that rig.

    //
    // END VALIDATION
    //

    if(count($ret['errors']) == 0){ unset($ret['errors']);}
    if(count($ret['warnings']) == 0){ unset($ret['warnings']);}
    return $ret;
}

function doTime($name, $title, $time_regex, &$ret, &$times_ts, &$min_time_so_far, &$changed_date)
{

    // TODO - for outcomes that don't require certain times, go back and unset these errors
    if(!isset($_POST[$name]) || trim($_POST[$name]) == "" || ! preg_match($time_regex, $_POST[$name]))
    {
	$ret['errors'][$name] = "$title Time - invalid time format.";
    }
    else
    {
	// the time is here, add it.
	if(strtotime($_POST['Date']." ".$_POST[$name]) < strtotime($_POST['Date']." ".$min_time_so_far) || $changed_date)
	{
	    // this time is before the last time. Assume we're changing the date forward a day
	    $min_time_so_far = $_POST[$name];
	    if(! $changed_date)
	    {
		$ret['warnings'][$name] = "$title Time - this time is earlier in the day than the time before it, so the date has been changed to the NEXT day (assuming the call crossed over from one day to another). Please be SURE that the Date on the call is the date it was dispatched, NOT the date it was filled in.";
		$changed_date = strtotime(date("Y-m-d", strtotime($_POST['Date'])+86400));
	    }
	    $_POST['ts_'.$name] = strtotime(date("Y-m-d", $changed_date)." ".$_POST[$name]);
	    $times_ts[] = $_POST['ts_'.$name];
	}
	else
	{
	    $_POST['ts_'.$name] = strtotime($_POST['Date']." ".$_POST[$name]);
	    $times_ts[] = $_POST['ts_'.$name];
	}
    }

}

//
// FUNCTIONS TO DETERMINE "CLASSES" OF CALLS
//  for use in validation, such as NoPatient or NoHosp
//

function call_NoHosp()
{
    $foo = trim($_POST['OC']);
    if($foo == "Canceled" || $foo == "Refusal" || $foo == "DOA" || $foo == "NoCrew")
    {
	return true;
    }
    return false;
}

function call_NoRig()
{
    if(trim($_POST['OC']) == "NoCrew" || trim($_POST['unit']) == "" || trim($_POST['unit']) == "N/A")
    {
	return true;
    }
    return false;
}

function call_type_other()
{
    if(substr($_POST['CallType'], 0, 8) == "Other - ")
    {
	return true;
    }
    return false;
}

function checkCrew(&$ret)
{
    $count = 0;
    for($i = 0; $i < 12; $i++)
    {
	if(isset($_POST["crew_id_".$i]) && trim($_POST["crew_id_".$i]) != "")
	{
	    $count++;
	}
    }
    // edited 2010-04-19 by jantman, allow "NoCrew" calls without any crew members
    if($count == 0 && $_POST['OC'] != "NoCrew")
    {
	$ret['errors']['crew_id_0'] = "You must specify at least one crew member unless call type is 'No Crew'.";
    }

    require('config/crew.php');
    for($i = 0; $i < 12; $i++)
    {
	if(isset($_POST["crew_id_".$i]) && trim($_POST["crew_id_".$i]) != "" && ! in_array(trim($_POST["crew_id_".$i]), $CREW_ARRAY))
	{
	    $ret['errors']["crew_id_".$i] = "Crew $i (".trim($_POST["crew_id_".$i]).") is not a valid EMTid.";
	}
    }
}

function check_signature(&$ret)
{
    // no signature
    if(! isset($_POST["signature_EMTid"]) || trim($_POST["signature_EMTid"]) == "")
    {
	$ret['errors']["signature_EMTid"] = "You must specify an EMTid for signature.";
    }
    else
    {
	require('config/crew.php');
	if(! in_array(trim($_POST['signature_EMTid']), $CREW_ARRAY))
	{
	    $ret['errors']['signature_EMTid'] = "Signature ID must be a valid EMTid.";
	}
    }

}

function call_transport()
{
    $foo = trim($_POST['OC']);
    $bar = trim($_POST["ALS"]);
    if($foo == "BLS" || $foo == "ALS" || $foo == "Air" || $bar == "ALS_RespALS")
    {
	return true;
    }
    return false;
}

function call_has_patient()
{
    if(trim($_POST['ptpkey']) != "" || trim($_POST['NameFirst']) != "" || trim($_POST['DOB']) != "" || trim($_POST['Address']) != "")
    {
	return true;
    }
    return false;
}

function call_ALS()
{
    $foo = trim($_POST['ALS']);
    if($foo == "RespRel" || $foo == "RespALS" || $foo == "RespNoTrans")
    {
	return true;
    }
    return false;
}

?>