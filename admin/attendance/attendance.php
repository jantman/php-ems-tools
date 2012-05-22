<?php
$dbName = "pcr";
$conn = mysql_connect()   or die("Error: I'm sorry, the MySQL connection failed.");
mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database!");

if(isset($_POST['date']))
{
    // process form
    processForm();
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>MPAC Attendance</title>
<link rel="stylesheet" type="text/css" href="attendance.css" />
<script language="javascript" type="text/javascript" src="attendance.js"></script>
</head>

<body>

<h1>MPAC Attendance</h1>

<form name="attendance" method="POST">

<p><a href="javascript:startMe()">Start Taking Attendance</a></p>

<p>
<label for="date"><strong>Date:</strong> </label>
<?php
echo '<input type="text" id="date" name="date" size="10" value="'.date("Y-m-d").'" />'."\n";
?>
<label for="type"><strong>Event Type:</strong> </label>
<input type="radio" id="type" name="type" value="Meeting" checked="checked" /> Meeting 
<input type="radio" id="type" name="type" value="Drill" /> Drill 
<input type="radio" id="type" name="type" value="other" /> Other:
<input type="text" id="typeOther" name="typeOther" size="15" />
</p>

<p><em>Press "p" or "1" for present, "a" or "2" for absent, "e" or "3" for excused.</em></p>

<p>To set defaults, append to URL "?default=X" where X is one of p, a, e, etc.</p>

<table class="attendance">
<tr><th>Name</th><th>ID</th><th>Attendance</th></tr>

<?php
if(isset($_GET['default'])){ $default = $_GET['default'];}

$query = "SELECT EMTid,FirstName,LastName FROM roster WHERE status='Senior' OR status='Driver' OR status='Probie' ORDER BY LastName,FirstName;";
$result = mysql_query($query) or die ("Query Error");
$count = 0;
while($row = mysql_fetch_array($result))
{
    $name = $row['LastName'].", ".$row['FirstName'];
    $id = $row['EMTid'];
    echo '<tr id="row_'.$count.'">';
    echo '<td id="name_'.$count.'">'.$name.'</td>';
    echo '<td id="id_'.$count.'">'.$id.'</td>';
    echo '<td id="check_'.$count.'">';
    writeInputFields($id, $count);
    echo '</td>';
    echo '</tr>'."\n";
    $count++;
}


echo '<input type="hidden" name="rowCount" id="rowCount" value="'.$count.'" />'."\n";

?>


</table>

<div><input type="submit" value="Submit" id="submit" name="submit" /></div>


</form>

</body>

</html>

<?php
function processForm()
{
    $type = trim($_POST['type']);
    if($type == "other"){ $type = trim($_POST['typeOther']);}
    $date = strtotime($_POST['date']);

    $att = array();

    foreach($_POST as $key => $val)
    {
	if(substr($key, 0, 3) != "id_"){ continue;}
	$foo = explode("_", $key);
	$query = "INSERT INTO attendance SET date_ts=".$date.",type='".mysql_real_escape_string($type)."',EMTid='".mysql_real_escape_string(trim($foo[1]))."',status='".getStatus($val)."';";
	$result = mysql_query($query) or die ("Query Error");
    }

    header("Location: viewAttendance.php?date=".$date."&type=".$type);
}

function getStatus($s)
{
    switch ($s)
    {
	case "p":
	    return "Present";
	case "s":
	    return "School";
	case "a":
	    return "Absent";
	case "w":
	    return "Work";
	case "e":
	    return "Excused";
	case "l":
	    return "Leave";
	default:
	    return $s;
    }
}

function writeInputFields($id, $count)
{
    global $default;

    $opts = array("p" => "Present (1)", "a" => "Absent (2)", "e" => "Excused (3)", "w" => "Working (4)", "l" => "Leave (5)", "s" => "School (6)");

    foreach($opts as $letter => $text)
    {
	if($default == $letter)
	{
	    echo '<input type="radio" name="id_'.$id.'_row_'.$count.'" id="'.$count.'_'.$letter.'" value="'.$letter.'" checked="checked" /> '.$text;
	}
	else
	{
	    echo '<input type="radio" name="id_'.$id.'_row_'.$count.'" id="'.$count.'_'.$letter.'" value="'.$letter.'" /> '.$text;
	}
    }
}

?>