<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-11-30 11:46:46 jantman"                                                              |
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
 | $LastChangedRevision:: 73                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/index.php                                              $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * Index page.
 *
 * @package MPAC-NewCall-Pages
 */

require_once('inc/runNum.php');
require_once('inc/newcall.php.inc');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php echo '<meta name="generator" content="MPAC PCR version '.$_VERSION.' (r'.stripSVNstuff($_SVN_rev).')">'."\n"; ?>
<title><?php echo $shortName;?> Call Reports</title>
<link rel="stylesheet" type="text/css" href="css/index.css" />
</head>

<body>

<div style="margin-top: 0.5em; margin-bottom: 0; text-align: left;"><a href="../">Home</a></div>

<h1><?php echo $shortName;?> Call Reports</h1>
<h2><a href="newcall.php">&rarr;Input New Call&larr;</a></h2>

<table class="callTable">
<tr><th>Run Number</th><th>Date</th><th>Unit</th><th>Dispatch Time</th><th>Crew</th><th>Call Type</th><th>Outcome</th><th>&nbsp;</th></tr>
<?php
$query = "SELECT c.RunNumber,c.call_type,c.date_ts,c.outcome,c.is_duty_call,c.is_second_rig,t.dispatched,cu.unit FROM calls AS c LEFT JOIN calls_times AS t ON c.RunNumber=t.RunNumber LEFT JOIN calls_units AS cu ON c.RunNumber=cu.RunNumber WHERE c.is_deprecated=0 AND t.is_deprecated=0 ORDER BY RunNumber DESC LIMIT 20;";
$result = mysql_query($query) or die("Error in query: $query <br />ERROR: ".mysql_error());
while($row = mysql_fetch_assoc($result))
{
    echo '<tr>';
    echo '<td>'.formatRunNum($row['RunNumber']).'</td>';
    echo '<td>'.date("m/d/Y", $row['date_ts']).'</td>';
    echo '<td>'.$row['unit'].'</td>';
    echo '<td>'.date("H:i", $row['dispatched']).'</td>';

    if($row['is_duty_call'] == 1){ echo '<td>Duty</td>';}
    elseif($row['is_second_rig'] == 1){ echo '<td>2<sup>nd</sup> Rig</td>';}
    else { echo '<td>General</td>';}

    echo '<td>'.$row['call_type'].'</td>';
    echo '<td>'.$row['outcome'].'</td>';

    echo '<td>';
    echo '<a href="newcall.php?RunNumber='.$row['RunNumber'].'">View</a>';
    echo '&nbsp;&nbsp;&nbsp;';
    echo 'Edit';
    echo '&nbsp;&nbsp;&nbsp;';
    echo '<a href="printCall.php?runNum='.$row['RunNumber'].'">Print</a>';
    echo '</td>';
	
    echo '</tr>'."\n";
}

?>
</table>

<div style="margin-top: 2em; margin-bottom: 2em;">
<a href="pcr2c.pdf">Print Blank Call Report</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="RMAform.pdf">Print Blank RMA Form</a>
</div>

<div style="margin-top: 2em; margin-bottom: 2em;">
<form name="goToCall" method="GET" action="newcall.php">
<label for="RunNumber"><strong>View Call by Run Number:</strong> <input type="text" name="RunNumber" id="RunNumber" size="10" />
<input type="submit" value="View Call" />
</form>
</div>

<div style="margin-top: 2em; margin-bottom: 2em;">
<p><strong><a href="../newcall-stats/">Call Stats</a></strong></p>
</div>

<div class="bottomdiv">
<?php echo "MPAC PCR version $_VERSION (r".stripSVNstuff($_SVN_rev).")"; ?>
</div>

</body>
</html>