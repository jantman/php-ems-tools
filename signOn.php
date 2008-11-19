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
if(! empty($_GET['slot']))
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

<SCRIPT LANGUAGE="JavaScript">
<!--hide
     opener.location.reload(true);
	self.close();
//-->
</SCRIPT>

<?php

}
function processForm($formItems)
{
    global $action;
    global $minRightsEdit;
    global $minRightsChangePast;
    global $dbName;

    $action = $formItems['action'];
    $adminID = addslashes($formItems['adminID']);
    $adminPW = md5($formItems['adminPW']);
    $year = addslashes($formItems['year']);
    $month = addslashes($formItems['month']);
    $date = addslashes($formItems['date']);
    $shift = addslashes($formItems['shift']);
    $slot = addslashes($formItems['slot']);
    $EMTid = addslashes($formItems['EMTid']);
    $start = addslashes($formItems['start']);
    $end = addslashes($formItems['end']);

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
	die("Either your ID/password is incorrect or you are not authorized to perform this action.");
    }
    if($requireAuthToEdit && ($action=="edit") && ((! $auth) || ($rightsLevel < $minRightsEdit)))
    {
	die("Either your ID/password is incorrect or you are not authorized to perform this action.");
    }
    if($requireAuthToRemove && ($action=="remove") && ((! $auth) || ($rightsLevel < $minRightsEdit)))
    {
	die("Either your ID/password is incorrect or you are not authorized to perform this action.");
    }
    if($requireAuthToChangePast && ((! $auth) || ($rightsLevel < $minRightsChangePast)))
    {
	$changeDay = strtotime($year."-".$month."-".$date);
	$difference = time() - $changeDay;
	// TODO - get rid of this day and night stuff
	if($shift=="night")
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
	    die("Either your ID/password is incorrect or you are not authorized to perform this action.");
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
	$query = 'UPDATE schedule_'.$year.'_'.$month.'_'.$shift.' SET '.$slot.'ID=null,'.$slot.'Start="0000-00-00 00:00:00",'.$slot.'End="0000-00-00 00:00:00" WHERE date='.$date.';';
	schedule_remove_mail($year, $month, $date, $shift, $formItems['EMTid']);

	

    }
    else
    {
	//let's validate the times
	// TODO - get rid of this day and night stuff
	if($shift=="day")
	{
	    if(substr($formItems['start'],0,2) > substr($formItems['end'],0,2))
	    {
		$failed = true;
	    }
	}
	if($shift=="night")
	{
	    $sS = substr($formItems['start'],0,2);
	    $eS = substr($formItems['end'],0,2);
	    if($sS < 24 && $sS > 17 && $eS < 24 && $eS > 17) // both between 18-23
	    {
		if($sS > $eS)
		{
		    $failed = true;
		}
	    }
	    if($sS < 7 && $eS < 7) // both between 0-6
	    {
		if($sS > $eS)
		{
		    $failed = true;
		}
	    }
	    if($eS >= 0 && $eS < 7 && $sS > 17 && $sS < 24) // end 0-6 start 18-23
	    {
		$failed = false;
	    }
	    if($sS >=0 && $sS < 7 && $eS > 17 && $eS < 24) // start 0-6 end 18-23
	    {
		$failed = true;
	    }
	    if($failed)
	    {
		die("I'm sorry, but the times you selected are invalid.");
	    }
	}


	$query = 'UPDATE schedule_'.$year.'_'.$month.'_'.$shift.' SET '.$slot.'ID="'.$EMTid.'",'.$slot.'Start="'.$start.'",'.$slot.'End="'.$end.'" WHERE date='.$date.';';
	schedule_edit_mail($year, $month, $date, $shift, $EMTid, $start, $end); // for edit and add

    }
    $result = mysql_query($query) or die ("Query Error");

    // change logging
    $chQuery =  'CREATE TABLE IF NOT EXISTS schedule_'.$year.'_'.$month.'_change LIKE schedule_change_template;';
    $result = mysql_query($chQuery) or die ("Query Error");
    $address = $_SERVER['REMOTE_ADDR'];
    $host = gethostbyaddr($address);
    $chQuery = 'INSERT INTO schedule_'.$year.'_'.$month.'_change SET timestamp='.time().',EMTid="'.$adminID.'",query="'.make_safe($query).'",host="'.$host.'",address="'.$address.'",form="signOn";';
    mysql_query($chQuery) or die ("Query Error".mysql_error()." in query ".$chQuery);
}
  
function populateMe() 
{
	global $form; 
	global $defaults;
	global $date;
	global $year;
	global $month;
	global $shift;
	global $slot;
	global $dbName;

    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.".$errorMsg);
    mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!".$errorMsg);
    $query = 'SELECT * FROM schedule_'.$year.'_'.$month.'_'.$shift.' WHERE date='.$date.';';
    $result = mysql_query($query);
    $row = mysql_fetch_array($result) or die("Error fetching result for defaults.");
    if($row[$slot."ID"]<>"")
    {
	//we have someone
	$defaults['action'] = "edit";
	$defaults['EMTid'] = $row[$slot.'ID'];
	$defaults['start'] = $row[$slot.'Start'];
	$defaults['end'] = $row[$slot.'End'];
    }
    else
    {
	$defaults['action'] = "signOn";
    }

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
	for($i = $ts; $i < ($ts + 43200); $i += 1800)
	{
	    $startTimes[date("H:i:s", $i)] = date("H:i:s", $i);
	}
	for($i = $ts + 1800; $i < ($ts + 45000); $i += 1800)
	{
	    $endTimes[date("H:i:s", $i)] = date("H:i:s", $i);
	}
	// END DEBUG new timestamp-based start and end time calculation

	// create elements

	$form->addElement('header', null, $year."-".$month."-".$date." ".$shift);


	$form->addElement('radio','action','Action:','Sign On','signOn');
	$form->addElement('radio','action',null,'Edit','edit');
	$form->addElement('radio','action',null,'Remove','remove');

	$form->addElement('text', 'EMTid', 'ID#', array('size' => 10, 'maxlength' => 5)); 
	$startElement =& $form->createElement('select', 'start', 'Start Time:'); 
	$endElement =& $form->createElement('select', 'end', 'End Time:'); 

	$startElement -> loadArray($startTimes, 'NULL');
	$endElement -> loadArray($endTimes, 'NULL');
	$form->addElement($startElement);
	$form->addElement($endElement);


	$buttonGroup[] =& HTML_QuickForm::createElement('reset', 'btnReset', 'Reset');
	$buttonGroup[] =& HTML_QuickForm::createElement('submit', 'btnSubmit', 'Submit');
	$form->addGroup($buttonGroup, 'buttonGroup', null, "    ");

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