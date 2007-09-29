<?php
// rigCheckHandler.php
//
// Nice, simple for to select which rig to view or print a rig check for.
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


require_once('./config/config.php'); // main configuration

require_once('./config/rigCheckData.php'); // roster configuration

?>

<html>
<head>

<?php
echo '<title>'.$shortName.' - PHP EMS Tools Index</title>';
global $serverWebRoot;
//echo '<link rel="stylesheet" href="'.$serverWebRoot.'php_ems.css" type="text/css">'; // the location of the CSS file for the schedule
?>

<body>
<h1 align=center>Rig Check Selection</h1><br>
<table border=1 align=center>
<tr><td><b>Fill in</b></td><td><b>Print Blank</b></td>
<?php
// generate table
global $rigChecks;
foreach($rigChecks as $key => $subarray)
{
    echo '<tr><td align=center><b>';
    echo '<a href="rigCheck.php?index='.$key.'">'.$subarray['name'].'</a>';
    echo '</b></td><td align=center><b>';
    echo '<a href="blankRigCheck.php?index='.$key.'">'.$subarray['name'].'</a>';
    echo '</b></td></tr>';
}
?>
</table>
</body>
</html>