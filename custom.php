<?php

//(C) 2006 Jason Antman. All Rights Reserved.
// with questions, go to www.jasonantman.com
// or email jason AT jasonantman DOT com
// Time-stamp: "2010-07-22 16:15:40 jantman"

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

require_once('config/config.php');

require_once('config/memberTypes.php');


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
$rigCheckData[0]['items'] = array(0 => 'Disaster Packs (3)', 1 => 'Stair Chair', 2 => 'Onboard Oxygen ( > 500 lbs)', 3 => 'Tool kit');
$rigCheckData[1]['name'] = 'Exterior Compartment II';
$rigCheckData[1]['items'] = array(0 => 'Collars - 2 Adjustable', 1 => 'Pediatric Collars - 2 Adjustable', 2 => 'KED (2)', 3 => 'Add-a-Splint', 4 => 'Head Blocks (2)', 5=> 'Head Beds');

$rigCheckData[2]['name'] = 'Exterior Compartment III';
$rigCheckData[2]['items'] = array(0 => 'Helmets and Safety Goggles (3)', 1 => 'Work Gloves (3)', 2 => 'Rope', 3 => 'Disposable blankets (3)', 4 => 'Safety Vests (4)', 5 => 'HazMat Suits (3)');
$rigCheckData[3]['name'] = 'Exterior Compartment IV';
$rigCheckData[3]['items'] = array(0 => 'Backboards w/ Straps (2)', 1 => 'Scoop Stretcher', 2 => 'Reeves (2)');
$rigCheckData[4]['name'] = 'Exterior Compartment V';
$rigCheckData[4]['items'] = array(0 => 'BVM - Adult(2)', 1 => 'BVM - Child', 2 => 'BVM - Infant', 3 => 'AED - charged, with pads, scissors, razor, gloves', 4 => 'Portable Sunction - charging w/ tubing, Yankauer, Gloves, French', 5=> 'Fire Extinguisher (charged)', 6 => 'MAST Pants', 7 => 'Streamlight (charged)');
$rigCheckData[5]['name'] = 'Interior Compartments';
$rigCheckData[5]['items'] = array(0 => 'Linens, Cot Sheets, Blankets', 1 => 'Spare Oxygen Tanks (2)', 2 => 'Hi-Con Masks (6)', 3 => 'Nasal Cannulas (6)', 4 => 'Oxygen Supply Tubing (6)', 5 => 'Pedi O2 Masks (6)', 6 => 'Oxygen Wall Unit (working)', 7 => 'Suction Wall Unit (working)', 8 => 'BP Cuff (wall-mounted)', 9 => 'Stethoscope', 10 => 'BP Multi-Cuff Unit', 11 => 'Nitrile Gloves (Med & Large)', 12 => 'Anti-Microbial Wipes & Cleaner', 13 => 'Face Masks with Eye Shields', 14 => 'N95 Masks, Biohazard bags', 15 => 'Hard Restraints', 16 => 'Nasal Airways, Suction Caths');
$rigCheckData[6]['name'] = "Driver's Seat Rear Compartment";
$rigCheckData[6]['items'] = array(0 => 'OB Kit', 1 => 'Assorted Dressings (2x2,4x4,5x9,8x10)', 2 => 'Cravats', 3 => 'Ice and Hot Packs', 4 => 'Kling, Tape (1in, 2in, 3in)', 5 => 'Water', 6 => 'Oral Glucose (not expired)', 7 => 'Burn Sheets');
$rigCheckData[7]['name'] = 'Bench Seat';
$rigCheckData[7]['items'] = array(0 => 'CPR Board', 1 => 'Hare Traction', 2 => 'Board Splints', 3 => 'Water Jugs (full) and cups', 4 => 'Empty Garbage Bin', 5 => 'Change Sharps box if needed');
$rigCheckData[8]['name'] = 'Jump Kit';
$rigCheckData[8]['items'] = array(0 => 'BP Cuff (Adult & Pediatric)', 1 => 'Stethoscope', 2 => 'Gloves, Scissors, Pen Light', 3 => 'Ring Cutter, Seat Belt Cutter', 4 => 'Kling, Ice Packs, Cravats', 5 => 'Tape, Dressings', 6 => 'Oxygen Tank ( > 750 lbs)', 7 => 'Hi-Con Masks (3)', 8 => 'Nasal Cannulas (3)', 9 => 'Pediatric Masks (3)', 10 => 'Supply Tubing (3)', 11 => 'Oral Airways (Assorted)', 12 => 'Oral Glucose (not expired)', 13 => 'PulseOx (working, battery OK)', 14 => 'EpiPens (Adult & Jr) - not expired, good color');
$rigCheckData[9]['name'] = 'Vehicle';
$rigCheckData[9]['items'] = array(0 => 'Fuel (1/2 tank or more)', 1 => 'Portable Radios (Charged & Working)', 2 => 'Warning Lights Working', 3 => 'Siren Working', 4 => 'Wipers / Lights / Backup Alarm', 5 => 'Interior Lighting', 6 => 'Registration / Insurance Card', 7 => 'Street Maps / Directories', 8 => 'Digital Camera & Spare Batteries', 9 => 'Diganostic Tests OK', 10 => 'Clipboard w/ pens, 10+ blank PCRs, blank RMA forms');

