<?php 
/*
 * Form to edit the organization's address book.
 * @package php-ems-tools
 */

// addBkEdit.php
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
// | $LastChangedRevision:: 156                                         $ |
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/addBkEdit.php  $ |
// +----------------------------------------------------------------------+

/*
 * required for HTML_QuickForm PEAR Extension
 */
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/element.php';

/*
 * Main configuration file.
 */
require_once('./config/config.php');

/*
 * tell PHP to ignore any errors less than E_ERROR
 */
error_reporting(1);

//if the variables are specified in the URL, get them. 
if(! empty($_GET['action']))
{
    /*
     * @global string $action form action - 'new', 'edit' or 'remove'
     */
    $action = $_GET['action'];
    // possible values are 'new', 'edit', 'remove'
}
if(! empty($_GET['pKey']))
{
    /*
     * @global int $pKey the database primary key for the record
     */
    $pKey = $_GET['pKey'];
} 

/*
 * Instantiate the HTML_QuickForm
 * @global HTML_QuickForm $form
 */
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

/*
 * Process the form input. Automagically called via HTML_QuickForm on submit.
 * @param array $formItems automagically passed by HTML_QuickForm
 * @global int database primary key
 * @global string form action
 */
function processForm($formItems)
{
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

/*
 * Put form information in DB - should only be called by processForm()
 * @see processForm()
 * @param array $formItems as gotten from HTML_QuickForm
 * @param string $action form action
 * @global int database primary key
 * @global string database name
 */
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
	/*
	 * @global string $dbName the database name
	 * @see config/config.php
	 */
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

/*
 * Populate the form from the database.
 * @param int $pKey database record primary key
 * @global string form action
 */
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

/*
 * Validate whether the given credentials have admin rights or not.
 * @global $minRightsRoster minimum rights level to edit roster
 * @param string $adminID the admin EMTid
 * @param string $adminPW the admin password
 * @return boolean
 * @todo this needs to be removed and replaced with a central function
 */
function validateAdmin($adminID, $adminPW)
{
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

/*
 * Call another escaping function to handle quotes
 * @todo replace this with explicit calls to mysql_real_escape_string
 * @param string $s string to escape
 * @return string
 */
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
	<link rel="stylesheet" href="php_ems.css" type="text/css">
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
