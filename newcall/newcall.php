<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2011-02-08 16:35:50 jantman"                                                              |
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
 | $LastChangedRevision:: 77                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/newcall.php                                            $ |
 +--------------------------------------------------------------------------------------------------------+
*/

require_once('config/PCRconfig.php');
require_once('inc/JAforms.php');
require_once('inc/validatePCR.php');
require_once('inc/formFuncs.php');
require_once('inc/newcall.php.inc');
require_once('inc/session.php');
require_once('inc/PCRprocess.php');
require_once('inc/PCRvalidation.php');
require_once('inc/PCRpopulate.php');
require_once('inc/crew.php');
require_once('inc/mileage.php');
require_once('inc/nav.php');
require_once('inc/runNum.php');
require_once('inc/PCRedit.php');

// DEFAULTS for new calls
$defaults = array("Other_AddressStreetNum" => "St#", "Other_AddressStreet" => "Street", "Other_AddressApt" => "Apt#", "Date" => date($PCRconfig_date_format), "nextVitalsRow" => 1, "nextCrewRow" => 3, "loc" => "no", "ALS" => "NotCalled");

// Get or calculate the Run Number
if(isset($_POST['RunNumber']))
{
    $RunNumber = (int)$_POST['RunNumber'];
}
else
{
    // figure out a new run number
    $RunNumber = getNewRunNumber(date("Y"));
}


if(isset($_POST['Date']))
{
    // we're getting the same call spit back to us
    $foo = validate_PCR_form(); // validate the form
    $vals = $_POST; // pre-populate with the same values that were submitted.
    if(count($foo) == 0 || ! isset($foo['errors']) || count($foo['errors']) < 1)
    {
	echo '<!-- entered form processing if - no errors -->'."\n"; // DEBUG
	// in the line below, the post['warn'] is for warnings that have already been seen and should be ignored.
	if(! isset($foo['warnings']) || (isset($foo['warnings']) && isset($_POST['warn'])))
	{
	    echo '<!-- no warnings or warnings have been seen before -->'."\n"; // DEBUG
	    unset($warnings);
	    unset($errors);
	    if(isset($_POST['action']) && $_POST['action'] == "edit")
	    {
		echo "<!-- PROCESSING FORM...-->\n";
		error_log("PCRDEBUG - newcall.php - processing run number $RunNumber to DB as edit.");
		$bar = process_edited_PCR_form($RunNumber); // put the form into the DB
		echo "<!-- DONE PROCESSING FORM...-->\n";
	    }
	    else
	    {
		echo "<!-- PROCESSING FORM...-->\n";
		error_log("PCRDEBUG - newcall.php - processing run number $RunNumber to DB.");
		$bar = process_new_PCR_form($_POST); // put the form into the DB
		echo "<!-- DONE PROCESSING FORM...-->\n";
	    }

	    if($bar != true)
	    {
		// TODO - how to show this error and allow the person to easily re-submit?
		$errors = array("foo" => "I'm sorry, but there was an error while submitting this call. Please contact the administrator and leave this form open.");
	    }
	    else
	    {
		mail("jason@jasonantman.com", "Call $RunNumber submitted.", "Call $RunNumber has been successfully submitted by ".$_POST["signature_EMTid"]);
		if(! (isset($_POST['action']) && $_POST['action'] == "edit"))
		{
		    // TODO - get rid of this before we allow editing.
		    header("Location: printCall.php?runNum=".$_POST['RunNumber']);
		}
		die();
	    }

	}
	else
	{
	    $warnings = $foo['warnings'];
	    // DEBUG
	    echo '<!-- ELSE WE HAVE UNSEEN WARNINGS -->'."\n";
	    echo '<pre>';
	    echo var_dump($foo);
	    echo '</pre>'."\n";
	    // END DEBUG
	}
    }
    else
    {
	// we'll show the warnings and errors
	$warnings = $foo['warnings'];
	$errors = $foo['errors'];
	error_log("PCRDEBUG - newcall.php - runNumber $RunNumber");
	error_log("PCRDEBUG - warnings: ".var_dump_string($warnings));
	error_log("PCRDEBUG - errors: ".var_dump_string($errors));
	mail("jason@jasonantman.com", "Call $RunNumber ERROR.", "Call $RunNumber was reloaded with errors: \n\nWARNINGS:\n".var_dump_string($warnings)."\n\nERRORS:\n".var_dump_string($errors));
    }
}
elseif(isset($_GET['RunNumber']))
{
    // View or Edit a call.
    // TODO - implement this
    $RunNumber = (int)str_replace("-", "", $_GET['RunNumber']);
    $vals = getRunFromDB($RunNumber);
}
else
{
    // new call - defaults
    $vals = $defaults;
}

