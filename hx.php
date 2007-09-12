#! /usr/bin/php

<?php

// Patient History Search for PCRpro
//PHP CLI version

$cli = true;

$runNum = -1;
$resA = null;

$fields = array(0 => 'RunNumber', 1 => 'Date', 2 => 'PtLastName', 3 => 'PtFirstName', 4 => 'Age', 5 => 'PtAddress'); // for printing
$maxLen = null; // for printing

newline();
start();

// Exit correctly
exit(0);

function start()
{
	banner();
	fwrite(STDOUT, "\n");	
	fwrite(STDOUT, "PHP CLI Patient History Search.\n");
	dashedLine();
	fwrite(STDOUT, "\n");
	fwrite(STDOUT, "(C) 2006 Jason Antman. All Rights Reserved.\n");
	fwrite(STDOUT, "This program may not be copied or redistributed without the express written permission of the author.\n");
	dashedLine();
	fwrite(STDOUT, "\n");
	fwrite(STDOUT, "\n");
	fwrite(STDOUT, "Available Commands (please type a number)\n");
	dashedLine();
	fwrite(STDOUT, "You may type '0' at any prompt to exit the program.\n");
	fwrite(STDOUT, "If the program should freeze, you may press Crtl+C to end it IMMEDIATELY.\n");
	dashedLine();
	fwrite(STDOUT, "0) EXIT \n");
	fwrite(STDOUT, "1) Search by Last Name\n");
	fwrite(STDOUT, "2) Search by First Name\n");
	fwrite(STDOUT, "3) Search by Patient Address\n");
	fwrite(STDOUT, "4) Search by Call Location\n");
	// Read the input
	$command = fgets(STDIN);
	
	if($command==0)
	{
		exit(0);
	}
	elseif($command==1)
	{
		searchLastName("");
	}
	elseif($command==2)
	{
		searchFirstName("");
	}
	elseif($command==3)
	{
		searchPtAddress("");
	}
	elseif($command==4)
	{
		searchCallLoc("");
	}
	else
	{
		fwrite(STDOUT, "\n");
		fwrite(STDOUT, "\n");		
		fwrite(STDOUT, "\n");		
		fwrite(STDOUT, "INVALID OPTION. PLEASE TRY AGAIN, OR TYPE 'CANCEL' TO EXIT.\n");
		fwrite(STDOUT, "\n");
		fwrite(STDOUT, "\n");
		fwrite(STDOUT, "\n");
		start();
	}
}

function searchLastName($lastName)
{
    global $resA;
    global $header;
    $resA = null;
	fwrite(STDOUT, "\n");
	fwrite(STDOUT, "\n");

	if($lastName == null || $lastName == "")
	{
	    fwrite(STDOUT, "Enter the patient's last name:\n");

	    $origLastName = trim(fgets(STDIN));
	    $lastName = strtoupper($origLastName);
	}
	else
	{
	    $origLastName = $lastName;
	}
	newline();
	$conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
	mysql_select_db('pcr') or die ('Unable to select database!');
	$query  = "SELECT RunNumber,PtLastName FROM calls ORDER BY RunNumber;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());


	while($row = mysql_fetch_array($result))
	{
	    $last = strtoupper($row['PtLastName']);
	 
	    if(similar_text($lastName, $last) > (strlen($last) * (3/4))) // if 75% of chars are similar
	    {
		$resA[count($resA) + 1] = $row['RunNumber'];
	    }
	    
	}

	for($i = 1; $i < count($resA)+1; $i++)
	{
	    $query  = "SELECT * FROM calls WHERE RunNumber=".$resA[$i]." ORDER BY RunNumber;";
	    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	    setFormatting($result);
	}

	// setup display
	fwrite(STDOUT, "Patient History for Last Name '".$origLastName."'"."\n");
	printHeader();
	dashedLine();
	for($i = 1; $i < count($resA)+1; $i++)
	{
	    $query  = "SELECT * FROM calls WHERE RunNumber=".$resA[$i]." ORDER BY RunNumber;";
	    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	    printFormatted($result, $i);
	}

	dashedLine();
	fwrite(STDOUT, "TOTAL CALLS FOUND: ".count($resA)."\n");
	dashedLine();
	fwrite(STDOUT, "PRESS: \n 0 TO GO BACK TO MAIN MENU \n OR\n The First number of a line to Display CALL INFORMATION.\n");

	$command = trim(fgets(STDIN));
	if($command==0)
	{
	    newline();
	    start();
	}
	else
	{
	    newline();
	    showCallSummary($resA[$command], "lastName", $lastName);
	}
}

