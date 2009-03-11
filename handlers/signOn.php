<?php
//
// handlers/signOn.php
//
// this is the handler for the schedule signon form
// it is retrieved via JS (Ajax) and the result is evaluated as either
// an error message (which is displayed) or *not*, in which case the popup
// is hidden and the schedule is refreshed
//
// +----------------------------------------------------------------------+
// | PHP EMS Tools      http://www.php-ems-tools.com                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006, 2007 Jason Antman.                               |
// |                                                                      |
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation; either version 3 of the License, or    |
// | (at your option) any later version.                                  |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to:                           |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+
// |Please use the above URL for bug reports and feature/support requests.|
// +----------------------------------------------------------------------+
// | Authors: Jason Antman <jason@jasonantman.com>                        |
// +----------------------------------------------------------------------+
// | $LastChangedRevision::                                             $ |
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/handlers/signO#$ |
// +----------------------------------------------------------------------+


// this file will import the user's customization
require_once('../config/config.php');

// schedule configuration
require_once('../config/scheduleConfig.php');

// for email notifications:
require_once('../inc/notify.php');
require_once('../inc/global.php');
require_once('../inc/logging.php');

// for i18n
require_once('../inc/'.$config_i18n_filename);

$action = $_POST['action'];
if(isset($_POST['signonID'])){ $signonID = $_POST['signonID'];}
$adminID = addslashes($_POST['adminID']);
$adminPW = md5($_POST['adminPW']);
$EMTid = addslashes($_POST['EMTid']);
$start = $_POST['start'];
$end = $_POST['end'];
$ts = $_POST['ts'];
$signonID = $_POST['id'];

$temp_ts_ary = makeTimestampsFromTimes($ts, $start, $end);
$start_ts = $temp_ts_ary['start'];
$end_ts = $temp_ts_ary['end'];
$year = date("Y", $start_ts);
$month = date("m", $start_ts);
$date = date("d", $start_ts);
$shift = tsToShiftName($start_ts);

// to append to all error messages - cancel button to close popup
$errorCancel = '<div style="text-align: center;"><input name="buttonGroup[btnCancel]" value="'.$i18n_strings["signOn"]["Cancel"].'" onClick="hidePopup(\'popup\')" type="button" /></div>';

// start MySQL connection
$conn = mysql_connect()   or die("ERROR: ".$i18n_strings["signOnWarnings"]["noDBconnect"].$errorCancel);
mysql_select_db($dbName) or die ("ERROR: ".$i18n_strings["signOnWarnings"]["noDBselect"].$errorCancel);

//AUTHENTICATION
$query = 'SELECT pwdMD5,rightsLevel FROM roster WHERE EMTid="'.$adminID.'";';
$result = mysql_query($query) or die ("ERROR: ".$i18n_strings["signOnWarnings"]["authQueryError"].$errorCancel);
$row = mysql_fetch_array($result);
$auth = false;
$rightsLevel = $row['rightsLevel'];
if($adminPW == $row['pwdMD5'])
{
    $auth = true;
}

global $requireAuthToSignOn;
global $requireAuthToEdit;
global $requireAuthToRemove;
global $requireAuthToChangePast;

if($requireAuthToSignOn && ($action=="signOn") && (! $auth))
{
    die("ERROR: ".$i18n_strings["signOnWarnings"]["errorSignOn"].$errorCancel);
}
if($requireAuthToEdit && ($action=="edit") && ((! $auth) || ($rightsLevel < $minRightsEdit)))
{
    die("ERROR: ".$i18n_strings["signOnWarnings"]["errorEdit"].$errorCancel);
}
if($requireAuthToRemove && ($action=="remove") && ((! $auth) || ($rightsLevel < $minRightsEdit)))
{
    die("ERROR: ".$i18n_strings["signOnWarnings"]["errorRemove"].$errorCancel);
}
if($requireAuthToChangePast && ((! $auth) || ($rightsLevel < $minRightsChangePast)))
{
    $changeDay = $start_ts;
    $difference = time() - $changeDay;
    $sameShift = false;
    // TODO - get rid of this day and night stuff
    if(strtolower($shift)=="night")
    {
	global $nightLastHour;
	$lastHr = $nightLastHour;
    }
    else
    {
	global $dayLastHour;
	$lastHr = $dayLastHour;
    }
    if((date("Y-m") == $year."-".$month) && (date("j")==($date+1)) && date("G")<$lastHr)
    {
	$sameShift = true;
    }
    if($difference > 86400 && (! $sameShift))
    {
	//we are more than a day in the past; fail.
	die("ERROR: ".$i18n_strings["signOnWarnings"]["errorChangePast"].$errorCancel);
    }
}