// DEBUG
echo '<!--'."\n";
echo var_dump($vals);
echo '-->'."\n";
// END DEBUG

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php echo '<meta name="generator" content="MPAC PCR version '.$_VERSION.' (r'.stripSVNstuff($_SVN_rev).')">'."\n"; ?>
<title><?php echo $shortName." PCR #".formatRunNum($RunNumber);?> </title>
<link rel="stylesheet" type="text/css" href="css/PCRform.css" />
<link rel="stylesheet" type="text/css" href="css/scheduleForm.css" />
<script language="javascript" type="text/javascript" src="js/PCRlayout.js"></script>
<script language="javascript" type="text/javascript" src="js/PCRajax.js"></script>
<script language="javascript" type="text/javascript" src="js/i18n.js"></script>
<script language="javascript" type="text/javascript" src="js/grayout.js"></script>
<script language="javascript" type="text/javascript" src="js/json2.js"></script>
<script language="javascript" type="text/javascript" src="js/patient.js"></script>
<script language="javascript" type="text/javascript" src="js/callLoc.js"></script>
<script language="javascript" type="text/javascript" src="js/crew.js"></script>
<script language="javascript" type="text/javascript" src="js/mileage.js"></script>
<script language="javascript" type="text/javascript" src="js/oc.js"></script>
</head>

<body onload="load()">
<?php
if(isset($_GET['RunNumber']))
{
    if(isset($_GET['action']) && $_GET['action'] == "edit")
    {
	echo genNavControls($RunNumber, true);
    }
    else
    {
	echo genNavControls($RunNumber);
    }
}

if((isset($vals['action']) && $vals['action'] == "edit") || (isset($_GET['action']) && $_GET['action'] == "edit"))
{
    if(is_valid_session() != 0)
    {
	echo '<p style="text-align: center;"><strong>You must log in to edit calls. Please <a href="session.php">Click Here</a>.</strong></p>';
    }
    else
    {
	echo '<p style="text-align: center;"><strong>Logged in as EMTid '.$_SESSION['EMTid'].'.</strong></p>';
	update_session_time();
    }
}

?>
<form name="PCR" method="POST">

<?php

if(isset($_GET['RunNumber']))
{
    if(isset($_GET['action']) && $_GET['action'] == "edit")
    {
	$vals['action'] = "edit";
	echo ja_hidden("action", array(), $vals);    
	echo '<h2 style="text-align: center;">Edit Call</h2>'."\n";
	// TODO - check if session, logged in, if not, redirect to login page
    }
    else
    {
	$vals['action'] = "view";
	echo ja_hidden("action", array(), $vals);    
	echo '<h2 style="text-align: center;">View Only<br />This page cannot edit/update a call.</h2>'."\n";
    }
}

