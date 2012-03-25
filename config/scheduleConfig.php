<?php
// config/scheduleConfig.php
//
// schedule configuration file.
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
//      $Id: scheduleConfig.php 118 2008-11-02 23:09:37Z jantman $

// FOR THE SCHEDULE:

// this is the default number of crews per day. It can be either 1 or 2
// 1 crew will allow 6 members
// 2 crews will allow 3 members per crew
$defaultCrews = 1; // integer

// this determines whether the schedule will show names or ID#'s
// if names, it will display the "shownAs" field from the database
// 1 will show names, 0 will show ID's
$showNames = 1; // integer

// whether or not to show times after name if signed on for complete shift
$showTimeCompleteShift = false;

//time format displayed after names/IDs on schedule
// 1: name 6-18
// 2: name 0600-1800
// 3: name 06:00-18:00
// default is 3
$schedTimeFormat = 3;

//AUTHENTICATION
// what actions does the user need to enter their ID and password for?

// require auth to sign on schedule?
$requireAuthToSignOn = false;

//require auth to edit existing sign-ons?
$requireAuthToEdit = false;

//require auth to remove entries from schedule?
$requireAuthToRemove = false;

//require auth to change entries from shifts before current?
$requireAuthToChangePast = true;

//require auth to change the daily message
$requireAuthDailyMessage = true;

// minimum rightsLevel to edit/remove
// should be 1 or 2
$minRightsEdit = 1;

// minimum rightsLevel to change past
// should be 1 or 2
$minRightsChangePast = 2;

// minimum rights level to edit daily message
$minRightsDailyMessage = 2;

// at the moment, this program is hard-coded with 2 shifts per day
// days - 0600-1800
// nights - 1800-0600
// the date of the night schedule is the date that it BEGINS
// i.e. for January 1st, the day is 0600-1800 January 1
//             and the night is 1800 on January 1 - 0600 on January 2

$dayFirstHour = "06";
$dayLastHour = "17";
$nightFirstHour = "18";
$nightLastHour = "05";

/*
function showHoursSetup()
{
    echo "Hours Setup: dayFirstHour=".$dayFirstHour." dayLastHour=".$dayLastHour." nightFirstHour=".$nightFirstHour." nightLastHour=".$nightLastHour."<br>";
}
*/

?>