<?php
require_once('../../custom.php');
require_once('../../inc/web_session.php.inc');
require_once('inc/common.php');

$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

require_once('config/smarty-config.php');

// BEGIN HEADER TEMPLATE
$smarty->assign('bodyID', 'tab5');
$smarty->assign('title', 'Members');
$smarty->display('head.tpl');
$smarty->clear_all_assign(); // clear all variables
$smarty->assign('config', $SMARTY_config); // reassign config
// END HEADER TEMPLATE

$smarty->assign('title', $shortName.' - Equipment Signout - Members');

// 
// BEGIN CONTENT
//

$query = "SELECT e.e_id,e.e_serial,e.e_size,emod.emod_name,emod.emod_model_num,et.et_name,em.em_name,eos.es_name,evt.evt_EMTid,r.FirstName,r.LastName FROM eso_events AS evt LEFT JOIN eso_equipment AS e ON e.e_id=evt.evt_equip_id LEFT JOIN eso_opt_model AS emod ON e.e_emod_id=emod.emod_id LEFT JOIN eso_opt_equipTypes AS et ON emod.emod_et_id=et.et_id LEFT JOIN eso_opt_mfr AS em ON emod.emod_mfr_id=em.em_id LEFT JOIN eso_opt_status AS eos ON evt.evt_status_id=eos.es_id LEFT JOIN roster AS r ON evt.evt_EMTid=r.EMTid WHERE evt.evt_is_deprecated=0 AND eos.es_is_final=0 AND evt.evt_EMTid IS NOT NULL ORDER BY lpad(evt_EMTid,10,'0');";
smarty_add_row($query, 'items');

//
// END CONTENT
//

$smarty->display('members.tpl');

$smarty->clear_all_assign(); // clear all variables
$smarty->assign('config', $SMARTY_config); // reassign config


// BEGIN FOOTER TEMPLATE
$smarty->display('footer.tpl');
// END FOOTER TEMPLATE
?>

