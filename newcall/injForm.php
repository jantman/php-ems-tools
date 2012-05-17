<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2009-12-29 11:32:21 jantman"                                                              |
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
 | $LastChangedRevision:: 12                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/injForm.php                                            $ |
 +--------------------------------------------------------------------------------------------------------+
*/

$vals = array(); // TODO - populate from GET or POST
if(isset($_GET['inj']))
{
    $vals = explode(", ", $_GET['inj']);
}

require_once('inc/simpleForms.php');

if(! isset($_SERVER["HTTP_REFERER"]) || trim($_SERVER["HTTP_REFERER"]) == "")
{
    echo '<html><head><title>Injured Area Form</title>';
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
<form name="InjForm" id="InjForm">
<table>
<tr>
<td><strong>Injured Area</strong></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td><strong>L</strong></td>
<td><strong>R</strong></td>
<td>&nbsp;</td>
<td><strong>L</strong></td>
<td><strong>R</strong></td>
</tr>
<tr>
<td><?php echo simpleFormCheckBox("InjForm_Head", "Head", $vals);?></td>
<td><?php echo simpleFormCheckBox("InjForm_Back", "Back", $vals);?></td>
<td>Arm</td>
<td><?php echo simpleFormCheckBox("InjForm_ArmL", "Left Arm", $vals, false);?></td>
<td><?php echo simpleFormCheckBox("InjForm_ArmR", "Right Arm", $vals, false);?></td>
<td>Hip</td>
<td><?php echo simpleFormCheckBox("InjForm_HipL", "Left Hip", $vals, false);?></td>
<td><?php echo simpleFormCheckBox("InjForm_HipR", "Right Hip", $vals, false);?></td>
</tr>
<tr>
<td><?php echo simpleFormCheckBox("InjForm_Face", "Face", $vals);?></td>
<td><?php echo simpleFormCheckBox("InjForm_Chest", "Chest", $vals);?></td>
<td>Hand</td>
<td><?php echo simpleFormCheckBox("InjForm_HandL", "Left Hand", $vals, false);?></td>
<td><?php echo simpleFormCheckBox("InjForm_HandR", "Right Hand", $vals, false);?></td>
<td>Leg</td>
<td><?php echo simpleFormCheckBox("InjForm_LegL", "Left Leg", $vals, false);?></td>
<td><?php echo simpleFormCheckBox("InjForm_LegR", "Right Leg", $vals, false);?></td>
</tr>
<tr>
<td><?php echo simpleFormCheckBox("InjForm_Neck", "Neck", $vals);?></td>
<td><?php echo simpleFormCheckBox("InjForm_Abd", "Abdomen", $vals);?></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>Foot</td>
<td><?php echo simpleFormCheckBox("InjForm_FootL", "Left Foot", $vals, false);?></td>
<td><?php echo simpleFormCheckBox("InjForm_FootR", "Right Foot", $vals, false);?></td>
</tr>
<tr>
<td colspan="8"><div style="text-align: center;"><a href="javascript:submitInjForm()">Done.</a></div></td>
</tr>
</table>
</form>