<?php
// handlers/login.php
// login form handler for session-based logins

// $Id$
require_once('../config/config.php');
require_once('../inc/'.$config_i18n_filename);
require_once('../inc/web_session.php.inc');

// mysql connection
$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

// TODO: implement alternative auth sources. For now, just use the DB.

/*
echo "SESSION:<br />";
echo '<pre>';
echo var_dump($_SESSION);
echo '</pre>';
die();
*/

if(isset($_POST['request']) && trim($_POST['request']) != "")
{
    $request = $_POST['request'];
}
else
{
    $request = "";
}

if(isset($_POST['action']))
{
    $action = $_POST['action'];
}
else
{
    $action = 'login';
}

if($action == "logout")
{
    // logout
    php_ems_kill_session();
}
else
{
    php_ems_login_handler($_POST['user'], $_POST['pass'], $request);
}

?>