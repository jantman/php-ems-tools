<?php
// Time-stamp: "2010-09-13 10:20:25 jantman"
// +----------------------------------------------------------------------+
// | MPAC/PHP EMS Tools Finance Component - http://www.php-ems-tools.com  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2009 Jason Antman.                                     |
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
// | $LastChangedRevision:: 7                                           $ |
// | $HeadURL:: http://svn.jasonantman.com/mpac-finance/inc/common.php  $ |
// +----------------------------------------------------------------------+

function smarty_add_row($query, $varName)
{
    global $smarty;
    $result = mysql_query($query) or db_error($query, mysql_error());
    while($row = mysql_fetch_assoc($result))
    {
	$smarty->append($varName, $row);
    }
    mysql_free_result($result);
}

function db_error($query, $error, $isQuery = true)
{
    if($isQuery)
    {
	$msg = '<div class="dbError"><strong>Error in Query:</strong> '.$query.'<br /><strong>Error:</strong> '.$error.'</div>';
	echo $msg;

	error_log("FINANCE - DBerror - ".$_SERVER["PHP_SELF"]." - $query - ".mysql_error());

	die();
    }
    $msg = '<div class="dbError"><strong>'.$query.'</strong><br /><strong>Error:</strong> '.$error.'</div>';
    echo $msg;
    die();
}

function trans_start()
{
    $query = "SET autocommit=0;";
    $result = mysql_query($query) or db_error($query, mysql_error(), false);
    $query = "START TRANSACTION;";
    $result = mysql_query($query) or db_error($query, mysql_error(), false);
}

function trans_safe_query($query)
{
    $result = mysql_query($query);
    if(! $result)
    {
	$query2 = "ROLLBACK;";
	$error = mysql_error();
	$result2 = mysql_query($query2);
	db_error($query, $error, false);
    }
    return $result;
}

function trans_commit()
{
    $query = "COMMIT;";
    $result = mysql_query($query);
    if(! $result)
    {
	$query2 = "ROLLBACK;";
	$error = mysql_error();
	$result = mysql_query($query2);
	db_error($query, $error, false);
    }
}

function trans_rollback()
{
    $query = "ROLLBACK;";
    $result = mysql_query($query);
    if(! $result)
    {
	$query2 = "ROLLBACK;";
	$error = mysql_error();
	$result = mysql_query($query2);
	db_error($query, $error, false);
    }
}

function var_dump_string($v)
{
    $s = "";
    ob_start();
    var_dump($v);
    $s = ob_get_clean();
    return $s;
}

function getMemberList($order = "EMTid", $selectPrompt = false)
{
    $query = "SELECT EMTid,FirstName,LastName FROM roster WHERE status != 'Resigned' ORDER BY $order;";
    $result = mysql_query($query) or db_error($query, mysql_error());
    $foo = array();
    while($row = mysql_fetch_assoc($result))
    {
	$foo[$row['EMTid']] = array('LastName' => $row['LastName'], 'FirstName' => $row['FirstName'], 'EMTid' => $row['EMTid']);
    }
    $foo['MPAC'] = array('LastName' => 'z MPAC', 'FirstName' => 'Building', 'EMTid' => 'MPAC');
    return $foo;
}

?>