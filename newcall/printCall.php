<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2011-02-08 16:35:36 jantman"                                                              |
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
 | $LastChangedRevision:: 58                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/printCall.php                                          $ |
 +--------------------------------------------------------------------------------------------------------+
*/

// includes
require_once('config/PCRconfig.php');
require_once('inc/JAforms.php');
require_once('inc/validatePCR.php');
require_once('inc/formFuncs.php');
require_once('inc/newcall.php.inc');
require_once('inc/PCRprocess.php');
require_once('inc/PCRvalidation.php');
require_once('inc/PCRpopulate.php');
require_once('inc/runNum.php');

//get variables from the URL
$runNum = $_GET['runNum'];
//GLOBAL variables 
$errorMsg = "<br>Please try again. If you recieve this message more than once while trying to perform the same action, please notify the administrator of this system.";

// the full http path to the PDF form (original) 
$form = 'pcr3.pdf';

// Fill in text fields
$strings = array(); 

// Fill in check boxes/radio buttons
$keys = array(); 

//get the data
getCallData($runNum);
 
$fdf = create_fdf($form, $strings, $keys); 

$fdf_fn= 'tempFDF.fdf';
$out_file = 'filledCalls/run_'.$runNum.'.pdf';
//delete the temp files from the las session. 
if(file_exists($fdf_fn))
{
	//unlink($fdf_fn);
}
if(file_exists($out_file))
{
	//unlink($out_file);
}

$fp= fopen( $fdf_fn, 'w' );
if( $fp ) 
{
    fwrite( $fp, $fdf );
    fclose( $fp );
}
else 
{ // error
  die('Error: unable to open temp file for writing fdf data: '. $fdf_fn);
}

exec( 'pdftk pcr3.pdf fill_form '.$fdf_fn.' output '.$out_file.' flatten' );
if(! file_exists($out_file))
{
    die("Error: something went wrong in creating the output PDF - the file '$out_file' does not exist.");
}

header( 'Location: '.$out_file );

//FUNCTIONS

