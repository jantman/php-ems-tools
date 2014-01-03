#!/usr/bin/php
<?php
if(php_sapi_name() != "cli"){ die("This script must be run from the command line.");}

$dbName = "pcr";

$conn = mysql_connect() or die("unable to connect to MySQL.\n");
mysql_select_db($dbName);

$q = "SELECT * FROM patients WHERE Street IS NULL OR Street = '';";
$res = mysql_query($q) or die("Error in query: $q\nERROR: ".mysql_error()."\n");

$numRows = mysql_num_rows($res);
$count = 1;

while($row = mysql_fetch_assoc($res))
{
    system("clear");
    echo "record $count of $numRows\n\n";
    echo "Pkey: ".$row['Pkey']."\n";
    echo "Name: ".$row['PtLastName'].", ".$row['PtFirstName']." ".$row['PtMiddleName']."\n";
    echo "Address: ".$row['PtAddress']."\n";
    echo "City: ".$row['PtTown']."\n";
    echo "\n";
    
    $streetNum = substr($row['PtAddress'], 0, strpos($row['PtAddress'], " "));
    $apt = "";
    $street = doStreetAbbrevs($row['PtAddress']);
    $street = ucwords(substr($street, strpos($street, " ")+1));
    $street = str_replace(".", "", $street);

    // apartment number
    if(strpos($street, "#"))
    {
	$foo = $street;
	$street = substr($street, 0, strrpos($street, "#"));
	$apt = substr($foo, strrpos($foo, "#")+1);
    }

    if(strripos($street, "apt"))
    {
	$foo = $street;
	$street = substr($street, 0, strripos($street, "apt"));
	$apt = substr($foo, strripos($foo, "apt")+3);
    }

    $street = trim($street);
    $apt = trim(strtoupper($apt));

    echo "Street Number [$streetNum] (or x for none): ";
    $foo = fgets(STDIN);
    if(trim($foo) != ""){ $streetNum = trim($foo);}

    echo "Street [$street]: ";
    $foo = fgets(STDIN);
    if(trim($foo) != ""){ $street = trim($foo);}

    echo "Apartment [$apt]: ";
    $foo = fgets(STDIN);
    if(trim($foo) != ""){ $apt = trim($foo);}

    $city = ucwords(strtolower($row['PtTown']));
    echo "City: [$city] ";
    $foo = fgets(STDIN);
    if(trim($foo) != ""){ $city = trim($foo);}

    $state = "NJ";
    echo "State: [$state] ";
    $foo = fgets(STDIN);
    if(trim($foo) != ""){ $state = trim($foo);}

    echo "==============\n";
    echo "Street Number: =$streetNum=\n";
    echo "Street: =$street=\n";
    echo "Apt: =$apt=\n";
    echo "City: =$city=\n";
    echo "State: =$state=\n";
    
    $query = "UPDATE patients SET ";
    if(trim($streetNum) != "x"){ $query .= "StreetNumber='".$streetNum."',";}
    $query .= "Street='".$street."',";
    if($apt != ""){ $query .= "AptNumber='".$apt."',";}
    $query .= "city='".$city."',";
    $query .= "state='".$state."',";
    $query .= "new_ismigrated=1 WHERE Pkey=".$row['Pkey'].";";

    echo $query."\n\n";

    echo "Good to update? [YES/no] ";
    $foo = fgets(STDIN);
    if($foo == "YES" || $foo == "yes" || $foo == "Yes" || trim($foo) == "")
    {
	$result = mysql_query($query);
	if(! $result)
	{
	    echo "Error in query: ".$query."\nERROR: ".mysql_error()."\n";
	}
    }
    $count++;
}

function doStreetAbbrevs($s)
{
    $s .= " ";
    $s = strtolower($s);
    $abbrevs = array();
    $abbrevs["place"] = "pl";
    $abbrevs["street"] = "st";
    $abbrevs["drive"] = "dr";
    $abbrevs["road"] = "rd";
    $abbrevs["avenue"] = "ave";
    foreach($abbrevs as $full => $abbr)
    {
	$s = str_replace($full, $abbr, $s);
    }
    return $s;
}

?>