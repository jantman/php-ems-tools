<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-25 22:48:43 jantman"                                                              |
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
 | $LastChangedRevision:: 67                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/inc/version.php                                        $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * Variables for version, SVN revision, SVN head; stripSVNstuff() function to get SVN rev as clean string
 *
 * @package MPAC-NewCall-PHP
 */

/**
 * Variable to hold LastChangedRevision string from SVN. Variable content is updated by SVN, making it accessible to the scripts.
 * 
 * @global string $_SVN_rev
 */
$_SVN_rev = "\$LastChangedRevision: 67 $";
/**
 * Variable to hold the HeadURL SVN string. Variable content is updated by SVN, making it accessible to the scripts.
 * 
 * @global string $_SVN_headURL
 */
$_SVN_headURL = "\$HeadURL: http://svn.jasonantman.com/newcall/inc/version.php $"; 
/**
 * Current human-readable version string for the program
 *
 * @global string $_VERSION
 */
$_VERSION = "1.1";

/**
 * Extract the numeric revision number from a SVN LastChangedRevision string, return it
 *
 * @param string $s svn LastChangedRevision string
 * @return int revision number
 */
function stripSVNstuff($s)
{
    $s = substr($s, strpos($s, ":")+1);
    $s = str_replace("$", "", $s);
    return trim($s);
} 

?>