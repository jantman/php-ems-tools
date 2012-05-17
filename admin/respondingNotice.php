<?php
// page to edit the responding screen notices
$conn = mysql_connect() or die("Unable to connect to MySQL.");
mysql_select_db("pcr") or die("Unable to select database.");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Responding Screen Notices</title>
<link rel="stylesheet" type="text/css" href="../php_ems.css" />
</head>

<?php
if(isset($_POST['action']) && $_POST['action'] == "add")
{
    doAdd();
}
elseif(isset($_POST['action']) && $_POST['action'] == "remove")
{
    doRemove();
}
?>

<body>
<h1>Responding Screen Notices</h1>

<form name="respondingNotice" method="POST">
<input type="hidden" name="action" value="remove" />
<table class="roster">
<tr><th>id</th><th>start</th><th>end</th><th>type</th><th>message</th><th>removed?</th><th>remove</th></tr>
<?php
$query = "SELECT * FROM responding_notices ORDER BY start_ts DESC;";
$result = mysql_query($query) or die("Error in query.");
while($row = mysql_fetch_assoc($result))
{
    echo '<tr>';
    echo '<td>'.$row['id'].'</td>';
    echo '<td>'.date("Y-m-d H:i:s", $row['start_ts']).'</td>';
    echo '<td>'.date("Y-m-d H:i:s", $row['end_ts']).'</td>';
    echo '<td>'.$row['type'].'</td>';
    echo '<td>'.$row['message'].'</td>';
    echo '<td>'.($row['is_killed'] == 0 ? "no" : "yes").'</td>';
    echo '<td><input type="checkbox" name="remove_'.$row['id'].'" id="remove_'.$row['id'].'" /></td>';
    echo '</tr>'."\n";
}
?>
</table>
<div><br /><input type="submit" value="Remove Selected Messages" /></div>
</form>

<div style="margin-top: 2em;">
<h2>Add Message</h2>
<form name="respondingNotice" method="POST">
<input type="hidden" name="action" value="add" />
<div><label for="start">Start Time:</label><input type="text" name="start" id="start" <?php echo 'value="'.date("Y-m-d")."T".date("H:i:s").'"'?> size="20" /><em>(YYYY-mm-ddThh:mm:ss)</em></div>
<div><label for="end">End Time:</label><input type="text" name="end" id="end" <?php echo 'value="'.date("Y-m-d", time()+86400)."T".date("H:i:s", time() + 86400).'"'?> size="20" /><em>(YYYY-mm-ddThh:mm:ss)</em></div>
<div><label for="type">Type:</label>
<select name="type">
<option value="warn">Warning</option>
</select>
</div>
<div>
<label for="message">Message:</label>
<textarea id="message" name="message" rows="4" cols="30"></textarea>
<em>(plain text or HTML)</em>
</div>
<div><input type="submit" value="Add Message" /></div>
</form>
</div>

</body>

</html>

<?php

function doAdd()
{
    echo '<p>Adding new message...</p>';
    $error = 0;
    if(strlen($_POST['message']) < 5){ echo '<p class="errorText">ERROR: Message is too short.</p>'; $error = 1;}
    if(strlen($_POST['message']) >= 498){ echo '<p class="errorText">ERROR: Message is too long (must be 498 characters or less).</p>'; $error = 1;}
    $message = mysql_real_escape_string($_POST['message']);
    $start = strtotime($_POST['start']);
    $end = strtotime($_POST['end']);
    if($end <= $start){ echo '<p class="errorText">ERROR: End time must be after start time.</p>'; $error = 1;}
    if($end <= 100){ echo '<p class="errorText">ERROR: End time is not correct.</p>'; $error = 1;}
    if($start <= 100){ echo '<p class="errorText">ERROR: Start time is not correct.</p>'; $error = 1;}

    if(overlappingMessageExists($start, $end)){ echo '<p class="errorText">ERROR: Message cannot overlap with another active message. Either remove an existing message or change the time for this message.</p>'; $error = 1;}

    $type = mysql_real_escape_string($_POST['type']);

    if($error == 1)
    {
	echo '<p class="errorText">I&#39;m sorry, but the above errors prevented your message from being added.</p>';
    }
    else
    {
	$query = "INSERT INTO responding_notices SET start_ts=$start,end_ts=$end,message='$message',type='$type';";
	$result = mysql_query($query) or die("Error in query.");
	echo '<p>Message added.</p>';
    }
}

function doRemove()
{
    foreach($_POST as $key => $val)
    {
	if(substr($key, 0, 7) == "remove_")
	{
	    $id = substr($key, strpos($key, "_")+1);
	    $id = (int)$id;
	    echo "<p>Removing notice $id...";
	    $query = "UPDATE responding_notices SET is_killed=1 WHERE id=$id;";
	    $result = mysql_query($query) or die("Error in query.");
	    echo "done.</p>";
	}
    }
}

function overlappingMessageExists($start, $end)
{
    $query = "SELECT * FROM responding_notices WHERE (start_ts >= $start AND start_ts <= $end) OR (end_ts >= $start AND end_ts <= $end) AND is_killed=0;";
    $result = mysql_query($query) or die("Error in query.");
    if(mysql_num_rows($result) > 0){ return true;}
    return false;
}

?>