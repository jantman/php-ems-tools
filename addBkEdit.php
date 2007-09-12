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
if(! empty($_GET['pKey']))
{
	$pKey = $_GET['pKey'];
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
$form->addElement('header', null, 'Contact Information');
$form->addElement('text', 'company', 'Company', array('size' => 40, 'maxlength' => 254)); 
$form->addElement('text', 'description', 'Description', array('size' => 40, 'maxlength' => 254));

$form->addElement('text', 'contact', 'Contact', array('size' => 40, 'maxlength' => 254));
$form->addElement('text', 'address', 'Address', array('size' => 40, 'maxlength' => 254));

$form->registerRule('phone','regex','/^\d{3}-\d{3}-\d{4}$/');
$form->addElement('text', 'phone1', 'Phone1', array('size' => 15, 'maxlength' => 14));
$form->addRule('phone1','Please enter a valid Phone number.','phone');
$form->addElement('text', 'phone2', 'Phone2', array('size' => 15, 'maxlength' => 14));
$form->addRule('phone2','Please enter a valid Phone number.','phone');
$form->addElement('text', 'fax', 'Fax', array('size' => 15, 'maxlength' => 14));
$form->addRule('fax','Please enter a valid Phone number.','phone');
$form->addElement('static',null,' ','Format: 555-555-5555');
$form->addElement('text', 'email', 'Email', array('size' => 30, 'maxlength' => 255, 'id' => 'email'));
$form->addRule('emailorblank', 'This must be a valid EMail Address or it must be blank.', 'Email');
$form->addElement('text', 'web', 'Web Site', array('size' => 30, 'maxlength' => 255, 'id' => 'web'));
$form->addElement('textarea', 'notes', 'Notes');


//HIDDEN FIELDS to keep values between refreshing the form	
if(! empty($_GET['action']))
{ 
	$form->addElement('hidden','action',$_GET['action']);
}
if(! empty($_GET['pKey']))
{
	$form->addElement('hidden','pKey',$_GET['pKey']);
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
	$def = populateMe($pKey);
	$form->setDefaults($def);  
	$form->freeze(); 
	$tempE = HTML_QuickForm::createElement('header', null, 'Remove Entry '.$pKey);
	$form->insertElementBefore($tempE, 'adminID');
}
elseif ($_GET[action]=='edit') 
{
	$tempE = HTML_QuickForm::createElement('header', null, 'Edit Entry '.$pKey);
	$form->insertElementBefore($tempE, 'adminID');
	$def = populateMe($pKey);
	$form->setDefaults($def);
}
else 
{
	// NEW entry
	$tempE = HTML_QuickForm::createElement('header', null, 'New Entry');
	$form->insertElementBefore($tempE, 'adminID');
	$defaults = array(); 
	
	$form->setDefaults($defaults);
}

if(! $_GET['action'])
{
    $form->freeze();
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
	global $pKey;
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
	global $pKey;

	$pKey = $formItems['pKey'];

	if(! validateAdmin($formItems['adminID'], $formItems['adminPW']))
	{
	    die("ERROR AUTH1: I'm sorry, the Administrator ID and Password that you provided is invalid, or you do not have the proper rights level to perform the requested action.");
	}

	if(($action=='edit')||($action=='remove'))
	{
		$pKey = $formItems['pKey']; 
	}

	$conn = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
	global $dbName;
	mysql_select_db($dbName) or die ('ERROR: Unable to select database!');
	// global $pKey;

	$statementBody = 'company="'.$formItems['company'].'",description="'.$formItems['description'].'",contact="'.$formItems['contact'].'",address="'.$formItems['address'].'",phone1="'.$formItems['phone1'].'",phone2="'.$formItems['phone2'].'",fax="'.$formItems['fax'].'",email="'.$formItems['email'].'",web="'.$formItems['web'].'",notes="'.$formItems['notes'].'"';

	if($action=='edit')
	{
		$query = "UPDATE addBk SET ";
		$query .= $statementBody;
		$query .= ' WHERE pKey="'.$pKey.'";';
	}
	elseif($action=='remove')
	{
	    $query = 'DELETE FROM addBk WHERE pKey="'.$pKey.'";';
	}
	else//if($action=='new')
	{
	    $query = "INSERT INTO addBk SET ";
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

function populateMe($pKey) 
{
	global $action; 
	//populate from the DB 
	$defaults = array(); 
	$query  = "SELECT * FROM addBk WHERE pKey='".$pKey."';";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	if(mysql_num_rows($result)==0)
	{
		die("I'm sorry, but the pKey that you entered (".$pKey.") is not in the database. Please try again.");
	}
	while ($row = mysql_fetch_array($result))  
	{
		$defaults = $row;
		$defaults['pKey'] = $pKey;
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


function doQuotes($s) 
{
    return addslashes($s);
}

mysql_close($connection);

?>
 
<HTML> 
<HEAD> 
<?php
echo '<TITLE>'.$shortName.' Address Book - Administrative Tool</TITLE>';
?>
	<link rel="stylesheet" href="style.css" type="text/css">
</HEAD>
<BODY>
<?php
	echo '<h2 align=center>'.$shortName.' Address Book Administrative Tool</h2>';
?>
<?php
	// display the form
	$form->display();
?>
</BODY>
</HTML>
