<?php
// admin/dumpTableSQL.php
//
// This script can either dump the blank table schema (for installation)
// to STDOUT or to text if called with a browser.
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

$dumpFile = ""; // FULL path to the file to dump to, if called with -f

require_once('./dbTableSchema.php'); // db table schema
// this file has $dbTableSchemaA which is the array that holds all queries to setup the table

// find out whether we're running CLI or as a server script
if(php_sapi_name() != 'cli')
{
    // we're running through a web server.
    dumpToWeb();
}
else
{
    // we're running CLI, so work with arguments
    dumpToSTDOUT();
}


function dumpToSTDOUT()
{
    global $dbTableSchemaA; // array with the queries and information
    // begin table creation loop
    foreach($dbTableSchemaA as $val)
    {
	$tblName = $val['name'];
	$tblDesc = $val['description'];
	$query = $val['query'];
	fwrite(STDOUT, "# ".$tblName." ".$tblDesc."\n");
	fwrite(STDOUT, $query."\n");
    }
}

function dumpToWeb()
{
    global $dbTableSchemaA; // array with the queries and information
    // begin table creation loop
    foreach($dbTableSchemaA as $val)
    {
	$tblName = $val['name'];
	$tblDesc = $val['description'];
	$query = $val['query'];
	echo("# ".$tblName." ".$tblDesc."<br>");
	echo($query."<br>");
    }
}

?>