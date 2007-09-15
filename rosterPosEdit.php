<?php 
// rosterPosEdit.php
//
// Form to edit position and committee data for roster
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

//comments needing attention are tagged with TODO or DEBUG or TEST depending on their purpose 
//code to be removed is prefaced by '//DEP' for deprecated code 

//required for HTML_QuickForm PEAR Extension
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/element.php';
require('./config/config.php');


// tell PHP to ignore any errors less than E_ERROR
error_reporting(1);

if(! empty($_GET['EMTid']))
{
	$id = $_GET['EMTid'];
} 

$EMTid = $id;

//instantiate the form 
$form = new HTML_QuickForm('firstForm');

// mysql connection
$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

//BEGIN CREATING ELEMENTS
//
//

$form->addElement('text', 'adminID', 'Administrator ID#', array('size' => 10, 'maxlength' => 5));
$form->addElement('password', 'adminPW', 'Password', array('size' => 10, 'maxlength' => 10));
$form->addElement('header', null, 'Member Information');
$form->addElement('text', 'EMTid', 'EMT ID#', array('size' => 10, 'maxlength' => 5)); 
$form->freeze('EMTid');

$form->addElement('header', null, 'Positions:');

// officer position
$officerA = array();
global $officerPositions;
for($i=0; $i < count($officerPositions); $i++)
{
    $officerA[$officerPositions[$i]] = $officerPositions[$i]; 
}
$officerE =& $form->createElement('select', 'officer', 'Officer Position:'); 
$officerE -> loadArray($officerA);
$form->addElement($officerE);

// positions
$positionA = array();
global $positions;
for($i=0; $i < count($positions); $i++)
{
    $positionA[$positions[$i]] = $positions[$i]; 
}
$positionE =& $form->createElement('select', 'position', 'Position:'); 
$positionE -> loadArray($positionA);
$form->addElement($positionE);

// trustee
$form->addElement('checkbox', 'trustee', 'Trustee');

$form->addElement('header', null, 'Committees:');

// COMMITTEEs
$commPosA = array();
global $commPositions;
for($i=0; $i < count($commPositions); $i++)
{
    $commPosA[$commPositions[$i]] = $commPositions[$i]; 
}
$committeeA = array();
global $committees;
for($i=0; $i < count($committees); $i++)
{
    $committeeA[$committees[$i]] = $committees[$i]; 
}
// COMMITTEE 1
$comm1E =& $form->createElement('select', 'comm1', 'Committee 1:'); 
$comm1E -> loadArray($committeeA);
$form->addElement($comm1E);
$comm1posE =& $form->createElement('select', 'comm1pos', 'Committee 1 Position:'); 
$comm1posE -> loadArray($commPosA);
$form->addElement($comm1posE);
// COMMITTEE 2
$comm2E =& $form->createElement('select', 'comm2', 'Committee 2:'); 
$comm2E -> loadArray($committeeA);
$form->addElement($comm2E);
$comm2posE =& $form->createElement('select', 'comm2pos', 'Committee 2 Position:'); 
$comm2posE -> loadArray($commPosA);
$form->addElement($comm2posE);

$form->addElement('header', null, '  ');

//HIDDEN FIELDS to keep values between refreshing the form	
if(! empty($_GET['EMTid']))
{
	$form->addElement('hidden','EMTid',$_GET['EMTid']);
	$buttonGroup[] =& HTML_QuickForm::createElement('reset', 'btnReset', 'Reset');
	$buttonGroup[] =& HTML_QuickForm::createElement('submit', 'btnSubmit', 'Submit');
	$form->addGroup($buttonGroup, 'buttonGroup', null, "    ");
}
//FINISHED CREATING ELEMENTS

	$tempE = HTML_QuickForm::createElement('header', null, 'Edit EMT '.$EMTid);
	$form->insertElementBefore($tempE, 'adminID');
if(! empty($_GET['EMTid']))
{
	$def = populateMe($EMTid);
	$form->setDefaults($def);  
	$form->freeze('EMTid');
}
$freeze = true; 
// Try to validate the form 

if ($form->validate()) 
{
    # If the form validates, freeze and process the data
    //post-validation filters here 
    $form->applyFilter('__ALL__', 'trim');
    $form->applyFilter('__ALL__','doQuotes');
     
    $form->process('processForm', false);   
  	 
    // exit the script, on successful insertion

?>

<SCRIPT LANGUAGE="JavaScript">
<!--hide
	window.opener.location=window.opener.location;
	window.close();
//-->
</SCRIPT>

<?php

}

