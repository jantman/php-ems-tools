<?php
//
// inc/i18n_EN_US.php
//
// this is the file that contains the dictionary for internationalization (i18n)
// it should be included through a statement in config/config.php
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
// | $LastChangedRevision:: 155                                         $ |
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/inc/i18n_EN_US#$ |
// +----------------------------------------------------------------------+


// TODO - only partially implemented

// master array to hold all strings, which will be printed literally in the HTML
$i18n_strings = array();

$i18n_strings["signOn"]["Action"] = "Action";
$i18n_strings["signOn"]["Sign On"] = "Sign On";
$i18n_strings["signOn"]["Edit"] = "Edit";
$i18n_strings["signOn"]["Remove"] = "Remove";
$i18n_strings["signOn"]["ID Num"] = "ID#";
$i18n_strings["signOn"]["Start Time"] = "Start Time";
$i18n_strings["signOn"]["End Time"] = "End Time";
$i18n_strings["signOn"]["Reset"] = "Reset";
$i18n_strings["signOn"]["Cancel"] = "Cancel";
$i18n_strings["signOn"]["Submit"] = "Submit";
$i18n_strings["signOn"]["changingPast"] = "For changing past only";
$i18n_strings["signOn"]["adminID"] = "Administrator ID#";
$i18n_strings["signOn"]["adminPW"] = "Password";

$i18n_strings["signOnWarnings"]["noDBconnect"] = "I'm sorry, the MySQL connection failed at mysql_connect.";
$i18n_strings["signOnWarnings"]["noDBselect"] = "I'm sorry, I was unable to select the database!";
$i18n_strings["signOnWarnings"]["authQueryError"] = "Auth Query Error";
$i18n_strings["signOnWarnings"]["errorSignOn"] = "Either your ID/password is incorrect or you are not authorized to perform this action (signing on).";
$i18n_strings["signOnWarnings"]["errorEdit"] = "Either your ID/password is incorrect or you are not authorized to perform this action (editing a signon).";
$i18n_strings["signOnWarnings"]["errorRemove"] = "Either your ID/password is incorrect or you are not authorized to perform this action (removing a signon).";
$i18n_strings["signOnWarnings"]["errorChangePast"] = "Your user is not authorized to change a signon from the past. You must login with a username and password, or contact an administrator.";
$i18n_strings["signOnWarnings"]["errorMemberType1"] = "I'm sorry, but a member of type";
$i18n_strings["signOnWarnings"]["errorMemberType2"] = "cannot sign up for duty.";
$i18n_strings["signOnWarnings"]["errorTimeInvalid"] = "I'm sorry, but the times you selected are invalid.";
$i18n_strings["signOnWarnings"]["errorOverlap"] = "Your are already signed on for a portion of this time. You cannot have two signons that overlap.";
//$i18n_strings["signOnWarnings"][""] = "";


?>