<?php

//(C) 2006 Jason Antman. All Rights Reserved.
// with questions, go to www.jasonantman.com
// or email jason AT jasonantman DOT com
// Time-stamp: "2007-03-21 01:17:03 jantman"

//This software may not be copied, altered, or distributed in any way, shape, form, or means.
// version: 0.1 as of 2006-10-3

// INFORMATION:
// this is the customization file attached to the php_ems package
// this file MUST be in place for the packages to work.

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
$serverWebRoot = "http://192.168.1.107/"; // string
// this is the root for the external/auth folder
$serverExtRoot = "http://yourdomain/auth/"; // string
// this is the name of the database on the server used by php-ems-tools
// the default is php-ems-tools
$dbName = "php-ems-tools";


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

function showHoursSetup()
{
    echo "Hours Setup: dayFirstHour=".$dayFirstHour." dayLastHour=".$dayLastHour." nightFirstHour=".$nightFirstHour." nightLastHour=".$nightLastHour."<br>";
}

//
// This code defines the items for the RIG CHECK sheet.
//
// setup is as follows - 
// the rigCheckData array holds arrays for the sections - each section is highlighted in bold
// each section holds a number of items.
//

// the information is displayed in three tables as columns within one master table. 
// the variables table2start and table3start determine the first item (index for rigCheckData) that appears at the top of each column.
//

$rigCheckData = array();
$rigCheckData[0]['name'] = 'Exterior Compartment I';
$rigCheckData[0]['items'] = array(0 => 'Disaster Packs (3)', 1 => 'Stair Chair', 2 => 'Onboard Oxygen ( > 500 lbs)');
$rigCheckData[1]['name'] = 'Exterior Compartment II';
$rigCheckData[1]['items'] = array(0 => 'Collars - 2 Adjustable', 1 => 'Pediatric, Baby No-Neck', 2 => 'KED (2)', 3 => 'Add-a-Splint', 4 => 'Head Blocks (2)', 5=> 'Head Beds');

$rigCheckData[2]['name'] = 'Exterior Compartment III';
$rigCheckData[2]['items'] = array(0 => 'Helmets and Safety Goggles (3)', 1 => 'Work Gloves (3)', 2 => 'Rope', 3 => 'Disposable blankets (3)');
$rigCheckData[3]['name'] = 'Exterior Compartment IV';
$rigCheckData[3]['items'] = array(0 => 'Backboards w/ Straps (2)', 1 => 'Scoop Stretcher', 2 => 'Reeves (2)');
$rigCheckData[4]['name'] = 'Exterior Compartment V';
$rigCheckData[4]['items'] = array(0 => 'BVM - Adult(2)', 1 => 'BVM - Child', 2 => 'BVM - Infant', 3 => 'AED - charged, with pads, scissors, razor, gloves', 4 => 'Portable Sunction - charging w/ tubing, Yankauer, Gloves, French', 5=> 'Fire Extinguisher (charged)', 6 => 'MAST Pants', 7 => 'Streamlight (charged)');
$rigCheckData[5]['name'] = 'Interior Compartments';
$rigCheckData[5]['items'] = array(0 => 'Linens, Cot Sheets, Blankets', 1 => 'Spare Oxygen Tanks (2)', 2 => 'Hi-Con Masks (6)', 3 => 'Nasal Cannulas (6)', 4 => 'Oxygen Supply Tubing (6)', 5 => 'Pedi O2 Masks (6)', 6 => 'Oxygen Wall Unit (working)', 7 => 'Suction Wall Unit (working)', 8 => 'BP Cuff (wall-mounted)', 9 => 'BP Multi-Cuff Unit', 10 => 'Nitrile Gloves (Med & Large)', 11 => 'Anti-Microbial Wipes & Cleaner', 12 => 'Face Masks with Eye Shields', 13 => 'N95 Masks', 14 => 'Gloves (Med. and Large boxes)');
$rigCheckData[6]['name'] = "Driver's Seat Rear Compartment";
$rigCheckData[6]['items'] = array(0 => 'OB Kit', 1 => 'Assorted Dressings', 2 => 'Cravats', 3 => 'Ice and Hot Packs', 4 => 'Kling, Tape', 5 => 'Water');
$rigCheckData[7]['name'] = 'Bench Seat';
$rigCheckData[7]['items'] = array(0 => 'CPR Board', 1 => 'Hare Traction', 2 => 'Board Splints', 3 => 'Water Jugs (full) and cups', 4 => 'Empty Garbage Bin', 5 => 'Change Sharps box if needed');
$rigCheckData[8]['name'] = 'Jump Kit';
$rigCheckData[8]['items'] = array(0 => 'BP Cuff (Adult & Pediatric)', 1 => 'Stethoscope', 2 => 'Gloves, Scissors, Pen Light', 3 => 'Ring Cutter, Seat Belt Cutter', 4 => 'Kling, Ice Packs, Cravats', 5 => 'Tape, Dressings', 6 => 'Oxygen Tank ( > 750 lbs)', 7 => 'Hi-Con Masks (3)', 8 => 'Nasal Cannulas (3)', 9 => 'Pediatric Masks (3)', 10 => 'Supply Tubing (3)', 11 => 'Oral Airways (Assorted)');
$rigCheckData[9]['name'] = 'Vehicle';
$rigCheckData[9]['items'] = array(0 => 'Fuel (1/2 tank or more)', 1 => 'Portable Radios (Charged & Working)', 2 => 'Warning Lights Working', 3 => 'Siren Working', 4 => 'Wipers / Lights / Backup Alarm', 5 => 'Interior Lighting', 6 => 'Registration / Insurance Card', 7 => 'Street Maps / Directories', 8 => 'Digital Camera & Spare Batteries', 9 => 'Diganostic Tests OK');

$table2start = 5;
$table3start = 8;

?>