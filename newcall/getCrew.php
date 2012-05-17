<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-01-26 16:05:32 jantman"                                                              |
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
 | $HeadURL:: http://svn.jasonantman.com/newcall/getCrew.php                                            $ |
 +--------------------------------------------------------------------------------------------------------+
*/

require_once('inc/newcall.php.inc');
require_once('inc/crew.php');

if(isset($_GET['type']) && isset($_GET['date']) && isset($_GET['time']))
{
    $type = $_GET['type'];
    $date = $_GET['date'];
    $time = $_GET['time'];
    $ts = strtotime($date." ".$time.":00");
}
else
{
    error_log("getCrew.php: invalid arguments.");
    die("ERROR: invalid arguments");
}

$foo = getDutyCrewByTS($ts);
$s = "";
foreach($foo as $val)
{
    $s .= $val.",";
}
$s = trim($s, ", ");

echo $s;

?>