if(count($errors) > 0)
{
    echo '<div class="errorDiv">'."\n";
    echo '<h1>Errors</h1>'."\n";
    echo '<ul>'."\n";
    foreach($errors as $field => $val)
    {
	if(is_string($val))
	{
	    echo '<li>'.$val.'</li>'."\n";
	}
	else
	{
	    foreach($val as $err)
	    {
		echo '<li>'.$err.'</li>'."\n";
	    }
}
    }
    echo '</ul>'."\n";
    echo '<p>Please correct these errors and then re-submit the form.</p>'."\n";
    echo '<p><span style="font-size: 2em; font-weight: bold;">WARNING - THIS CALL HAS NOT BEEN SUBMITTED. DO NOT PRINT THIS PAGE.</span><br /><span style="font-size: 1.5em; font-weight: bold;">If you CANNOT get this call to submit after correcting these errors, please call Jason IMMEDIATELY, 24x7x365, at (201)906-7347.</span></p>';
    echo '<input type="hidden" name="error" value="yes" />';
    echo '</div>'."\n";
}
if(count($warnings) > 0)
{
    echo '<input type="hidden" name="warn" value="warn" />'."\n";
    echo '<div class="warnDiv">'."\n";
    echo '<h1>Warnings</h1>'."\n";
    echo '<ul>'."\n";
    foreach($warnings as $field => $val)
    {
	if(is_string($val))
	{
	    echo '<li>'.$val.'</li>'."\n";
	}
	else
	{
	    foreach($val as $err)
	    {
		echo '<li>'.$err.'</li>'."\n";
	    }
	}
    }
    echo '</ul>'."\n";
    echo '<p>You will be able to re-submit the form if it is correct, but please check over these fields before doing so... something appears a bit off.</p>'."\n";
    echo '<input type="hidden" name="warn" value="yes" />';
    echo '</div>'."\n";
}

echo '<input type="hidden" name="RunNumber" id="RunNumber" value="'.$RunNumber.'" />'."\n";
?>

<div id="bodyContainerDiv">
<table class="outerTable">
<tr> <!-- row 1 - header, Date line -->
<td>

<table class="levelOneTable">
<tr><td colspan="5"><div id="headerDiv"><?php echo $orgName." Patient Care Report #".formatRunNum($RunNumber);?></div></td></tr>
<tr><!-- row starting with Date -->
<td><label for="Date">Date: </label>
<?php echo ja_text("Date", array("size" => 10), $vals); ?>
<?php echo '<input type="hidden" name="PrePopulateDate" id="PrePopulateDate" value="'.$vals['Date'].'" />';?>
</td>
<td id="unit_td">
<?php
echo '<label for="unit">Unit: </label>';
$query = "SELECT * FROM opt_units;";
$result = mysql_query($query) or die("Error in Query: ".$query."<br />ERROR: ".mysql_error()."<br />");
$opts = array();
$opts[""] = "&nbsp;";
while($row = mysql_fetch_assoc($result))
{
    $opts[$row['oU_unitID']] = $row['oU_unitID'];
}
echo ja_select("unit", array("onChange" => "checkMileage()"), $opts, $vals);
?>
</td>
<td id="ptof_td">
<label for="PatientOf">Patient </label>
<?php echo ja_text("patientNum", array("size" => 1, "value" => 1, "maxlength" => 2), $vals); ?>
<label for="PatientNum"> of </label>
<?php echo ja_text("patientOf", array("size" => 1, "value" => 1, "maxlength" => 2), $vals); ?>
</td>
<td id="mileage_td"><label for="Mileage">Mileage: </label><?php echo ja_text("mileage", array("size" => 6, "maxlength" => 6, "onChange" => "checkMileage()"), $vals); ?></td>
<td><label for="MAcheck">Mutual Aid</label><?php echo ja_check("MAcheck", array('onChange' => 'update_MA()'), $vals);?><div id="MAdiv"><?php echo gen_MAdiv($vals); ?></div></td>
</tr><!-- END row starting with Date -->
</table> <!-- end row1 levelOneTable -->

</td>
</tr> <!-- end row 1 -->
<tr> <!-- row 2 - patient information, rowspan of times -->
<td>

<?php echo ja_hidden("ptpkey", array(), $vals);?>

<table class="levelOneTable">
<tr>
<td class="sectionHeader" id="pt_info_td">Patient Information<br />
<a href="javascript:findPatient()">Find Patient</a>
<br />
<?php
if(isset($_POST['ptpkey']))
{
    echo '<a href="javascript:updatePatientByPkey('.$_POST['ptpkey'].')">Update Current Patient</a>';
}
?>
</td>
<td id="pt_age_td"><label for="age">Age: </label><?php echo ja_text("age", array("size" => 3, "readonly" => "readonly"), $vals); ?></td>
<td id="pt_dob_td"><label for="DOB">DOB: </label><?php echo ja_text("DOB", array("size" => 10, "onChange" => "update_DOB()", "readonly" => "readonly", "maxlength" => 10), $vals); ?></td>
<td id="pt_sex_td"><label for="sex">Sex: </label>  <?php echo ja_text("sex", array("size" => 7, "readonly" => "readonly"), $vals); ?></td>
<td rowspan="4">

