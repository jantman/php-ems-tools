<?php
// MPAC simple stats functions

$temp = truckMileageLast30days();
echo var_dump($temp);

function truckMileageLast30days()
{
	$conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
	mysql_select_db('pcr') or die ('Unable to select database!');
	$daysAgo30 = date("Y-m-d", (time()-(30*24*60*60)));
	$query = 'SELECT Unit,EndMileage FROM calls WHERE Date >= "'.$daysAgo30.'";';
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());  

	$start588 = PHP_INT_MAX;
	$start589 = PHP_INT_MAX;
	$end588 = 0;
	$end589 = 0;
	$count588 = 0;
	$count589 = 0;

	while ($row = mysql_fetch_array($result)) 
	{
	    if($row['Unit'] == "588")
	    {
		if($row['EndMileage'] < $start588){ $start588 = $row['EndMileage'];}
		if($row['EndMileage'] > $end588){ $end588 = $row['EndMileage'];}
		$count588++;
	    }
	    elseif($row['Unit'] == "589")
	    {
		if($row['EndMileage'] < $start589){ $start589 = $row['EndMileage'];}
		if($row['EndMileage'] > $end589){ $end589 = $row['EndMileage'];}
		$count589++;
	    }
	}

	$retVal = array("count" => array("588" => $count588, "589" => $count589), "miles" => array("588" => ($end588 - $start588), "589" => ($end589 - $start589)));
	return $retVal;
}


?>