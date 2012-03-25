<?php
// config/rosterConfig.php
//
// Configuration file for roster - member types, positions, etc.
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
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/config/rosterC#$ |
// +----------------------------------------------------------------------+

// MEMBER TYPES
// this defines the types of members for roster and schedule, as well as how many hours per month they are required to do, how many of them are required to complete a crew, how they show up in the roster, and whether they can sign on duty
// for each member type, we have the above six values
//  EXAMPLE:
//  EMT
//$memberTypes[0]['name'] = "EMT";
//$memberTypes[0]['requiredHours'] = 30;
//$memberTypes[0]['crewRequires'] = 2;
//$memberTypes[0]['rosterName'] = "";
//$memberTypes[0]['canPullDuty'] = true;
//$memberTypes[1]['shownInShort'] = true;
//  Probie
//$memberTypes[1]['name'] = "Probie";
//$memberTypes[1]['requiredHours'] = 30;
//$memberTypes[1]['crewRequires'] = 0;
//$memberTypes[1]['rosterName'] = "P";
//  Driver/Lifter
//$memberTypes[2]['name'] = "Driver/Lifter";
//$memberTypes[2]['requiredHours'] = 0;
//$memberTypes[2]['crewRequires'] = 0;
//$memberTypes[2]['rosterName'] = "D";
//  The above statements define an situation with 5 member types = EMT, Probie, Driver/Lifter, Resigned, and Inactive Life. A crew requires 2 EMT's, 0 (or more) Probies, and 0 (or more) Driver/Lifters. The EMT's and Probies are required to have 30 hours on duty each month, driver/lifters have no required duty hours. The EMTs and Probies can sign on the schedule, the Driver/Lifters cannot (they are on a second-call basis only). In the roster, under "Type", EMT's display nothing, whereas Driver/Lifters and Probies display "D" or "P", respectively.

// the shownInShort field determines whether members of this type are shown on the short roster (IDs and names only). This is convenient for inactive members which are still listed on the roster, such as (Inactive) Life members or recently resigned members for whom contact information needs to be retained.

// the shownInRoster field determines whether the type will be shown on the regular (or short) roster. If not, it will only be visible to administrative view. This is good for resigned members.

//EMT
$memberTypes[0]['name'] = "Senior";
$memberTypes[0]['requiredHours'] = 30;
$memberTypes[0]['crewRequires'] = 2;
$memberTypes[0]['rosterName'] = "";
$memberTypes[0]['canPullDuty'] = true;
$memberTypes[0]['shownInShort'] = true;
$memberTypes[0]['shownInRoster'] = true;
//Probie
$memberTypes[1]['name'] = "Probie";
$memberTypes[1]['requiredHours'] = 30;
$memberTypes[1]['crewRequires'] = 0;
$memberTypes[1]['rosterName'] = "P";
$memberTypes[1]['canPullDuty'] = true;
$memberTypes[1]['shownInShort'] = true;
$memberTypes[1]['shownInRoster'] = true;
//Driver/Lifter
$memberTypes[2]['name'] = "Driver";
$memberTypes[2]['requiredHours'] = 0;
$memberTypes[2]['crewRequires'] = 0;
$memberTypes[2]['rosterName'] = "D";
$memberTypes[2]['canPullDuty'] = false;
$memberTypes[2]['shownInShort'] = true;
$memberTypes[2]['shownInRoster'] = true;
//Resigned
$memberTypes[3]['name'] = "Resigned";
$memberTypes[3]['requiredHours'] = 0;
$memberTypes[3]['crewRequires'] = 0;
$memberTypes[3]['rosterName'] = "R";
$memberTypes[3]['canPullDuty'] = false;
$memberTypes[3]['shownInShort'] = false;
$memberTypes[3]['shownInRoster'] = false;
//Inactive Life
$memberTypes[4]['name'] = "Inactive Life";
$memberTypes[4]['requiredHours'] = 0;
$memberTypes[4]['crewRequires'] = 0;
$memberTypes[4]['rosterName'] = "I";
$memberTypes[4]['canPullDuty'] = false;
$memberTypes[4]['shownInShort'] = false;
$memberTypes[4]['shownInRoster'] = false;
//Life
//$memberTypes[5]['name'] = "Active Life";
//$memberTypes[5]['requiredHours'] = 0;
//$memberTypes[5]['crewRequires'] = 0;
//$memberTypes[5]['rosterName'] = "L";
//$memberTypes[5]['canPullDuty'] = true;
//$memberTypes[5]['shownInShort'] = true;
//$memberTypes[5]['shownInRoster'] = true;

//defines the default type that is selected in the add member to roster form
$typeDefault = $memberTypes[1]['name'];

// FOR THE ROSTER:
// minimum rights level to add/edit/remove members
$minRightsRoster = 1;

// this determines whether, if a member has a unitID defined, it will be displayed instead of the EMTid
$showUnitID = true;

// this determines the possible officer positions
$officerPositions = array(0 => 'None', 1 => 'Captain', 2 => '1st Lieutenant', 3 => '2nd Lieutenant');

// this determines the possible non-officer positions
$positions = array(0 => 'None', 1 => 'President', 2 => '1st VP', 3 => '2nd VP', 4 => 'Secretary', 5 => 'Treasurer');

// this defines the committees and possible positions
$committees = array(0 => 'None', 1 => 'Good & Welfare', 2 => 'Building & Grounds', 3 => 'Awards', 4 => 'Publicity', 5 => 'Membership', 6 => 'Computers', 7 => 'Fund Drive', 8 => 'Grievance', 9 => '20th District', 10 => 'Uniforms', 11 => 'By-Laws', 12 => 'Insurance', 13 => 'Safety', 14 => 'Points & LOSAP', 15 => 'Activities');
$commPositions = array(0 => 'None', 1 => 'Chairman', 2 => 'Co-Chairman', 3 => 'Member');

// Extended Certifications:
// to use certifications other than the default, define them in an array here. 
// if you don't want to use any, just leave tha array blank.
// you cannot have a comma in any of the certification strings!

$extdCerts = array("ICS100", "ICS200", "ICS700/NIMS", "ALS", "OtherCert");

// Extended Types:
// this allows a second set of member types such as driver, medic, trainee, etc.
// this is intended for uses where members have MULTIPLE types.
// if your members only have one type, please use the member types array in config.php 
// and leave useExtdTypes = false.

$useExtdTypes = true; // whether or not to use the array below as an option

$extdTypes = array("EMT/Medic", "Driver", "AIC", "Probie"); // extended member types

?>