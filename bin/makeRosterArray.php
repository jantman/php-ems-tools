#!/usr/bin/php
<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-03-08 22:30:37 jantman"                                                              |
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
 | $LastChangedRevision:: 62                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/bin/makeRosterArray.php                                $ |
 +--------------------------------------------------------------------------------------------------------+
*/

// DB and includes
$dbName = "pcr";
$conn = mysql_connect() or die("Error connecting to MySQL.");
mysql_select_db($dbName) or die("Error selecting database: '".$dbName."'.");

$fh = fopen('/srv/www/htdocs/newcall/config/crew.php', 'w');
fwrite($fh, '<?php'."\n");
fwrite($fh, "\n");
fwrite($fh, '$'."CREW_ARRAY = array();\n");

$query = "SELECT EMTid FROM roster WHERE status='Inactive Life' OR status='Senior' OR status='Driver' OR status='Probie';";
$result = mysql_query($query);
while($row = mysql_fetch_assoc($result))
{
    fwrite($fh, '$'."CREW_ARRAY[] = '".$row['EMTid']."';\n");
}

fwrite($fh, "\n");
fwrite($fh, "?>\n");

?>