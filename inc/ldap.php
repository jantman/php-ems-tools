<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-24 22:36:35 jantman"                                                              |
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
 * Functions related to LDAP.
 *
 * @package MPAC-NewCall-PHP
 */

require_once('config/ldap.php');

function ldapSetup()
{
    global $ds, $bindDN, $bindPass, $dbName;
    // LDAP STUFF
    $ds = ldap_connect();   
    if( !$ds )
    {
        if($debug) { error_log("ldap_auth.php: Error in contacting the LDAP server (debug 1)");}
        return false;
    }
    if (! ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3))
    {
        if($debug) { error_log("ldap_auth.php: Failed to set protocol version to 3");}
        return false;
    }

    //Connection made -- bind as manager/admin
    $bind = ldap_bind($ds, $bindDN, $bindPass);

    //Check to make sure we're bound.
    if( !$bind )
    {
        die("Bind as $bindDN failed.\n");
    }
}

function authByLDAP($username, $password, $groupDN)
{
    /*
     / @param username
     / @param password
     / @param groupDN - DN of group to require
     / @param debug - whether to log debug info or not
     /
     / @return 0 on success, 1 on invalid credentials, 2 on group error, 3 on system error
    */

    if($debug) { error_log("ldap_auth.php: Attempting LDAP Auth username=".$username);}

    global $topdn, $debug;

    $ds = ldap_connect();
    
    if( !$ds )
    {
        if($debug) { error_log("ldap_auth.php: Error in contacting the LDAP server (debug 1)");}
	return 3;
    }

    if (! ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3))
    {
	if($debug) { error_log("ldap_auth.php: Failed to set protocol version to 3");}
	return 3;
    }
    
    //Connection made -- bind anonymously and get dn for username.
    $bind = @ldap_bind($ds);
    
    //Check to make sure we're bound.
    if( !$bind )
    {
        if($debug) { error_log("ldap_auth.php: Anonymous bind to LDAP FAILED.  Contact Tech Services! (Debug 2)");}
	return 3;
    }
    
    $search = ldap_search($ds, $topdn, "uid=$username"); // do the search
    if($debug){ error_log("ldap_auth.php: searcing established ds with topdn: $topdn search: 'uid=$username'");}

    //Make sure only ONE result was returned -- if not, they might've thrown a * into the username.  Bad user!
    if( ldap_count_entries($ds,$search) != 1 )
    {
        if($debug) { error_log("ldap_auth.php: Error processing username -- please try to login again. (Debug 3) - more than one entry returned.");}
	return 3;
    }
    
    $info = ldap_get_entries($ds, $search);
    
    $userdn = $info[0]['dn'];
    if($debug) { error_log("ldap_auth.php: GOT dn for user: ".$userdn);}

    //Now, try to rebind with their full dn and password.
    $bind = @ldap_bind($ds, $info[0]['dn'], $password);
    if( !$bind || !isset($bind))
    {
	if($debug) { error_log("ldap_auth.php: AUTH failed: ".ldap_error($ds)." (".ldap_errno($ds).")");}
	return 1;
    }
    
    //Now verify the previous search using their credentials.
    $search = ldap_search($ds, $topdn, "uid=$username");
        
    $info = ldap_get_entries($ds, $search);
    if( $username == $info[0]['uid'][0] )
    {
        if($debug) { error_log("ldap_auth.php: AUTH OK"); }
        $fullname = $info[0][cn][0];
    }
    else
    {
	if($debug) { error_log("ldap_auth.php: AUTH failed: ".ldap_error($ds)." (".ldap_errno($ds).")"); }
	return 1;
    }

	//$search = ldap_search($ds, $groupDN, $userdn);
	//$info = ldap_get_entries($ds, $search);

    $members = getGroupMembers($ds, $groupDN);
    if(! in_array($userdn, $members))
    {
	ldap_close($ds);
	return 2;
    }
    else
    {
	$_SESSION['username'] = $username;
	$_SESSION['fullname'] = $fullname;
	return 0; // both user and group ok
    }

    return false;
}

function authWrapper($username, $pass, $groupDN)
{
    $foo = authByLDAP($username, $pass, $groupDN);
    if($foo == 0)
    {
	// do nothing, auth OK
	return true;
    }
    elseif($foo == 1)
    {
	echo '<p style="color: red;"><strong>ERROR:</strong> Invalid username or password.</p>';
    }
    elseif($foo == 2)
    {
	echo '<p style="color: red;"><strong>ERROR:</strong> User is not a member of the required group.</p>';
    }
    else
    {
	echo '<p style="color: red;"><strong>ERROR:</strong>Unknown error occurred in LDAP lookup.</p>';
    }
    return false;
}

// from mpac_ldap_funcs.php.inc in /root/bin/
function getGroupMembers($ds, $groupDN)
{
    if(is_string($groupDN))
    {
	$ri = ldap_read($ds, $groupDN, "objectClass=*");
	if(! $ri){ return false;}
	$foo = ldap_get_entries($ds, $ri);
	$foo = $foo[0]['member'];
	if(isset($foo['count'])){ unset($foo['count']);}
    }

    $recurse = array();
    foreach($foo as $key => $val)
    {
	if(strstr($val, "ou=groups")){ $recurse[] = $val;}
    }
    
    // else is array
    $result = array();
    foreach($recurse as $key => $val)
    {
	$ri = ldap_read($ds, $val, "objectClass=*");
	if(! $ri){ return false;}
	$foo = ldap_get_entries($ds, $ri);
	$foo = $foo[0]['member'];
	if(isset($foo['count'])){ unset($foo['count']);}
	$result = array_merge($result, $foo);
    }
    return $result;
}


?>