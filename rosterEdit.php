<?php 
//
// rosterEdit.php
//
// Version 0.1 as of Time-stamp: "2006-12-13 21:06:57 jantman"
//
// This file is part of the php-ems-tools package
// available at 
//
// (C) 2006 Jason Antman.
// This package is licensed under the terms of the
// GNU General Public License (GPL)
//

//comments needing attention are tagged with TODO or DEBUG or TEST depending on their purpose 
//code to be removed is prefaced by '//DEP' for deprecated code 

//required for HTML_QuickForm PEAR Extension
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/element.php';
require('custom.php');


// tell PHP to ignore any errors less than E_ERROR
error_reporting(1);

//if the variables are specified in the URL, get them. 
if(! empty($_GET['action']))
{
	$action = $_GET['action'];
	// possible values are 'new', 'edit', 'remove'
}
if(! empty($_GET['EMTid']))
{
	$id = $_GET['EMTid'];
} 

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
$form->addElement('text', 'unitID', 'Unit ID# (optional)', array('size' => 10, 'maxlength' => 5));

// member types
$types = array();
global $typeDefault;
for($i=0; $i < count($memberTypes); $i++)
{
    $types[$memberTypes[$i]['name']] = $memberTypes[$i]['name']; 
}
$typeE =& $form->createElement('select', 'type', 'Member Type:'); 
$typeE -> loadArray($types, $typeDefault);
$form->addElement($typeE);

//rights level
$levels = array(0 => 'None (0)', 1 => 'User (1)', 2 => 'Admin/Officer (2)');
$rightsE =& $form->createElement('select', 'rightsLevel', 'Rights Level:'); 
$rightsE -> loadArray($levels, '0');
$form->addElement($rightsE);

$form->addElement('header', null, 'Personal Information:');

$form->addElement('text', 'FirstName', 'First Name', array('size' => 30, 'maxlength' => 30, 'id' => 'FirstName'));
$form->addElement('text', 'LastName', 'Last Name', array('size' => 30, 'maxlength' => 30, 'id' => 'LastName'));
$form->addElement('text', 'shownAs', 'Shown As', array('size' => 30, 'maxlength' => 30, 'id' => 'shownAs'));
$form->addElement('text', 'SpouseName', 'Spouse Name', array('size' => 30, 'maxlength' => 30, 'id' => 'SpouseName'));
$form->addElement('text', 'Address', 'Address', array('size' => 30, 'maxlength' => 30, 'id' => 'Address'));
// rule for validating phone
//$form->registerRule('phone','regex','/^\(\d{3}\)\d{3}-\d{4}|\d{3}-\d{3}-\d{4}$/');
$form->registerRule('phone','regex','/^\d{3}-\d{3}-\d{4}$/');
$form->addElement('text', 'HomePhone', 'Home Phone', array('size' => 15, 'maxlength' => 14, 'id' => 'HomePhone'));
$form->addRule('HomePhone','Please enter a valid Phone number.','phone');
$form->addElement('text', 'CellPhone', 'Cell Phone', array('size' => 15, 'maxlength' => 14, 'id' => 'CellPhone'));
$form->addRule('CellPhone','Please enter a valid Phone number.','phone');
$form->addElement('static',null,' ','Format: 555-555-5555');
$form->addElement('text', 'Email', 'Email', array('size' => 30, 'maxlength' => 255, 'id' => 'Email'));
$form->addRule('emailorblank', 'This must be a valid EMail Address or it must be blank.', 'Email');

$form->addElement('text', 'TextEmail', 'Text Email', array('size' => 30, 'maxlength' => 255, 'id' => 'TextEmail'));
$form->addRule('emailorblank', 'This must be a valid EMail Address or it must be blank.', 'TextEmail');

$form->addElement('password', 'password', 'Password', array('size' => 15, 'maxlength' => 14, 'id' => 'password'));
$form->addElement('header', null, '  ');

//HIDDEN FIELDS to keep values between refreshing the form	
if(! empty($_GET['action']))
{ 
	$form->addElement('hidden','action',$_GET['action']);
}
if(! empty($_GET['EMTid']))
{
	$form->addElement('hidden','EMTid',$_GET['EMTid']);
}

if($_GET['action']=='edit' || $_GET['action']=='remove' || $_GET['action']=='new')
{	
	$buttonGroup[] =& HTML_QuickForm::createElement('reset', 'btnReset', 'Reset');
	$buttonGroup[] =& HTML_QuickForm::createElement('submit', 'btnSubmit', 'Submit');
	$form->addGroup($buttonGroup, 'buttonGroup', null, "    ");
}
//FINISHED CREATING ELEMENTS

