<?php 
//
// signOn.php
//
// this is the pop-up signon form for the schedule
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
//      $Id$


/*
TODO:
seems to be working, for days at least, for add remove and edit.
The one remaining problem is that after successful submission, the browser
displays signOn.php fullscreen - leaves the schedule and doesn't just close the popup.

Can we fix this somehow with editing the signOn.php code or looking at the JS handler (is signOn.php
doing something to the browser?)

Or do we have to go away from HTML_QuickForm and implement the form ourselves?

*/


//required for HTML_QuickForm PEAR Extension
if(file_exists('../HTML/QuickForm.php'))
{
    require_once '../HTML/QuickForm.php';
}
else
{
    require_once 'HTML/QuickForm.php';
}
if(file_exists('../HTML/QuickForm/element.php'))
{
    require_once '../HTML/QuickForm/element.php';
}
else
{
    require_once 'HTML/QuickForm/element.php';
}

// this file will import the user's customization
require_once('./config/config.php');

// schedule configuration
require_once('./config/scheduleConfig.php');

// for email notifications:
require_once('./inc/notify.php');
require_once('./inc/global.php');
require_once('./inc/logging.php');

//instantiate the form
$form = new HTML_QuickForm('firstForm');


// get the URL variables
if(! empty($_GET['ts']))
{
    $ts = ((int)$_GET['ts']);
    $form->addElement('hidden','ts',$ts);
}
else
{
    $ts = HTML_QuickForm_element::getValue($form->getElement('ts'));
}

if(! empty($_GET['id']))
{
    $signonID = ((int)$_GET['id']);
    $form->addElement('hidden','signonID',$signonID);
}
else
{
    $signonID = HTML_QuickForm_element::getValue($form->getElement('signonID'));
}


// parse out the year, month, date, and shift
$year = date("Y", $ts);
$month = date("m", $ts);
$date = date("d", $ts);
$shift = tsToShiftName($ts);

// define an error message
$errorMsg = "<br>Please try again. If you recieve this message more than once while trying to perform the same action, please notify the administrator of this system.";

//start working with the form

$defaults = array();
addFormElements(); 

// DEBUG - TODO - do we need this anymore, since we're using IDs not slots?
if(! empty($_GET['id']))
{
    populateMe();
}
$form->setDefaults($defaults);

//try to validate form 
if ($form->validate())
{
	# If the form validates, freeze and process the data
	//post-validation filters here 
	$form->applyFilter('__ALL__', 'trim');
	$form->process('processForm', false);   
	// exit the script, on successful insertion

?>

<?php

}

function processForm($formItems)
{
    global $action;
    global $minRightsEdit;
    global $minRightsChangePast;
    global $dbName;
    global $config_sched_table;
    global $nightLastHour;
    global $dayLastHour;


    $action = $formItems['action'];
    if(isset($formItems['signonID'])){ $signonID = $formItems['signonID'];}
    $adminID = addslashes($formItems['adminID']);
    $adminPW = md5($formItems['adminPW']);
    $EMTid = addslashes($formItems['EMTid']);
    $start = $formItems['start'];
    $end = $formItems['end'];
    $ts = $formItems['ts'];

    $temp_ts_ary = makeTimestampsFromTimes($ts, $start, $end);
    $start_ts = $temp_ts_ary['start'];
    $end_ts = $temp_ts_ary['end'];
    $year = date("Y", $start_ts);
    $month = date("m", $start_ts);
    $date = date("d", $start_ts);
    $shift = tsToShiftName($start_ts);

    // DEBUG
    //error_log("COM_JASONANTMAN_DEBUG - signOn.php - action= $action signonID= $signonID adminID= $adminID EMTid= $EMTid start= $start end= $end year= $year month= $month date= $date shift= $shift");

    // start MySQL connection
    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.".$errorMsg);
    mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!".$errorMsg);

    //AUTHENTICATION
    $query = 'SELECT pwdMD5,rightsLevel FROM roster WHERE EMTid="'.$adminID.'";';
    $result = mysql_query($query) or die ("Auth Query Error");
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
	die("Either your ID/password is incorrect or you are not authorized to perform this action (signing on).");
    }
    if($requireAuthToEdit && ($action=="edit") && ((! $auth) || ($rightsLevel < $minRightsEdit)))
    {
	die("Either your ID/password is incorrect or you are not authorized to perform this action (editing a signon).");
    }
    if($requireAuthToRemove && ($action=="remove") && ((! $auth) || ($rightsLevel < $minRightsEdit)))
    {
	die("Either your ID/password is incorrect or you are not authorized to perform this action (removing a signon).");
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
	    die("Your user is not authorized to change a signon from the past. You must login with a username and password, or contact an administrator.");
	}
    }

    //figure out whether this member is eligable to pull duty
    $query = 'SELECT status FROM roster WHERE EMTid="'.$formItems['EMTid'].'";';
    $result = mysql_query($query) or die ("Auth Query Error");
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
		die("I'm sorry, but a member of type ".$type." cannot sign up for duty.");
	    }
	}
    }

    if($formItems['action']=='remove')
    {
	//remove 
	$query = 'UPDATE '.$config_sched_table.' SET deprecated=1 WHERE sched_entry_id='.$signonID.';';
	$result = mysql_query($query) or die ("Query Error");
	// TODO - reimplement mails
	//schedule_remove_mail($year, $month, $date, $shift, $formItems['EMTid'], $signonID);
	logEditForm($signonID, null, $adminID, $auth, "signOn.php", $query);
    }
    else
    {
	//let's validate the times
	// TODO - get rid of this day and night stuff
	if($start_ts >= $end_ts)
	{
	    die("I'm sorry, but the times you selected are invalid.");
	}

	if($action == "edit")
	{
	    $query = 'UPDATE '.$config_sched_table.' SET deprecated=1 WHERE sched_entry_id='.$signonID.';';
	    $result = mysql_query($query) or die ("Query Error");
	    $query2 = 'INSERT INTO '.$config_sched_table.' SET EMTid="'.mysql_real_escape_string($EMTid).'",start_ts='.$start_ts.',end_ts='.$end_ts.',sched_year='.$year.',sched_month='.$month.',sched_date='.$date.',sched_shift_id='.shiftNameToID($shift).' ;';
	    $result = mysql_query($query2) or die ("Query Error");
	    $newID = mysql_insert_id();
	    logEditForm($signonID, $newID, $adminID, $auth, "signOn.php", ($query." ".$query2));
	}
	else
	{
	    // new signon
	    $query2 = 'INSERT INTO '.$config_sched_table.' SET EMTid="'.mysql_real_escape_string($EMTid).'",start_ts='.$start_ts.',end_ts='.$end_ts.',sched_year='.$year.',sched_month='.$month.',sched_date='.$date.',sched_shift_id='.shiftNameToID($shift).' ;';
	    $result = mysql_query($query2) or die ("Query Error");
	    $newID = mysql_insert_id();
	    logEditForm(null, $newID, $adminID, $auth, "signOn.php", ($query." ".$query2));
	}

	// TODO - re-code email stuff
	//schedule_edit_mail($year, $month, $date, $shift, $EMTid, $start, $end, $signonID); // for edit and add
    }
}
  
