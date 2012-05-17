<?php
// page to edit the responding screen notices

function getRespondingNotice()
{
    $conn = mysql_connect() or die("Unable to connect to MySQL.");
    mysql_select_db("pcr") or die("Unable to select database.");

    $query = "SELECT * FROM responding_notices WHERE start_ts <= ".time()." AND end_ts >= ".time()." AND is_killed=0 LIMIT 1;";
    $result = mysql_query($query) or die("Error in query.");
    if(mysql_num_rows($result) < 1){ return "";}
    $row = mysql_fetch_assoc($result);
    $foo = $row['message'];
    if($row['type'] == "warn")
    {
	$foo = '<div class="respondingWarn">'.$foo.'</div>';
	return $foo;
    }
}

?>