<?php 
// inc/logging.php
//
// Handles logging of schedule changes.
//
// +----------------------------------------------------------------------+
// | PHP EMS Tools	http://www.php-ems-tools.com                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006, 2007 Jason Antman.	                          |
// |                                                                      |
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation; either version 3 of the License, or    |
// | (at your option) any later version.                                  |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to:                           |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+
// |Please use the above URL for bug reports and feature/support requests.|
// +----------------------------------------------------------------------+
// | Authors: Jason Antman <jason@jasonantman.com>                        |
// +----------------------------------------------------------------------+
//	$Id$


function logEditForm($original_entry_id, $new_entry_id, $admin_username, $is_authenticated, $form, $action, $queries)
{
    // logs a change to the schedule
    $query = "INSERT INTO schedule_changes SET ";
    if($original_entry_id != null){ $query .= "deprecated_sched_ID=".$original_entry_id.",";}
    if($new_entry_id != null){ $query .= "deprecated_by_sched_ID=".$new_entry_id.",";}
    if(trim($admin_username) != ""){ $query .= "admin_username='".mysql_real_escape_string($admin_username)."',";}
    if($is_authenticated){ $query .= "admin_auth_success=1,";}
    $query .= "form='".mysql_real_escape_string($form)."',action='.".$action.".',";
    $query .= "queries='".make_safe($queries)."',";
    $query .= "remote_host='".mysql_real_escape_string($_SERVER["REMOTE_ADDR"])."',";
    if(trim($_SERVER['PHP_AUTH_USER']) != ""){ $query .= "php_auth_username='".mysql_real_escape_string($_SERVER['PHP_AUTH_USER'])."',";}
    if(trim($_SERVER['AUTH_TYPE']) != ""){$query .= "auth_type='".mysql_real_escape_string($_SERVER['AUTH_TYPE'])."',";}
    $query .= "change_ts=".time().";";
    $result = mysql_query($query) or die ("Query Error");
}

function logMessageForm($original_entry_id, $new_entry_id, $admin_username, $is_authenticated, $form, $action, $queries)
{
    // logs a change to a schedule message
    $query = "INSERT INTO schedule_message_changes SET ";
    if($original_entry_id != null){ $query .= "deprecated_message_ID=".$original_entry_id.",";}
    if($new_entry_id != null){ $query .= "deprecated_by_message_ID=".$new_entry_id.",";}
    if(trim($admin_username) != ""){ $query .= "admin_username='".mysql_real_escape_string($admin_username)."',";}
    if($is_authenticated){ $query .= "admin_auth_success=1,";}
    $query .= "form='".mysql_real_escape_string($form)."',action='.".$action.".',";
    $query .= "queries='".make_safe($queries)."',";
    $query .= "remote_host='".mysql_real_escape_string($_SERVER["REMOTE_ADDR"])."',";
    if(trim($_SERVER['PHP_AUTH_USER']) != ""){ $query .= "php_auth_username='".mysql_real_escape_string($_SERVER['PHP_AUTH_USER'])."',";}
    if(trim($_SERVER['AUTH_TYPE']) != ""){$query .= "auth_type='".mysql_real_escape_string($_SERVER['AUTH_TYPE'])."',";}
    $query .= "change_ts=".time().";";
    $result = mysql_query($query) or die ("Query Error");
}

?>