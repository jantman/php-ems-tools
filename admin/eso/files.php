<?php
require_once('../../custom.php');
require_once('inc/common.php');

$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

require_once('config/smarty-config.php');

// BEGIN HEADER TEMPLATE
$smarty->assign('bodyID', 'tab9');
$smarty->assign('title', 'Files');
$smarty->display('head.tpl');
$smarty->clear_all_assign(); // clear all variables
$smarty->assign('config', $SMARTY_config); // reassign config
// END HEADER TEMPLATE

$smarty->assign('title', $shortName.' - Equipment Signout - Files');

// 
// BEGIN CONTENT
//

$query = "SELECT eu.eu_id,eu.eu_ts,eu.eu_name,eu.eu_type,eu.eu_size_b,eu.eu_mime_type,eu.eu_eid,eu.eu_comment,e.e_serial,e.e_size,emod.emod_name,emod.emod_model_num,et.et_name,em.em_name FROM eso_uploads AS eu LEFT JOIN eso_equipment AS e ON eu.eu_eid=e.e_id LEFT JOIN eso_opt_model AS emod ON e.e_emod_id=emod.emod_id LEFT JOIN eso_opt_equipTypes AS et ON emod.emod_et_id=et.et_id LEFT JOIN eso_opt_mfr AS em ON emod.emod_mfr_id=em.em_id ORDER BY et.et_name,em.em_name,emod.emod_name,e.e_serial;";
smarty_add_row($query, 'files');

//
// END CONTENT
//

$smarty->display('files.tpl');

$smarty->clear_all_assign(); // clear all variables
$smarty->assign('config', $SMARTY_config); // reassign config


// BEGIN FOOTER TEMPLATE
$smarty->display('footer.tpl');
// END FOOTER TEMPLATE

?>