//GET THE CALL DATA EXAMPLE 
function getCallData($runN) 
{
    global $strings;
    global $keys; 
    $f1 = 'form1[0].#subform[0].#area[0].#area[1].'; //common part of field names 

    $row = getRunFromDB($runN);

    $strings[$f1.'timeDisp[0]'] = $row['time_disp']; 
    $strings[$f1.'timeIn[0]'] = $row['time_insvc']; 
    $strings[$f1.'timeBy[0]'] = $row['time_onscene'];
    $strings[$f1.'timeInRt[0]'] = $row['time_enroute']; 
    $strings[$f1.'timeArrived[0]'] = $row['time_arrived']; 
    $strings[$f1.'timeAvail[0]'] = $row['time_avail']; 
    $strings[$f1.'timeOut[0]'] = $row['time_out']; 
    $strings[$f1.'txtDate[0]'] = $row['Date']; 
    if(isset($row['MAcheck']))
    {
	if($row['MA_town'] == "--Other"){ $strings[$f1.'txtMaTo[1]'] = $row['MA_town_other'];}
	else { $strings[$f1.'txtMaTo[1]'] = $row['MA_town'];}
	$keys['form1[0].#subform[0].cbxMA[0]'] = 1;
    }
    $strings[$f1.'txtRemarks[0]'] = $row['remarks'];
    $strings[$f1.'txtUnit[0]'] = $row['unit'];
    $strings[$f1.'txtPt[0]'] = $row['patientNum'];
    $strings[$f1.'txtPtOf[0]'] = $row['patientOf'];
    $strings[$f1.'txtMileage[0]'] = $row['mileage'];
    $strings[$f1.'txtHx[0]'] = $row['hx'];
    $strings[$f1.'txtPtLoc[0]'] = $row['PtLocation'];

    if(isset($row['ptpkey']) && $row['ptpkey'] != 0)
    {
	$strings[$f1.'txtAge[0]'] = $row['age'];
	$strings[$f1.'txtDOB[0]'] = $row['DOB'];
	$strings[$f1.'txtPhysician[0]'] = $row['PtPhysician'];
	$strings[$f1.'txtName[0]'] = $row['NameLast'].', '.$row['NameFirst'].' '.$row['NameMiddle'];
	$strings[$f1.'txtAddress[0]'] = $row['fdf_Address'];
	$strings[$f1.'txtTown[0]'] = $row['fdf_Town'];
    }
    $strings[$f1.'txtCallType[0]'] = $row['CallType'];

    $strings[$f1.'txtCC[0]'] = $row['chief_complaint'];
    $strings[$f1.'txtRunN[0]'] = formatRunNum($runN); 
    $strings[$f1.'txtOnset[0]'] = $row['time_onset'];
    $strings[$f1.'txtAid[0]'] = $row['AidGiven'];
    $strings[$f1.'txtAllergies[0]'] = $row['Allergies'];
    $strings[$f1.'txtMeds[0]'] = $row['Medications'];
		
    // TODO - vitals
    for($i = 0; $i < $row['nextVitalsRow']; $i++)
    {
	//vitals1
	$strings[$f1.'vTime['.$i.']'] = $row['Vitals_'.$i.'_time'];
	$strings[$f1.'vBP['.$i.']'] = $row['Vitals_'.$i.'_bp'];
	$strings[$f1.'vPulse['.$i.']'] = $row['Vitals_'.$i.'_pulse'];
	$strings[$f1.'vResp['.$i.']'] = $row['Vitals_'.$i.'_resp'];
	$strings[$f1.'vLung['.$i.']'] = $row['Vitals_'.$i.'_lungSounds'];
	$strings[$f1.'vConsc['.$i.']'] = $row['Vitals_'.$i.'_consciousness'];
	$strings[$f1.'vPupils['.$i.']'] = doPupils($row['Vitals_'.$i.'_pupilL'], $row['Vitals_'.$i.'_pupilR']);
	$strings[$f1.'vSkin['.$i.']'] = doSkin($row['Vitals_'.$i.'_skinTemp'], $row['Vitals_'.$i.'_skinColor'], $row['Vitals_'.$i.'_skinMoisture']);
    }
		
    $strings[$f1.'txtTx[0]'] = $row['tx'];

    //crew and drivers 
    // drivers
    $query = "SELECT * FROM calls_crew WHERE RunNumber=$runN AND is_deprecated=0 AND (is_driver_to_scene=1 OR is_driver_to_hosp=1 OR is_driver_to_bldg=1);";
    $result = mysql_query($query);
    while($r = mysql_fetch_assoc($result))
    {
	if($r['is_driver_to_scene'] == 1){ $strings[$f1.'txtDrvScene[0]'] = $r['EMTid'];}
	if($r['is_driver_to_hosp'] == 1){ $strings[$f1.'txtDrvHosp[0]'] = $r['EMTid'];}
	if($r['is_driver_to_bldg'] == 1){ $strings[$f1.'txtDrvBldg[0]'] = $r['EMTid'];}
    }

    // non-drivers, not on scene
    $i = 1;
    $query = "SELECT * FROM calls_crew WHERE RunNumber=$runN AND is_deprecated=0 AND is_driver_to_scene=0 AND is_driver_to_hosp=0 AND is_driver_to_bldg=0 AND is_on_scene=0;";
    $result = mysql_query($query);
    while($r = mysql_fetch_assoc($result))
    {
	$strings[$f1.'txtC'.$i.'[0]'] = $r['EMTid'];
	$i++;
    }

    // on scene
    $i = 1;
    $query = "SELECT * FROM calls_crew WHERE RunNumber=$runN AND is_deprecated=0 AND is_driver_to_scene=0 AND is_driver_to_hosp=0 AND is_driver_to_bldg=0 AND is_on_scene=1;";
    $result = mysql_query($query);
    while($r = mysql_fetch_assoc($result))
    {
	$strings[$f1.'txtOS'.$i.'[0]'] = $r['EMTid'];
    }

    //als
    $strings[$f1.'txtALSunit[0]'] = $row['ALSunit'];

    // Signature
    $strings[$f1.'txtSigID[0]'] = $row['signature_EMTid'];

    // Trans To
    if($row['PtTransTo'] == 'Valley')
    {
	$keys[$f1.'cbxTransValley[0]'] = 1;
    }
    elseif($row['PtTransTo'] == 'None')
    {
	$keys[$f1.'cbxTransNone[0]'] = 1;
    }
    else
    {
	$strings[$f1.'txtTransOtr[0]'] = $row['PtTransTo'];
	$keys[$f1.'cbxTransOtr[0]'] = 1; 
    }
    $strings[$f1.'txtPassengers[0]'] = $row['Passengers'];
    $strings[$f1.'txtEqLeft[0]'] = $row['EquipLeft'];
		
    //outcome
    if($row['OC'] == 'ALS')
    {
	$keys[$f1.'cbxOCals[0]'] = 1;
    }
    elseif($row['OC'] == 'BLS')
    {
	$keys[$f1.'cbxOCbls[0]'] = 1;
    }
    elseif($row['OC'] == 'Air')
    {
	$keys[$f1.'cbxOCair[0]'] = 1;
    }
    elseif($row['OC'] == 'Refusal')
    {
	$keys[$f1.'cbxOCrma[0]'] = 1;
    }
    elseif($row['OC'] == 'Canceled')
    {
	$keys[$f1.'cbxOCcanc[0]'] = 1;
    }
    elseif($row['OC'] == 'DOA')
    {
	$keys[$f1.'cbxOCdoa[0]'] = 1;
    }
    elseif($row['OC'] == 'NoCrew')
    {
	$keys[$f1.'cbxOCnoCrew[0]'] = 1;
    }
    else
    {
	$keys[$f1.'cbxOCotr[0]'] = 1; 
    }

    //als boxes 
    if($row['ALS'] == 'RespRel')
    {
	$keys[$f1.'cbxALSrespRel[0]'] = 1;
    }
    elseif($row['ALS'] == 'RespNoTrans')
    {
	$keys[$f1.'cbxALSrespBLS[0]'] = 1;
    }
    elseif($row['ALS'] == 'NotCalled')
    {
	$keys['form1[0].#subform[0].cbxALSnc[0]'] = 1;
    }
    elseif($row['ALS'] == 'Unavail')
    {
	$keys['form1[0].#subform[0].cbxALSunavail[0]'] = 1;
    }
    elseif($row['ALS'] == 'CancelledPD')
    {
	$keys['form1[0].#subform[0].cbxALScanPD[0]'] = 1;
    }
    elseif($row['ALS'] == 'CancelledBLS')
    {
	$keys['form1[0].#subform[0].cbxALScanBLS[0]'] = 1;
    }
    elseif($row['ALS'] == 'CancelledMD')
    {
	$keys['form1[0].#subform[0].cbxALScanMD[0]'] = 1;
    }
    elseif($row['ALS'] == 'RespALS')
    {
	$keys[$f1.'cbxALSrespALS[0]'] = 1;
    }

    // RMA attached?
    if($row['OC']=="Refusal")
    {
	$keys['form1[0].#subform[0].#area[0].#area[1].cbxRMAatt[0]'] = 1;
    }
    else
    {
	$keys['form1[0].#subform[0].#area[0].#area[1].cbxRMAatt[0]'] = 'Off';
    }
	
    if($row['sex'] == 'Male')
    {
	$keys['form1[0].#subform[0].#area[0].#area[1].cbxM[0]'] = 1; //male 
    }
    if($row['sex'] == 'Female')
    {
	$keys['form1[0].#subform[0].#area[0].#area[1].cbxF[0]'] = 1; //female 
    }

    if($row['CallLoc']=='Home')
    {
	$keys['form1[0].#subform[0].cbxLocHome[0]'] = 1;
    }
    else
    {
	$keys['form1[0].#subform[0].cbxLocOtr[0]'] = 1;
	$strings[$f1.'txtLocOtr[0]'] = $row['call_loc_other'];
    }

    //aid given by check boxes 
    if(isset($row['AidGivenBy_PD'])){ $keys['form1[0].#subform[0].#area[0].#area[1].cbxAidPD[0]'] = 1; }
    if(isset($row['AidGivenBy_Family'])){ $keys['form1[0].#subform[0].#area[0].#area[1].cbxAidFam[0]'] = 1; }
    if(isset($row['AidGivenBy_Bystander'])){ $keys['form1[0].#subform[0].#area[0].#area[1].cbxAidByst[0]'] = 1; }
    if(isset($row['AidGivenBy_Other']))
    {
	$keys['form1[0].#subform[0].#area[0].#area[1].cbxAidOtr[0]'] = 1;
	$strings[$f1.'txtAidOtr[0]'] = $row['AidGivenBy_Other_Text'];
    }
    //LOC yes or no 
    if($row['loc']=="yes")
    {
	$keys[$f1.'cbxLOCyes[0]'] = 1;
    }
    else
    {
	$keys[$f1.'cbxLOCno[0]'] = 1; 
    }

    // TODO - do this - crew type
    //crew type 
    if($row['is_second_rig'] == 1) 
    {
	$keys[$f1.'cbx2nd[0]'] = 1;
    }
    elseif($row['is_duty_call'] == 1) 
    {
	$keys['form1[0].#subform[0].#area[0].#area[1].cbxDuty[0]'] = 1;
    }
    else 
    {
	// default to general
	$keys[$f1.'cbxGen[0]'] = 1;
    }

    //injured area
    if(strstr($row['injured_area'], 'Head') == TRUE)
    {
	$keys[$f1.'cbxHead[0]'] = 1;
    }
    if(strstr($row['injured_area'], 'Face') == TRUE)
    {
	$keys[$f1.'cbxFace[0]'] = 1;
    }
    if(strstr($row['injured_area'], 'Neck') == TRUE)
    {
	$keys[$f1.'cbxNeck[0]'] = 1;
    }
    if(strstr($row['injured_area'], 'Back') == TRUE)
    {
	$keys[$f1.'cbxBack[0]'] = 1;
    }
    if(strstr($row['injured_area'], 'Chest') == TRUE)
    {
	$keys[$f1.'cbxChest[0]'] = 1;
    }
    if(strstr($row['injured_area'], 'Abdomen') == TRUE)
    {
	$keys[$f1.'cbxAbd[0]'] = 1;
    }
    if(strstr($row['injured_area'], 'Left Arm') == TRUE)
    {
	$keys[$f1.'cbxLArm[0]'] = 1;
    }
    if(strstr($row['injured_area'], 'Right Arm') == TRUE)
    {
	$keys[$f1.'cbxRArm[0]'] = 1;
    }
    if(strstr($row['injured_area'], 'Left Hand') == TRUE)
    {
	$keys[$f1.'cbxLHand[0]'] = 1;
    }
    if(strstr($row['injured_area'], 'Right Hand') == TRUE)
    {
	$keys[$f1.'cbxRHand[0]'] = 1;
    }
    if(strstr($row['injured_area'], 'Left Hip') == TRUE)
    {
	$keys[$f1.'cbxLHip[0]'] = 1;
    }
    if(strstr($row['injured_area'], 'Right Hip') == TRUE)
    {
	$keys[$f1.'cbxRHip[0]'] = 1;
    }
    if(strstr($row['injured_area'], 'Left Leg') == TRUE)
    {
	$keys[$f1.'cbxLLeg[0]'] = 1;
    }
    if(strstr($row['injured_area'], 'Right Leg') == TRUE)
    {
	$keys[$f1.'cbxRLeg[0]'] = 1;
    }
    if(strstr($row['injured_area'], 'Left Foot') == TRUE)
    {
	$keys[$f1.'cbxLFoot[0]'] = 1;
    }
    if(strstr($row['injured_area'], 'Right Foot') == TRUE)
    {
	$keys[$f1.'cbxRFoot[0]'] = 1;
    }
}