function processForm($formItems)
{
	//this processes the forum when it is submitted. 
	global $EMTid;
	putToDB($formItems);
}
function putToDB($formItems)
{
    $id = $formItems['EMTid'];
	$EMTid = $id;



	if(! validateAdmin($formItems['adminID'], $formItems['adminPW']))
	{
	    die("ERROR AUTH1: I'm sorry, the Administrator ID and Password that you provided is invalid, or you do not have the proper rights level to perform the requested action.");
	}

		idInDB($EMTid) or die("I'm sorry, but EMT number ".$EMTid." is not in the database.");

	$conn = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
	global $dbName;
	mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

	$statementBody = 'EMTid="'.$formItems['EMTid'].'",officer="'.$formItems['officer'].'",position="'.$formItems['position'].'",comm1="'.$formItems['comm1'].'",comm1pos="'.$formItems['comm1pos'].'",comm2="'.$formItems['comm2'].'",comm2pos="'.$formItems['comm2pos'].'"';

	if($formItems['trustee']=="1")
	{
	    $statementBody .= ',trustee="'.$formItems['trustee'].'"';
	    //$query .= ',trustee="Yes"';
	}
	else
	{
    	    $statementBody .= ',trustee="'.$formItems['trustee'].'"';
	    //$query .= ',trustee=""';
	}

	$query = "UPDATE roster SET ";
	$query .= $statementBody;
	$query .= ' WHERE EMTid="'.$EMTid.'";';

	if(mysql_query($query))
	{
		// success
	}
	else
	{
		echo "MYSQL error: ".mysql_error();
	}
	mysql_close($conn);
} 

function populateMe($EMTid) 
{
	//populate from the DB 
	$defaults = array(); 
	$query  = "SELECT * FROM roster WHERE EMTid='".$EMTid."';";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	if(mysql_num_rows($result)==0)
	{
		die("I'm sorry, but the ID# that you entered (".$EMTid.") is not in the database. Please try again.");
	}
	while ($row = mysql_fetch_array($result))  
	{
		$defaults = $row;
		$defaults['EMTid'] = $EMTid;
		$defaults['type'] = $row['status'];
		if($row['trustee'] <> "" && $row['trustee'] <> null)
		{
		    $defaults['trustee'] = "1";
		}
	}

	mysql_free_result($result); 
	return $defaults; 
}

function validateAdmin($adminID, $adminPW)
{
    // this function checks with mySQL to see if the admin is valid
    global $minRightsRoster;
    $query  = "SELECT EMTid,rightsLevel,pwdMD5 FROM roster WHERE EMTid=".$adminID.";";
    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
    if(mysql_num_rows($result)==0)
    {
	// the admin ID given is not a valid EMT id in database
	return false;
    }
    while ($row = mysql_fetch_array($result))
    {
	if($row['rightsLevel'] < $minRightsRoster)
	{
	    // the rights level in the database is less than 1 (officer)
	    return false;
	}
	if($row['pwdMD5']<>md5($adminPW))
	{
	    //provided password does not match database
	    return false;
	}
    }
    mysql_free_result($result);

    return true; 
}

function idInDB($EMTid)
{
    // this function checks with mySQL to see if the admin is valid

    $query  = 'SELECT EMTid FROM roster WHERE EMTid="'.$EMTid.'";';
    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
    if(mysql_num_rows($result)==0)
    {
	// the ID given is not in the database
	return false;
    }
    while ($row = mysql_fetch_array($result))
    {
	if($row['EMTid']==$EMTid)
	{
	    // the specified ID actually is in the table
	    return true;
	}
    }
    mysql_free_result($result);
    // just to make sure we didn't miss anything
    return false; 
}

function doQuotes($s) 
{
    return addslashes($s);
}

mysql_close($connection);

?>
 
<HTML> 
<HEAD> 
<?php
echo '<TITLE>'.$shortName.' Roster - Administrative Tool</TITLE>';
?>
	<link rel="stylesheet" href="php_ems.css" type="text/css">
</HEAD>
<BODY>
<?php
	echo '<h2 align=center>'.$shortName.' Roster Administrative Tool</h2>';
?>
<?php
	// display the form
	$form->display();
?>
</BODY>
</HTML>