function searchFirstName($firstName)
{
    global $resA;
    $resA = null;
	fwrite(STDOUT, "\n");
	fwrite(STDOUT, "\n");

	if($firstName == null || $firstName == "")
	{
	    fwrite(STDOUT, "Enter the Patient's first name:\n");

	    $origFirstName = trim(fgets(STDIN));
	    $firstName = strtoupper($origFirstName);
	}
	else
	{
	    $origFirstName = $firstName;
	}
	newline();
	$conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
	mysql_select_db('pcr') or die ('Unable to select database!');
	$query  = "SELECT RunNumber,PtFirstName FROM calls ORDER BY RunNumber;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());



	while($row = mysql_fetch_array($result))
	{
	    $first = strtoupper($row['PtFirstName']);
	 
	    if(similar_text($firstName, $first) > (strlen($first) * (3/4))) // if 75% of chars are similar
	    {
		$resA[count($resA) + 1] = $row['RunNumber'];
	    }
	    
	}

	for($i = 1; $i < count($resA)+1; $i++)
	{
	    $query  = "SELECT * FROM calls WHERE RunNumber=".$resA[$i]." ORDER BY RunNumber;";
	    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	    setFormatting($result);
	}

	// setup display
	fwrite(STDOUT, "Patient History for First Name '".$origFirstName."'"."\n");
	printHeader();
	dashedLine();

	for($i = 1; $i < count($resA)+1; $i++)
	{
	    $query  = "SELECT * FROM calls WHERE RunNumber=".$resA[$i]." ORDER BY RunNumber;";
	    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	    printFormatted($result, $i);
	}
	dashedLine();
	fwrite(STDOUT, "TOTAL CALLS FOUND: ".count($resA)."\n");
	dashedLine();
	fwrite(STDOUT, "PRESS: \n 0 TO GO BACK TO MAIN MENU \n OR\n The First number of a line to Display CALL INFORMATION.\n");

	$command = trim(fgets(STDIN));
	if($command==0)
	{
	    newline();
	    start();
	}
	else
	{
	    newline();
	    showCallSummary($resA[$command], "firstName", $firstName);
	}
}

function searchPtAddress($address)
{
    global $resA;
    $resA = null;
	fwrite(STDOUT, "\n");
	fwrite(STDOUT, "\n");

	if($address == null || $address == "")
	{
	    fwrite(STDOUT, "Enter the patient's home address (excluding town):\n");

	    $origAddress = trim(fgets(STDIN));
	    $address = strtoupper($origAddress);
	}
	else
	{
	    $origAddress = $address;
	}
	newline();
	$conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
	mysql_select_db('pcr') or die ('Unable to select database!');
	$query  = "SELECT RunNumber,PtAddress FROM calls ORDER BY RunNumber;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());

	while($row = mysql_fetch_array($result))
	{
	    $add = strtoupper($row['PtAddress']);
	 
	    if(compareAddress($address, $add)) // if 75% of chars are similar
	    {
		$resA[count($resA) + 1] = $row['RunNumber'];
	    }
	    
	}

	for($i = 1; $i < count($resA)+1; $i++)
	{
	    $query  = "SELECT * FROM calls WHERE RunNumber=".$resA[$i]." ORDER BY RunNumber;";
	    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	    setFormatting($result);
	}

	// setup display
	fwrite(STDOUT, "Patient History for Address '".$origAddress."'"."\n");
	printHeader();
	dashedLine();

	for($i = 1; $i < count($resA)+1; $i++)
	{
	    $query  = "SELECT * FROM calls WHERE RunNumber=".$resA[$i]." ORDER BY RunNumber;";
	    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	    printFormatted($result, $i);
	}
	dashedLine();
	fwrite(STDOUT, "TOTAL CALLS FOUND: ".count($resA)."\n");
	dashedLine();
	fwrite(STDOUT, "PRESS: \n 0 TO GO BACK TO MAIN MENU \n OR\n The First number of a line to Display CALL INFORMATION.\n");

	$command = trim(fgets(STDIN));
	if($command==0)
	{
	    newline();
	    start();
	}
	else
	{
	    newline();
	    showCallSummary($resA[$command], "address", $address);
	}
}

