<?php
require_once('../../custom.php');
require_once('inc/common.php');

$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

require_once('config/smarty-config.php');

// BEGIN HEADER TEMPLATE
$smarty->assign('bodyID', 'tab7');
$smarty->assign('title', 'Equipment');
$smarty->display('head.tpl');
$smarty->clear_all_assign(); // clear all variables
$smarty->assign('config', $SMARTY_config); // reassign config
// END HEADER TEMPLATE

$smarty->assign('title', $shortName.' - Equipment Signout - Equipment');

// 
// BEGIN CONTENT
//

$msg = process_inputs();
if($msg != null)
{
    $smarty->assign('msg', $msg);
}

$query = "SELECT * FROM eso_opt_equipTypes;";
smarty_add_row($query, 'types');

$query = "SELECT e.e_id,e.e_serial,e.e_size,emod.emod_name,emod.emod_model_num,et.et_name,em.em_name,eos.es_name,evt.evt_EMTid FROM eso_events AS evt LEFT JOIN eso_equipment AS e ON e.e_id=evt.evt_equip_id LEFT JOIN eso_opt_model AS emod ON e.e_emod_id=emod.emod_id LEFT JOIN eso_opt_equipTypes AS et ON emod.emod_et_id=et.et_id LEFT JOIN eso_opt_mfr AS em ON emod.emod_mfr_id=em.em_id LEFT JOIN eso_opt_status AS eos ON evt.evt_status_id=eos.es_id WHERE evt.evt_is_deprecated=0 AND eos.es_is_final=0 ORDER BY et.et_name,em.em_name,emod.emod_name,e.e_serial;";
smarty_add_row($query, 'items');

//
// END CONTENT
//

$smarty->display('equipment.tpl');

$smarty->clear_all_assign(); // clear all variables
$smarty->assign('config', $SMARTY_config); // reassign config


// BEGIN FOOTER TEMPLATE
$smarty->display('footer.tpl');
// END FOOTER TEMPLATE

function process_inputs()
{
    if(! isset($_POST['emod_id']) || trim($_POST['emod_id']) == ""){ return null;}

    $query = "SELECT * FROM eso_equipment WHERE e_emod_id=".((int)$_POST["emod_id"])." AND e_serial='".mysql_real_escape_string(trim($_POST["serial"]))."';";
    $result = mysql_query($query) or die("Error in query: $query<br />Error: ".mysql_error);
    if(mysql_num_rows($result) > 0){ return "An item with this model and serial number already exists.";}

    trans_start();

    // insert into equipment
    $query = "INSERT INTO eso_equipment SET e_emod_id=".((int)$_POST["emod_id"]).",e_et_id=".((int)$_POST["type_id"]);
    if(isset($_POST["serial"])){ $query .= ",e_serial='".mysql_real_escape_string(trim($_POST["serial"]))."'";}
    if(isset($_POST["size"])){ $query .= ",e_size='".mysql_real_escape_string(trim($_POST["size"]))."'";}
    if(isset($_POST["comment"]) && trim($_POST["comment"]) != ""){ $query .= ",e_comment='".mysql_real_escape_string(trim($_POST["comment"]))."'";}
    $query .= ";";
    trans_safe_query($query);
    $eID = mysql_insert_id();

    $query = "SELECT es_id FROM eso_opt_status WHERE es_default_new=1;";
    $result = mysql_query($query) or die("Error in query: $query<br />ERROR: ".mysql_error());
    $row = mysql_fetch_assoc($result);
    $status = $row['es_id'];

    $query = "INSERT INTO eso_events SET evt_ts=".time().",evt_equip_id=$eID,evt_status_id=$status,evt_comment='Added new equipment.',evt_admin_EMTid='".mysql_real_escape_string(trim($_SERVER["PHP_AUTH_USER"]))."';";
    trans_safe_query($query);
    trans_commit();
}

?>

