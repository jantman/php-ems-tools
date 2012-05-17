<?php
require_once('../../custom.php');
require_once('../../inc/web_session.php.inc');
require_once('inc/common.php');

$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

require_once('config/smarty-config.php');

// BEGIN HEADER TEMPLATE
$smarty->assign('bodyID', 'tab11');
$smarty->assign('title', 'Admin');
$smarty->display('head.tpl');
$smarty->clear_all_assign(); // clear all variables
$smarty->assign('config', $SMARTY_config); // reassign config
// END HEADER TEMPLATE

$smarty->assign('title', $shortName.' - Equipment Signout - Admin');

// 
// BEGIN CONTENT
//

$msg = process_inputs();
if($msg != null)
{
    echo '<div>';
    echo $msg;
    echo '</div>';
}

$query = "SELECT * FROM eso_opt_equipTypes;";
smarty_add_row($query, 'types');

$query = "SELECT et.et_name AS type,em.em_id,em.em_name AS name FROM eso_opt_mfr AS em LEFT JOIN eso_opt_equipTypes AS et ON em.et_id=et.et_id;";
smarty_add_row($query, 'mfrs');

$query = "SELECT emod.emod_name AS name,emod.emod_model_num AS modelnum, em.em_name AS mfr, et.et_name AS type FROM eso_opt_model AS emod LEFT JOIN eso_opt_mfr AS em ON emod.emod_mfr_id=em.em_id LEFT JOIN eso_opt_equipTypes AS et ON emod.emod_et_id=et.et_id;";
smarty_add_row($query, 'models');

//
// END CONTENT
//

$smarty->display('admin.tpl');

$smarty->clear_all_assign(); // clear all variables
$smarty->assign('config', $SMARTY_config); // reassign config


// BEGIN FOOTER TEMPLATE
$smarty->display('footer.tpl');
// END FOOTER TEMPLATE

function process_inputs()
{
    if(! isset($_POST['action'])){ return null;}
    if($_POST['action'] == "addType")
    {
	$query = "INSERT INTO eso_opt_equipTypes SET et_name='".mysql_real_escape_string(trim($_POST['name']))."';";
	$result = mysql_query($query);
	if(! $result){ return "<strong>Error adding equipment type.</strong><br />Error in query: $query<br />ERROR: ".mysql_error();}
	return null;
    }
    elseif($_POST['action'] == "addMfr")
    {
	$query = "INSERT INTO eso_opt_mfr SET em_name='".mysql_real_escape_string(trim($_POST['name']))."',et_id=".((int)$_POST['type_id']).";";
	$result = mysql_query($query);
	if(! $result){ return "<strong>Error adding equipment type.</strong><br />Error in query: $query<br />ERROR: ".mysql_error();}
	return null;
    }
    elseif($_POST['action'] == "addModel")
    {
	$query = "SELECT et_id FROM eso_opt_mfr WHERE em_id=".((int)$_POST['mfr_id']).";";
	$result = mysql_query($query);
	if(! $result){ return "<strong>Error adding equipment type.</strong><br />Error in query: $query<br />ERROR: ".mysql_error();}
	$row = mysql_fetch_assoc($result);
	$typeID = $row['et_id'];
	$query = "INSERT INTO eso_opt_model SET emod_mfr_id=".((int)$_POST['mfr_id']).",emod_et_id=".((int)$typeID).", emod_name='".mysql_real_escape_string(trim($_POST['model']))."'";
	if(isset($_POST['modelnum']) && trim($_POST['modelnum']) != "")
	{
	    $query .= ",emod_model_num='".mysql_real_escape_string(trim($_POST['modelnum']))."'";
	}
	if(isset($_POST['sizes'])){ $query .= ",emod_has_size=1";}
	$query .= ";";
	$result = mysql_query($query);
	if(! $result){ return "<strong>Error adding equipment type.</strong><br />Error in query: $query<br />ERROR: ".mysql_error();}
	return null;
    }
}

?>

