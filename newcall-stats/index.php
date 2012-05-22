<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-07-26 09:37:21 jantman"                                                              |
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
 | $LastChangedRevision:: 57                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/stats/index.php                                        $ |
 +--------------------------------------------------------------------------------------------------------+
*/

if(isset($_POST['action'])){ processForm();}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $shortName;?> Call Reports</title>
<link rel="stylesheet" type="text/css" href="css/index.css" />
</head>

<body>
<h1 style="text-align: center;">Call Statistics and Reports</h1>
<p style="margin-left: 2em;"><em><strong>NOTE:</strong> This is for the NEW call report software only. The statistics here are from January 1, 2010 on.</em></p>

<ul>
<li><a href="countGenerals.php">Generals Count</a></li>
<li><a href="mutual_aid_log.php">Mutual Aid Log</a></li>
<li><a href="godwinCount.php">Calls to 301 Godwin Ave</a></li>
<li><a href="kentshireCount.php">Calls to Kentshire</a></li>
<li><a href="night_generals.php">General Calls at Night (6PM-9AM)</a></li>
</ul>

<hr />

<form action="index.php" method="post" name="firstForm" id="firstForm">
<div>

<p><strong>Call Statistics</strong></p>

<div style="float: left; margin-left: 2em;">
<label for="action"><strong>Action:</strong></label>
</div>
<div style="float: left; margin-left: 2em;">
<input id="action_mc" name="action" value="monthCall" type="radio" /><label for="action_mc">Monthly Call Stats (Simple)</label><br />
<input id="action_yc" name="action" value="yearCall" type="radio" /><label for="action_yc">Yearly Call Stats (Simple)</label><br />
<input id="action_memb" name="action" value="membCall" type="radio" /><label for="action_memb">Calls Summary by Member</label><br />
<input id="action_graph" name="action" value="membGraph" type="radio" /><label for="action_graph">Graph of calls and hours by month, for a single member</label>
</div>
<div style="clear: both;"></div>

<div style="float: left; margin-left: 2em; margin-top: 1em;">
<label for="month"><strong>Month:</strong></label>
</div>
<div style="float: left; margin-left: 2em; margin-top: 1em;">
<select name="month">
<?php
for($i = 1; $i < 13; $i++)
{
    echo '<option value="'.$i.'"';
    if($i == date("m")){ echo ' selected="selected"';}
    echo '">'.date("F", strtotime("2010-".$i."-01")).'</option>';
}
?>
</select>
</div>
<div style="clear: both;"></div>

<div style="float: left; margin-left: 2em; margin-top: 1em;">
<label for="year"><strong>Year:</strong></label>
</div>
<div style="float: left; margin-left: 2em; margin-top: 1em;">
<select name="year">
<?php
for($i = (date("Y")-10); $i < (date("Y")+2); $i++)
{
    echo '<option value="'.$i.'"';
    if($i == date("Y")){ echo ' selected="selected"';}
    echo '">'.date("Y", strtotime($i."-01-01")).'</option>';
}
?>
</select>
</div>
<div style="clear: both;"></div>

<div style="float: left; margin-left: 2em; margin-top: 1em;">
<label for="emtid"><strong>EMTid:</strong></label>
</div>
<div style="float: left; margin-left: 2em; margin-top: 1em;">
<input id="emtid" name="emtid" type="text" size="5" /> <em>(for member call summary)</em>
</div>
<div style="clear: both;"></div>

<div style="margin-left: 2em; margin-top: 1em;">
<input name="btnSubmit" value="Get Statistics" type="submit" />
<input name="btnReset" value="Reset" type="reset" />
</div>


</div>
</form>

</body>
</html>


<?php
function processForm()
{
   $action = $_POST['action'];
   $month = $_POST['month'];
   $year = $_POST['year'];
   $emtid = $_POST['emtid'];
   if ($action == 'monthCall')
   {
       $loc = "simpleMonthly.php?month=".$month."&year=".$year;
   }
   else if ($action == 'yearCall')
   {
       $loc = "simpleMonthly.php?year=".$year;
   }
   else if ($action == 'membCall')
   {
       $loc = "membCalls.php?EMTid=".$emtid;
   } 
   elseif($action == "membGraph")
   {
       $loc = "membGraphs.php?EMTid=".$emtid;
   }
   header('location: '.$loc);  
}
?>