<table class="timeTable">
<tr><td class="sectionHeader" colspan="2">Times (Military / HH:MM)</td></tr>
<tr><td class="timeLabel">Dispatched:</td><td class="timeField">
<?php echo ja_text("time_disp", array("size" => 5, "maxlength" => 5, "onChange" => "formatTime('time_disp')"), $vals); ?>
</td></tr>
<tr><td class="timeLabel">In Service:</td><td class="timeField">
<?php echo ja_text("time_insvc", array("size" => 5, "maxlength" => 5, "onChange" => "formatTime('time_insvc')"), $vals); ?>
</td></tr>
<tr><td class="timeLabel">On Scene:</td><td class="timeField">
<?php echo ja_text("time_onscene", array("size" => 5, "maxlength" => 5, "onChange" => "formatTime('time_onscene')"), $vals); ?>
</td></tr>
<tr><td class="timeLabel">Enroute:</td><td class="timeField">
<?php echo ja_text("time_enroute", array("size" => 5, "maxlength" => 5, "onChange" => "formatTime('time_enroute')"), $vals); ?>
</td></tr>
<tr><td class="timeLabel">Arrived:</td><td class="timeField">
<?php echo ja_text("time_arrived", array("size" => 5, "maxlength" => 5, "onChange" => "formatTime('time_arrived')"), $vals); ?>
</td></tr>
<tr><td class="timeLabel">Available:</td><td class="timeField">
<?php echo ja_text("time_avail", array("size" => 5, "maxlength" => 5, "onChange" => "formatTime('time_avail')"), $vals); ?>
</td></tr>
<tr><td class="timeLabel">Out of Service:</td><td class="timeField">
<?php echo ja_text("time_out", array("size" => 5, "maxlength" => 5, "onChange" => "formatTime('time_out')"), $vals); ?>
</td></tr>
</table>

</td>
</tr>
<tr id="pt_name_tr">
<td colspan="4">
<nobr>
<label for="NameLast">Name: </label><?php echo ja_text("NameLast", array("size" => 40, "readonly" => "readonly"), $vals); ?>, 
<?php echo ja_text("NameFirst", array("size" => 30, "readonly" => "readonly"), $vals); ?>&nbsp;
<?php echo ja_text("NameMiddle", array("size" => 2, "readonly" => "readonly"), $vals); ?>
</nobr>
</td>
</tr>
<tr id="pt_address_tr">
<td colspan="4">
<label for="Address">Address:</label> 
<?php echo ja_text("Address", array("size" => 50, "readonly" => "readonly"), $vals); ?>
</td>
</tr>
</table> <!-- end row2 levelOneTable -->

</td>
</tr> <!-- end row 2 -->
<tr> <!-- row 3 - call information, rowspan of times -->
<td>

<table class="levelOneTable">
<tr>
<td class="sectionHeader">Call Information</td>
<td>
<?php
echo '<label for="CallType">Call Type: </label>';
$foo = array();
$foo[""] = "";
$query = "SELECT * FROM opt_CallType ORDER BY oCT_order ASC;";
$result = mysql_query($query) or die("Error in Query: ".$query."<br />ERROR: ".mysql_error()."<br />");
while($row = mysql_fetch_assoc($result))
{
    $foo[$row['oct_name']] = $row['oct_name'];
    //echo '<option value="'.$row['oct_id'].'">'.$row['oct_name'].'</option>';
}
echo ja_select("CallType", array(), $foo, $vals);
?>
</td>
<td id="pt_loc_scene_td"><label for="PtLocation">Location of Pt at Scene: </label>
<?php echo ja_text("PtLocation", array("size" => 30, "maxlength" => 37), $vals); ?></td>
</tr>
<tr>
<td colspan="2"><label for="CallLocPtHome">Location of Call: </label>
<?php echo ja_hidden("calllocid", array(), $vals);?>
<?php
echo ja_radio("CallLoc", "CallLocPtHome", "Home", $vals, array("onchange" => "update_call_loc(0)"))." Patients Home";
echo ja_radio("CallLoc", "CallLocOther", "Other", $vals, array("onchange" => "update_call_loc(1)"))." Other";
?>

