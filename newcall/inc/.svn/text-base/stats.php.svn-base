<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-23 23:39:25 jantman"                                                              |
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
 | $HeadURL:: http://svn.jasonantman.com/newcall/inc/newcall.php.inc                                    $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * Simple statistics functions. DEPRECATED. MOVE THIS ELSEWHERE.
 *
 * @deprecated r64
 * @todo remove this file, merge one function somewhere else -jantman 2010-08-23
 * @package MPAC-NewCall
 */

/**
 * Return array of members (EMTid) on a specified call.
 *
 * @param int $runNum the run number (integer)
 * @return array array of EMTids
 */
function getMembersOnCall($runNum)
{
    $a = array();
    $query = "SELECT DISTINCT EMTid FROM calls_crew WHERE RunNumber=$runNum AND is_deprecated=0;";
    $result = mysql_query($query) or die("Error in query: $query <br />ERROR: ".mysql_error());
    while($row = mysql_fetch_assoc($result))
    {
	$a[] = $row['EMTid'];
    }
    return $a;
}



?>