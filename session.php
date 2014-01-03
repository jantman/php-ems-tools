<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-25 22:53:05 jantman"                                                              |
 +--------------------------------------------------------------------------------------------------------+
 | Copyright (c) 2009, 2010 Jason Antman. All rights reserved.                                            |
 |                                                                                                        |
 | This program is free software; you can redistribute it and/or modify                                   |
 | it under the terms of the GNU General Public License as published by                                   |
 | the Free Software Foundation; either version 3 of the License, or                                      |
 | (at your option) any later version.                                                                    |
 |                                                                                                        |
 | This program is distributed in the hope that it will be useful,                                        |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of                                         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                                          |
 | GNU General Public License for more details.                                                           |
 |                                                                                                        |
 | You should have received a copy of the GNU General Public License                                      |
 | along with this program; if not, write to:                                                             |
 |                                                                                                        |
 | Free Software Foundation, Inc.                                                                         |
 | 59 Temple Place - Suite 330                                                                            |
 | Boston, MA 02111-1307, USA.                                                                            |
 +--------------------------------------------------------------------------------------------------------+
 |Please use the above URL for bug reports and feature/support requests.                                  |
 +--------------------------------------------------------------------------------------------------------+
 | Authors: Jason Antman <jason@jasonantman.com>                                                          |
 +--------------------------------------------------------------------------------------------------------+
 | $LastChangedRevision:: 51                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/index.php                                              $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * Sessions / login form.
 *
 * @package MPAC-NewCall-Pages
 */

require_once('inc/newcall.php.inc');
require_once('inc/session.php');
require_once('inc/ldap.php');

$badPass = false;
if(isset($_POST['username']))
{
    $foo = processLogin();
    if(! $foo){ $badPass = true;}
}

$foo = is_valid_session();
$needs_auth = false; $expired = false; $logged_out = false;
if($foo == 1 || $foo == 2){ $needs_auth = true;}
elseif($foo == 3){ $needs_auth = true; $expired = true;}
elseif($foo == 0 && isset($_SESSION['EMTid'])){ update_session_time();}

if(isset($_GET['action']) && $_GET['action'] == "logout")
{
    kill_session();
    $logged_out = true;
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $shortName;?> Log In</title>
<link rel="stylesheet" type="text/css" href="css/index.css" />
</head>

<body>
<?php
if(isset($_SESSION['EMTid']))
{
    echo '<p><a href="session.php?action=logout">Log Out</a></p>';
}

if($logged_out)
{
    echo '<p><strong>Logged Out.</strong> <a href="session.php">Click here to log in.</a></p>';
}

if($badPass)
{
    echo '<p style="color: red;"><strong>ERROR: Invalid Credentials.</strong></p>';
}

if($needs_auth && ! $logged_out)
{
    echo '<form name="login" method="POST" AUTOCOMPLETE="off" >'."\n";
    echo '<table>'."\n";
    echo '<tr colspan="2"><th>Log In</th></tr>'."\n";
    echo '<tr><th>Username</th><td><input type="text" name="username" size="10" /></td>'."\n";
    echo '<tr><th>Password</th><td><input type="password" name="passwd" size="10" /></td>'."\n";
    echo '<tr><td colspan="1"><input type="submit" value="Login" /></td>'."\n";
    echo '</table>'."\n";

    if(isset($_SERVER['HTTP_REFERER']))
    {
	$referrer = substr($_SERVER['HTTP_REFERER'], strrpos($_SERVER['HTTP_REFERER'], "/")+1);
	echo '<input type="hidden" name="referrer" value="'.$referrer.'" />'."\n";
    }

}

/* DEBUG
echo '<p>is_valid_session returns: '.is_valid_session().'</p>';
echo '<p>_SESSION:</p>';
echo '<pre>';
echo var_dump($_SESSION);
echo '</pre>';
echo '<p><strong>SID: </strong>'.session_id()."</p>";
echo '<p><strong>Referrer:</strong> '.$_SERVER['HTTP_REFERER'].'<br />'.$referrer.'</p>';
echo '<p><strong>POST Referrer:</strong> '.$_POST['referrer'].'</p>';
*/ 

echo '</form>'."\n";
?>

</body>
</html>

<?php

function processLogin()
{
    if(! isset($_POST['username']) || ! isset($_POST['passwd'])){ return false;}
    $res = login_start_sess($_POST['username'], $_POST['passwd']);
    if(! $res){ return false;}
    if(isset($_POST['referrer']))
    {
	header("Location: ".$_POST['referrer']);
	die();
    }
    return true;
}

?>