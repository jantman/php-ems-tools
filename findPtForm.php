<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-25 22:57:13 jantman"                                                              |
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
 | $HeadURL:: http://svn.jasonantman.com/newcall/findPtForm.php                                         $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * Find patient form.
 *
 * @package MPAC-NewCall-Forms
 */

require_once('inc/newcall.php.inc');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php echo '<meta name="generator" content="MPAC PCR version '.$_VERSION.' (r'.stripSVNstuff($_SVN_rev).')">'."\n"; ?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Find Patient</title>
</head>
<body>
<form name="signon_form">

<div>

<p><em>Fill in only what information you know. Filling in more fields will narrow down the results.</em> <strong>It's probably best to just enter the last name (be as general as possible).</strong></p>
<p><em>TIP: Enter as much of the last name as you *know* is accurate. Just entering the first few letters is probably best. Or enter the name of the street the patient lives on (but this is a problem for pts who went from home to 36 Faner to 187 Paterson).</em></p>
<p><strong>DO NOT use this form if you didn't make physical contact with a patient. If the call was canceled, DOA, unable to fill crew, etc. then just enter a Call Location.</strong></p>

<table border="0">
<tr>
<td><label for="FindPt_FirstName"><strong>First Name:</strong></label><input type="text" size="20" name="FindPt_FirstName" id="FindPt_FirstName" <?php if(isset($_GET['FindPt_FirstName'])){ echo 'value="'.$_GET['FindPt_FirstName'].'" ';} ?>/></td>
<td><label for="FindPt_LastName"><strong>Last Name:</strong></label><input type="text" size="20" name="FindPt_LastName" id="FindPt_LastName" <?php if(isset($_GET['FindPt_LastName'])){ echo 'value="'.$_GET['FindPt_LastName'].'" ';} ?>/></td>
</tr>
<tr>
<td><label for="FindPt_DOB"><strong>DOB:</strong></label><input type="text" size="10" name="FindPt_DOB" id="FindPt_DOB" <?php if(isset($_GET['FindPt_DOB'])){ echo 'value="'.$_GET['FindPt_DOB'].'" ';} ?>/> <em>mm/dd/YYYY</em></td>
<td><label for="FindPt_Address"><strong>Street:</strong></label><input type="text" size="30" name="FindPt_Address" id="FindPt_Address" <?php if(isset($_GET['FindPt_Address'])){ echo 'value="'.$_GET['FindPt_Address'].'" ';} ?>/></td>
</tr>

</table>

<h2 style="text-align: center;"><a href="javascript:submitFindPtForm()">Search Patients</a></h2>

</div></form>

<div> <!-- BEGIN results div -->
<?php
if(count($_GET) > 0)
{
    $query = "SELECT Pkey,patient_id,FirstName,LastName,MiddleName,Sex,DOB,StreetNumber,Street,AptNumber,City,State FROM patients WHERE ";
    if(isset($_GET['FirstName']))
    {
	$query .= "(FirstName LIKE '%".$_GET['FirstName']."%' OR SOUNDEX(FirstName) = SOUNDEX('".$_GET['FirstName']."'))";
    }
    if(isset($_GET['LastName']))
    {
	if(substr($query, -1) == "'"){ $query .= " AND ";}
	$query .= "(LastName LIKE '%".$_GET['LastName']."%' OR SOUNDEX(LastName) = SOUNDEX('".$_GET['LastName']."') OR LEVENSHTEIN_RATIO(LastName, '".$_GET['LastName']."') > 60)";
    }
    if(isset($_GET['DOB']))
    {
	if(substr($query, -1) == "'"){ $query .= " AND ";}
	$query .= "DOB LIKE '".date("Y-m-d", strtotime($_GET['DOB']))."'";
    }
    if(isset($_GET['Address']))
    {
	if(substr($query, -1) == "'"){ $query .= " AND ";}
	$query .= "Street LIKE '%".$_GET['Address']."%'";
    }

    $query .= " AND is_deprecated=0;";
    $result = mysql_query($query) or die("Error in Query: ".$query."<br />ERROR: ".mysql_error()."<br />");

    echo '<table class="ptSearch">'."\n";
    echo '<tr><th>&nbsp;</th><th>Last Name</th><th>First Name</th><th>Address</th><th>City</th><th>Sex</th><th>DOB</th><th>Age</th><th>ID</th></tr>'."\n";
    if(mysql_num_rows($result) < 1)
    {
	echo '<tr><td colspan="9"><span style="font-weight: bold; text-align: center;">No results found.</span></td></tr>'."\n";
    }
    else
    {
	while($row = mysql_fetch_assoc($result))
	{
	    echo '<tr>';
	    echo '<td><a href="javascript:setPatient('.$row['patient_id'].')">Select</a>  <a href="javascript:updatePatient('.$row['patient_id'].')">Update</a></td>';
	    echo '<td>'.$row['LastName'].'</td>';
	    echo '<td>'.$row['FirstName'].'</td>';
	    echo '<td>'.makeAddress($row['StreetNumber'], $row['Street'], $row['AptNumber']).'</td>';
	    echo '<td>'.$row['City'].', '.$row['State'].'</td>';
	    echo '<td>'.$row['Sex'].'</td>';
	    echo '<td>'.$row['DOB'].'</td>';
	    echo '<td>'.findAgeFromDOB($row['DOB']).'</td>';
	    echo '<td>'.$row['patient_id'].'</td>';
	    echo '</tr>'."\n";
	}
    }
    echo '</table>'."\n";
    echo '<h2 style="text-align: center;"><a href="javascript:addPatient()">Add New Patient</a></h2>'."\n";
}
?>
</div> <!-- END results div -->

</body>
</html>


