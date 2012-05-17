<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-23 23:39:47 jantman"                                                              |
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
 | $LastChangedRevision:: 49                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/inc/PCRvalidation.php                                  $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * Session-related functions, including setting timeout and calling session_start()
 *
 * @package MPAC-NewCall
 */


// must be included AFTER inc/newcall.php.inc
session_start();

define("SESS_LOAD_TIMEOUT", 600); // if page last loaded more than X seconds ago, kill session
define("SESS_TIMEOUT", 600); // session timeout in seconds

// return values:
// 0 - valid
// 1 - not started yet (no complete data in DB)
// 2 - not authenticated
// 3 - expired
function is_valid_session()
{
    $query = "SELECT * FROM web_sessions WHERE sessid='".session_id()."';";
    $result = mysql_query($query);
    if(mysql_num_rows($result) < 1){ return 1;}
    $row = mysql_fetch_assoc($result);
    if(time() >= $row['expire_ts']) { kill_session(); return 3;}
    if(! isset($row['EMTid'])){ return 2;}
    if(! isset($_SESSION['EMTid'])){ return 2;}
    if($_SERVER['REMOTE_ADDR'] != $row['remote_ip']){ kill_session(); return 2;}
    if($row['last_load_ts'] < time() - SESS_LOAD_TIMEOUT){ kill_session(); return 1;}
    return 0;
}

function login_start_sess($userid, $pass)
{
    if(ldap_auth_handler($userid, $pass) == false)
    {	
	error_log("login_start_sess: ldap_auth_handler returned False for ($userid)");
	return false;
    }
    $query = "INSERT INTO web_sessions SET sessid='".session_id()."',start_ts=".time().",last_load_ts=".time().",EMTid='".mysql_real_escape_string($userid)."',remote_ip='".mysql_real_escape_string($_SERVER['REMOTE_ADDR'])."',user_agent='".mysql_real_escape_string($_SERVER['HTTP_USER_AGENT'])."',expire_ts=".(time() + SESS_TIMEOUT).";";
    $result = mysql_query($query);
    $_SESSION['EMTid'] = $userid;
    return true;
}

function ldap_auth_handler($userid, $pass)
{
    global $debug;
    ldapSetup();
    $groupDN = "cn=AllActive,ou=groups,dc=midlandparkambulance,dc=com";
    $foo = authByLDAP($userid, $pass, $groupDN);
    if($foo != 0){ error_log("ldap_auth_handler: authByLDAP returned $foo for user $userid."); return false; }
    return true;
}

function update_session_time()
{
    $query = "UPDATE web_sessions SET last_load_ts=".time()." WHERE sessid='".session_id()."';";
    $result = mysql_query($query);
}

function kill_session()
{
    $query = "DELETE FROM web_sessions WHERE sessid='".session_id()."';";
    $result = mysql_query($query);
    session_unset();
    session_destroy();
}


?>