//figure out whether this member is eligable to pull duty
$query = 'SELECT status FROM roster WHERE EMTid="'.$EMTid.'";';
$result = mysql_query($query) or die ("ERROR: Duty Eligibility Query Error");
$row = mysql_fetch_array($result);
$type = $row['status'];
global $memberTypes;
for($i=0; $i < count($memberTypes); $i++)
{
    // TODO: move which member types can pull duty (and the rest of the memberTypes config) to the database and use a JOIN
    if($memberTypes[$i]['name']==$type)
    {
	if(! $memberTypes[$i]['canPullDuty'])
	{
	    die("ERROR: ".$i18n_strings["signOnWarnings"]["errorMemberType1"]." ".$type." ".$i18n_strings["signOnWarnings"]["errorMemberType2"]." ".$errorCancel);
	}
    }
}

if($action=='remove')
{
    //remove 
    $query = 'UPDATE '.$config_sched_table.' SET deprecated=1 WHERE sched_entry_id='.$signonID.';';
    $result = mysql_query($query) or die ("ERROR: 'Update Deprecated' Query Error");
    // TODO - reimplement mails
    //schedule_remove_mail($year, $month, $date, $shift, $formItems['EMTid'], $signonID);
    logEditForm($signonID, null, $adminID, $auth, "signOn.php", $action, $query);
}
else
{
    //let's validate the times
    // TODO - get rid of this day and night stuff
    if($start_ts >= $end_ts)
    {
	die("ERROR: ".$i18n_strings["signOnWarnings"]["errorTimeInvalid"].$errorCancel);
    }
    
    if($action == "edit")
    {
	$query = 'UPDATE '.$config_sched_table.' SET deprecated=1 WHERE sched_entry_id='.$signonID.';';
	$result = mysql_query($query) or die ("ERROR: 'Update Deprecated' Query Error");
	$query2 = 'INSERT INTO '.$config_sched_table.' SET EMTid="'.mysql_real_escape_string($EMTid).'",start_ts='.$start_ts.',end_ts='.$end_ts.',sched_year='.$year.',sched_month='.$month.',sched_date='.$date.',sched_shift_id='.shiftNameToID($shift).' ;';
	$result = mysql_query($query2) or die ("ERROR: INSERT Query Error");
	$newID = mysql_insert_id();
	logEditForm($signonID, $newID, $adminID, $auth, "signOn.php", $action, ($query." ".$query2));
    }
    else
    {
	// new signon

	// first, make sure nothing is duplicated.
	$checkQuery = "SELECT EMTid,start_ts,end_ts FROM ".$config_sched_table." WHERE EMTid='".$EMTid."' AND deprecated=0 AND ((start_ts <= ".$start_ts." AND end_ts >= ".$start_ts.") OR (start_ts <= ".$end_ts." AND end_ts >= ".$end_ts."));";
	$checkResult = mysql_query($checkQuery) or die ("ERROR: checkQuery Error");
	if(mysql_num_rows($checkResult) > 0)
	{
	    die('ERROR: '.$i18n_strings["signOnWarnings"]["errorOverlap"].$errorCancel);
	}

	// allow the signin
	$query2 = 'INSERT INTO '.$config_sched_table.' SET EMTid="'.mysql_real_escape_string($EMTid).'",start_ts='.$start_ts.',end_ts='.$end_ts.',sched_year='.$year.',sched_month='.$month.',sched_date='.$date.',sched_shift_id='.shiftNameToID($shift).' ;';
	$result = mysql_query($query2) or die ("ERROR: INSERT Query Error");
	$newID = mysql_insert_id();
	logEditForm(null, $newID, $adminID, $auth, "signOn.php", $action, $query2);
    }
    
    // TODO - re-code email stuff
    //schedule_edit_mail($year, $month, $date, $shift, $EMTid, $start, $end, $signonID); // for edit and add
}

echo "OK.";

?>