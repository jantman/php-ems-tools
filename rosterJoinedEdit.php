<?php 
// rosterJoinedEdit.php
//
// Form to edit/input dates members joined/became active
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
// 	$Id$

//required for HTML_QuickForm PEAR Extension
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/element.php';

require_once('./config/config.php'); // main configuration

// tell PHP to ignore any errors less than E_ERROR
error_reporting(1);

//if the variables are specified in the URL, get them. 
if(! empty($_GET['EMTid']))
{
    $EMTid = $_GET['EMTid'];
}
elseif(! empty($_POST['EMTid']))
{
    $EMTid = $_POST['EMTid'];
}

//instantiate the form 
$form = new HTML_QuickForm('firstForm');

// mysql connection
$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

$query = "SELECT EMTid,FirstName,LastName FROM roster WHERE EMTid='".$EMTid."';";
$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
if(mysql_num_rows($result) < 1)
{
    die("Invalid EMTid.");
}
$row = mysql_fetch_assoc($result);
$membername = $row['LastName'].', '.$row['FirstName'];


//BEGIN CREATING ELEMENTS
//
//

$form->addElement('header', null, 'Date Joined/Active for: '.$membername.' ('.$EMTid.')');
$form->addElement('static',null,' ','Date Format: YYYY-mm-dd');
$form->addElement('text', 'DateJoined', 'Date Joined', array('size' => 10, 'maxlength' => 10));
$form->addElement('text', 'DateActive', 'Date Active', array('size' => 10, 'maxlength' => 10));

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

$def = populateMe($EMTid);
$form->setDefaults($def);  

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

	if(trim($formItems['DateJoined']) != "")
	{
	    $date = strtotime(trim($formItems['DateJoined']));
	    if($date > 0)
	    {
		$query = "UPDATE roster SET dateJoined_ts=".$date." WHERE EMTid='".$EMTid."';";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	    }	    
	}
	if(trim($formItems['DateActive']) != "")
	{
	    $date = strtotime(trim($formItems['DateActive']));
	    if($date > 0)
	    {
		$query = "UPDATE roster SET dateActive_ts=".$date." WHERE EMTid='".$EMTid."';";
		$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	    }	    
	}
}

function populateMe($EMTid) 
{
	global $action; 
	//populate from the DB 
	$defaults = array(); 
	$query  = "SELECT dateJoined_ts,dateActive_ts FROM roster WHERE EMTid='".$EMTid."';";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	if(mysql_num_rows($result) < 1){ die("No member with specified EMTid found.");}
	$row = mysql_fetch_array($result);
	if($row['dateJoined_ts'] != NULL && $row['dateJoined_ts'] > 0)
	{
	    $defaults['DateJoined'] = date("Y-m-d", $row['dateJoined_ts']);
	}
	if($row['dateActive_ts'] != NULL && $row['dateActive_ts'] > 0)
	{
	    $defaults['DateActive'] = date("Y-m-d", $row['dateActive_ts']);
	}
	mysql_free_result($result); 
	return $defaults;
}

mysql_close($connection);

?>
 
<HTML> 
<HEAD> 
<?php
echo '<TITLE>'.$shortName.' Edit Roster Date Joined - '.$EMTid.'</TITLE>';
?>
	<link rel="stylesheet" href="php_ems.css" type="text/css">
</HEAD>
<BODY>
<?php
	echo '<h2 align=center>'.$shortName.' - Roster - Edit Date Joined/Active</h2>';
?>
<?php
	// display the form
	$form->display();
?>
</BODY>
</HTML>
