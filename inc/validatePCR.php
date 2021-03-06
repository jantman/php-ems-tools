<?php

/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-24 22:35:36 jantman"                                                              |
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
 | $HeadURL:: http://svn.jasonantman.com/newcall/inc/validatePCR.php                                    $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * PCR form validation functions.
 *
 * @package MPAC-NewCall-PHP
 */


$warnings = array();
$errors = array();


function validate_form($values)
{
    global $errors, $warnings;

    // DEBUG
    $errors = array("time_disp" => "foo Time Disp", "DOB" => "foo DOB", "sexM" => "foo SexM", "unit" => "foo unit", "MAcheck" => array("foo MAcheck1", "foo MAcheck 2"));
    $warnings = array("time_avail" => "w time_avail", "age" => "w age", "patientOf" => "w patientOf", "sexF" => "w sexF", "AddressState" => array("w AddressState1", "w State 2"));
    // END DEBUG

    // VALIDATE HERE
    validate_DOB($values);
    validate_times($values);
    validate_misc($values);

    $foo = array();
    if(count($warnings) > 0){ $foo['warnings'] = $warnings;}
    if(count($errors) > 0){ $foo['errors'] = $errors;}
    return $foo;
}

function validate_misc($values)
{
    global $errors, $warnings;
    if(((int)$values['patientNum']) > ((int)$values['patientOf']))
    {
	add_error("patientNum", "You can not have a patient number higher than the total number of patients.");
    }
}

function validate_DOB($values)
{
    global $errors, $warnings;
    return true;
}

function validate_times($values)
{
    global $errors, $warnings;
    $names = array("time_disp", "time_insvc", "time_onscene", "time_enroute", "time_arrived", "time_avail", "time_out");
    $times = array();
    foreach($names as $name)
    {
	$times[$name] = strtotime($values['date']." ".$values[$name]);
    }
    return true;
}

function add_error($name, $error)
{
    global $errors;
    if(isset($errors[$name]))
    {
	$foo = $errors[$name];
	$errors[$name] = array();
	$errors[$name][] = $foo;
	$errors[$name][] = $error;
    }
    else
    {
	$errors[$name] = $error;
    }
}

function add_warning($name, $error)
{
    global $warnings;
    if(isset($warnings[$name]))
    {
	$foo = $warnings[$name];
	$warnings[$name] = array();
	$warnings[$name][] = $foo;
	$warnings[$name][] = $error;
    }
    else
    {
	$warnings[$name] = $error;
    }
}

?>