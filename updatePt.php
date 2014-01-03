<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-25 22:51:11 jantman"                                                              |
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
 | $HeadURL:: http://svn.jasonantman.com/newcall/updatePt.php                                           $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * Page with update patient form.
 *
 * @package MPAC-NewCall-Forms
 */

require_once('inc/newcall.php.inc');
require_once('inc/JAforms.php');
require_once('inc/formFuncs.php');

$MP_streets = getMPstreets();

if(isset($_GET['pkey']))
{
    $pkey = (int)$_GET['pkey'];
    $query = "SELECT patient_id FROM patients WHERE Pkey=$pkey;";
    $result = mysql_query($query) or die("Error in query: $query<br />ERROR: ".mysql_error());
    if(mysql_num_rows($result) < 1){ $id = -1;}
    $row = mysql_fetch_assoc($result);
    $_GET['id'] = $row['patient_id'];
}

if(isset($_GET['id']) && $_GET['id'] != -1)
{
    $id = (int)$_GET['id'];
    $query = "SELECT Pkey,patient_id,FirstName,LastName,MiddleName,Sex,DOB,StreetNumber,Street,AptNumber,City,State FROM patients WHERE patient_id=$id AND is_deprecated=0;";
    $result = mysql_query($query) or die("Error in query: $query<br />ERROR: ".mysql_error());
    if(mysql_num_rows($result) < 1){ $id = -1;}
    $row = mysql_fetch_assoc($result);
    $vals['pt_DOB'] = date("m/d/Y", strtotime($row['DOB']));
    $vals['pt_NameLast'] = $row['LastName'];
    $vals['pt_NameFirst'] = $row['FirstName'];
    $vals['pt_NameMiddle'] = $row['MiddleName'];
    $vals['pt_sex'] = $row['Sex'];
    $vals['pt_AddressCity'] = $row['City'];
    $vals['pt_city_other'] = $row['City'];
    $vals['pt_AddressState'] = $row['State'];
    $vals['pt_AddressStreetNum'] = $row['StreetNumber'];
    $vals['pt_AddressStreet'] = $row['Street'];
    $vals['pt_AddressStreetMP'] = $row['Street'];
    $vals['pt_AddressApt'] = $row['AptNumber'];
}
else
{
    // set defaults
    $vals = array("pt_DOB" => "MM/DD/YYYY", "pt_NameLast" => "Last", "pt_NameFirst" => "First", "pt_AddressStreetNum" => "St#", "pt_AddressStreet" => "Street", "pt_AddressApt" => "Apt#", "pt_AddressState" => "NJ", "pt_AddressCity" => "Midland Park");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php echo '<meta name="generator" content="MPAC PCR version '.$_VERSION.' (r'.stripSVNstuff($_SVN_rev).')">'."\n"; ?>
<title>
<?php if(isset($id)){ echo 'Update Patient '.$id;} else { echo 'Add New Patient';}?>
</title>
</head>
<body>
<form name="updatePtForm">
<?php if(isset($id)){ echo '<input type="hidden" name="ptid" value="'.$id.'" />';} else { echo '<input type="hidden" name="ptid" value="-1" />';}?>
<div>

<div>
<label for="age">Age: </label><?php echo ja_text("pt_age", array("size" => 3, "readonly" => "readonly"), $vals); ?></td>
<label for="DOB">DOB: </label><?php echo ja_text("pt_DOB", array("size" => 10, "value" => "MM/DD/YYYY", "onChange" => "update_DOB()"), $vals); ?>
<label for="pt_sex">Sex: </label>  <?php echo ja_radio("pt_sex", "pt_sexM", "Male", $vals);?><label for="pt_sexM">Male</label> <?php echo ja_radio("pt_sex", "pt_sexF", "Female", $vals);?><label for="pt_sexF">Female</label>
</div>

<div>
<label for="pt_NameLast">Name: </label><?php echo ja_text("pt_NameLast", array("size" => 40, "maxlength" => 30), $vals); ?>,&nbsp;&nbsp;
<?php echo ja_text("pt_NameFirst", array("size" => 30, "maxlength" => 30), $vals); ?>&nbsp;&nbsp;
<?php echo ja_text("pt_NameMiddle", array("size" => 2, "maxlength" => 2), $vals); ?>
</div>

<div>
<div id="pt_AddressStateDiv">
<?php
global $state_abbrevs;
echo '<label for="AddressState">State: </label>'."\n";
$state_abbrevs["Non-US"] = "Non-US";
echo ja_select("pt_AddressState", array("value" => "NJ", "onChange" => "update_state()"), $state_abbrevs, $vals)."\n";
?>
</div>
<div id="pt_AddressCityDiv"><?php echo genCityDiv($vals['pt_AddressState'], $vals)."\n"; ?></div>
</div>

<div>
<label for="pt_AddressStreet">Address:</label> Street &#35;: <?php echo ja_text("pt_AddressStreetNum", array("size" => 6, "maxlength" => 6), $vals)."\n"; ?>

<?php
if((isset($vals['pt_AddressCity']) && $vals['pt_AddressCity'] != "Midland Park") || $vals['pt_AddressState'] != 'NJ')
{
    echo " Street: ".ja_text("pt_AddressStreet", array("size" => 40, "maxlength" => 40), $vals)."\n";
    echo ja_select("pt_AddressStreetMP", array("style" => "display: none;"), $MP_streets, $vals)."\n";
}
else
{
    echo " Street: ".ja_text("pt_AddressStreet", array("size" => 40, "maxlength" => 40, "style" => "display: none;"), $vals)."\n";
    echo ja_select("pt_AddressStreetMP", array(), $MP_streets, $vals)."\n";
}
echo " Apt &#35;: ".ja_text("pt_AddressApt", array("size" => 6, "maxlength" => 6), $vals)."\n";
?>

</div>

</div>
</form>
<div style="text-align: center;"><h2><a href="javascript:submitPatientForm()">Submit</a></h2></div>

</body>
</html>


