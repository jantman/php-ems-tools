<?php

require_once('/srv/www/htdocs/config/config.php');

if(isset($_POST['EMTid']))
{
    processForm();
}
else
{
    showForm();
}

function processForm()
{
    echo '<pre>';
    echo var_dump($_POST);
    echo '</pre>';
    
    // check auth if needed
    require_once('/srv/www/htdocs/inc/auth.php');
    if(isset($_POST['password']))
    {
	$auth = member_auth_MySQL($_POST['EMTid'], trim($_POST['password']));
	if(! $auth)
	{
	    die("ERROR: Invalid credentials.");
	}
    }

    $EMTid = $_POST['EMTid'];
    $DTMF = ((int)$_POST['DTMF']);
    $status = get_member_status($EMTid);

    // make sure that user exists
    if(! member_exists($EMTid))
    {
	die("ERROR: Invalid EMTid.");
    }

    // put to DB
    require_once('/srv/www/htdocs/config/openEScallin.php');
    $rmt_conn = mysql_connect($callin_host, $callin_user);
    if(! $rmt_conn)
    {
	echo '<div class="maintWarning"><h1>Call-in server ('.$callin_host.') appears to be down.</h1><h2>No call-ins will show up here.</h2></div>'."\n";
    }
    else
    {
	mysql_select_db($callin_db, $rmt_conn) or die("Unable to select database '".$callin_db."' on host ".$callin_host."\n");
    }
    $query = "INSERT INTO callins SET start_ts=".time().",end_ts=".time().",DTMF_select=$DTMF,status='$status',cid='".$_SERVER['REMOTE_ADDR']."',EMTid='$EMTid';";
    echo '<p>'.$query.'</p>';
    mysql_query($query) or die("Error inserting callin record in database.");

    // redirect
    require_once('/srv/www/htdocs/inc/Browser.php');
    $browser = new Browser();

    if($browser->isMobile())
    {
	header("Location: responding-WAP.php");
    }
    else
    {
	header("Location: responding.php");
    }

}

function showForm()
{
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> <html xmlns="http://www.w3.org/1999/xhtml"> <head> <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> <title>MPAC - Respond</title> </head> <body>';

    echo '<h1>Web-Based Call-In</h1>';

    echo '<form name="callin" method="POST">'."\n";
    if(! is_local_user() && isset($_SERVER['PHP_AUTH_USER']))
    {
	$EMTid = $_SERVER['PHP_AUTH_USER'];
	echo '<input type="hidden" name="EMTid" id="EMTid" value="'.$EMTid.'" />'."\n";
	echo '<p><strong>EMTid:</strong> '.$EMTid.'</p>';

	// not a local user, and we have auth
	echo '<p><label for="DTMF"><strong>Responding...</strong></label>';
	echo '<select name="DTMF" id="DTMF">';
	echo '<option value="1">1) To Building</option>';
	echo '<option value="2">2) To Scene</option>';
	echo '<option value="3">3) At Building</option>';
	echo '</select></p>';
	// show a select for responding
    }
    else
    {
	// else, display an EMTid select and a password field
	echo '<p><label for="EMTid"><strong>EMTid </strong></label> <input type="text" size="3" name="EMTid" id="EMTid" /></p>';
	echo '<p><label for="password"><strong>Password: </strong></label><input type="password" size="10" name="password" id="password" /></p>';

	// also default to At HQ.
	echo '<input type="hidden" name="DTMF" id="DTMF" value="3" />'."\n";
	echo '<p><strong>Responding...</strong> At Building.</p>';
    }

    echo '<input type="submit" value="SUBMIT" />'."\n";
    
    echo '</body> </html>';
}

function is_local_user()
{
    $foo = $_SERVER['REMOTE_ADDR'];
    if(substr($foo, 0, 10) == "192.168.0.")
    {
	return true;
    }
    return false;
}

?>