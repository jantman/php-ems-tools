<?php
//(C) 2006 Jason Antman. All Rights Reserved.
// with questions, go to www.jasonantman.com
// or email jason AT jasonantman DOT com
// Time-stamp: "2010-03-14 17:42:09 jantman"

//This software may not be copied, altered, or distributed in any way, shape, form, or means.
// version: 2.0 as of 2006-10-3

require('../custom.php');
$conn = mysql_connect() or die("ERROR: Unable to connect to MySQL.");
mysql_select_db($dbName) or die("ERROR: Unable to select database.");

if(isset($_GET['rig']))
{
    $rig = trim($_GET['rig']);
}
else
{
    echo '<p>You must select a rig to edit:</p>';
    echo '<ul>';
    $query = "SELECT * FROM rigcheck_rigs;";
    $result = mysql_query($query) or die("Error in query: $query\nERROR: ".mysql_error());
    while($row = mysql_fetch_assoc($result))
    {
	echo '<li><a href="editRigCheck.php?rig='.$row['rig_id'].'">'.$row['rig_id'].'</a></li>';
    }

    echo '</ul>';
    die();
}

if(isset($_GET['action']))
{
    handleAction();
}

?>
<html>
<head>
<?php
global $shortName;
echo '<title>'.$shortName.' - Edit Rig Check</title>';
echo '<link rel="stylesheet" href="'.'../php_ems.css" type="text/css">'; // the location of the CSS file for the schedule
?>
</head>
<body>
<?php
echo "<h2>Edit Rig Check - $rig</h2>\n";

echo '<table border=1 align=center class="rigCheck"><tr>'."\n";
for($col = 1; $col < 4; $col++)
{
    echo '<td>';
    $query1 = "SELECT * FROM rigcheck_sections WHERE rig_id='$rig' AND is_deprecated=0 AND col_num=$col ORDER BY col_order;";
    $result1 = mysql_query($query1) or die("Error in query: $query1\nERROR: ".mysql_error());
    while($row1 = mysql_fetch_assoc($result1))
    {
	rca_show_section($row1);
	$query2 = "SELECT * FROM rigcheck_items WHERE rig_id='$rig' AND is_deprecated=0 AND sec_id=".$row1['sec_id']." ORDER BY item_order;";
	$result2 = mysql_query($query2) or die("Error in query: $query1\nERROR: ".mysql_error());
	while($row2 = mysql_fetch_assoc($result2))
	{
	    rca_show_item($row2);
	}
    }

    echo '</td>'."\n"; 
}
echo '</tr></table>';
?>
</html>

<?php

function rca_show_section($row1)
{
    global $rig;
    echo '<strong>'.$row1['title'].'</strong>';
    echo '&nbsp;&nbsp;&nbsp;';
    if($row1['col_num'] > 1)
    {
	echo '<a href="editRigCheck.php?rig='.$rig.'&action=moveSecL&secid='.$row1['sec_id'].'">&larr</a>';
    }
    echo '<a href="editRigCheck.php?rig='.$rig.'&action=moveSecU&secid='.$row1['sec_id'].'">&uarr</a>';
    echo '<a href="editRigCheck.php?rig='.$rig.'&action=moveSecD&secid='.$row1['sec_id'].'">&darr</a>';
    if($row1['col_num'] < 3)
    {
	echo '<a href="editRigCheck.php?rig='.$rig.'&action=moveSecR&secid='.$row1['sec_id'].'">&rarr</a>';
    }
    echo '<br />';
}

function rca_show_item($row2)
{
    echo '&nbsp;&nbsp;&nbsp;'.$row2['title'].'<br />';
}

function handleAction()
{
    $action = $_GET['action'];
    $rig = trim($_GET['rig']);
    if($action == "moveSecR")
    {
	$sec = (int)$_GET['secid'];
	$query = "UPDATE rigcheck_sections SET col_num=(col_num + 1) WHERE sec_id=$sec;";
	$result = mysql_query($query) or die("Error in query: $query\nERROR: ".mysql_error());
    }
    elseif($action == "moveSecL")
    {
	$sec = (int)$_GET['secid'];
	$query = "UPDATE rigcheck_sections SET col_num=(col_num - 1) WHERE sec_id=$sec;";
	$result = mysql_query($query) or die("Error in query: $query\nERROR: ".mysql_error());
    }
    elseif($action == "moveSecD")
    {
	$sec = (int)$_GET['secid'];
	$query = "SELECT * FROM rigcheck_sections WHERE sec_id=$sec;";
	$result = mysql_query($query) or die("Error in query: $query\nERROR: ".mysql_error());
	$row = mysql_fetch_assoc($result);
	$old_order = $row['col_order'];
	$query = "SELECT * FROM rigcheck_sections WHERE rig_id='".$row['rig_id']."' AND col_num=".$row['col_num']." AND is_deprecated=0 AND col_order > ".$row['col_order']." ORDER BY col_order ASC LIMIT 1;";
	$result = mysql_query($query) or die("Error in query: $query\nERROR: ".mysql_error());
	if(mysql_num_rows($result) < 1)
	{
	    // no next row
	    header("Location: editRigCheck.php?rig=".$rig);
	    die();
	}
	// else have a next row
	$row = mysql_fetch_assoc($result);
	$new_order = $row['col_order'];
	$new_id = $row['sec_id'];
	echo "sec=$sec old_order=$old_order new_id=$new_id new_order=$new_order";
	$query = "UPDATE rigcheck_sections SET col_order=$old_order WHERE sec_id=$new_id;";
	$result = mysql_query($query) or die("Error in query: $query\nERROR: ".mysql_error());
	$query = "UPDATE rigcheck_sections SET col_order=$new_order WHERE sec_id=$sec;";
	$result = mysql_query($query) or die("Error in query: $query\nERROR: ".mysql_error());
    }
    elseif($action == "moveSecU")
    {
	$sec = (int)$_GET['secid'];
	$query = "SELECT * FROM rigcheck_sections WHERE sec_id=$sec;";
	$result = mysql_query($query) or die("Error in query: $query\nERROR: ".mysql_error());
	$row = mysql_fetch_assoc($result);
	$old_order = $row['col_order'];
	$query = "SELECT * FROM rigcheck_sections WHERE rig_id='".$row['rig_id']."' AND col_num=".$row['col_num']." AND is_deprecated=0 AND col_order < ".$row['col_order']." ORDER BY col_order DESC LIMIT 1;";
	$result = mysql_query($query) or die("Error in query: $query\nERROR: ".mysql_error());
	if(mysql_num_rows($result) < 1)
	{
	    // no next row
	    header("Location: editRigCheck.php?rig=".$rig);
	    die();
	}
	// else have a next row
	$row = mysql_fetch_assoc($result);
	$new_order = $row['col_order'];
	$new_id = $row['sec_id'];
	echo "sec=$sec old_order=$old_order new_id=$new_id new_order=$new_order";
	$query = "UPDATE rigcheck_sections SET col_order=$old_order WHERE sec_id=$new_id;";
	$result = mysql_query($query) or die("Error in query: $query\nERROR: ".mysql_error());
	$query = "UPDATE rigcheck_sections SET col_order=$new_order WHERE sec_id=$sec;";
	$result = mysql_query($query) or die("Error in query: $query\nERROR: ".mysql_error());
    }

    header("Location: editRigCheck.php?rig=".$rig);
}

?>