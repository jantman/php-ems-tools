<?php
//
// roster.php
//
// Version 0.1 as of Time-stamp: "2007-09-13 16:18:03 jantman"
//
// This file is part of the php-ems-tools package
// available at 
//
// (C) 2006 Jason Antman.
// This package is licensed under the terms of the
// GNU General Public License (GPL)
//

require_once('./config/config.php');

// this script views the roster from the DB

if(! empty($_GET['sort']))
{
    $sort = $_GET['sort'];
}
else
{
    $sort = "EMTid";
}

if(! empty($_GET['adminView']))
{
    $adminView = $_GET['adminView'];
}
else
{
    $adminView = 0;
}

// shortView determines whether we show type/ID/Names only, or everything
if((! empty($_GET['shortView'])) && $_GET['shortView'] == 1)
{
    $shortView = true;
}
else
{
    $shortView = false;
}

echo '<head>';
echo '<meta http-equiv="refresh" content="180">';
echo '<link rel="stylesheet" href="'.$serverWebRoot.'php_ems.css" type="text/css">'; // the location of the CSS file for the schedule
echo '<title>'.$shortName.' - View Certifications Roster</title>';
echo '<script type="text/javascript" src="php-ems-tools.js"> </script>';
echo '</head>';
echo '<body>';
echo '<table class="roster">';
// END OF PAGE TOP HTML

//Finish setting up the table
if($adminView==1)
{ $colspan = 15;}
else
{ $colspan = 14;}
echo "\n"; // linefeed
echo '<td align=center colspan="'.$colspan.'"><b>'.$orgName.' Certifications Roster</b><br> (as of '.date("M d Y").')';
echo '<a href="javascript:helpPopUp('."'docs/roster_help.php'".')">HELP</a>';
if(!$shortView)
{
    echo '&nbsp;&nbsp;<a href="rosterCerts.php?sort='.$sort.'&shortView=1">Short View</a>';
}
else
{
    echo '&nbsp;&nbsp;<a href="rosterCerts.php?sort='.$sort.'&shortView=0">Normal View</a>';
}
if($adminView==1)
{
    echo '<br><a href="javascript:rosterPopUp('."'rosterCertsEdit.php?action=new'".')">Add New Member</a>';
    echo '&nbsp; &nbsp; &nbsp; <a href="rosterCerts.php?adminView=0&sort='.$sort.'">Standard View</a>';
}
echo '</td>';
echo "\n"; // linefeed

//CONNECT TO THE DB
$connection = mysql_connect() or die ("I'm sorry, but I was unable to connect! (MySQL error: unable to connect).".$errorMsg);
//SELECT pcr
mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!".$errorMsg);
//QUERY
if($sort=="EMTid")
{
    $query =  "SELECT * FROM roster ORDER BY lpad(EMTid,10,'0');";
}
else
{
    $query  = "SELECT * FROM roster ORDER BY ".$sort.";";
}
$result = mysql_query($query) or die ("I'm sorry, but there was an error in your SQL query: ".$query."<br><br>" . mysql_error().'<br><br>'.$errorMsg);
mysql_close($connection); 

//setup the table
    echo '<tr>';
    echo '<td>&nbsp;</td>'; // memberType
    echo '<td><a href="rosterCerts.php?sort=EMTid';
    if($adminView==1){ echo '&adminView=1';}
    echo '">ID</a></td>';
    if($adminView==1)
    {
	echo '<td>Edit</td>';
    }
    echo '<td><a href="rosterCerts.php?sort=LastName';
    if($adminView==1){ echo '&adminView=1';}
    echo '">Last Name</a></td>';
    echo '<td><a href="rosterCerts.php?sort=FirstName';
    if($adminView==1){ echo '&adminView=1';}
    echo '">First Name</a></td>';
    echo '<td><a href="rosterCerts.php?sort=CPR';
    if($adminView==1){ echo '&adminView=1';}
    echo '">CPR</a></td>';
    echo '<td><a href="rosterCerts.php?sort=EMT';
    if($adminView==1){ echo '&adminView=1';}
    echo '">EMT</a></td>';
    echo '<td><a href="rosterCerts.php?sort=FR';
    if($adminView==1){ echo '&adminView=1';}
    echo '">1st Resp.</a></td>';
    echo '<td><a href="rosterCerts.php?sort=HazMat';
    if($adminView==1){ echo '&adminView=1';}
    echo '">HazMat</a></td>';
    echo '<td><a href="rosterCerts.php?sort=BBP';
    if($adminView==1){ echo '&adminView=1';}
    echo '">BBP</a></td>';
    echo '<td><a href="rosterCerts.php?sort=ICS100';
    if($adminView==1){ echo '&adminView=1';}
    echo '">ICS100</a></td>';
    echo '<td><a href="rosterCerts.php?sort=ICS200';
    if($adminView==1){ echo '&adminView=1';}
    echo '">ICS200</a></td>';
    echo '<td><a href="rosterCerts.php?sort=NIMS';
    if($adminView==1){ echo '&adminView=1';}
    echo '">NIMS</a></td>';
    echo '<td><a href="rosterCerts.php?sort=PHTLS';
    if($adminView==1){ echo '&adminView=1';}
    echo '">PHTLS</a></td>';
    echo '<td><a href="rosterCerts.php?sort=NREMT';
    if($adminView==1){ echo '&adminView=1';}
    echo '">NREMT</a></td>';
    echo '</tr>';

