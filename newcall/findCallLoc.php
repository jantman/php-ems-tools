<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-23 23:14:07 jantman"                                                              |
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
 | $LastChangedRevision:: 64                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/findCallLoc.php                                        $ |
 +--------------------------------------------------------------------------------------------------------+
*/

require_once('inc/newcall.php.inc');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php echo '<meta name="generator" content="MPAC PCR version '.$_VERSION.' (r'.stripSVNstuff($_SVN_rev).')">'."\n"; ?>
<title>Find Patient</title>
</head>
<body>
<form name="findCallLocForm">


<div> <!-- BEGIN results div -->
<?php
$query = "SELECT Pkey,call_loc_id,place_name,StreetNumber,Street,AptNumber,City,State,Intsct_Street FROM calls_locations WHERE is_deprecated=0 ORDER BY Intsct_Street DESC,Street ASC;";
$result = mysql_query($query) or die("Error in Query: ".$query."<br />ERROR: ".mysql_error()."<br />");

echo '<table class="ptSearch">'."\n";
echo '<tr><th>&nbsp;</th><th>Place Name</th><th>Address</th><th>City</th><th>ID</th></tr>'."\n";
if(mysql_num_rows($result) < 1)
{
    echo '<tr><td colspan="5"><span style="font-weight: bold; text-align: center;">No results found.</span></td></tr>'."\n";
}
else
{
    while($row = mysql_fetch_assoc($result))
    {
	echo '<tr>';
	echo '<td><a href="javascript:setCallLoc('.$row['call_loc_id'].')">Select</a></td>';
	echo '<td>'.$row['place_name'].'</td>';
	if(trim($row['Intsct_Street']) != "")
	{
	    echo '<td>'.makeIntsctAddress($row['Street'], $row['Intsct_Street']).'</td>';
	}
	else
	{
	    echo '<td>'.makeAddress($row['StreetNumber'], $row['Street'], $row['AptNumber']).'</td>';
	}
	echo '<td>'.$row['City'].', '.$row['State'].'</td>';
	echo '<td>'.$row['call_loc_id'].'</td>';
	echo '</tr>'."\n";
    }
}
echo '</table>'."\n";
echo '<h2 style="text-align: center;"><a href="javascript:addCallLoc()">Add New Call Location</a></h2>'."\n";
?>
</div> <!-- END results div -->

</body>
</html>


