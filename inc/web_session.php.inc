<?php

// inc/web_session.php.inc

// this script handles everything related to authentication and sessions for the non-schedule parts.

// WARNING: prior to calling anything from this script, you must establish a DB connection and include the configs.
// WARNING: this should be the FIRST thing on the page, before ANY headers.

function php_ems_authenticate($EMTid, $pass)
{
    // checks login credentials.
    // returns false on failure or an array on success
    // return array: rightsLevel, status
    $query  = "SELECT EMTid,rightsLevel,status,pwdMD5,FirstName,LastName FROM roster WHERE EMTid=".mysql_real_escape_string($EMTid).";";
    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
    if(mysql_num_rows($result)==0)
    {
	return false;
    }
    while ($row = mysql_fetch_array($result))
    {
	if($row['pwdMD5']<>md5($pass))
	{
	    //provided password does not match database
	    return false;
	}
	else
	{
	    $temp = array("rightsLevel" => $row['rightsLevel'], "status" => $row['status'], "name" => ($row['FirstName']." ".$row['LastName']));
	    return $temp;
	}
    }
    return $false; // default to deny
}

function php_ems_validSession()
{
    // returns true if the user has a valid session, otherwise false
    if(! isset($_COOKIE) && ! isset($_COOKIE['PHPSESSID']))
    {
	return false;
    }
    // else, make sure it's active in the DB...
    session_start();
    $sessid = session_id();
    // find if we've already logged this sessid in the database
    $query = "SELECT sessid,last_load_ts,expire_ts FROM web_sessions WHERE sessid='".mysql_real_escape_string($sessid)."' AND expire_ts>=".time().";";
    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
    if(mysql_num_rows($result) >= 1)
    {
	return true;
    }
    // else we didn't have a valid session    
    return false;
}

function php_ems_auth_handler($minRightsLevel)
{
    // handles authentication logic
    // takes as an argument the minimum rights level required to perform an action
    
    // first, check if the user has a session started
    $hasValidSession = php_ems_validSession();
    if($hasValidSession)
    {
	// IF SO, check rightsLevel of user against minRightsLevel
	if($_SESSION['rightsLevel'] < $minRightsLevel)
	{
	    // TODO: throw rights level error.
	}
	else
	{
	    // pass through to the requested document - i.e. do nothing, let the calling script continue
	}
    }
    else
    {
	// no session, redirect us to a login page.
	$requestURL = urlencode($_SERVER['REQUEST_URI']);
	header("Location: login.php?action=login&request=".$requestURL); // redirect to the login page
    }
}

function php_ems_login_handler($EMTid, $pass, $requestURL)
{
    // handles the login action
    $result = php_ems_authenticate($EMTid, $pass);
    if(! $result)
    {
	header("Location: login.php?action=failed&request=".urlencode($requestURL));
    }
    else
    {
	// start a session, register cookie
	session_start();
	$_SESSION['EMTid'] = $EMTid;
	$_SESSION['rightsLevel'] = $result['rightsLevel'];
	$_SESSION['membername'] = $result['name'];
	register_session_in_db(session_id(), $EMTid); // register the session in the database
	// redirect to the requested page
	header("Location: ".$requestURL);
    }
}

function register_session_in_db($sessid, $EMTid)
{
    // registers a session in the database
    global $session_timeout_seconds;
    $query = "SELECT sessid FROM web_sessions WHERE sessid='".mysql_real_escape_string($sessid)."';";
    $result = mysql_query($query) or die("Error adding session to database.");
    if(mysql_num_rows($result) > 0)
    {
	$query = "UPDATE web_sessions SET last_load_ts=".time()." WHERE sessid='".mysql_real_escape_string($sessid)."';";
	$result = mysql_query($query) or die("Error adding session to database.");
    }
    else
    {
	$query = "INSERT INTO web_sessions SET sessid='".$sessid."',EMTid='".$EMTid."',start_ts=".time().",expire_ts=".(time()+$session_timeout_seconds).",remote_ip='".$_SERVER['REMOTE_ADDR']."',user_agent='".mysql_real_escape_string($_SERVER['HTTP_USER_AGENT'])."';";
	$result = mysql_query($query) or die("Error adding session to database.");
    }
}

function php_ems_kill_session()
{
    session_start();
    $sessid = session_id();
    // kill the DB record
    $query = "UPDATE web_sessions SET expire_ts=".(time()-1)." WHERE sessid='".$sessid."';";
    $result = mysql_query($query) or die("Error adding session to database.");
    session_destroy();
    // TODO: we need to destroy the cookie to really remove session info.
}

?>