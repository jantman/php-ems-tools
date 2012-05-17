<?php

if(isset($_GET['year']) && isset($_GET['month']))
{
    $year = (int)$_GET['year'];
    $month = (int)$_GET['month'];
}
else
{
    die("No year and month specified.");
}

echo '<pre>';

banner();
echo "MEMBERS/CALLS FOR : ".$year."-".$month."\n";
dashedline();

// DB Connection
$conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
mysql_select_db('pcr') or die ('Unable to select database!');
$query  = "SELECT RunNumber,Date FROM calls WHERE YEAR(Date)=".$year." AND MONTH(Date)=".$month." ORDER BY YEAR(Date) ASC, RunNumber ASC;";

$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());

while($row = mysql_fetch_array($result))
{
    // SHOW THE CALL
    showCall($row['RunNumber']);
}
mysql_free_result($result);

echo '</pre>';

function banner()
{
	echo "=====================================================\n";
	echo "=       =====     ===       =========================\n";
	echo "=  ====  ===  ===  ==  ====  ========================\n";
	echo "=  ====  ==  ========  ====  ========================\n";
	echo "=  ====  ==  ========  ===   ==    ===  =   ====   ==\n";
	echo "=       ===  ========      ====  =  ==    =  ==     =\n";
	echo "=  ========  ========  ====  ==  =  ==  =======  =  =\n";
	echo "=  ========  ========  ====  ==    ===  =======  =  =\n";
	echo "=  =========  ===  ==  ====  ==  =====  =======  =  =\n";
	echo "=  ==========     ===  ====  ==  =====  ========   ==\n";
	echo "=====================================================\n";
}

function echoSpace($i)
{
    for($c = 0; $c < $i; $c++)
    {
	echo " ";
    }
}

function dashedLine()
{
    echo "-------------------------------------------------------------------------------------------------------\n";
}

function showCall($runNum)
{
    $conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
    mysql_select_db('pcr') or die ('Unable to select database!');
    $query  = "SELECT * FROM calls WHERE RunNumber=".$runNum.";";
    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
    $row = mysql_fetch_array($result);

    $callInfo = $row['RunNumber'];
    if(strlen($row['RunNumber']) == 5)
    {
	$callInfo .= "     ";
    }
    elseif(strlen($row['RunNumber']) == 6)
    {
	$callInfo .= "    ";
    }
    else
    {
	$callInfo .= "   ";
    }
    $callInfo .= $row['Date'];
    $callInfo .= "   ";
    $callInfo .= $row['TimeDisp'];
    $callInfo .= "-";
    $callInfo .= $row['TimeOut'];
    $callInfo .= "   ";
    $callInfo .= getCrewStr($row);
    echo $callInfo."\n";
}

function parseArgs($argA)
{
    $parsedA = array();
    
    for($i = 1; $i <= count($argA) - 1; $i++)
    {
	$temp = $argA[$i];
	$temp = substr($temp, strripos($temp, "-") + 1, strlen($temp) - strripos($temp, "-"));
	$temp = explode("=", $temp);
	$parsedA[$temp[0]] = $temp[1];
    }

    return $parsedA;
}

function usage()
{
    echo "checkMonthMembers \n";
    echo "for PCRpro for MPAC \n";
    echo "USAGE: \n";
    echo "checkMonthMembers --year=XXXX --month=XX \n";
}

function getCrewStr($row)
{
    $crewStr = "";
    $crewA = array();
    if($row['DriverToScene'] != "")
    {
	$crewA[$row['DriverToScene']] = $row['DriverToScene'];
    }

    if($row['DriverToHosp'] != "")
    {
	$crewA[$row['DriverToHosp']] = $row['DriverToHosp'];
    }

    if($row['DriverToBldg'] != "")
    {
	$crewA[$row['DriverToBldg']] = $row['DriverToBldg'];
    }

    if($row['crew1'] != "")
    {
	$crewA[$row['crew1']] = $row['crew1'];
    }
    if($row['crew2'] != "")
    {
	$crewA[$row['crew2']] = $row['crew2'];
    }
    if($row['crew3'] != "")
    {
	$crewA[$row['crew3']] = $row['crew3'];
    }
    if($row['crew4'] != "")
    {
	$crewA[$row['crew4']] = $row['crew4'];
    }
    if($row['crew5'] != "")
    {
	$crewA[$row['crew5']] = $row['crew5'];
    }
    if($row['crew6'] != "")
    {
	$crewA[$row['crew6']] = $row['crew6'];
    }

    foreach ($crewA as $key => $value)
    {
	if($crewStr == "")
	{
	    $crewStr .= $value;
	}
	else
	{
	    $crewStr .= ", ".$value;
	}
    }

    return $crewStr;
}

?>