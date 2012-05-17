<?php

require_once('../../config/config.php');
require_once('../smarty-config.php');

$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

$query = "SELECT EMTid,FirstName,LastName FROM roster WHERE status='Inactive Life' OR status='Senior' OR status='Driver' OR status='Probie' ORDER BY LastName,FirstName;";
$result = mysql_query($query) or die("Error in query: $query<br /><strong>Error:</strong> ".mysql_error());
$EMTs = array();
while($row = mysql_fetch_assoc($result))
{
    $EMTs[] = $row;
}

$smarty->assign('EMTs', $EMTs);

if($_GET['type'] == 1)
{
    // uniform
    echo "uniform";
}
elseif($_GET['type'] == 2)
{
    // pager
    $query = "SELECT * FROM opt_equip WHERE type_id=".((int)$_GET['type']).";";
    $result = mysql_query($query) or die("Error in query: $query<br /><strong>Error:</strong> ".mysql_error());
    $models = array();
    while($row = mysql_fetch_assoc($result))
    {
	$models[] = $row;
    }
    $smarty->assign('models', $models);
    $smarty->display('equipSignoutForms/pager.tpl');
}
elseif($_GET['type'] == 3)
{
    // radio
    echo "radio";
}
else
{
    echo "Bad type!";
}

//$smarty->display('equipSignout.tpl');


?>