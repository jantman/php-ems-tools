<?php
require_once('../../custom.php');
require_once('../../inc/web_session.php.inc');
require_once('inc/common.php');
require_once('inc/returnForm.php');

$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

require_once('config/smarty-config.php');

// BEGIN HEADER TEMPLATE
$smarty->assign('bodyID', 'tab4');
$smarty->assign('title', 'Return');
$smarty->display('head.tpl');
$smarty->clear_all_assign(); // clear all variables
$smarty->assign('config', $SMARTY_config); // reassign config
// END HEADER TEMPLATE

$smarty->assign('title', $shortName.' - Equipment Signout - Return');

// 
// BEGIN CONTENT
//

if(isset($_POST['EMTid']))
{
    $msg = process_inputs();
    if($msg != null)
    {
	$smarty->assign('msg', $msg);
    }
}

if(isset($_GET['EMTid']) && isset($_GET['eid']))
{
    $smarty->assign("form", genReturnForm($_GET['EMTid'], $_GET['eid']));
}
else
{
    $query = "SELECT DISTINCT e.evt_EMTid,r.FirstName,r.LastName FROM eso_events AS e LEFT JOIN roster AS r ON e.evt_EMTid=r.EMTid WHERE e.evt_EMTid IS NOT NULL;";
    smarty_add_row($query, 'members');
    
    $query = "SELECT e.e_id,e.e_serial,e.e_size,emod.emod_name,emod.emod_model_num,et.et_name,em.em_name,eos.es_name,evt.evt_EMTid FROM eso_events AS evt LEFT JOIN eso_equipment AS e ON e.e_id=evt.evt_equip_id LEFT JOIN eso_opt_model AS emod ON e.e_emod_id=emod.emod_id LEFT JOIN eso_opt_equipTypes AS et ON emod.emod_et_id=et.et_id LEFT JOIN eso_opt_mfr AS em ON emod.emod_mfr_id=em.em_id LEFT JOIN eso_opt_status AS eos ON evt.evt_status_id=eos.es_id WHERE evt.evt_is_deprecated=0 AND eos.es_is_final=0 ORDER BY et.et_name,em.em_name,emod.emod_name,e.e_serial;";
    smarty_add_row($query, 'items');
}

//
// END CONTENT
//
$smarty->display('return.tpl');

$smarty->clear_all_assign(); // clear all variables
$smarty->assign('config', $SMARTY_config); // reassign config


// BEGIN FOOTER TEMPLATE
$smarty->display('footer.tpl');
// END FOOTER TEMPLATE

function process_inputs()
{
    if(! isset($_POST['EMTid']) || $_POST['EMTid'] == 0){ return "Error: EMTid not selected.";}
    if(! isset($_POST['eq_id']) || $_POST['eq_id'] == 0){ return "Error: Equipment ID not selected.";}
    if(! isset($_POST['reason']) || $_POST['reason'] == "0"){ return "Error: You must select a reason for the return.";}
    if($_POST['reason'] == "Other" && (! isset($_POST['returnReason']) || trim($_POST['returnReason']) == "")){ return "Error: If you select 'other' as a return reason, you must specify the reason.";}

    trans_start();

    $eq_id = (int)$_POST['eq_id'];
    $reason = mysql_real_escape_string($_POST['reason']);
    if($reason == "Other"){ $reason = mysql_real_escape_string($_POST['returnReason']);}

    // deprecate old events
    $query = "UPDATE eso_events SET evt_is_deprecated=1 WHERE evt_equip_id=$eq_id;";
    trans_safe_query($query);

    // add event
    $query = "INSERT INTO eso_events SET evt_ts=".time().",evt_equip_id=$eq_id,evt_status_id=5,evt_comment='Returned - ".$reason."',evt_admin_EMTid='".mysql_real_escape_string(trim($_SERVER["PHP_AUTH_USER"]))."';";
    trans_safe_query($query);
    trans_commit();
    return "Item successfully returned.";
}

?>

