<?php
//(C) 2006 Jason Antman. All Rights Reserved.
// with questions, go to www.jasonantman.com
// or email jason AT jasonantman DOT com
// Time-stamp: "2007-03-20 16:00:16 jantman"

//This software may not be copied, altered, or distributed in any way, shape, form, or means.

// countGenerals.php.inc
// counts generals from PCRpro

// this turns debugging mode on and off
$debug = false;

function countMonthGenerals($year, $month)
{
    // will return an array of all members' generals for the month
    // like array($EMTid => array('count' => $generalsTotal, calls => (string with all run #s))

    global $dbName;
    global $debug;

    $generals = array();
    // select DB
    // SELECT calls from this month, with all member info, runNumber, times
    // loop through all calls
    // for each call:

    // make array of EMTid's on call ($members)
    $members = array();
    // figure out dispatch timestamp for call
    // foreach $EMTid in $members
    // if(isOnDuty($EMTid, $timestamp)==true)
    // $generals[$EMTid]["count"]++;
    // $generals[$EMTid]["calls"] .= $row['RunNumber']." ";

    // end loops
    // return $generals;
}

/* handler:
for one month, runs countMonthGenerals, uses keys to generate a table with a count column

for THIS year:
1) makes separate arrays for each month up to (inclusive) the current month from results of countMonthGenerals
for PREVIOUS year:
1) makes separate arrays for each month from results of countMonthGenerals

2) loop through all arrays, make a master array of all EMTids.
3) Setup table - columns: EMTid, month names, yearly total
4) for each row (EMTid in master array):
     output each month total, checking to be sure that the EMTid is defined
     total up all months as we go along
     finally output the total

*/

?>