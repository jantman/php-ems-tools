<?php
require_once('../../custom.php');
require_once('../../inc/web_session.php.inc');
require_once('inc/common.php');

$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

require_once('config/smarty-config.php');

// BEGIN HEADER TEMPLATE
$smarty->assign('bodyID', 'tab2');
$smarty->assign('title', 'Available');
$smarty->display('head.tpl');
$smarty->clear_all_assign(); // clear all variables
$smarty->assign('config', $SMARTY_config); // reassign config
// END HEADER TEMPLATE

$smarty->assign('title', $shortName.' - Equipment Signout - Available');

// 
// BEGIN CONTENT
//

$msg = process_inputs();
if($msg != null)
{
    $smarty->assign('msg', $msg);
}

$query = "SELECT e.e_id,e.e_serial,e.e_size,emod.emod_name,emod.emod_model_num,et.et_name,em.em_name,eos.es_name FROM eso_events AS evt LEFT JOIN eso_equipment AS e ON e.e_id=evt.evt_equip_id LEFT JOIN eso_opt_model AS emod ON e.e_emod_id=emod.emod_id LEFT JOIN eso_opt_equipTypes AS et ON emod.emod_et_id=et.et_id LEFT JOIN eso_opt_mfr AS em ON emod.emod_mfr_id=em.em_id LEFT JOIN eso_opt_status AS eos ON evt.evt_status_id=eos.es_id WHERE evt.evt_is_deprecated=0 AND eos.es_in_stock=1 ORDER BY et.et_name,em.em_name,emod.emod_name,e.e_serial;";
smarty_add_row($query, 'items');

if(isset($_GET['eid']) && ! isset($_POST['eid']))
{
    $smarty->assign('eid', $_GET['eid']);

    $foo = getMemberList('LastName,FirstName', true);
    $smarty->assign('members', $foo);

    $query = "SELECT e.e_id,e.e_serial,e.e_size,emod.emod_name,emod.emod_model_num,et.et_name,em.em_name,eos.es_name FROM eso_events AS evt LEFT JOIN eso_equipment AS e ON e.e_id=evt.evt_equip_id LEFT JOIN eso_opt_model AS emod ON e.e_emod_id=emod.emod_id LEFT JOIN eso_opt_equipTypes AS et ON emod.emod_et_id=et.et_id LEFT JOIN eso_opt_mfr AS em ON emod.emod_mfr_id=em.em_id LEFT JOIN eso_opt_status AS eos ON evt.evt_status_id=eos.es_id WHERE evt.evt_is_deprecated=0 AND eos.es_in_stock=1 AND e.e_id=".$_GET['eid']." ORDER BY et.et_name,em.em_name,emod.emod_name,e.e_serial;";
    $result = mysql_query($query) or die("Error in query: $query<br />Error: ".mysql_error);
    $row = mysql_fetch_assoc($result);
    $smarty->assign('myitem', $row);
}

//
// END CONTENT
//

$smarty->display('available.tpl');

$smarty->clear_all_assign(); // clear all variables
$smarty->assign('config', $SMARTY_config); // reassign config


// BEGIN FOOTER TEMPLATE
$smarty->display('footer.tpl');
// END FOOTER TEMPLATE

function process_inputs()
{
    if(! isset($_POST['eid'])){ return null;}
    $eID = (int)$_POST['eid'];
    if(! isset($_POST['EMTid'])){ return null;}
    $EMTid = trim($_POST['EMTid']);
    if($EMTid == 0 && $EMTid != "MPAC"){ return "Please select an EMTid.";}

    trans_start();
    $query = "UPDATE eso_events SET evt_is_deprecated=1 WHERE evt_equip_id=$eID;";
    trans_safe_query($query);

    $query = "INSERT INTO eso_events SET evt_ts=".time().",evt_equip_id=$eID,evt_status_id=2,evt_admin_EMTid='".mysql_real_escape_string(trim($_SERVER["PHP_AUTH_USER"]))."',evt_EMTid='".mysql_real_escape_string($EMTid)."';";

    trans_safe_query($query);

    trans_commit();
}

?>