$table2start = 5;
$table3start = 8;

// RIG CHECK FOR 588

$rigCheckData88 = array();
$rigCheckData88[0]['name'] = 'Exterior Compartment I';
$rigCheckData88[0]['items'] = array(0 => 'Disaster Packs (3)', 1 => 'Reeves (2)', 2 => 'Fire Extinguisher (Charged)', 3 => 'Onboard Oxygen ( > 500 lbs)');
$rigCheckData88[1]['name'] = 'Exterior Compartment II';
$rigCheckData88[1]['items'] = array(0 => '2 Adult Adjustable Collars', 1 => '2 Peeds Adjustable Collars', 2 => 'KED (2)', 3 => 'Add-a-Splint', 4 => 'Head Blocks (2)', 5=> 'Head Beds', 6 => 'Traction Splints (2)', 7 => 'Spider Straps (1)');
$rigCheckData88[2]['name'] = 'Exterior Drawer';
$rigCheckData88[2]['items'] = array(0 => 'Tool Kit', 1 => 'Window Punch', 2 => 'Work Gloves');
$rigCheckData88[3]['name'] = 'Exterior Compartment III';
$rigCheckData88[3]['items'] = array(0 => 'Helmets and Safety Goggles (3)', 1 => 'Rope', 2 => 'Disposable blankets (3)', 3 => 'HazMat Suits (4)', 4 => 'Safety Vests (4)');
$rigCheckData88[4]['name'] = 'Exterior Compartment IV';
$rigCheckData88[4]['items'] = array(0 => 'Backboards w/ Straps (2)', 1 => 'Scoop Stretcher', 2 => 'Stair Chair', 3 => 'Cravats');
$rigCheckData88[5]['name'] = 'Exterior Compartment V';
$rigCheckData88[5]['items'] = array(0 => 'BVM - Adult(2)', 1 => 'BVM - Child', 2 => 'BVM - Infant', 3 => 'AED - charged, with pads, scissors, razor, gloves', 4 => 'Portable Sunction - charging w/ tubing, Yankauer, Gloves, French', 5=> 'Fire Extinguisher (charged)', 6 => 'MAST Pants', 7 => 'Streamlight (charged)', 8 => 'Oxygen Tanks (2, Full)');
$rigCheckData88[6]['name'] = "Compartment behind Driver's Seat";
$rigCheckData88[6]['items'] = array(0 => 'Sheets & Blankets', 1 => 'Pillows (2)', 2 => 'Chucks', 3 => 'Towels & Wash Cloths', 4 => 'Spare blue cot covers');
$rigCheckData88[7]['name'] = 'Under Oxygen Counter';
$rigCheckData88[7]['items'] = array(0 => 'Fluid Shield Masks', 1 => 'Cleaning Wipes & Sprays', 2 => 'Suction Cannisters', 3 => 'Vomit Bags, Garbage bags, biohazard bags', 4 => 'Multi-Cuff Kit');
$rigCheckData88[8]['name'] = 'Oxygen Counter';
$rigCheckData88[8]['items'] = array(0 => 'Medium & Large Gloves', 1 => 'Tissues', 2 => 'Pen Light', 3 => 'Stethoscope & BP cuff', 4 => 'Oral Airway Kit', 5 => 'Portable Radio (Charged & Working)', 6 => 'Suction ready to use w/ tubing & yankauer');
$rigCheckData88[9]['name'] = 'Above Oxygen Counter';
$rigCheckData88[9]['items'] = array(0 => 'Nasal Airways', 1 => 'Suction Catheters - French', 2 => 'Suction Tubing & Yankauers', 3 => 'Peds Non-Rebreathers (6)', 4 => 'Adult Non-Rebreathers (6)', 5 => 'Nasal Cannulas (6)', 6 => 'O2 Supply Tubing');
$rigCheckData88[10]['name'] = 'Cabinet next to CPR seat';
$rigCheckData88[10]['items'] = array (0 => 'Empty Garbage, Switch out Sharps', 1 => 'Gauze (4x4, 2x2, etc.)', 2 => 'Eye Pads', 3 => 'Kling (1", 2", 3")', 4 => 'Band-Aids, Assorted', 5 => 'Tape', 6 => 'Glucose');
$rigCheckData88[11]['name'] = "Driver's Side Rear Cabinet";
$rigCheckData88[11]['items'] = array(0 => 'Burn Sheets (3)', 1 => 'Burn Kit', 2 => 'Water - Sterile & Drinking', 3 => 'OB Kit', 4 => 'Cold Packs - Regular & Jr.', 5 => 'Dressings - 8x10, 5x9', 6 => 'Eye Wash Kit');
$rigCheckData88[12]['name'] = 'Bench Seat';
$rigCheckData88[12]['items'] = array (0 => 'Empty Garbage, Switch out Sharps', 1 => 'CPR Board', 2 => 'Pedi Board', 3 => 'Reeves Seat');
$rigCheckData88[13]['name'] = 'Cabinet Over Bench Seat';
$rigCheckData88[13]['items'] = array(0 => 'Medium & Large Gloves', 1 => 'ERG', 2 => 'Beanie Babies, Peds Stuff');
$rigCheckData88[14]['name'] = 'Jump Kit';
$rigCheckData88[14]['items'] = array(0 => 'BP Cuff (Adult & Pediatric)', 1 => 'Stethoscope', 2 => 'Gloves, Scissors, Pen Light', 3 => 'Ring Cutter, Seat Belt Cutter', 4 => 'Kling, Ice Packs, Cravats', 5 => 'Tape, Dressings', 6 => 'Oxygen Tank ( > 750 lbs)', 7 => 'Hi-Con Masks (3)', 8 => 'Nasal Cannulas (3)', 9 => 'Pediatric Masks (3)', 10 => 'Supply Tubing (3)', 11 => 'Oral Airways (Assorted)', 12 => 'Pulse Ox - working, good battery', 13 => 'EpiPens (Adult & Jr) - not expired, good color');
$rigCheckData88[15]['name'] = 'Vehicle';
$rigCheckData88[15]['items'] = array(0 => 'Fuel (1/2 tank or more)', 1 => 'Portable Radio (Charged & Working)', 2 => 'Warning Lights Working', 3 => 'Siren Working', 4 => 'Air Horns Working', 5 => 'Wipers / Lights / Backup Alarm', 6 => 'Interior Lighting', 7 => 'Registration / Insurance Card', 8 => 'Street Maps / Directories', 9 => 'Digital Camera & Spare Batteries', 10 => 'Diganostic Tests OK', 11 => 'Clipboard w/ pens, 10+ blank PCRs, blank RMA forms');

$table2start88 = 6;
$table3start88 = 12;


?>