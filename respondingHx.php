<?php
// respondingHx.php
// shows history of responding calls
// Time-stamp: "2009-02-22 12:25:41 jantman"
require_once('/srv/www/htdocs/inc/responding.php.inc'); // this provides the functions for everything
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php echo '<title>'.$shortName.' Call-In History</title>'; ?>
<link rel="stylesheet" type="text/css" href="respondingHx.css" />
</head>

<body>
<h1><?php echo $shortName;?> Call-In History</h1>

<table class="resultTable">
<tr><th>Key</th><th>Date/Time</th><th>Call<br />Length</th><th>EMTid</th><th>Responding...</th><th>Cleared Time</th><th>Cleared By</th></tr>
<?php
// $rmt_conn
$rmt_query = "SELECT c.callin_id,c.start_ts,c.end_ts,c.EMTid,d.description,c.cleared_ts,c.cleared_by FROM callins AS c LEFT JOIN dtmf_options AS d ON c.DTMF_select=d.dtmf_key ORDER BY c.callin_id DESC;";
$rmt_result = mysql_query($rmt_query, $rmt_conn) or die("Error in Query: ".$rmt_query."\n Error: ".mysql_error($rmt_conn)."\n");
while($row = mysql_fetch_assoc($rmt_result))
{
    echo '<tr>';
    echo '<td>'.$row['callin_id'].'</td>';
    echo '<td>'.date("Y-m-d H:i:s", $row['start_ts']).'</td>';
    echo '<td>'.($row['end_ts'] - $row['start_ts']).'s</td>';
    echo '<td>'.$row['EMTid'].'</td>';
    echo '<td>'.$row['description'].'</td>';
    echo '<td>'.date("Y-m-d H:i:s", $row['cleared_ts']).'</td>';
    $temp = $row['cleared_by'];
    $clearedBy = "";
    if(strpos($temp, "@"))
    {
	$arr = explode("@", $temp);
	if(substr($arr[1], 0, 10) == "192.168.1.")
	{
	    $clearedBy = $arr[0]."@".$arr[1];
	}
	else
	{
	    $clearedBy = $arr[0]."@&lt;hiddenRemoteIP&gt;";
	}
    }
    else
    {
	$clearedBy = $temp;
    }
    echo '<td>'.$clearedBy.'</td>';
    echo '</tr>'."\n";
}

?>
</table>


</body>

</html>

