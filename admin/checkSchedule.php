<?php
// checks to see where a member is on the schedule for the rest of the year
// Time-stamp: "2008-10-22 17:27:38 jantman"
// $Id: checkSchedule.php,v 1.1 2008/10/22 21:35:57 jantman Exp $

$conn = mysql_connect();
mysql_select_db("pcr") or die("Unable to select database.");

$signonData = array();

$HTMLoutput = false;

if(isset($argv[1]))
{
    $ID = mysql_real_escape_string(trim($argv[1]));
}
elseif(isset($_GET['EMTid']))
{
    $ID = mysql_real_escape_string(trim($_GET['EMTid']));
    $HTMLoutput = true;
}

if(! isset($ID))
{
    echo "USAGE: checkSchedule EMTid\n\n\n";
}

if($HTMLoutput == true){ echo '<h2>Schedule Data - '.date('M Y').' through end of year for EMT '.$ID.'</h2><pre>';}

for($x = date("n"); $x <= 12; $x++)
{
    $monthNum = str_pad($x, 2, "0", STR_PAD_LEFT);
    $year = date("Y");
    // DAY
    $tblName = "schedule_".$year."_".$monthNum."_day";
    $query = "SELECT * FROM ".$tblName." WHERE 1ID='".$ID."' OR 2ID='".$ID."' OR 3ID='".$ID."' OR 4ID='".$ID."' OR 5ID='".$ID."' OR 6ID='".$ID."';";
    $result = mysql_query($query) or die("Error in query: ".$query."\nError: ".mysql_error());
    while($row = mysql_fetch_assoc($result))
    {
	for($i = 1; $i < 7; $i++)
	{
	    if($row[$i.'ID'] == $ID)
	    {
		addSignon($year, $monthNum, $row['Date'], "day", $row[$i.'Start'], $row[$i.'End']);
	    }
	}
    }

    // NIGHT
    $tblName = "schedule_".date("Y")."_".$monthNum."_night";
    $query = "SELECT * FROM ".$tblName." WHERE 1ID='".$ID."' OR 2ID='".$ID."' OR 3ID='".$ID."' OR 4ID='".$ID."' OR 5ID='".$ID."' OR 6ID='".$ID."';";
    $result = mysql_query($query) or die("Error in query: ".$query."\nError: ".mysql_error());
    while($row = mysql_fetch_assoc($result))
    {
	for($i = 1; $i < 7; $i++)
	{
	    if($row[$i.'ID'] == $ID)
	    {
		addSignon($year, $monthNum, $row['Date'], "night", $row[$i.'Start'], $row[$i.'End']);
	    }
	}
    }

}

echo "YEAR\tMo\tD\tShift\tStart   \tEnd\n";
echo "--------------------------------------------------------\n";
foreach($signonData as $key => $arr)
{
    echo $arr[0]."\t".$arr[1]."\t".$arr[2]."\t".$arr[3]."\t".$arr[4]."\t".$arr[5]."\n";
}

if($HTMLoutput == true){ echo '</pre><p><em>As of '.date("Y-m-d H:i:s").'</em></p>';}

function addSignon($year, $month, $date, $shift, $start, $end)
{
    global $signonData;
    // make a timestamp for the start of the signon
    $signonTS = strtotime($year."-".$month."-".$date." ".$start);

    // if we're on the night shift but 0000-0600, the date given is one day behind the calendar date
    if($shift == "night" && date("H", $signonTS) < 6)
    {
	$signonTS += 86400;
    }

    $signonData[$signonTS] = array($year, $month, $date, $shift, $start, $end);
}


?>