function searchCallLoc($address)
{
    global $resA;
    $resA = null;
	fwrite(STDOUT, "\n");
	fwrite(STDOUT, "\n");

	if($address == null || $address == "")
	{
	    fwrite(STDOUT, "Enter the location of the call (excluding town):\n");

	    $origAddress = trim(fgets(STDIN));
	    $address = strtoupper($origAddress);
	}
	else
	{
	    $origAddress = $address;
	}
	newline();
	$conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
	mysql_select_db('pcr') or die ('Unable to select database!');
	$query  = "SELECT RunNumber,CallLoc,PtAddress FROM calls ORDER BY RunNumber;";
	$result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());

	while($row = mysql_fetch_array($result))
	{
	    $add = strtoupper($row['CallLoc']);
	    $home = strtoupper($row['PtAddress']);

	    if(compareAddress($address, $add))
	    {
		$resA[count($resA) + 1] = $row['RunNumber'];
	    }
	    elseif($add == "HOME")
	    {
		if(compareAddress($address, $home)) // if 75% of chars are similar
		{
		    $resA[count($resA) + 1] = $row['RunNumber'];
		}
	    }

	}

	for($i = 1; $i < count($resA)+1; $i++)
	{
	    $query  = "SELECT * FROM calls WHERE RunNumber=".$resA[$i]." ORDER BY RunNumber;";
	    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	    setFormatting($result);
	}

	// setup display
	fwrite(STDOUT, "Patient History for Call Location: '".$origAddress."'"."\n");
	printHeader();
	dashedLine();

	for($i = 1; $i < count($resA)+1; $i++)
	{
	    $query  = "SELECT * FROM calls WHERE RunNumber=".$resA[$i]." ORDER BY RunNumber;";
	    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
	    printFormatted($result, $i);
	}
	dashedLine();
	fwrite(STDOUT, "TOTAL CALLS FOUND: ".count($resA)."\n");
	dashedLine();
	fwrite(STDOUT, "PRESS: \n 0 TO GO BACK TO MAIN MENU \n OR\n The First number of a line to Display CALL INFORMATION.\n");

	$command = trim(fgets(STDIN));
	if($command==0)
	{
	    newline();
	    start();
	}
	else
	{
	    newline();
	    showCallSummary($resA[$command], "callLoc", $address);
	}
}

