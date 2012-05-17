<?php
require_once('../../custom.php');
require_once('inc/common.php');

$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

require_once('config/smarty-config.php');

// BEGIN HEADER TEMPLATE
$smarty->assign('bodyID', 'tab6');
$smarty->assign('title', 'History');
$smarty->display('head.tpl');
$smarty->clear_all_assign(); // clear all variables
$smarty->assign('config', $SMARTY_config); // reassign config
// END HEADER TEMPLATE

$smarty->assign('title', $shortName.' - Equipment Signout - History');

// 
// BEGIN CONTENT
//

// all equipment
$foo = array();
$query = "SELECT e.e_id,e.e_serial,e.e_size,emod.emod_name,emod.emod_model_num,et.et_name,em.em_name FROM eso_events AS evt LEFT JOIN eso_equipment AS e ON e.e_id=evt.evt_equip_id LEFT JOIN eso_opt_model AS emod ON e.e_emod_id=emod.emod_id LEFT JOIN eso_opt_equipTypes AS et ON emod.emod_et_id=et.et_id LEFT JOIN eso_opt_mfr AS em ON emod.emod_mfr_id=em.em_id WHERE evt.evt_is_deprecated=0 ORDER BY et.et_name,em.em_name,emod.emod_name,e.e_serial;";
$result = mysql_query($query) or die("Error in query: $query<br />Error: ".mysql_error());
while($row = mysql_fetch_assoc($result))
{
    $bar = array();
    $bar['eid'] = $row['e_id'];
    $str = $row['et_name']." - ".$row['em_name']." ".$row['emod_name'];
    if(trim($row['emod_model_num']) != ""){ $str .= " (".$row['emod_model_num'].")";}
    if(trim($row['e_serial']) != ""){ $str .= " ".$row['e_serial'];}
    $bar['str'] = $str;
    $foo[$row['e_id']] = $bar;
}
$smarty->assign('items', $foo);
$itemlist = $foo;

if(isset($_POST['eid'])){ $eid = (int)$_POST['eid'];}
elseif(isset($_GET['eid'])){ $eid = (int)$_GET['eid'];}
elseif(isset($_POST['serno']))
{
    $serno = $_POST['serno'];
    $query = "SELECT e_id FROM eso_equipment WHERE e_serial='".mysql_real_escape_string($serno)."';";
    $result = mysql_query($query) or die("Error in query: $query<br />Error: ".mysql_error());
    if(mysql_num_rows($result) < 1){ $smarty->assign('msg', "Error: No matching results found.");}
    else { $row = mysql_fetch_assoc($result); $eid = $row['e_id']; }
}

if(isset($eid))
{
    // display history for a piece of equipment

    $smarty->assign('itemID', $eid);
    $smarty->assign('itemDesc', $itemlist[$eid]['str']);

    // get statuses
    $statuses = array();
    $query = "SELECT * FROM eso_opt_status;";
    $result = mysql_query($query) or die("Error in query: $query<br />Error: ".mysql_error());
    while($row = mysql_fetch_assoc($result))
    {
	$statuses[$row['es_id']] = $row['es_name'];
    }

    // handle events
    $foo = array();
    $query = "SELECT * FROM eso_events WHERE evt_equip_id=$eid;";
    $result = mysql_query($query) or die("Error in query: $query<br />Error: ".mysql_error());
    while($row = mysql_fetch_assoc($result))
    {
	$bar = array();
	$bar['ts'] = $row['evt_ts'];
	$bar['date'] = date("Y-m-d", $row['evt_ts']);
	$bar['text'] = handle_eso_event($row, $statuses);
	$foo[$row['evt_ts']] = $bar;
    }

    // handle comments
    $query = "SELECT * FROM eso_comments WHERE cmt_e_id=$eid;";
    $result = mysql_query($query) or die("Error in query: $query<br />Error: ".mysql_error());
    while($row = mysql_fetch_assoc($result))
    {
	$bar = array();
	$bar['ts'] = $row['cmt_ts'];
	$bar['date'] = date("Y-m-d", $row['cmt_ts']);
	$bar['text'] = "Comment added by ".$row['cmt_admin_EMTid'].":<br />".$row['cmt_text'];
	$foo[$row['cmt_ts']] = $bar;
    }

    arsort($foo);

    $query = "SELECT eu_comment,eu_id,eu_ts,eu_name,eu_type,eu_size_b FROM eso_uploads WHERE eu_eid=$eid ORDER BY eu_ts DESC;";
    smarty_add_row($query, 'files');

    $smarty->assign('hx', $foo);
}


//
// END CONTENT
//

$smarty->display('history.tpl');

$smarty->clear_all_assign(); // clear all variables
$smarty->assign('config', $SMARTY_config); // reassign config


// BEGIN FOOTER TEMPLATE
$smarty->display('footer.tpl');
// END FOOTER TEMPLATE

function handle_eso_event($row, $statuses)
{
    $str = "";
    $str = "Status changed to &#39;".$statuses[$row['evt_status_id']];
    if($row['evt_EMTid'] != ""){ $str .= " to ".$row['evt_EMTid'];}
    $str .= "&#39; by ".$row['evt_admin_EMTid'];
    if(trim($row['evt_comment']) != ""){ $str .= " Comment: ".$row['evt_comment'];}
    return $str;
}

?>

