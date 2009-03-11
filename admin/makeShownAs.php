<?php
// admin/makeShownAs.php
//
// Script which fills in the shownAs field for users where it has not been set
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
// | $LastChangedRevision::                                             $ |
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/admin/makeShow#$ |
// +----------------------------------------------------------------------+

require_once("./config/config.php");

$conn = mysql_connect() or die("error making connection");
mysql_select_db($dbName) or die("error selecting DB");

$query = "SELECT EMTid,shownAs,LastName FROM roster;";
$result = mysql_query($query) or die("error in query.");

while ($row = mysql_fetch_array($result))
{
	$shownAs=$row['LastName'];
	if($row['shownAs']=='')
	{
		mysql_query('UPDATE roster SET shownAs="'.$shownAs.'" WHERE EMTid="'.$row['EMTid'].'";') or die("query error");
		echo "UPDATED ".$row['EMTid'].' shownAs='.$shownAs.'<br>';
	}
	else
	{
		echo "IGNORED ".$row['EMTid'].'<br>';
	}
}
echo '<br><br>DONE.';

?>
