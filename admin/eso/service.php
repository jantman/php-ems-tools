<?php
require_once('../../custom.php');
require_once('inc/common.php');

$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

require_once('config/smarty-config.php');

// BEGIN HEADER TEMPLATE
$smarty->assign('bodyID', 'tab10');
$smarty->assign('title', 'Service');
$smarty->display('head.tpl');
$smarty->clear_all_assign(); // clear all variables
$smarty->assign('config', $SMARTY_config); // reassign config
// END HEADER TEMPLATE

$smarty->assign('title', $shortName.' - Equipment Signout - Service');

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

if(isset($_GET['eid']))
{
    $eid = (int)$_GET['eid'];
    $smarty->assign('eid', $eid);
    $smarty->assign('itemDesc', $itemlist[$eid]['str']);
}
else
{
    $smarty->assign('eid', "");
}

if(isset($_POST['action']) && $_POST['action'] == "comment")
{
    $query = "INSERT INTO eso_comments SET cmt_ts=".time().",cmt_text='".mysql_real_escape_string(trim($_POST['comment']))."',cmt_admin_EMTid='".mysql_real_escape_string(trim($_SERVER["PHP_AUTH_USER"]))."',cmt_e_id=".((int)$_POST['eid']).";";
    $result = mysql_query($query) or die("Error in query: $query<br />Error: ".mysql_error());
    $msg = '<strong>Comment added.</strong> <a href="history.php?eid='.((int)$_POST['eid']).'">Click here to view equiment history</a>.';
    $smarty->assign('msg', $msg);
}
elseif(isset($_POST['action']) && $_POST['action'] == "status")
{
    $eID = (int)$_POST['eid'];
    trans_start();
    $query = "UPDATE eso_events SET evt_is_deprecated=1 WHERE evt_equip_id=$eID;";
    trans_safe_query($query);

    $query = "INSERT INTO eso_events SET evt_ts=".time().",evt_equip_id=$eID,evt_status_id=".((int)$_POST['statusID']).",evt_admin_EMTid='".mysql_real_escape_string(trim($_SERVER["PHP_AUTH_USER"]))."';";
    trans_safe_query($query);

    trans_commit();
    $msg = '<strong>Status updated.</strong> <a href="history.php?eid='.((int)$_POST['eid']).'">Click here to view equiment history</a>.';
    $smarty->assign('msg', $msg);
}
elseif(isset($_POST['action']) && $_POST['action'] == "file")
{
    if($_FILES['uploadFile']['error'] != 0 || $_FILES['uploadFile']['size'] == 0){ $msg = "Error uploading file."; break;}
    if($_FILES['uploadFile']['size'] != filesize($_FILES['uploadFile']['tmp_name'])){ $msg = "Error uploading file - sizes to not match."; break;}

    $eid = (int)$_POST['eid'];
    $content = addslashes(trim(fread(fopen($_FILES['uploadFile']['tmp_name'], 'r'), $_FILES['uploadFile']['size'])));
    $query = "INSERT INTO eso_uploads SET eu_ts=".time().",eu_name='".mysql_real_escape_string($_FILES['uploadFile']['name'])."',eu_type='".mysql_real_escape_string($_POST['fileType'])."',eu_size_b=".filesize($_FILES['uploadFile']['tmp_name']).",eu_mime_type='".mysql_real_escape_string($_FILES['uploadFile']['type'])."',";
    if(trim($_POST['comment']) != ""){ $query .= "eu_comment='".mysql_real_escape_string(trim($_POST['comment']))."',";}
    $query .= "eu_eid=$eid,eu_content='$content';";

    $result = mysql_query($query);
    if(! $result){ $msg = "MySQL Error: ".mysql_error();}
    else {$msg = "Uploaded file as ID ".mysql_insert_id().' ('.$_FILES['uploadFile']['tmp_name'].'). <a href="files.php">files</a>.';}

    $smarty->assign('msg', $msg);
}

//
// END CONTENT
//

$smarty->display('service.tpl');

$smarty->clear_all_assign(); // clear all variables
$smarty->assign('config', $SMARTY_config); // reassign config


// BEGIN FOOTER TEMPLATE
$smarty->display('footer.tpl');
// END FOOTER TEMPLATE
?>

