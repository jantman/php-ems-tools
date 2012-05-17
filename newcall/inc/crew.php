<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-23 23:44:10 jantman"                                                              |
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
 | $LastChangedRevision:: 66                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/inc/crew.php                                           $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * Functions dealing with crew.
 *
 * @package MPAC-NewCall
 */

function getDutyCrewByTS($ts)
{
    $query = "SELECT EMTid FROM schedule WHERE start_ts <= $ts AND end_ts >= $ts AND deprecated=0;";
    $result = mysql_query($query) or die("ERROR: Error in query: $query ERROR: ".mysql_error());
    $foo = array();
    while($row = mysql_fetch_assoc($result))
    {
	$foo[] = $row['EMTid'];
    }
    return $foo;
}

function getProbiesByEMTid()
{
    $query = "SELECT EMTid FROM roster WHERE status='Probie';";
    $result = mysql_query($query) or die("ERROR: Error in query: $query ERROR: ".mysql_error());
    $foo = array();
    while($row = mysql_fetch_assoc($result))
    {
	$foo[] = $row['EMTid'];
    }
    return $foo;
}

function getCurrentDBname()
{
    $query = "SELECT DATABASE() AS name;";
    $result = mysql_query($query) or die("ERROR: Error in query: $query ERROR: ".mysql_error());
    $row = mysql_fetch_assoc($result);
    return $row['name'];
}

?>