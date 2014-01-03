<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-25 22:59:15 jantman"                                                              |
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
 | $HeadURL:: http://svn.jasonantman.com/newcall/addCallLoc.php                                         $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * Form to add a new call location.
 *
 * @package MPAC-NewCall-Forms
 */

require_once('inc/newcall.php.inc');
require_once('inc/JAforms.php');
require_once('inc/formFuncs.php');

$MP_streets = getMPstreets();

if(isset($_GET['id']) && $_GET['id'] != -1)
{
    $id = (int)$_GET['id'];
    $query = "SELECT Pkey,call_loc_id,place_name,StreetNumber,Street,AptNumber,City,State,Intsct_Street FROM calls_locations WHERE call_loc_id=$id AND is_deprecated=0;";
    $result = mysql_query($query) or die("Error in query: $query<br />ERROR: ".mysql_error());
    if(mysql_num_rows($result) < 1){ $id = -1;}
    $row = mysql_fetch_assoc($result);
    $vals['cl_PlaceName'] = $row['place_name'];
    $vals['cl_City'] = $row['City'];
    $vals['cl_city_other'] = $row['City'];
    $vals['cl_State'] = $row['State'];
    $vals['cl_AddressStreetNum'] = $row['StreetNumber'];
    $vals['cl_AddressStreet'] = $row['Street'];
    $vals['cl_AddressStreetMP'] = $row['Street'];
    $vals['cl_AddressApt'] = $row['AptNumber'];
    $vals['cl_IntsctStreet'] = $row['Intsct_Street'];
}
else
{
    // set defaults
    $vals = array("cl_AddressStreetNum" => "St#", "cl_AddressStreet" => "Street", "cl_AddressApt" => "Apt#", "cl_AddressState" => "NJ", "cl_AddressCity" => "Midland Park", "cl_intsct" => "No");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>
<?php if(isset($id)){ echo 'Update Patient '.$id;} else { echo 'Add Call Location';}?>
</title>
</head>
<body>
<form name="addCallLocForm">
<?php if(isset($id)){ echo '<input type="hidden" name="id" value="'.$id.'" />';} else { echo '<input type="hidden" name="id" value="-1" />';}?>
<div>

<div>
<label for="cl_intsct">Street Address or Intersection: </label>  <?php echo ja_radio("cl_intsct", "cl_intsct_no", "No", $vals, array("onchange" => "updateCLintsct(0)"));?><label for="cl_intsct_no">Street Address</label> <?php echo ja_radio("cl_intsct", "cl_intsct_yes", "Yes", $vals, array("onchange" => "updateCLintsct(1)"));?><label for="cl_intsct_yes">Intersection</label>
</div>

<div>
<label for="cl_PlaceName">Place Name (optional): </label><?php echo ja_text("cl_PlaceName", array("size" => 40, "maxlength" => 30), $vals); ?>
</div>

<div>
<div id="cl_AddressStateDiv">
<?php
global $state_abbrevs;
echo '<label for="AddressState">State: </label>'."\n";
$state_abbrevs["Non-US"] = "Non-US";
echo ja_select("cl_AddressState", array("value" => "NJ", "onChange" => "update_state_CL()"), $state_abbrevs, $vals)."\n";
?>
</div>
<div id="cl_AddressCityDiv"><?php echo genCityDiv($vals['cl_AddressState'], $vals, "_CL")."\n"; ?></div>
</div>

<div id="cl_AddressStreetDiv">
<label for="cl_AddressStreet">Address:</label> Street &#35;: <?php echo ja_text("cl_AddressStreetNum", array("size" => 6, "maxlength" => 6), $vals)."\n"; ?>

<?php
echo " Street: ".ja_text("cl_AddressStreet", array("size" => 40, "maxlength" => 40, "style" => "display: none;"), $vals)."\n";
echo ja_select("cl_AddressStreetMP", array(), $MP_streets, $vals)."\n";
echo " Apt &#35;: ".ja_text("cl_AddressApt", array("size" => 6, "maxlength" => 6), $vals)."\n";
?>
</div>

<div id="cl_AddressIntsctDiv" style="display: none;">
<?php
echo " Intersection Of: ".ja_text("cl_AddressIntsct1", array("size" => 20, "maxlength" => 40, "style" => "display: none;"), $vals)."\n";
echo ja_select("cl_AddressIntsctMP1", array(), $MP_streets, $vals)."\n";
echo " and ".ja_text("cl_AddressIntsct2", array("size" => 20, "maxlength" => 40, "style" => "display: none;"), $vals)."\n";
echo ja_select("cl_AddressIntsctMP2", array(), $MP_streets, $vals)."\n";
?>
</div>

</div>
</form>
<div style="text-align: center;"><h2><a href="javascript:submitCallLocForm()">Submit</a></h2></div>

</body>
</html>


