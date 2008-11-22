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


// DEBUG - TODO - do we still need this file?

/*
URL VARS (GET):
EDIT:
action == edit
year
month
shift ("Day" | "Night")
date
key
ts

NEW:
ts
shift

*/

// this file will import the user's customization
require_once('./config/config.php');
// schedule configuration
require_once('./config/scheduleConfig.php');
// for email notifications:
require_once('./inc/notify.php');
require_once('./inc/global.php');

// begin setting up HTML and start the form

echo '<form name="signOnForm">'."\n";

//
// PARSE GET, create hidden fields, add to vars as needed
//
if(! empty($_GET['year']))
{
    $year = $_GET['year'];
    echo '<input type="hidden" name="year" value="'.$year.'" id="form_year" />'."\n";
}
else
{
    $year = $_POST['year'];
}
if(! empty($_GET['month']))
{
    $month = $_GET['month'];
    echo '<input type="hidden" name="month" value="'.$month.'" id="form_month" />'."\n";
}
else
{
    $month = $_POST['month'];
}
if(! empty($_GET['date']))
{
    $date = $_GET['date'];
    echo '<input type="hidden" name="date" value="'.$date.'" id="form_date" />'."\n";
}
else
{
    $date = $_POST['date'];
}
if(! empty($_GET['shift']))
{
    $shift = strtolower($_GET['shift']);
    echo '<input type="hidden" name="shift" value="'.$shift.'" id="form_shift" />'."\n";
}
else
{
    $shift = $_POST['shift'];
}
if(! empty($_GET['slot']))
{
    $slot = $_GET['slot'];
    echo '<input type="hidden" name="slot" value="'.$slot.'" id="form_slot" />'."\n";
}
else
{
    $slot = $_POST['slot'];
}
if(! empty($_GET['ts']))
{
    $ts = $_GET['ts'];
    echo '<input type="hidden" name="ts" value="'.$ts.'" id="form_ts" />'."\n";
}
else
{
    $ts = $_POST['ts'];
}
if(! empty($_GET['monthTS']))
{
    $monthTS = $_GET['monthTS'];
    echo '<input type="hidden" name="monthTS" value="'.$monthTS.'" id="form_monthTS" />'."\n";
}
else
{
    $monthTS = $_POST['monthTS'];
}

addFormElements(); 
// TODO: split into 2 functions, one which populates.

