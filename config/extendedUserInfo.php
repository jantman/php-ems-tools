<?php
// config/extendedUserInfo.php
//
// Defines extended positions, certifications, etc.
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

// Extended Certifications:
// to use certifications other than the default, define them in an array here. 
// if you don't want to use any, just leave tha array blank.
// you cannot have a comma in any of the certification strings!

$extdCerts = array("ALS", "OtherCert");

// Extended Types:
// this allows a second set of member types such as driver, medic, trainee, etc.
// this is intended for uses where members have MULTIPLE types.
// if your members only have one type, please use the member types array in config.php 
// and leave this as an empty array.

$extdTypes = array("EMT/Medic", "Driver", "AIC", "Probie");

?>