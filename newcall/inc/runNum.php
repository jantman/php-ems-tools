<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-23 23:41:27 jantman"                                                              |
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
 | $HeadURL:: http://svn.jasonantman.com/newcall/inc/runNum.php                                         $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * Run number formatting and generation functions.
 *
 * @package MPAC-NewCall
 */

  /**
   * Get the next run number for a given year
   *
   * @param int $year 
   * @return int next run number
   */
function getNewRunNumber($year)
{
    $year = (int)$year;
    $query = "SELECT COUNT(*) AS cnt FROM calls WHERE YEAR(date_date)=$year;";
    $result = mysql_query($query);
    if(! $result){ db_error($query, mysql_error());}
    $row = mysql_fetch_assoc($result);
    return $year.str_pad($row['cnt']+1, 3, "0", STR_PAD_LEFT);
}

/**
 * Format a run number as a string with a hyphen.
 *
 * @param int $num RunNumber
 * @return string
 */
function formatRunNum($num)
{
    return substr($num, 0, 4)."-".substr($num,4);
}

?>