function showCallSummary($runNum, $searchType, $lastSearch)
{
    $conn = mysql_connect()   or die("Error. MySQL connection failed at mysql_connect"); 
    mysql_select_db('pcr') or die ('Unable to select database!');
    $query  = "SELECT * FROM calls WHERE RunNumber=".$runNum.";";
    $result = mysql_query($query) or die ("Error in query: $query. " . mysql_error());
    $row = mysql_fetch_array($result);
    //fwrite(STDOUT, "Patient History  - Call # ".$runNum." on ");

    $dateTS = strtotime($row['Date']);
    $disp = explode(":", $row['TimeDisp']);
    $disp = $disp[0].":".$disp[1];
    $text = "Patient History  - Call # ".$runNum." on ".date("D, M j, Y",$dateTS)." at ".$disp;
    blueText($text);
    echo "\n";
    dashedLine();
    echo "Patient Name: ".strtoupper($row['PtLastName']).", ".strtoupper($row['PtFirstName'])."\t Pt Sex: ".$row['PtSex']."\n";
    echo "Age: ".$row['Age']."\t\tDOB: ".$row['DOB']."\n";
    echo "Address: ".strtoupper($row['PtAddress']).", ".strtoupper($row['PtTown'])."\n";
    redText("Call Type: ".$row['CallType']);
    echo "\n";
    echo "Call Location: ".$row['CallLoc']."\t Location of Pt on Scene: ".$row['LocPtAtScene']."\n";
    echo "Chief Complaint: ".$row['ChiefComplaint']."\n";
    echo "Pt Physician: ".$row['PtPhysician']."\n";
    redText("Allergies: ".$row['Allergies']);
    echo "\n";
    echo "Medications: ".$row['Medications']."\n";
    echo "Hx: ".$row['Hx']."\n";
    echo "Remarks: ".$row['Remarks']."\n";
    echo "Injuries: ".$row['Injuries']."\n";
    if($row['LOC'] == 1)
    {
	redText("LOSS OF CONSCIOUSNESS.");
	echo "\n";
    }
    else
    {
	echo "no loss of consciousness recorded.\n";
    }
    //TODO: add vitals here
    echo "Transported To: ".$row['ToHosp']."\n";
    redText("ALS Status: ".$row['ALSstatus']);
    echo "\tUnit: ".$row['ALSunit'];
    echo "\n";
    echo "Call Outcome: ".$row['OC']."\n";
    dashedLine();

    fwrite(STDOUT, "PRESS: \n 0 to EXIT. \n 1 TO GO BACK. \n 2 For Main Menu. \n");
	
	$command = fgets(STDIN);
	if($command==0)
	{
		exit(0);
	}
	elseif($command==1)
	{
	    if($searchType == "lastName")
	    {
		newline();
		searchLastName($lastSearch);
	    }
	    elseif($searchType == "address")
	    {
		newline();
		searchPtAddress($lastSearch);
	    }
	    elseif($searchType == "callLoc")
	    {
		newline();
		searchCallLoc($lastSearch);
	    }
	    elseif($searchType == "firstName")
	    {
		newline();
		searchFirstName($lastSearch);
	    }
	}
	else
	{
		newline();
		start();
	}
}


function newline()
{
	//this will clear all of the screen with newline characters
	for($i=0;$i<50;$i++)
	{
		fwrite(STDOUT, "\n");
	}
}

function continueOrNot()
{
	//after finishing a function, decide whether to continue or not.

	fwrite(STDOUT, "PRESS: \n 0 to EXIT \n 1 To DISPLAY CALL \n 2 TO GO BACK TO MAIN MENU \n");
	
	$command = fgets(STDIN);
	if($command==0)
	{
	    newline();
	    exit(0);
	}
	elseif($command==1)
	{
	    showCallSummary();
	}
	else
	{
		newline();
		start();
	}
}

