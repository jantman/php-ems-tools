<?php
require_once('../../custom.php');
require_once('inc/common.php');
require_once('../../inc/web_session.php.inc');

$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

require_once('config/smarty-config.php');

// BEGIN HEADER TEMPLATE
$smarty->assign('bodyID', 'tab1');
$smarty->assign('title', 'Home');
$smarty->display('head.tpl');
$smarty->clear_all_assign(); // clear all variables
$smarty->assign('config', $SMARTY_config); // reassign config
// END HEADER TEMPLATE

$smarty->assign('title', $shortName.' - Equipment Signout - Home');

// 
// BEGIN CONTENT
//

$query = "SELECT * FROM eso_opt_equipTypes;";
smarty_add_row($query, 'types');

$query = "SELECT e.e_id,e.e_serial,e.e_size,emod.emod_name,emod.emod_model_num,et.et_name,em.em_name,eos.es_name,evt.evt_EMTid FROM eso_events AS evt LEFT JOIN eso_equipment AS e ON e.e_id=evt.evt_equip_id LEFT JOIN eso_opt_model AS emod ON e.e_emod_id=emod.emod_id LEFT JOIN eso_opt_equipTypes AS et ON emod.emod_et_id=et.et_id LEFT JOIN eso_opt_mfr AS em ON emod.emod_mfr_id=em.em_id LEFT JOIN eso_opt_status AS eos ON evt.evt_status_id=eos.es_id WHERE evt.evt_is_deprecated=0 AND eos.es_is_final=0 AND (eos.es_name='Broken' OR eos.es_name='Possible Problem' )ORDER BY et.et_name,em.em_name,emod.emod_name,e.e_serial;";
smarty_add_row($query, 'items');

//
// END CONTENT
//

$smarty->display('index.tpl');

$smarty->clear_all_assign(); // clear all variables
$smarty->assign('config', $SMARTY_config); // reassign config


// BEGIN FOOTER TEMPLATE
$smarty->display('footer.tpl');
// END FOOTER TEMPLATE
?>

