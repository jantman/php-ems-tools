<?php 
// committeeEdit.php
//
// Form to edit/input committee information
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
// | $LastChangedRevision:: 155                                         $ |
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/committeeEdit.#$ |
// +----------------------------------------------------------------------+

//required for HTML_QuickForm PEAR Extension
require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/element.php';

require_once('./config/config.php'); // main configuration

// tell PHP to ignore any errors less than E_ERROR
error_reporting(1);

//if the variables are specified in the URL, get them. 
if(! empty($_GET['id']))
{
	$id = $_GET['id'];
}
elseif(! empty($_POST['id']))
{
    $id = $_POST['id'];
}

//instantiate the form 
$form = new HTML_QuickForm('firstForm');

// mysql connection
$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

$query = "SELECT comm_name FROM committees WHERE comm_id=".$id.";";
$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
if(mysql_num_rows($result) < 1)
{
    die("Invalid committee ID.");
}
$row = mysql_fetch_assoc($result);
$comm_name = $row['comm_name'];

//
// Array of member options
//
$query = "SELECT EMTid,LastName,FirstName FROM roster WHERE status='Senior' OR status='Driver' ORDER BY LastName,FirstName;";
$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
$memberOptions = array();
$memberOptions[0] = "";
while($row = mysql_fetch_assoc($result))
{
    $memberOptions[$row['EMTid']] = $row['LastName'].', '.$row['FirstName'].' ('.$row['EMTid'].')';
}

//BEGIN CREATING ELEMENTS
//
//

$form->addElement('header', null, 'Committee: '.$comm_name);

$chair =& $form->createElement('select', 'chairman', 'Chairman:'); 
$chair -> loadArray($memberOptions);
$form->addElement($chair);

$member1 =& $form->createElement('select', 'member1', 'Member:'); 
$member1 -> loadArray($memberOptions);
$form->addElement($member1);

$member2 =& $form->createElement('select', 'member2', 'Member:'); 
$member2 -> loadArray($memberOptions);
$form->addElement($member2);

$member3 =& $form->createElement('select', 'member3', 'Member:'); 
$member3 -> loadArray($memberOptions);
$form->addElement($member3);

$member4 =& $form->createElement('select', 'member4', 'Member:'); 
$member4 -> loadArray($memberOptions);
$form->addElement($member4);

//HIDDEN FIELDS to keep values between refreshing the form	
if(! empty($_GET['action']))
{ 
	$form->addElement('hidden','action',$_GET['action']);
}
if(! empty($_GET['id']))
{
	$form->addElement('hidden','id',$_GET['id']);
}

if($_GET['action']=='edit' || $_GET['action']=='remove' || $_GET['action']=='new')
{	
	$buttonGroup[] =& HTML_QuickForm::createElement('reset', 'btnReset', 'Reset');
	$buttonGroup[] =& HTML_QuickForm::createElement('submit', 'btnSubmit', 'Submit');
	$form->addGroup($buttonGroup, 'buttonGroup', null, "    ");
}
//FINISHED CREATING ELEMENTS

$def = populateMe($id);
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
	global $id;
	global $action;

	if(empty($action)) 
	{
		$action = $formItems['action'];	
	}
	$members = array($formItems['member1'], $formItems['member2'], $formItems['member3'], $formItems['member4']);
	
	updateCommMembers($formItems['id'], $formItems['chairman'], $members);
}

function updateCommMembers($comm_id, $chairman, $members)
{
    // updates committee membership from form

    // first the chairman
    $query = "SELECT mc.*,cp.comm_pos_name FROM members_committees AS mc LEFT JOIN committee_positions AS cp ON mc.pos_id=cp.comm_pos_id WHERE comm_pos_name='Chairman' AND comm_id=".$comm_id."  AND removed_ts IS NULL;";
    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
    if(mysql_num_rows($result) < 1)
    {
	// we don't yet have a chairman, add one
	addCommMember($comm_id, $chairman, "Chairman");
    }
    else
    {
	$row = mysql_fetch_assoc($result);
	if($row['EMTid'] != $chairman)
	{
	    // we have a new chairman
	    removeCommMember($row['memb_comm_id']);
	    addCommMember($comm_id, $chairman, "Chairman");
	}
    }

    // now the members
    foreach($members as $EMTid)
    {
	if($EMTid == 0){ continue;}
	// add any EMTid's not currently associated with the committee
	$query = "SELECT mc.*,cp.comm_pos_name FROM members_committees AS mc LEFT JOIN committee_positions AS cp ON mc.pos_id=cp.comm_pos_id WHERE comm_pos_name='Member' AND EMTid='".$EMTid."' AND comm_id=".$comm_id."  AND removed_ts IS NULL;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	if(mysql_num_rows($result) < 1)
	{
	    // we don't yet have a chairman, add one
	    addCommMember($comm_id, $EMTid, "Member");
	}
    }

    // remove any EMTids associated with the committee but not in our list
    $query = "SELECT mc.*,cp.comm_pos_name FROM members_committees AS mc LEFT JOIN committee_positions AS cp ON mc.pos_id=cp.comm_pos_id WHERE comm_pos_name='Member' AND comm_id=".$comm_id."  AND removed_ts IS NULL;";
    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
    while($row = mysql_fetch_assoc($result))
    {
	if(! in_array($row['EMTid'], $members))
	{
	    removeCommMember($row['memb_comm_id']);
	}
    }

}

function removeCommMember($memb_comm_id)
{
    $query = "UPDATE members_committees SET removed_ts=".time()." WHERE memb_comm_id=".$memb_comm_id.";";
    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
}

function addCommMember($comm_id, $EMTid, $positionName)
{
    // adds a new committee member
    $query = "SELECT comm_pos_id FROM committee_positions WHERE comm_pos_name='".$positionName."';";
    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
    $row = mysql_fetch_assoc($result);
    $pos_id = $row['comm_pos_id'];
    
    $query = "INSERT INTO members_committees SET EMTid='".$EMTid."',comm_id=".$comm_id.",pos_id=".$pos_id.",appointed_ts=".time().";";
    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
}

function populateMe($id) 
{
	global $action; 
	//populate from the DB 
	$defaults = array(); 
	$query  = "SELECT mc.*,p.comm_pos_name FROM members_committees AS mc LEFT JOIN committee_positions AS p ON p.comm_pos_id=mc.pos_id WHERE comm_id=".$id." AND removed_ts IS NULL;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());

	$nextMember = 1;
	while ($row = mysql_fetch_array($result))
	{
	    if($row['comm_pos_name'] == "Chairman")
	    {
		$defaults['chairman'] = $row['EMTid'];
	    }
	    elseif($row['pos_id'] == "Co-Chairman")
	    {
		// TODO: implement this
	    }
	    else
	    {
		// member
		$defaults['member'.$nextMember] = $row['EMTid'];
		$nextMember++;
	    }
	}

	mysql_free_result($result); 
	return $defaults; 
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
echo '<TITLE>'.$shortName.' Committees</TITLE>';
?>
	<link rel="stylesheet" href="php_ems.css" type="text/css">
</HEAD>
<BODY>
<?php
	echo '<h2 align=center>'.$shortName.' Committees</h2>';
?>
<?php
	// display the form
	$form->display();
?>
</BODY>
</HTML>