function compareAddress($input, $record)
{
    //input is the user's query
    //record is the current call record

    //simlple 75% similarity
    if(similar_text($input, $record) > (strlen($record) * (3/4))) // if 75% of chars are similar
    {
	return true;
    }

    //new variables to work on
    $inA = explode(" ",$input); //split input into a space-deliminated array
    $recA = explode(" ",$input); //split record into a space-deliminated array

    // if the second word in input is in the record
    if(strstr($record, $inA[1]))
    {
	return true;
    }

    $noNums = ereg_replace("[^0-9]", "", $input); // string with no numbers
    $noNums = trim($noNums);
    
    for($i = 0; $i < count($inA); $i++) // iterate through input array
    {
	for($c = 0; $c < count($recA); $c++) // iterate through record array
	{
	    if(is_numeric($recA[$c]) && is_numeric($inA[$i])) // both records numeric
	    {
		if(($recA[$c] >= ($inA[$i] -6)) && ($recA[$c] <= ($inA[$i] + 6))) // if the address is +/- 6 of specified
		{
		    if(similar_text($input, $record) > (strlen($record) * (1/2))) // and 50% of chars are similar
		    {
			return true;
		    }
		}
	    }

	}

    }

    //get rid of common stuff
    $concise1 = $input;
    $concise1 = str_replace('ROAD', '', $concise1);
    $concise1 = str_replace('RD', '', $concise1);
    $concise1 = str_replace('ST', '', $concise1);
    $concise1 = str_replace('STREET', '', $concise1);
    $concise1 = str_replace('AVE', '', $concise1);
    $concise1 = str_replace('AVENUE', '', $concise1);
    $concise1 = str_replace('PL', '', $concise1);
    $concise1 = str_replace('PLACE', '', $concise1);
    $concise1 = str_replace('APT', '', $concise1);
    $concise1 = str_replace('APARTMENT', '', $concise1);
    $concise1 = str_replace('#', '', $concise1);
    $concise1 = str_replace('ROOM', '', $concise1);
    $concise1 = str_replace('RM', '', $concise1);
    $concise1 = str_replace('DR', '', $concise1);
    $concise1 = str_replace('DRIVE', '', $concise1);
    $concise1 = str_replace('.', '', $concise1);
    $concise1 = str_replace('FLOOR', '', $concise1);
    $concise1 = str_replace('COURT', '', $concise1);
    $concise1 = str_replace('CT', '', $concise1);
    $concise1 = str_replace('LN', '', $concise1);
    $concise1 = str_replace('LANE', '', $concise1);
    $concise1 = str_replace('WAY', '', $concise1);

    $concise2 = $record;
    $concise2 = str_replace('ROAD', '', $concise2);
    $concise2 = str_replace('RD', '', $concise2);
    $concise2 = str_replace('ST', '', $concise2);
    $concise2 = str_replace('STREET', '', $concise2);
    $concise2 = str_replace('AVE', '', $concise2);
    $concise2 = str_replace('AVENUE', '', $concise2);
    $concise2 = str_replace('PL', '', $concise2);
    $concise2 = str_replace('PLACE', '', $concise2);
    $concise2 = str_replace('APT', '', $concise2);
    $concise2 = str_replace('APARTMENT', '', $concise2);
    $concise2 = str_replace('#', '', $concise2);
    $concise2 = str_replace('ROOM', '', $concise2);
    $concise2 = str_replace('RM', '', $concise2);
    $concise2 = str_replace('DR', '', $concise2);
    $concise2 = str_replace('DRIVE', '', $concise2);
    $concise2 = str_replace('.', '', $concise2);
    $concise2 = str_replace('FLOOR', '', $concise2);
    $concise2 = str_replace('COURT', '', $concise2);
    $concise2 = str_replace('CT', '', $concise2);
    $concise2 = str_replace('LN', '', $concise2);
    $concise2 = str_replace('LANE', '', $concise2);
    $concise2 = str_replace('WAY', '', $concise2);

    $noSpace1 = str_replace(" ", '', $concise1);
    $noSpace2 = str_replace(" ", '', $concise2);

    if(similar_text($noSpace1, $noSpace2) > (strlen($noSpace1) * (7/10))) // and 50% of chars are similar
    {
	return true;
    }	    

    return false;
}

