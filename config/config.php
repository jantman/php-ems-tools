<?php
// config/config.php
//
// Main configuration file.
//
// +----------------------------------------------------------------------+
// | PHP EMS Tools      http://www.php-ems-tools.com                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006, 2007 Jason Antman.                               |
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
//      $Id$

// INSTRUCTIONS:
// set the variables in the file to the appropriate values for your squad.
// It may be helpful to know some PHP.
// If you do not, I have included some basic instructions in the comments.
// after each variable, I have included the variable type
// on the line before each, I have invluded a description of it

// FOR ALL PROGRAMS IN THE PACKAGE:

// this is the full name of your organization
$orgName = "Ambulance Corps"; // string
// this is the short name/abbreviation for your organization
$shortName = "AC"; // string
// this is the base URL of the folder which php_ems resides in
// with a trailing /, as seen from the rest of the world.
$serverWebRoot = "http://jantman.dyndns.org:10011/cvswork/php-ems-tools-trunk/"; // string
// this is the name of the database on the server used by php-ems-tools
// the default is php-ems-tools
$dbName = "php_ems_tools_devel";

?>