<div id="CallLocDiv" <?php if(isset($vals["CallLoc"]) && $vals["CallLoc"] != "Other"){ echo 'style="display: none;"';}?> ><br />
<?php
echo ja_text("call_loc_other", array("size" => 40, "readonly" => "readonly", "onclick" => "findCallLoc()"), $vals);
//echo '<label for="Other_AddressStreet">Address:</label> ';
//echo ja_text("Other_AddressStreetNum", array("size" => 6, "maxlength" => 6), $vals);
//echo ja_text("Other_AddressStreet", array("size" => 30, "maxlength" => 40), $vals);
//echo ja_text("Other_AddressApt", array("size" => 6, "maxlength" => 6), $vals);
?>
</div>
</td>
<td id="pt_physican_td"><label for="PtPhysician">Pt Physician:</label><?php echo ja_text("PtPhysician", array("size" => 30, "maxlength" => 35), $vals); ?></tr>
</table> <!-- END row3 levelOneTable -->

</td>
</tr> <!-- end row 3 -->
<tr id="clinical_tr"> <!-- row 4 - clinical information -->
<td id="Clinical_td">

<table class="levelOneTable" id="Table_Clinical">
<tr>
<td class="sectionHeader" id="ClinicalTitle_td">Clinical Information</td>
<td><strong>Chief Complaint:</strong> <?php echo ja_text("chief_complaint", array("size" => 20, "maxlength" => 36), $vals); ?></td>
<td><strong>Time of Symptom Onset:</strong> <?php echo ja_text("time_onset", array("size" => 20, "maxlength" => 35), $vals); ?></td>
</tr>
<tr>
<td rowspan="5" id="PatientHistory_td">
<strong>&nbsp;&nbsp;&nbsp;Patient History:</strong><br />
<?php echo ja_textarea("hx", array("class" => "wideTextArea", "maxlength" => 250), $vals); ?>
</td>
<td colspan="2">
<nobr>
<strong>Aid Given:</strong>
<?php echo ja_text("AidGiven", array("size" => 20, "maxlength" => 33), $vals); ?>
<strong> By:</strong>
<?php echo ja_check("AidGivenBy_PD", array(), $vals);?> PD
<?php echo ja_check("AidGivenBy_Family", array(), $vals);?> Family
<?php echo ja_check("AidGivenBy_Bystander", array(), $vals);?> Bystander
<?php echo ja_check("AidGivenBy_Other", array('onChange' => 'update_AidGiven()'), $vals);?> Other
<?php
$foo = array("size" => 10, "maxlength" => 25);
if(! isset($vals['AidGivenBy_Other'])){ $foo["style"] = "display: none;";}
echo ja_text("AidGivenBy_Other_Text", $foo, $vals);
?>
</nobr>
</td>
</tr>
<!-- allergies/meds - separate by commas, use a popup selection? -->
<tr>
<td colspan="2">
<table class="tableNoBorder"><tr><td class="labelTD"><label for="Allergies" id="Allergies_Label">Allergies:</label></td><td> <?php echo ja_text("Allergies", array("size" => 30, "class" => "wideInput", "maxlength" => 91), $vals); ?></td></tr></table>
</td>
</tr>
<tr>
<td colspan="2" id="Medications_td">
<table class="tableNoBorder"><tr><td class="labelTD"><label for="Medications" id="Medications_Label">Medications:</label></td><td><?php echo ja_text("Medications", array("size" => 30, "class" => "wideInput", "maxlength" => 101), $vals); ?></td></tr></table>
</td>
</tr>
</table> <!-- END row4 levelOneTable -->

</td>
</tr> <!-- end row 4 -->
<tr> <!-- row 5 - remarks -->
<td>

<table class="levelOneTable">
<tr>
<td>
<strong>&nbsp;&nbsp;&nbsp;Remarks:</strong><br />
<?php echo ja_textarea("remarks", array("class" => "wideTextArea", "maxlength" => 850), $vals); ?>
</td>
</tr>
</table>

</td>
</tr> <!-- end row 5 -->
<tr id="vitals_tr"> <!-- row 6 - vitals -->
<td>

