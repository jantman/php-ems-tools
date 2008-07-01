<?php
// docs/roster_help.php
//
// Help popup for the roster.
//
// +----------------------------------------------------------------------+
// | PHP EMS Tools	http://www.php-ems-tools.com                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006, 2007 Jason Antman.	                          |
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
//	$Id$
?>
<html>
<head>
<title>PHP EMS Tools Roster Help</title>
</head>
<body>
<h3>PHP EMS Tools Roster Help</h3>
<p>For the administrative view of the roster, please go to the PHP EMS Tools
index page and select Roster Administrative View, or replace the current URL
(address) of "roster.php" with "roster.php?adminView=1".</p>
<p>To sort the roster by any of those fields, click on the column title (link)
and the roster will be re-sorted by that column.</p>

<p>Membership Status Letters:
<table border=1 cellpadding=5>
<?php
require_once("../custom.php");

for($i = 0; $i < count($memberTypes); $i++)
{
    echo '<tr><td>';
    if($memberTypes[$i]['rosterName'] == '')
    {
	echo '&nbsp;';
    }
    else
    {
	echo $memberTypes[$i]['rosterName'];
    }
    echo '</td><td>';
    echo $memberTypes[$i]['name'];
    echo '</td>';
}
?>

</table>
</p>

<p>When using the normal roster, clicking <b>"Short View"</b> will display only the membership status letter, ID, and first and last name for each member. When viewing the Short View, clicking <b>"Normal View"</b> will bring you back to the normal (full) view. The short view cannot be used when in the administrative (adminView) mode.

<p>For full documentatin, please see <a href="index.html">the docs</a> or the
<a href="http://www.php-ems-tools.com">PHP EMS Tools Homepage</a></p>
</body>
</html>