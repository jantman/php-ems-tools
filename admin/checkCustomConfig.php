<?php
// admin/checkCustomConfig.php
//
// Script to check the validity of config.php - included in install file
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
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/admin/checkCus#$ |
// +----------------------------------------------------------------------+

// this file will import the user's customization
include('./config/config.php');

// CLI or CGI is ok. so anything goes.

// script to check configuration of custom.php


// output a bit or beginning text
out("Checking custom.php...");

$val = checkCustom();

if($val == true)
{
    out("Completed Successfully.");
}
else
{
    out("FAILED. Please correct the above errors.");
}


function checkCustom()
{
    $configOK = true;

    if(! file_exists('./config/config.php'))
    {
	out("Cannot find custom.php. The sky is falling!!");
	return false;
    }

    global $dbName;
    if(! isset($dbName) || $dbName == null || $dbName == "")
    {
	out("Value of dbName is invalid.");
	$configOK = false;
    }

    global $orgName;
    if(! isset($orgName) || $orgName == "")
    {
	out("orgName is invalid.");
	$configOK = false;
    }

    global $shortName;
    if(! isset($shortName) || $shortName == "")
    {
	out("shortName is invalid.");
	$configOK = false;
    }

    global $serverWebRoot;
    if(! isset($serverWebRoot) || $serverWebRoot == "")
    {
	out("serverWebRoot is invalid.");
	$configOK = false;
    }

    global $memberTypes;
    if(! isset($memberTypes) || (count($memberTypes) < 1))
    {
	out("memberTypes is invalid or has less than one type.");
	$configOK = false;
    }

    $memberTypesValid = true;
    for($i = 0; $i < count($memberTypes); $i++)
    {
	if(! isset($memberTypes[$i]['name']) || $memberTypes[$i]['name'] == "")
	{
	    out("Problem with memberTypes[".$i."]. name is not set or invalid.");
	    $memberTypesValid = false;
	}
	
    }
    if($memberTypesValid == false)
    {
	$configOK = false;
	out("Problem with the memberTypes array.");
    }

    global $schedTimeFormat;
    if((! isset($schedTimeFormat)) || ($schedTimeFormat < 1) || ($schedTimeFormat > 3))
    {
	out("schedTimeFormat is invalid. It must be an integer between (inclusive) 1 and 3. It is set to ".$schedTimeFormat);
	$configOK = false;
    }

    global $dayFirstHour;
    if(! isset($dayFirstHour))
    {
	out("dayFirstHour is invalid.");
	$configOK = false;
    }

    global $dayLastHour;
    if(! isset($dayLastHour))
    {
	out("dayLastHour is invalid.");
	$configOK = false;
    }

    global $nightFirstHour;
    if(! isset($nightFirstHour))
    {
	out("nightFirstHour is invalid.");
	$configOK = false;
    }

    global $nightLastHour;
    if(! isset($nightLastHour))
    {
	out("nightLastHour is invalid.");
	$configOK = false;
    }

    global $officerPositions;
    if(! isset($officerPositions))
    {
	out("The officerPositions array is not declared. It must be at least declared and empty.");
	$configOK = false;
    }

    global $positions;
    if(! isset($positions))
    {
	out("The positions array is not declared. It must be at least declared and empty.");
	$configOK = false;
    }

    global $committees;
    if(! isset($committees))
    {
	out("The committees array is not declared. It must be at least declared and empty.");
	$configOK = false;
    }

    global $commPositions;
    if(! isset($commPositions))
    {
	out("The commPositions array is not declared. It must be at least declared and empty.");
	$configOK = false;
    }

    global $rigChecks;
    if(! isset($rigChecks))
    {
	out("The rigChecks array is not declared. It must be at least declared and empty.");
	$configOK = false;
    }

    return $configOK;
}


function out($string)
{
    $sapiName = php_sapi_name();
    
    if($sapiName=='cli')
    {
	fwrite(STDOUT, $string."\n");
    }
    else
    {
	echo $string."<br>";
    }
}

?>