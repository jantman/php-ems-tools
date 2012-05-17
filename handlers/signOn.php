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
// | $LastChangedRevision:: 155                                         $ |
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/handlers/signO#$ |
// +----------------------------------------------------------------------+

// debugging to error log
$debug = true;

// this file will import the user's customization
require_once('../config/config.php');

// signin stuff
require_once('../config/memberTypes.php');
// schedule configuration
require_once('../config/scheduleConfig.php');

// for email notifications:
require_once('../inc/notify.php');
require_once('../inc/global.php');
require_once('../inc/logging.php');

// for i18n
require_once('../inc/'.$config_i18n_filename);
// start MySQL connection
$conn = mysql_connect()   or die("ERROR: ".$i18n_strings["signOnWarnings"]["noDBconnect"].$errorCancel);
mysql_select_db($dbName) or die ("ERROR: ".$i18n_strings["signOnWarnings"]["noDBselect"].$errorCancel);

$action = $_POST['action'];
if(isset($_POST['signonID'])){ $signonID = $_POST['signonID'];}
$adminID = mysql_real_escape_string($_POST['adminID']);
$adminPW = md5($_POST['adminPW']);
$EMTid = mysql_real_escape_string($_POST['EMTid']);
$start = $_POST['start'];
$end = $_POST['end'];
$ts = $_POST['ts'];
$signonID = $_POST['id'];

$temp_ts_ary = makeTimestampsFromTimes($ts, $start, $end);
$start_ts = $temp_ts_ary['start'];
$end_ts = $temp_ts_ary['end'];
$year = date("Y", $ts);
$month = date("m", $ts);
$date = date("d", $ts);
$shift = tsToShiftName($ts);

if($debug){ error_log("handlers/signOn.php : action=".$action." adminID=".$adminID." EMTid=".$EMTid." start=".$start." end=".$end." ts=".$ts." signonID=".$signonID." start_ts=".$start_ts." end_ts=".$end_ts." year=".$year." month=".$month." date=".$date." shift=".$shift."\n");}

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
if($debug) { error_log("signOn.php: DEBUG: requireAuthToChangePast=$requireAuthToChangePast auth=$auth rightsLevel=$rightsLevel minRightsChangePast=$minRightsChangePast");}
if($requireAuthToChangePast && ((! $auth) || ($rightsLevel < $minRightsChangePast)))
{
    $changeDay = $start_ts;
    $shiftEndTS = $ts + 43200;
    $now = time();
    global $CONFIG_signon_grace_period;
    if($debug) { error_log("signOn.php: DEBUG: ts=$ts shiftEndTS=$shiftEndTS now=$now CONFIG_signon_grace_period=$CONFIG_signon_grace_period");}

    if($now > $shiftEndTS && ($now - $shiftEndTS) > $CONFIG_signon_grace_period)
    {
	//we are more than $CONFIG_signon_grace_period
	error_log("signOn.php: AUTH-FAIL: Attempt to change past shift: ts=$ts shiftEndTS=$shiftEndTS now=$now EMTid=$EMTid");
	die("ERROR: ".$i18n_strings["signOnWarnings"]["errorChangePast"].$errorCancel);
    }
}

// make sure EMTid exists
if(! idInDB($EMTid))
{
    die("ERROR: ".$i18n_strings["signOnWarnings"]["erroEMTidNoExist1"].$EMTid.$i18n_strings["signOnWarnings"]["erroEMTidNoExist2"]);
}

//figure out whether this member is eligable to pull duty
if(! canPullDuty($EMTid)) // function in inc/global.php
{
    die("ERROR: ".$i18n_strings["signOnWarnings"]["errorMemberType1"]." ".$type." ".$i18n_strings["signOnWarnings"]["errorMemberType2"]." ".$errorCancel);
}
// added 2009-09-13
if($action != 'remove' && trim($EMTid) == "")
{
    die("ERROR: ".$i18n_strings["signOnWarnings"]["errorNoId"]." ".$errorCancel);
}
// END added 2009-09-13

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
	if($debug){ die("ERROR: ".$i18n_strings["signOnWarnings"]["errorTimeInvalid"]."<br />".$start_ts."=".date("Y-m-d H:i:s", $start_ts)."<br />".$end_ts."=".date("Y-m-d H:i:s", $end_ts)."<br />".$errorCancel);}
	die("ERROR: ".$i18n_strings["signOnWarnings"]["errorTimeInvalid"].$errorCancel);
    }
    // CHECK AUTH FOR CHANGING ANYTHING IN THE PAST
    // added 2010-03-31 by jantman
    if($requireAuthToChangePast && ((! $auth) || ($rightsLevel < $minRightsChangePast)))
    {
	global $CONFIG_signon_grace_period;
	
	$now = time();
	if($now > $end_ts && ($now - $end_ts) > $CONFIG_signon_grace_period)
	{
	    error_log("signOn.php: AUTH-FAIL: Attempt to change past: now=$now=".date("Y-m-d H:i:s", $now)." EMTid=$EMTid start_ts=$start_ts=".date("Y-m-d H:i:s", $start_ts)." end_ts=$end_ts=".date("Y-m-d H:i:s", $end_ts));
	    die("ERROR: ".$i18n_strings["signOnWarnings"]["errorChangePast"].$errorCancel);
	}

	if($now > $start_ts && ($now - $start_ts) > $CONFIG_signon_grace_period)
	{
	    error_log("signOn.php: AUTH-FAIL: Attempt to change past: now=$now=".date("Y-m-d H:i:s", $now)." EMTid=$EMTid start_ts=$start_ts=".date("Y-m-d H:i:s", $start_ts)." end_ts=$end_ts=".date("Y-m-d H:i:s", $end_ts));
	    die("ERROR: ".$i18n_strings["signOnWarnings"]["errorChangePast"].$errorCancel);
	}
    }    
    // END CHECKING TIME
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
	$checkQuery = "SELECT EMTid,start_ts,end_ts FROM ".$config_sched_table." WHERE EMTid='".$EMTid."' AND deprecated=0 AND ((start_ts <= ".$start_ts." AND end_ts >= ".($start_ts+1).") OR (start_ts < ".$end_ts." AND end_ts >= ".($end_ts+1)."));";
	$checkResult = mysql_query($checkQuery) or die ("ERROR: checkQuery Error");
	if(mysql_num_rows($checkResult) > 0)
	{
	    die('ERROR: '.$i18n_strings["signOnWarnings"]["errorOverlap"].$errorCancel);
	}

	// allow the signin
	$query2 = 'INSERT INTO '.$config_sched_table.' SET EMTid="'.mysql_real_escape_string($EMTid).'",start_ts='.$start_ts.',end_ts='.$end_ts.',sched_year='.$year.',sched_month='.$month.',sched_date='.$date.',sched_shift_id='.shiftNameToID($shift).' ;';
	if($debug){ error_log("handlers/signOn.php - NEW SIGNON. Query: ".$query2."\n");}
	$result = mysql_query($query2) or die ("ERROR: INSERT Query Error");
	$newID = mysql_insert_id();
	logEditForm(null, $newID, $adminID, $auth, "signOn.php", $action, $query2);
    }
    
    // TODO - re-code email stuff
    //schedule_edit_mail($year, $month, $date, $shift, $EMTid, $start, $end, $signonID); // for edit and add
}

echo "OK.";

?>