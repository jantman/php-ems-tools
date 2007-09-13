<?php 
// PHP EMS Tools
// Edit Daily Message Form
// Time-stamp: "2007-09-13 16:22:49 jantman"
//

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
require('./config/config.php');

//instantiate the form
$form = new HTML_QuickForm('firstForm');


// get the URL variables
if(! empty($_GET['year']))
{
    $year = $_GET['year'];
    $form->addElement('hidden','year',$year);
}
if(! empty($_GET['month']))
{
    $month = $_GET['month'];
    $form->addElement('hidden','month',$month);
}
if(! empty($_GET['date']))
{
    $date = $_GET['date'];
    $form->addElement('hidden','date',$date);
}
if(! empty($_GET['shift']))
{
    $shift = $_GET['shift'];
    $form->addElement('hidden','shift',$shift);
}


// define an error message
$errorMsg = "<br>Please try again. If you recieve this message more than once while trying to perform the same action, please notify the administrator of this system.";

//start working with the form
$form->addElement('header', null, 'PHP-EMS-Tools: Edit Daily Message (Admin Only)');
$defaults = array();
addFormElements(); 
$buttonGroup[] =& HTML_QuickForm::createElement('reset', 'btnReset', 'Reset');
$buttonGroup[] =& HTML_QuickForm::createElement('submit', 'btnSubmit', 'Submit');
$form->addGroup($buttonGroup, 'buttonGroup', null, "    ");
if(! empty($_GET['shift']))
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
	window.opener.location=window.opener.location;
	window.close();
//-->
</SCRIPT>

<?php

}
function processForm($formItems)
{
    global $action;
    global $requireAuthDailyMessage;
    global $minRightsDailyMessage;
    global $dbName;

    $action = $formItems['action'];
    $year = addslashes($formItems['year']);
    $month = addslashes($formItems['month']);
    $date = addslashes($formItems['date']);
    $shift = addslashes($formItems['shift']);
    $message = addslashes($formItems['message']);
    $adminID = addslashes($formItems['adminID']);
    $adminPW = md5($formItems['adminPW']);

    // start MySQL connection
    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.".$errorMsg);
    mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!".$errorMsg);

    //AUTHENTICATION
    if($requireAuthDailyMessage)
    {
	$query = 'SELECT pwdMD5,rightsLevel FROM roster WHERE EMTid="'.$adminID.'";';
	$result = mysql_query($query) or die ("Auth Query Error");
	$row = mysql_fetch_array($result);
	$rightsLevel = $row['rightsLevel'];
	if(($adminPW <> $row['pwdMD5']) || ($rightsLevel < $minRightsDailyMessage))
	{
	    die("Invalid Admin Username or Password. This action requires authentication.");
	}
    }

    if($formItems['action']=='remove')
    {
	//remove 
	$query = 'UPDATE schedule_'.$year.'_'.$month.'_'.$shift.' SET message=null WHERE date='.$date.';';
    }
    else
    {
	$query = 'UPDATE schedule_'.$year.'_'.$month.'_'.$shift.' SET message="'.$message.'" WHERE date='.$date.';';
    }
    $result = mysql_query($query) or die ("Query Error");
}
  
function populateMe() 
{
	global $form; 
	global $defaults;
	global $date;
	global $year;
	global $month;
	global $shift;
	global $message;
	global $dbName;

    $conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed at mysql_connect.".$errorMsg);
    mysql_select_db($dbName) or die ("ERROR: I'm sorry, I was unable to select the database!".$errorMsg);
    $query = 'SELECT message FROM schedule_'.$year.'_'.$month.'_'.$shift.' WHERE date='.$date.';';
    $result = mysql_query($query);
    $row = mysql_fetch_array($result) or die("Error fetching result for defaults.");
    $defaults['action'] = "edit";
    $defaults['message'] = $row['message'];

}
function addFormElements() 
{
	global $form; 
	global $year;
	global $month;
	global $date;
	global $shift;
	global $defaults;
	
	// create elements
	$form->addElement('text', 'adminID', 'Administrator ID#', array('size' => 10, 'maxlength' => 5));
	$form->addElement('password', 'adminPW', 'Password', array('size' => 10, 'maxlength' => 10));
	$form->addElement('header', null, "PLEASE remember to keep this small!!");

	$form->addElement('radio','action',null,'Edit','edit');
	$form->addElement('radio','action',null,'Remove','remove');

	$form->addElement('text', 'message', 'Daily Message', array('size' => 30, 'maxlength' => 50)); 

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