function banner()
{
fwrite(STDOUT, "=====================================================\n");
fwrite(STDOUT, "=       =====     ===       =========================\n");
fwrite(STDOUT, "=  ====  ===  ===  ==  ====  ========================\n");
fwrite(STDOUT, "=  ====  ==  ========  ====  ========================\n");
fwrite(STDOUT, "=  ====  ==  ========  ===   ==    ===  =   ====   ==\n");
fwrite(STDOUT, "=       ===  ========      ====  =  ==    =  ==     =\n");
fwrite(STDOUT, "=  ========  ========  ====  ==  =  ==  =======  =  =\n");
fwrite(STDOUT, "=  ========  ========  ====  ==    ===  =======  =  =\n");
fwrite(STDOUT, "=  =========  ===  ==  ====  ==  =====  =======  =  =\n");
fwrite(STDOUT, "=  ==========     ===  ====  ==  =====  ========   ==\n");
fwrite(STDOUT, "=====================================================\n");
}

function setFormatting($result)
{
    global $maxLen;
    global $fields;

    while($row = mysql_fetch_array($result))
    {
	for($c = 0; $c < count($fields); $c++)
	{
	    if($maxLen[$fields[$c]] < strlen($row[$fields[$c]]))
	    {
		$maxLen[$fields[$c]] = strlen($row[$fields[$c]]);
	    }
	}
    }
}

function printFormatted($result, $i)
{
    global $fields;
    global $maxLen;



    $row = mysql_fetch_array($result);

    fwrite(STDOUT, $i.")\t");

    //Run Number
    $value = 'RunNumber';
    fwrite(STDOUT, $row[$value]);
    echoSpace( ($maxLen[$value] - strlen($row[$value])));
    fwrite(STDOUT, "\t");
    //Date
    $value = 'Date';
    fwrite(STDOUT, $row[$value]);
    echoSpace( ($maxLen[$value] - strlen($row[$value])));
    fwrite(STDOUT, "\t");

    //rest of fields
    for($c = 2; $c < count($fields); $c++) // iterate through the fields
    {
	$value = $fields[$c];
	fwrite(STDOUT, $row[$value]);
	echoSpace( ($maxLen[$value] - strlen($row[$value])) + 9);
	//fwrite(STDOUT, "\t");
    }

//    echo "\t".$row['Date']."\t".strtoupper($row['PtLastName'])."\t\t".strtoupper($row['PtFirstName'])."\t\t".$row['Age']."\t".strtoupper($row['PtAddress']);
fwrite(STDOUT, "\n");

    
}

function printHeader()
{
    global $fields;
    global $maxLen;



$row = array('RunNumber' => 'Run #', 'Date' => 'Date', 'PtLastName' => 'LastName', 'PtFirstName' => 'FirstName', 'Age' => 'Age', 'PtAddress' => 'Home Addrs');

    fwrite(STDOUT, "#\t");

    //Run Number
    $value = 'RunNumber';
    fwrite(STDOUT, $row[$value]);
    echoSpace( ($maxLen[$value] - strlen($row[$value])));
    fwrite(STDOUT, "\t");
    //Date
    $value = 'Date';
    fwrite(STDOUT, $row[$value]);
    echoSpace( ($maxLen[$value] - strlen($row[$value])));
    fwrite(STDOUT, "\t");

    //rest of fields
    for($c = 2; $c < count($fields); $c++) // iterate through the fields
    {
	$value = $fields[$c];
	fwrite(STDOUT, $row[$value]);
	echoSpace( ($maxLen[$value] - strlen($row[$value])) + 9);
	//fwrite(STDOUT, "\t");
    }

//    echo "\t".$row['Date']."\t".strtoupper($row['PtLastName'])."\t\t".strtoupper($row['PtFirstName'])."\t\t".$row['Age']."\t".strtoupper($row['PtAddress']);
fwrite(STDOUT, "\n");

    
}


function echoSpace($i)
{
    for($c = 0; $c < $i; $c++)
    {
	fwrite(STDOUT, " ");
    }
}

function dashedLine()
{
    fwrite(STDOUT, "-------------------------------------------------------------------------------------------------------\n");
}

function redText($text)
{
    echo exec("echo -e '\E[31m".$text."'");
    echo exec ("tput sgr0");
}

function blueText($text)
{
    echo exec("echo -e '\E[34m".$text."'");
    echo exec ("tput sgr0");
}

?>