if ($_GET['action']=='remove')
{
	$EMTid = $id;
	$def = populateMe($EMTid);
	$form->setDefaults($def);  
	$form->freeze('EMTid'); 
	$form->freeze('FirstName');
	$form->freeze('LastName');
	$form->freeze('shownAs');
	$form->freeze('rightsLevel');
	$form->freeze('SpouseName');
	$form->freeze('Address');
	$form->freeze('HomePhone');
	$form->freeze('CellPhone');
	$form->freeze('Email');
	$form->freeze('type');
	$form->freeze('password');
	$form->freeze('unitID');
	$form->freeze('TextEmail');
	$tempE = HTML_QuickForm::createElement('header', null, 'Remove EMT '.$EMTid);
	$form->insertElementBefore($tempE, 'adminID');
}
elseif ($_GET[action]=='edit') 
{
	$EMTid = $id;
	$tempE = HTML_QuickForm::createElement('header', null, 'Edit EMT '.$EMTid);
	$form->insertElementBefore($tempE, 'adminID');
	$def = populateMe($EMTid);
	$form->setDefaults($def);  
	$form->freeze('EMTid');
}
else 
{
	// NEW member 
	$EMTid = '';  
	$tempE = HTML_QuickForm::createElement('header', null, 'New Member');
	$form->insertElementBefore($tempE, 'adminID');
	$defaults = array(); 
	// TODO: here, we must find the next EMT ID# not assigned.
	// this will be organization-dependent. we should assume the next integer.
	
	$form->setDefaults($defaults);
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
     opener.location.reload(true);
	self.close();
//-->
</SCRIPT>

<?php

}

function processForm($formItems)
{
	//this processes the forum when it is submitted. 
	global $EMTid;
	global $action;

	if(empty($action)) 
	{
		$action = $formItems['action'];	
	}
	
	if($action=='new')
	{
		putToDB($formItems, $action);
	}
	if($action=='edit')
	{
		putToDB($formItems, $action); 
	}
	if($action=='remove')
	{
	    putToDB($formItems, $action);
	}
}
function putToDB($formItems, $action)
{
	global $EMTid;

	$EMTid = $formItems['EMTid'];

	if(! validateAdmin($formItems['adminID'], $formItems['adminPW']))
	{
	    die("ERROR AUTH1: I'm sorry, the Administrator ID and Password that you provided is invalid, or you do not have the proper rights level to perform the requested action.");
	}

	if(($action=='edit')||($action=='remove'))
	{
		$EMTid = $formItems['EMTid']; 
		idInDB($EMTid) or die("I'm sorry, but EMT number ".$EMTid." is not in the database.");
	}
	else //trying to create a new one
	{	
		(! idInDB($EMTid)) or die("I'm sorry, but EMT number ".$EMTid." is already created. Please try and edit the entry.");
	}
	$conn = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
	global $dbName;
	mysql_select_db($dbName) or die ('ERROR: Unable to select database!');
	global $EMTid;

	if($formItems['type'] == 'Resigned')
	{
	    $pwdMD5 = "RESIGNED";
	}
	else
	{
	    $pwdMD5 = md5($formItems['password']);
	}

	$statementBody = 'EMTid="'.$formItems['EMTid'].'",FirstName="'.$formItems['FirstName'].'",LastName="'.$formItems['LastName'].'",SpouseName="'.$formItems['SpouseName'].'",Address="'.$formItems['Address'].'",HomePhone="'.$formItems['HomePhone'].'",CellPhone="'.$formItems['CellPhone'].'",Email="'.$formItems['Email'].'",password="'.$formItems['password'].'",pwdMD5="'.$pwdMD5.'",status="'.$formItems['type'].'",rightsLevel="'.$formItems['rightsLevel'].'",textEmail="'.$formItems['TextEmail'].'"';

	if($formItems['shownAs']<>"")
	{
	    $statementBody.= ',shownAs="'.$formItems['shownAs'].'"';
	}
	else
	{
	    $statementBody.= ',shownAs="'.$formItems['LastName'].'"';
	}

	if($formItems['unitID']<>"")
	{
	    $statementBody.= ',unitID="'.$formItems['unitID'].'"';
	}

	if($action=='edit')
	{
		$query = "UPDATE roster SET ";
		$query .= $statementBody;
		$query .= ' WHERE EMTid="'.$EMTid.'";';
	}
	elseif($action=='remove')
	{
	    $query = 'DELETE FROM roster WHERE EMTid="'.$EMTid.'";';
	}
	else//if($action=='new')
	{
	    $query = "INSERT INTO roster SET ";
	    $query .= $statementBody;
	    $query .= ';';
	}
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
	global $action; 
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
	}

	//$defaults['unitID'] = $row['unitID'];
	$defaults['TextEmail'] = $row['textEmail'];

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
	<link rel="stylesheet" href="style.css" type="text/css">
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