function create_fdf ($pdffile, $strings, $keys)
{
   $fdf = "%FDF-1.2\n%????\n";
   $fdf .= "1 0 obj \n<< /FDF << /Fields [\n";

   foreach ($strings as $key => $value)
   {
       $key = addcslashes($key, "\n\r\t\\()");
       $value = addcslashes($value, "\n\r\t\\()");
       $fdf .= "<< /T ($key) /V ($value) >> \n";
   }
   foreach ($keys as $key => $value)
   {
       $key = addcslashes($key, "\n\r\t\\()");
       $fdf .= "<< /T ($key) /V /$value >> \n";
   }

   $fdf .= "]\n/F ($pdffile) >>";
   $fdf .= ">>\nendobj\ntrailer\n<<\n";
   $fdf .= "/Root 1 0 R \n\n>>\n";
   $fdf .= "%%EOF";

   return $fdf;
}
function parseTime($origTime)
{
	$s = explode(':', $origTime); 
	$newTime = $s[0].":".$s[1];
	return $newTime;
}
function parseDate($origDate)
{
	$s = explode('-', $origDate);
	$newDate = $s[1].'/'.$s[2].'/'.$s[0];
	return $newDate;
}
function doPupils($L, $R)
{
	if(trim($L) == 'Equal')
	{
		return "Equal";
	}
	elseif(trim($L) == "" && trim($R) == "")
	{
	    return "";
	}
	else
	{
	    $s = "L: ".$L." R: ".$R;
	    return $s;
	}
}

function doSkin($a, $b, $c)
{
    $s = "";
    if(trim($a) != ""){ $s .= $a.", ";}
    if(trim($b) != ""){ $s .= $b.", ";}
    if(trim($c) != ""){ $s .= $c.", ";}
    return trim($s, ", ");
}

?>  
