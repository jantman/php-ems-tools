<?php
// config/rigCheckData.php
//
// Data for the rig check items.
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
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/config/rigChec#$ |
// +----------------------------------------------------------------------+

// if true, show an alert on index.php if any rig has not been checked for rigCheckAlertTime seconds
$rigCheckAgeAlert = true; 

// alert on index.php if last rig check for a truck was longer than this amount of time
$rigCheckAlertTime = 691200; // time in seconds (691200 sec = 8 days)


// setup is as follows - 
// the rigCheckData array holds arrays for the sections - each section is highlighted in bold
// each section holds a number of items.
//

// the information is displayed in three tables as columns within one master table. 
// the variables table2start and table3start determine the first item (index for rigCheckData) that appears at the top of each column.
//


$rigChecks = array(); // array that holds ALL the rig check data

// THE WAY THIS WORKS is as follows:
// the rigChecks array above is an arrya indexed by integers. Each array element has four sub-elements: name, data, table2start, and table3start. the data element is just a reference to a giant array with all of the rig check data, as shown below. each rig should have its own array defined below. remember to define all variables like the rigCheckData array before you reference them in the rigChecks array.

// The following is the data for *ONE* Rig. It must be duplicated for each rig.

// BEGIN RIG DATA
$rigCheckData_1 = array();
$rigCheckData_1[0]['name'] = 'Exterior Compartment I';
$rigCheckData_1[0]['items'] = array(0 => 'Disaster Packs (3)', 1 => 'Stair Chair', 2 => 'Onboard Oxygen ( > 500 lbs)');
$rigCheckData_1[1]['name'] = 'Exterior Compartment II';
$rigCheckData_1[1]['items'] = array(0 => 'Collars - 2 Adjustable', 1 => 'Pediatric, Baby No-Neck', 2 => 'KED (2)', 3 => 'Add-a-Splint', 4 => 'Head Blocks (2)', 5=> 'Head Beds');

$rigCheckData_1[2]['name'] = 'Exterior Compartment III';
$rigCheckData_1[2]['items'] = array(0 => 'Helmets and Safety Goggles (3)', 1 => 'Work Gloves (3)', 2 => 'Rope', 3 => 'Disposable blankets (3)');
$rigCheckData_1[3]['name'] = 'Exterior Compartment IV';
$rigCheckData_1[3]['items'] = array(0 => 'Backboards w/ Straps (2)', 1 => 'Scoop Stretcher', 2 => 'Reeves (2)');
$rigCheckData_1[4]['name'] = 'Exterior Compartment V';
$rigCheckData_1[4]['items'] = array(0 => 'BVM - Adult(2)', 1 => 'BVM - Child', 2 => 'BVM - Infant', 3 => 'AED - charged, with pads, scissors, razor, gloves', 4 => 'Portable Sunction - charging w/ tubing, Yankauer, Gloves, French', 5=> 'Fire Extinguisher (charged)', 6 => 'MAST Pants', 7 => 'Streamlight (charged)');
$rigCheckData_1[5]['name'] = 'Interior Compartments';
$rigCheckData_1[5]['items'] = array(0 => 'Linens, Cot Sheets, Blankets', 1 => 'Spare Oxygen Tanks (2)', 2 => 'Hi-Con Masks (6)', 3 => 'Nasal Cannulas (6)', 4 => 'Oxygen Supply Tubing (6)', 5 => 'Pedi O2 Masks (6)', 6 => 'Oxygen Wall Unit (working)', 7 => 'Suction Wall Unit (working)', 8 => 'BP Cuff (wall-mounted)', 9 => 'BP Multi-Cuff Unit', 10 => 'Nitrile Gloves (Med & Large)', 11 => 'Anti-Microbial Wipes & Cleaner', 12 => 'Face Masks with Eye Shields', 13 => 'N95 Masks', 14 => 'Gloves (Med. and Large boxes)');
$rigCheckData_1[6]['name'] = "Driver's Seat Rear Compartment";
$rigCheckData_1[6]['items'] = array(0 => 'OB Kit', 1 => 'Assorted Dressings', 2 => 'Cravats', 3 => 'Ice and Hot Packs', 4 => 'Kling, Tape', 5 => 'Water');
$rigCheckData_1[7]['name'] = 'Bench Seat';
$rigCheckData_1[7]['items'] = array(0 => 'CPR Board', 1 => 'Hare Traction', 2 => 'Board Splints', 3 => 'Water Jugs (full) and cups', 4 => 'Empty Garbage Bin', 5 => 'Change Sharps box if needed');
$rigCheckData_1[8]['name'] = 'Jump Kit';
$rigCheckData_1[8]['items'] = array(0 => 'BP Cuff (Adult & Pediatric)', 1 => 'Stethoscope', 2 => 'Gloves, Scissors, Pen Light', 3 => 'Ring Cutter, Seat Belt Cutter', 4 => 'Kling, Ice Packs, Cravats', 5 => 'Tape, Dressings', 6 => 'Oxygen Tank ( > 750 lbs)', 7 => 'Hi-Con Masks (3)', 8 => 'Nasal Cannulas (3)', 9 => 'Pediatric Masks (3)', 10 => 'Supply Tubing (3)', 11 => 'Oral Airways (Assorted)');
$rigCheckData_1[9]['name'] = 'Vehicle';
$rigCheckData_1[9]['items'] = array(0 => 'Fuel (1/2 tank or more)', 1 => 'Portable Radios (Charged & Working)', 2 => 'Warning Lights Working', 3 => 'Siren Working', 4 => 'Wipers / Lights / Backup Alarm', 5 => 'Interior Lighting', 6 => 'Registration / Insurance Card', 7 => 'Street Maps / Directories', 8 => 'Digital Camera & Spare Batteries', 9 => 'Diganostic Tests OK');
$table2start_1 = 5;
$table3start_1 = 8;
// END RIG DATA

// we will now add this data into the main array. If you want different information/items for different rigs, you will need to create multiple arrays above (i.e. rigCheckData_2, table2start_2, etc. and add them separately below.

// NOTE: you must use an index value of 1 or greater. 0 will not work.

$rigChecks[1]['name'] = "588";
$rigChecks[1]['data'] = $rigCheckData_1; // if our rigs had things in different places, this array should be different for each rig
$rigChecks[1]['table2start'] = $table2start_1; // this should be different for each rig if the data array is different
$rigChecks[1]['table3start'] = $table3start_1; // and this should be as well

// in this example, both trucks have the same stuff in the same place

$rigChecks[2]['name'] = "589";
$rigChecks[2]['data'] = $rigCheckData_1; // if our rigs had things in different places, this array should be different for each rig
$rigChecks[2]['table2start'] = $table2start_1; // this should be different for each rig if the data array is different
$rigChecks[2]['table3start'] = $table3start_1; // and this should be as well

?>