<div id="vitalsContainer">
<?php echo ja_hidden("nextVitalsRow", array(), $vals);?>
<table id="vitalsTable">
<tr><th colspan="9" class="sectionHeader">Vitals</td></tr>
<tr><th>Time</th><th>BP</th><th>Pulse</th><th>Respirations</th><th>Lung Sounds</th><th>Consciousness</th><th>Pupils</th><th>Skin</th><th>SpO2</th></tr>
<?php
if(isset($_POST["nextVitalsRow"]))
{
    $_POST["nextVitalsRow"] = (int)$_POST["nextVitalsRow"];
    for($i = 0; $i < $_POST["nextVitalsRow"]; $i++)
    {
	echo makeVitalsRow($i, $vals);
    }
}
elseif(isset($vals['nextVitalsRow']))
{
    $_POST["nextVitalsRow"] = $vals["nextVitalsRow"];
    for($i = 0; $i < $vals["nextVitalsRow"]; $i++)
    {
	echo makeVitalsRow($i, $vals);
    }
}
else
{
    echo makeVitalsRow(0, $vals);
}
?>
</table>
<div style="text-align: center;"><a href="javascript:addVitalsRow()">Add another set of vitals</a></div>
</div> <!-- END vitalsContainer DIV -->

</td>
</tr> <!-- end row 6 -->
<tr> <!-- row 7 - bottom stuff -->
<td>

