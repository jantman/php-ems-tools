<html>
<head><title>Kentshire Call Count</title></head>
<body>
<?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-01-29 16:06:21 jantman"                                                              |
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
 | $LastChangedRevision:: 51                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/stats/index.php                                        $ |
 +--------------------------------------------------------------------------------------------------------+
*/

require_once("../newcall/inc/antman.php");
require_once('../newcall/inc/newcall.php.inc');
require_once('../newcall/inc/runNum.php');

if(! empty($_GET['year']))
{
    //style monthly or yearly
    $year = $_GET['year'];
}
else
{
    $year = date("Y");
}

$yearStart = strtotime($year."-05-01 00:00:00");
$yearEnd = strtotime(((int)$year+1)."-04-31 23:59:59");

echo '<h3>Midland Park Ambulance Corps - Kentshire Calls Count for '.$year.'<br>'.date("Y-m-d", $yearStart)." through ".date("Y-m-d", $yearEnd).'</h3>';
echo '<p><a href="kentshireCount.php?year='.($year-1).'">'.($year-1).'</a>&nbsp;&nbsp;&nbsp;<a href="kentshireCount.php?year='.($year+1).'">'.($year+1).'</a></p>';

echo '<p><em>NOTE: This will only show calls from 1/1/2010 on.</em></p>';

echo '<p><strong>Please check that all addresses are correct before using the final count!</strong></p>';

// hack to import 2009 calls from old software
if($year == 2009)
{
    echo '<h2>Begin 2009 Calls</h2>';
    $query = "SELECT * FROM OLDcalls WHERE YEAR(Date)=".$year." OR YEAR(Date)=".((int)$year+1).";";
    $result = mysql_query($query) or die("MySQL Query Error: ".mysql_error());
    echo '<table border=1>';
    $count = 0;
    echo '<tr>';
    echo '<td><b>Run #</b></td>';
    echo '<td><b>Date</b></td>';
    echo '<td><b>Disp. Time</b></td>';
    echo '<td><b>Call Type</b></td>';
    echo '<td><b>Pt. Address</b></td>';
    echo '<td><b>Call Location</b></td>';
    echo '</tr>';
    while($r = mysql_fetch_array($result))
    {
	if(validDate($r['Date']))
	{
	    $add = $r['PtAddress'];
	    $loc = $r['CallLoc'];
	    if(stristr($add, "Kents") || (stristr($add, "187") && stristr($add, "Pat")) || stristr($loc, "Kents") || (stristr($loc, "187") && stristr($loc, "Pat")))
	    {
		$count++;
		echo '<tr>';
		echo '<td>'.$r['RunNumber'].'</td>';
		echo '<td>'.$r['Date'].'</td>';
		echo '<td>'.$r['TimeDisp'].'</td>';
		echo '<td>'.$r['CallType'].'</td>';
		echo '<td>'.$r['PtAddress'].'</td>';
		if($loc != "Home")
		{
		    echo '<td><b>'.$r['CallLoc'].'</b></td>';
		}
		else
		{
		    echo '<td>'.$r['CallLoc'].'</td>';
		}
		echo '</tr>';
	    }
	}
    }

    echo '</table>';
    echo '<p><h3>'.$count.' calls shown above. Please confirm that all are actually to 187 Paterson Ave.</h3></p>';
    echo '<h2>End 2009 Calls</h2>';
}
// end 2009 hack
	
echo '<table border=1>';
$count = 0;
echo '<tr>';
echo '<th>Run #</b></td>';
echo '<th>Date</th>';
echo '<th>Disp. Time</th>';
echo '<th>Call Type</th>';
echo '<th>Outcome</th>';
echo '<th>Pt Address</th>';
echo '<th>Call Location</th>';
echo '<th>Pt Location</th>';
echo '</tr>';

$query = "SELECT c.RunNumber,c.date_date,c.outcome,c.call_type,cl.AptNumber,cl.Street,cl.StreetNumber,cl.City,c.pt_loc_at_scene,ct.dispatched,p.City AS ptCity,p.Street AS ptStreet,p.StreetNumber AS ptStreetNumber,p.AptNumber AS ptAptNumber,p.State AS ptState FROM calls AS c LEFT JOIN calls_locations AS cl ON c.call_loc_id=cl.call_loc_id LEFT JOIN calls_times AS ct ON c.RunNumber=ct.RunNumber LEFT JOIN patients AS p ON p.Pkey=c.patient_pkey WHERE c.is_deprecated=0 AND cl.is_deprecated=0 AND ct.is_deprecated=0 AND ((cl.City='Midland Park' AND cl.Street='Paterson Ave' AND cl.StreetNumber='187') OR (p.City='Midland Park' AND p.Street='Paterson Ave' AND p.StreetNumber='187')) AND c.date_ts >= $yearStart AND c.date_ts <= $yearEnd;";
$result = mysql_query($query) or die("MySQL Query Error: ".mysql_error());
while($r = mysql_fetch_array($result))
{
    $count++;
    echo '<tr>';
    echo '<td><a href="/newcall/newcall.php?RunNumber='.$r['RunNumber'].'">'.formatRunNum($r['RunNumber']).'</a></td>';
    echo '<td>'.$r['date_date'].'</td>';
    echo '<td>'.date("H:i:s", $r['dispatched']).'</td>';
    echo '<td>'.$r['call_type'].'</td>';
    echo '<td>'.$r['outcome'].'</td>';
    echo '<td>'.makeAddress($r['ptStreetNumber'], $r['ptStreet'], $r['ptAptNumber'], $r['ptCity'], $r['ptState']).'</td>';
    echo '<td>'.makeAddress($r['StreetNumber'], $r['Street'], $r['AptNumber'], $r['City']).'</td>';
    echo '<td>'.$r['pt_loc_at_scene'].'</td>';
    echo '</tr>';
}

echo '</table>';
echo '<p><h3>'.$count.' calls shown above. Please confirm that all are actually to 187 Paterson Ave (and not that the person just lives there but the call was at another location).</h3></p>';

?>
</body>
</html>

<?php
// this is only needed for the calls before 1/1/2010. once we're into CY2011 we should be able to ditch it.
function validDate($dateStr)
{
    $dateTS = strtotime($dateStr);
    global $yearStart;
    global $yearEnd;
    if($yearStart <= $dateTS && $yearEnd >= $dateTS)
    {
	return true;
    }
    else
    {
	return false;
    }
}

?>