<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-25 22:52:12 jantman"                                                              |
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
 | $HeadURL:: http://svn.jasonantman.com/newcall/txForm.php                                             $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * Page for treatment (tx) form.
 *
 * @package MPAC-NewCall-Forms
 */

$vals = array(); // TODO - populate from GET or POST
if(isset($_GET['tx']))
{
    $vals = explode(", ", $_GET['tx']);
    foreach($vals as $val)
    {
	if(substr($val, 0, 6) == "Oxygen")
	{
	    $foo = trim(substr($val, 8));
	    $bar = trim(substr($foo, 0, strpos($foo, "L")));
	    $baz = trim(substr($foo, strpos($foo, " ")));
	    $vals["TxForm_O2"] = $baz;
	    $vals["TxForm_O2L"] = $bar;
	}
    }
}
else
{
    $vals['TxForm_O2'] = 'None';
}

require_once('inc/simpleForms.php');

if(! isset($_SERVER["HTTP_REFERER"]) || trim($_SERVER["HTTP_REFERER"]) == "")
{
    echo '<html><head><title>Treatments Form</title>';
    echo '<link rel="stylesheet" type="text/css" href="css/PCRform.css" />';
    echo '<link rel="stylesheet" type="text/css" href="css/scheduleForm.css" />';
    echo '</head><body>';
}

/*
echo '<pre>';
echo var_dump($vals);
echo '</pre>';
*/

?>
<form name="TxForm" id="TxForm">
<table>
<tr>
<td>Oxygen</td>
<td><?php echo simpleFormCheckBox("TxForm_CPR", "CPR", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_Bandage", "Bandage/Cravat", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_Stretcher", "Regular Stretcher", $vals);?></td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;<?php echo simpleFormRadioButton("TxForm_O2", "TxForm_O2_None", "None", $vals);?> None</td>
<td><?php echo simpleFormCheckBox("TxForm_AED", "AED/SAED", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_PressureDressing", "Pressure Dressing", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_Scoop", "Scoop/Ortho", $vals);?></td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;<?php echo simpleFormRadioButton("TxForm_O2", "TxForm_O2_Nasal", "Nasal Cannula", $vals);?> Nasal</td>
<td>Assisted With:</td>
<td><?php echo simpleFormCheckBox("TxForm_TractionSplint", "Traction Splint", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_Reeves", "Reeves", $vals);?></td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;<?php echo simpleFormRadioButton("TxForm_O2", "TxForm_O2_NRB", "Non-Rebreather", $vals);?> Non-Rebreather</td>
<td>&nbsp;&nbsp;&nbsp;<?php echo simpleFormCheckBox("TxForm_NitroAssist", "Assisted with Nitro", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_FixationSplint", "Fixation Splint", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_StairChair", "Stair Chair", $vals);?></td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;<?php echo simpleFormRadioButton("TxForm_O2", "TxForm_O2_BlowBy", "Blow-By", $vals);?> Blow-By</td>
<td>&nbsp;&nbsp;&nbsp;<?php echo simpleFormCheckBox("TxForm_InhalerAssist", "Assisted With Inhaler", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_SlingSwath", "Sling/Swath", $vals);?></td>
<td>&nbsp;</td>
</tr>
<tr>
<td>&nbsp;&nbsp;&nbsp;<?php echo simpleFormText("TxForm_O2L", array("size" => 2, "maxlength" => 2), $vals); ?> LPM</td>
<td>&nbsp;&nbsp;&nbsp;<?php echo simpleFormCheckBox("TxForm_EpiPenAssist", "Assisted with Epi-Pen", $vals);?></td>
<td>&nbsp;</td>
<td><?php echo simpleFormCheckBox("TxForm_ElevHead", "Elevate Head", $vals);?></td>
</tr>
<tr>
<td><?php echo simpleFormCheckBox("TxForm_BVM", "BVM", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_Glucose", "Glucose", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_Backboard", "Backboard", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_ElevFeet", "Elevate Feet", $vals);?></td>
</tr>
<tr>
<td><?php echo simpleFormCheckBox("TxForm_Suction", "Suction", $vals);?></td>
<td>&nbsp;</td>
<td><?php echo simpleFormCheckBox("TxForm_KED", "KED", $vals);?></td>
<td>&nbsp;</td>
</tr>
<tr>
<td><?php echo simpleFormCheckBox("TxForm_OralAirway", "Oral Airway", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_ColdPack", "Cold Pack", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_HeadBlocks", "Head Blocks/Bed", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_AssistDelivery", "Assisted in Delivery", $vals);?></td>
<tr>
</tr>
<td><?php echo simpleFormCheckBox("TxForm_Nasal Airway", "Nasal Airway", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_HotPack", "Hot Pack", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_Collar", "C-Collar", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_HonoredDNR", "Honored DNR", $vals);?></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><?php echo simpleFormCheckBox("TxForm_FlushArea", "Flush Area", $vals);?></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>&nbsp;</td>
<td><?php echo simpleFormCheckBox("TxForm_BurnSheet", "Burn Sheet", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_Extricated", "Extricated", $vals);?></td>
<td><?php echo simpleFormCheckBox("TxForm_OurEpiPen", "Epi-Pen (from rig)", $vals);?></td>
</tr>

</table>

<div style="text-align: center;"><a href="javascript:submitTxForm()">Done.</a></div>
</form>