<table class="levelOneTable">
<tr id="tx_inj_loc_tr">
<td id="tx_td">
<div class="sectionHeaderWide">Treatment/Interventions Given</div>
<?php echo ja_textarea("tx", array("class" => "wideTextArea", "onclick" => "click_tx()", "readonly" => "readonly"), $vals); ?>
</td>
<td id="injarea_td">
<div class="sectionHeaderWide">Injured Area</div>
<?php echo ja_textarea("injured_area", array("class" => "wideTextArea", "onclick" => "click_injarea()", "readonly" => "readonly"), $vals); ?>
<td id="loc_td">
<div class="sectionHeaderWide">Loss Of Consciousness</div>
<label for="loc_yes">Yes</label><?php echo ja_radio("loc", "loc_yes", "yes", $vals);?> <label for="loc_no">No</label><?php echo ja_radio("loc", "loc_no", "no", $vals);?> 
</td>
</tr>
<tr>
<td rowspan="3">
<div class="sectionHeaderWide">Call Outcome</div>
<?php
echo ja_radio("OC", "OC_BLS", "BLS", $vals, array("onChange" => "update_OC()"))." BLS Transport<br />";
echo ja_radio("OC", "OC_ALS", "ALS", $vals, array("onChange" => "update_OC()"))." BLS/ALS Transport<br />";
echo ja_radio("OC", "OC_Air", "Air", $vals, array("onChange" => "update_OC()"))." Air Transport<br />";
echo ja_radio("OC", "OC_Refusal", "Refusal", $vals, array("onChange" => "update_OC()"))." Refusal (attach RMA form)<br />";
echo ja_radio("OC", "OC_DOA", "DOA", $vals, array("onChange" => "update_OC()"))." DOA<br />";
echo ja_radio("OC", "OC_Canceled", "Canceled", $vals, array("onChange" => "update_OC()"))." Cancelled<br />";
echo ja_radio("OC", "OC_Other", "Other", $vals, array("onChange" => "update_OC()"))." Other/Non-Emergency<br />";
echo ja_radio("OC", "OC_NoCrew", "NoCrew", $vals, array("onChange" => "update_OC()"))." No Crew/Unable to fill rig";
?>
</td>
<td>
<?php echo ja_hidden("nextCrewRow", array(), $vals);?>
<table id="crewTable">
<tr><th colspan="6" class="sectionHeader">Crew&nbsp;&nbsp;&nbsp;&nbsp;
<?php if(! isset($_GET['RunNumber'])){ echo '<a href="javascript:getDutyCrew()">Get Duty Crew</a>';} ?>
</td></tr>
<tr><th rowspan="2">EMTid</th><th colspan="3">Driver</th><th rowspan="2">On<br />Scene</th><th rowspan="2">Gen/<br />Duty</th></tr>
<tr><th>Scene</th><th>Hosp</th><th>Bldg</th></tr>
<?php
if(isset($_POST["nextCrewRow"]))
{
    echo genCrewTable(((int)$_POST["nextCrewRow"]), $vals); 
}
elseif(isset($vals['nextCrewRow']))
{
    echo genCrewTable($vals['nextCrewRow'], $vals); 
}
else
{
    echo genCrewTable(3, $vals); 
}
?>
</table>
<div style="text-align: center;"><span id="addcrew_span"><a href="javascript:addCrew()">Add Crew Member</a>&nbsp;&nbsp;&nbsp;</span><a href="javascript:popUp('/roster.php?sort=LastName&shortView=1')">View Roster</a><br />
<?php echo ja_check("is_second_rig", array(), $vals); ?>
<label for="is_second_rig"><strong> Second Rig</strong> (same call/location)</label>
</div>
</td>
<td rowspan="2">
<div class="sectionHeaderWide">ALS</div>
<label for="ALSunit">Unit #:</label>
<?php
echo ja_text("ALSunit", array("size" => 3, "maxlength" => 3), $vals)."<br />";
echo ja_radio("ALS", "ALS_NotCalled", "NotCalled", $vals)." Not Called<br />";
echo ja_radio("ALS", "ALS_Unavail", "Unavail", $vals)." Unavailable<br />";
echo ja_radio("ALS", "ALS_CancelledPD", "CancelledPD", $vals)." Cancelled By PD<br />";
echo ja_radio("ALS", "ALS_CancelledBLS", "CancelledBLS", $vals)." Cancelled By BLS<br />";
echo ja_radio("ALS", "ALS_CancelledMD", "CancelledMD", $vals)." Cancelled By MD<br />";
echo ja_radio("ALS", "ALS_RespRel", "RespRel", $vals)." Responded & Released<br />";
echo ja_radio("ALS", "ALS_RespALS", "RespALS", $vals)." Responded, ALS Transport<br />";
echo ja_radio("ALS", "ALS_RespNoTrans", "RespNoTrans", $vals)." Responded, No Transport";
?>
</td>
</tr>
<tr>
<td id="transto_td"><strong>Patient Transferred To:</strong>
<?php 
$foo = getTransToOptions();
echo ja_select("PtTransTo", array(), $foo, $vals);
?>
<br />
<strong>Passengers:</strong>
<?php echo ja_text("Passengers", array("size" => 30, "maxlength" => 40), $vals); ?>
<br />
<strong>Equipment Left:</strong>
<?php echo ja_text("EquipLeft", array("size" => 30, "maxlength" => 40), $vals); ?>
</td>
</tr>
<tr>
<td id="sig_id_td"><strong>Signature: ID </strong>
<?php
if(isset($vals['action']) && $vals['action'] == "edit")
{
    // TODO - session stuff
    echo ja_text("signature_EMTid", array("size" => 4, "maxlength" => 4), array("signature_EMTid" => $_SESSION['EMTid']));
}
else
{
    echo ja_text("signature_EMTid", array("size" => 4, "maxlength" => 4), $vals);
}
?>

</td>
<td class="sectionHeader">
<?php echo ja_check("RMA_attached", array(), $vals);?>
RMA Attached
</td>
</tr>
</table>

</td>
</tr> <!-- end row 7 -->
</table> <!-- CLOSE outerTable table -->
</div><!-- CLOSE bodyContainerDiv -->

<div style="text-align: center; margin-top: 1em;">
<?php
if(! isset($_GET['RunNumber']) || $vals['action'] == "edit")
{
    // TODO - for now, only allow viewing of an existing call
    echo '<input type="submit" name="submit" value="Submit PCR" />'."\n";
}
//echo '<a href="javascript:resetForm()">Clear ALL Input</a>';
?>
</div>

</form>

<div id="popup" class="popup">
<div id="popuptitleArea">
<div id="popuptitle"></div>
<div id="popupCloseBox" onClick="hidePopup()">X</div>
<div id="clearing"></div>
</div> <!-- END popuptitleArea DIV -->
<div id="popupbody">
</div> <!-- END popupbody DIV -->
</div> <!-- END popup DIV -->


</body>

</html>
