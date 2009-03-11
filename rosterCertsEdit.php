<?php 
// rosterCertsEdit.php
//
// Form to edit certifications data in roster
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
// | $LastChangedRevision::                                             $ |
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/rosterCertsEdi#$ |
// +----------------------------------------------------------------------+


//required for HTML_QuickForm PEAR Extension
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/element.php';

require_once('./config/config.php'); // main configuration

require_once('./config/rosterConfig.php'); // roster configuration

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

// member types

$form->addElement('checkbox', 'CPRchk', 'CPR');
$form->addElement('date', 'CPR', null);
$form->addElement('checkbox', 'EMTchk', 'EMT');
$form->addElement('date', 'EMT', null);
$form->addElement('checkbox', 'FRchk', 'First Responder');
$form->addElement('date', 'FR', null);
$form->addElement('checkbox', 'HazMatchk', 'HazMat Awareness');
$form->addElement('date', 'HazMat', null);
$form->addElement('checkbox', 'BBPchk', 'BBP/RTK');
$form->addElement('date', 'BBP', null);
$form->addElement('checkbox', 'PHTLSchk', 'PHTLS');
$form->addElement('date', 'PHTLS', null);
$form->addElement('checkbox', 'NREMTchk', 'NREMT');
$form->addElement('date', 'NREMT', null);

// the following code will handle the extended certifications
foreach($extdCerts as $val)
{
    // create a checkbox for each extended certification
    $form->addElement('checkbox', $val, $val);
}

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
	$form->freeze(); 
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
-->
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
    global $extdCerts; // global information for extended certifications info
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

	$conn = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
	global $dbName;
	mysql_select_db($dbName) or die ('ERROR: Unable to select database!');
	global $EMTid;

	$max = 1922331600; //2030-12-1

	if($formItems['EMTchk']=="1")
	{
	    $EMT = strtotime($formItems['EMT']['Y'].'-'.$formItems['EMT']['M'].'-'.$formItems['EMT']['d']);
	}
	else
	{
	    $EMT = $max;
	}
	if($formItems['CPRchk']=="1")
	{
	    $CPR = strtotime($formItems['CPR']['Y'].'-'.$formItems['CPR']['M'].'-'.$formItems['CPR']['d']);
	}
	else
	{
	    $CPR = $max;
	}
	if($formItems['FRchk']=="1")
	{
	    $FR = strtotime($formItems['FR']['Y'].'-'.$formItems['FR']['M'].'-'.$formItems['FR']['d']);
	}
	else
	{
	    $FR = $max;
	}	
	if($formItems['HazMatchk']=="1")
	{
	    $HazMat = strtotime($formItems['HazMat']['Y'].'-'.$formItems['HazMat']['M'].'-'.$formItems['HazMat']['d']);
	}
	else
	{
	    $HazMat = $max;
	}
	if($formItems['BBPchk']=="1")
	{
	    $BBP = strtotime($formItems['BBP']['Y'].'-'.$formItems['BBP']['M'].'-'.$formItems['BBP']['d']);
	}
	else
	{
	    $BBP = $max;
	}
	if($formItems['PHTLSchk']=="1")
	{
	    $PHTLS = strtotime($formItems['PHTLS']['Y'].'-'.$formItems['PHTLS']['M'].'-'.$formItems['PHTLS']['d']);
	}
	else
	{
	    $PHTLS = $max;
	}
	if($formItems['NREMTchk']=="1")
	{
	    $NREMT = strtotime($formItems['NREMT']['Y'].'-'.$formItems['NREMT']['M'].'-'.$formItems['NREMT']['d']);
	}
	else
	{
	    $NREMT = $max;
	}

	// handle the extended certs information:
	$otherCerts = ""; // string to hold the info.
	foreach($extdCerts as $val)
	{
	    // loop through the possible certs, check the status of each
	    if($formItems[$val]=="1")
	    {
		// if true, add to the comma-separated list of other certs
		$otherCerts .= $val.",";
	    }
	}
	// we now have a comma-separated list of all other certs.
	$otherCerts = trim($otherCerts, ","); // get rid of the trailing comma


	
	$statementBody = 'EMTid="'.$formItems['EMTid'].'",EMT='.$EMT.',CPR='.$CPR.',FR='.$FR.',HazMat='.$HazMat.',BBP='.$BBP.',PHTLS='.$PHTLS.',NREMT='.$NREMT;

	// if we have any extended certs info, include it in the statement
	if($otherCerts != "")
	{
	    $statementBody .= ',OtherCerts="'.$otherCerts.'"';
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
	global $extdCerts; // extended certs array
	//populate from the DB 
	$defaults = array(); 

	$max = 1922331600; //2030-12-1

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
		// certs
		$defaults['EMT'] = date("Y-M-d", $row['EMT']);
		if($row['EMT'] == 1)
		{
		    $defaults['EMTchk'] = 0;
		}
		else
		{
		    $defaults['EMTchk'] = 1;
		}
		
		$defaults['CPR'] = date("Y-M-d", $row['CPR']);
		if($row['CPR'] == 1)
		{
		    $defaults['CPRchk'] = 0;
		}
		else
		{
		    $defaults['CPRchk'] = 1;
		}

		$defaults['HazMat'] = date("Y-M-d", $row['HazMat']);
		if($row['HazMat'] == 1)
		{
		    $defaults['HazMatchk'] = 0;
		}
		else
		{
		    $defaults['HazMatchk'] = 1;
		}
		$defaults['BBP'] = date("Y-M-d", $row['BBP']);
		if($row['BBP'] == 1)
		{
		    $defaults['BBPchk'] = 0;
		}
		else
		{
		    $defaults['BBPchk'] = 1;
		}
		$defaults['PHTLS'] = date("Y-M-d", $row['PHTLS']);
		if($row['PHTLS'] == 1)
		{
		    $defaults['PHTLSchk'] = 0;
		}
		else
		{
		    $defaults['PHTLSchk'] = 1;
		}
		$defaults['NREMT'] = date("Y-M-d", $row['NREMT']);
		if($row['NREMT'] == 1)
		{
		    $defaults['NREMTchk'] = 0;
		}
		else
		{
		    $defaults['NREMTchk'] = 1;
		}
		
		// get the information for the extended certs from the database
		$otherCerts = $row['OtherCerts']; // other certs CSV list from database
		$otherCertsAry = explode(",", $otherCerts); // make an array of the certs
		foreach($extdCerts as $val)
		{
		    if(in_array($val, $otherCertsAry))
		    {
			// if true, this extd cert (val) is in the member's list (otherCertsA)
			$defaults[$val] = 1;
		    }
		}


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