function populateMe() 
{
    global $form; 
    global $defaults;
    global $dbName;
    global $signonID;
    global $ts;
    global $config_sched_table;

    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.".$errorMsg);
    mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!".$errorMsg);
    // TODO - remove this commented out query
    //$query = 'SELECT s.* FROM '.$config_sched_table.' AS s LEFT JOIN schedule_shifts AS ss ON s.sched_shift_id=ss.sched_shift_id WHERE sched_year='.$year.' AND sched_month='.$month.' AND sched_date='.$date.' AND ss.shiftTitle="'.$shift.'" AND s.deprecated=0 ORDER BY s.start_ts;';
    $query = 'SELECT * FROM '.$config_sched_table.' WHERE sched_entry_id='.$signonID.';';
    $result = mysql_query($query);
    if(mysql_num_rows($result) < 1){ $defaults['action'] = "signOn"; return null;}
    $row = mysql_fetch_array($result) or die("Error fetching result for defaults.");
    $defaults['action'] = "edit";
    $defaults['EMTid'] = $row['EMTid'];
    $defaults['start'] = date("H:i:s", $row['start_ts']);
    $defaults['end'] = date("H:i:s", $row['end_ts']);
    $defaults['start_ts'] = $row['start_ts'];
    $defaults['end_ts'] = $row['end_ts'];
}

// removed putToDB - was an empty function
function addFormElements() 
{
	global $form; 
	global $year;
	global $month;
	global $date;
	global $ts;
	global $shift;
	global $defaults;
		
	// DEBUG - new timestamp-based start and end time calculation, based on 12-hour shifts
	$startTimes = array();
	$endTimes = array();
	$minStart = "";
	$maxEnd = "";
	for($i = $ts; $i < ($ts + 43200); $i += 1800)
	{
	    $startTimes[date("H:i:s", $i)] = date("H:i:s", $i);
	    if($minStart == ""){ $minStart = date("H:i:s", $i); }
	}
	for($i = $ts + 1800; $i < ($ts + 45000); $i += 1800)
	{
	    $endTimes[date("H:i:s", $i)] = date("H:i:s", $i);
	    $maxEnd = date("H:i:s", $i);
	}
	// END DEBUG new timestamp-based start and end time calculation

	// create elements

	$form->addElement('header', null, $year."-".$month."-".$date." ".$shift);


	$form->addElement('radio','action','Action:','Sign On','signOn');
	$form->addElement('radio','action',null,'Edit','edit');
	$form->addElement('radio','action',null,'Remove','remove');
	$form->addElement('text', 'EMTid', 'ID#', array('size' => 10, 'maxlength' => 5)); 
	$defaults["start"] = $minStart;
	$defaults["end"] = $maxEnd;
	$startElement =& $form->createElement('select', 'start', 'Start Time:'); 
	$endElement =& $form->createElement('select', 'end', 'End Time:'); 

	$startElement -> loadArray($startTimes, 'NULL');
	$endElement -> loadArray($endTimes, 'NULL');
	$form->addElement($startElement);
	$form->addElement($endElement);


	$buttonGroup[] =& HTML_QuickForm::createElement('reset', 'btnReset', 'Reset');
	$buttonGroup[] =& HTML_QuickForm::createElement('submit', 'btnSubmit', 'Submit');
	$form->addGroup($buttonGroup, 'buttonGroup', null, "    ");

	$form->addElement('hidden','end_ts',"");
	$form->addElement('hidden','start_ts',"");

	$form->addElement('static',null,'<br><br>');
	$form->addElement('header',null, 'For changing past only:');
	$form->addElement('text', 'adminID', 'Administrator ID#', array('size' => 10, 'maxlength' => 5));
	$form->addElement('password', 'adminPW', 'Password', array('size' => 10, 'maxlength' => 10));
}

function hourToTime($hour)
{
    if(strlen($hour)==1)
    {
	return "0".$hour.":00:00";
    }
    else
    {
	return $hour.":00:00";
    }
}

?>
 
<HTML> 
<HEAD> 
	<TITLE>Schedule Sign On</TITLE>
	<BASEFONT face="Arial" size="2" >
	<link rel="stylesheet" href="php_ems.css" type="text/css">
</HEAD>
<BODY>	
<?php
	// display the form
	$form->display();
?>
</BODY>
</HTML>