echo '</form>'."\n";

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
function putToDB($formItems) 
{
	global $errorMsg;
}
function addFormElements() 
{
	global $form; 
	global $year;
	global $month;
	global $date;
	global $shift;

        // NOTE: the above code does not work yet, so we'll keep the hard-coded times:

	$dayStart='<option value="06:00:00" selected="selected">06:00:00</option><option value="07:00:00">07:00:00</option><option value="08:00:00">08:00:00</option><option value="09:00:00">09:00:00</option><option value="10:00:00">10:00:00</option><option value="11:00:00">11:00:00</option><option value="12:00:00">12:00:00</option><option value="13:00:00">13:00:00</option><option value="14:00:00">14:00:00</option><option value="15:00:00">15:00:00</option><option value="16:00:00">16:00:00</option><option value="17:00:00">17:00:00</option>';
	$dayEnd='<option value="07:00:00">07:00:00</option><option value="08:00:00">08:00:00</option><option value="09:00:00">09:00:00</option><option value="10:00:00">10:00:00</option><option value="11:00:00">11:00:00</option><option value="12:00:00">12:00:00</option><option value="13:00:00">13:00:00</option><option value="14:00:00">14:00:00</option><option value="15:00:00">15:00:00</option><option value="16:00:00">16:00:00</option><option value="17:00:00">17:00:00</option><option value="18:00:00" selected="selected">18:00:00</option>';
	$nightStart='<option value="18:00:00" selected="selected">18:00:00</option><option value="19:00:00">19:00:00</option><option value="20:00:00">20:00:00</option><option value="21:00:00">21:00:00</option><option value="22:00:00">22:00:00</option><option value="23:00:00">23:00:00</option><option value="00:00:00">00:00:00</option><option value="01:00:00">01:00:00</option><option value="02:00:00">02:00:00</option><option value="03:00:00">03:00:00</option><option value="04:00:00">04:00:00</option><option value="05:00:00">05:00:00</option>';
	$nightEnd='<option value="19:00:00">19:00:00</option><option value="20:00:00">20:00:00</option><option value="21:00:00">21:00:00</option><option value="22:00:00">22:00:00</option><option value="23:00:00">23:00:00</option><option value="00:00:00">00:00:00</option><option value="01:00:00">01:00:00</option><option value="02:00:00">02:00:00</option><option value="03:00:00">03:00:00</option><option value="04:00:00">04:00:00</option><option value="05:00:00">05:00:00</option><option value="06:00:00" selected="selected">06:00:00</option>';

	// create elements

	echo '<div class="formElementSet">'."\n";
	if(isset($_GET['action']) && $_GET['action'] == "edit")
	{
	    echo '<input type="radio" name="action" value="edit" checked="checked" id="form_action_edit" /> Edit';
	    echo '</br>';
	    echo '<input type="radio" name="action" value="remove" id="form_action_remove" /> Remove';
	}
	else
	{
	    echo '<input type="hidden" name="action" value="signOn" id="form_action_signon" />'."\n";
	}
	echo '</div> <!-- END formElementSet DIV -->'."\n";

	echo '<div class="formElementSet">'."\n";
	echo '<span class="formError" id="form_EMTid_error" ></span>'."\n";
	echo '<label for="EMTid">ID #: </label><input type="text" name="EMTid" size="10" maxlength="5" id="form_EMTid" /><br />'."\n";
	echo '</div> <!-- END formElementSet DIV -->'."\n";

	echo '<div class="formElementSet">'."\n";
	echo '<span class="formError" id="form_time_error" ></span>'."\n";
	if($shift=="night")
	{
	    echo '<label for="start">Start Time:</label>'."\n";
	    echo '<select name="start" id="form_start" >';
	    echo $nightStart;
	    echo '</select>'."\n";

	    echo '<br />'."\n";

	    echo '<label for="end">End Time:</label>'."\n";
	    echo '<select name="end" id="form_end" >';
	    echo $nightEnd;
	    echo '</select>'."\n";
	}
	else
	{
	    echo '<label for="start">Start Time:</label>'."\n";
	    echo '<select name="start" id="form_start">';
	    echo $dayStart;
	    echo '</select>'."\n";

	    echo '<br />'."\n";

	    echo '<label for="end">End Time:</label>'."\n";
	    echo '<select name="end" id="form_end">';
	    echo $dayEnd;
	    echo '</select>'."\n";
	}
	echo '</div> <!-- END formElementSet DIV -->'."\n";

	echo '<div id="formAdmin">'."\n";
	echo '<span class="formError" id="form_needsAdmin_error" ></span>'."\n";
	echo '<label for="needsAdmin">Administrator Override</label>'."\n";
	echo '<input type="checkbox" name="needsAdmin" onClick="javascript:showAdminLogin()" id="form_needsAdmin" />'."\n";
	echo '</div> <!-- END formAdmin DIV -->'."\n";	

	echo '<div id="formAdminDiv">'."\n";
	echo '<span class="formError" id="form_admin_error" ></span>'."\n";
	echo '<label for="adminID">Admin ID#: </label>'."\n";
	echo '<input type="text" name="adminID" size="10" maxlength="5" id="form_adminID" />'."\n";
	echo '<label for="adminPW">Password: </label>'."\n";
	echo '<input type="password" name="adminPW" size="10" maxlength="10" id="form_adminPW" />'."\n";
	echo '</div> <!-- END formAdminDiv DIV -->'."\n";

	echo '<div id="formButtons">'."\n";
	global $ts;
	echo '<input type="button" value="Update" onClick="submitSignonForm('.$ts.')" />'."\n";
	echo '<input type="reset" value="Reset" onClick="javascript:resetSignonForm()" />'."\n";
	echo '<input type="button" value="Cancel" onClick="hidePopup()" />'."\n";
	echo '</div> <!-- END formButtons DIV -->'."\n";
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