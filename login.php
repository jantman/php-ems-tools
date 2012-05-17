<?php

// login.php
// login form for session-based logins

// $Id$

require_once('config/config.php');
require_once('inc/'.$config_i18n_filename);


if(isset($_GET['action']))
{
    $action = $_GET['action'];
}
else
{
    $action = "login";
}

if(isset($_GET['request']))
{
    $request = $_GET['request'];
}

if($action == "login" || $action == "failed")
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $shortName." - ".$i18n_strings["signOn"]["Login"];?></title>
<link rel="stylesheet" type="text/css" href="login.css" />
</head>

<body>
<h1><?php echo $shortName." - ".$i18n_strings["signOn"]["Login"];?></h1>

<?php
if($action == "failed")
 {
     echo '<div class="failedLogin">'.$i18n_strings["signOnWarnings"]["LoginFail"].'</div>'."\n";
 }
?>

<form action="handlers/login.php?action=login" method="post" name="login" id="login">

<?php
echo '<input name="request" type="hidden" value="'.$request.'" />'."\n";
 echo '<label for="user">'.$i18n_strings["signOn"]["ID Num"].': </label>';
 echo '<input name="user" id="user" size="30" type="text" />';
 echo '<br />'."\n";
 echo '<label for="pass">'.$i18n_strings["signOn"]["adminPW"].': </label>';
 echo '<input name="pass" id="pass" size="30" type="password" />';
 echo '<br />'."\n";
 echo '<label for="authSource">'.$i18n_strings["signOn"]["Authentication"].': </label>'."\n";
 echo '<select name="authSource">'."\n";
 echo '<option value="Simple/DB" selected="selected">Simple/DB</option>'."\n";
 echo '<option value="LDAPsimple">LDAP Simple</option>'."\n";
 echo '<option value="LDAPsecure">LDAP Secure</option>'."\n";
 echo '</select>';
 echo '<br />'."\n";

 echo '<input name="buttonGroup[btnReset]" value="'.$i18n_strings["signOn"]["Reset"].'" type="reset" />    <input name="buttonGroup[btnSubmit]" value="'.$i18n_strings["signOn"]["Submit"].'" type="submit" />';
?>
</form>

</body>
</html>

<?php
}
elseif($action == "logout")
{
    header("Location: handlers/login.php?action=logout");
}