//loop through the members and call the showMember function
while ($row = mysql_fetch_array($result))  
{
    // figure out the member type
    $memberType = "";
    global $memberTypes;
    global $adminView;
    for($i = 0; $i < count($memberTypes); $i++)
    {
	if($memberTypes[$i]['name'] == $row['status'])
	{
	    $memberType = $memberTypes[$i]['name'];
	}
    }

    // figure out whether we show this member or not
    for($i = 0; $i < count($memberTypes); $i++)
    {
	if($memberTypes[$i]['name'] == $memberType)
	{
	    //this is the right type
	    $shownInShort = $memberTypes[$i]['shownInShort'];
	    $shownInRoster = $memberTypes[$i]['shownInRoster'];
	}
    }
    showMember($row);
}
mysql_free_result($result); 

//this function will display a row for a member
function showMember($r)
{
    global $adminView;
    
    // figure out the member type
    $memberType = "";
    global $memberTypes;
    for($i = 0; $i < count($memberTypes); $i++)
    {
	if($memberTypes[$i]['name'] == $r['status'])
	{
	    $memberType = $memberTypes[$i]['rosterName'];
	}
    }

    echo '<tr>';
    //get the roster view of the status/memberType
    
    if($memberType=="")
    {
	$memberType = "&nbsp;";
    }
    echo '<td>'.$memberType.'</td>';

    if($adminView<>1 && $r['unitID']<>"")
    {
	echo '<td>'.$r['unitID'].'</td>';
    }
    else
    {
	echo '<td>'.$r['EMTid'].'</td>';
    }
    if($adminView==1)
    {
	echo '<td><a href="javascript:rosterPopUp('."'rosterCertsEdit.php?EMTid=".$r['EMTid']."&action=edit'".')">EDIT</a></td>';
    }
    if(! empty($r['LastName']))
    {
	echo '<td>'.$r['LastName'].'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['FirstName']))
    {
	echo '<td>'.$r['FirstName'].'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['CPR']))
    {
	echo '<td>'.certDate($r['CPR']).'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['EMT']))
    {
	echo '<td>'.certDate($r['EMT']).'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['FR']))
    {
	echo '<td>'.certDate($r['FR']).'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['HazMat']))
    {
	echo '<td>'.certDate($r['HazMat']).'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['BBP']))
    {
	echo '<td>'.certDate($r['BBP']).'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['ICS100']))
    {
	if($r['ICS100'] == 1922331600)
	{
	    echo '<td>VALID</td>';
	}
	elseif($r['ICS100'] == 1)
	{
	    echo '<td>no cert</td>';
	}
	else
	{
	    echo '<td>&nbsp;</td>';
	}
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['ICS200']))
    {
	if($r['ICS200'] == 1922331600)
	{
	    echo '<td>VALID</td>';
	}
	elseif($r['ICS200'] == 1)
	{
	    echo '<td>no cert</td>';
	}
	else
	{
	    echo '<td>&nbsp;</td>';
	}
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['NIMS']))
    {
	if($r['NIMS'] == 1922331600)
	{
	    echo '<td>VALID</td>';
	}
	elseif($r['NIMS'] == 1)
	{
	    echo '<td>no cert</td>';
	}
	else
	{
	    echo '<td>&nbsp;</td>';
	}
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['PHTLS']))
    {
	echo '<td>'.certDate($r['PHTLS']).'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['NREMT']))
    {
	echo '<td>'.certDate($r['NREMT']).'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }

    echo '</tr>';
}

//this function will display a row for a member
function showMemberShort($r)
{
    global $adminView;

    // figure out the member type
    $memberType = "";
    global $memberTypes;
    for($i = 0; $i < count($memberTypes); $i++)
    {
	if($memberTypes[$i]['name'] == $r['status'])
	{
	    $memberType = $memberTypes[$i]['rosterName'];
	}
    }

    echo '<tr>';
    //get the roster view of the status/memberType
    
    if($memberType=="")
    {
	$memberType = "&nbsp;";
    }
    echo '<td>'.$memberType.'</td>';

    if($adminView<>1 && $r['unitID']<>"")
    {
	echo '<td>'.$r['unitID'].'</td>';
    }
    else
    {
	echo '<td>'.$r['EMTid'].'</td>';
    }

    if(! empty($r['LastName']))
    {
	echo '<td>'.$r['LastName'].'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    if(! empty($r['FirstName']))
    {
	echo '<td>'.$r['FirstName'].'</td>';
    }
    else
    {
	echo '<td>&nbsp;</td>';
    }
    echo '</tr>';
}

function certDate($i)
{
    if($i == 1922331600)
    {
	return "&nbsp;";
    }
    else
    {
	if($i - time() < 1296000)
	{
	    // less than 15 days
	    return '<font color="red"><u>'.date("Y-m-d", $i).'</u></font>';
	}

	if($i - time() < 15552000)
	{
	    // less than 6 months
	    return '<font color="orange">'.date("Y-m-d", $i).'</font>';
	}
	return date("Y-m-d", $i);
    }
}

?>  
</table>
</body>
</html>