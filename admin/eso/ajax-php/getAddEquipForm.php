<table>
<?php
require_once('../../../custom.php');
$connection = mysql_connect() or die ('ERROR: Unable to connect to MySQL!');
mysql_select_db($dbName) or die ('ERROR: Unable to select database!');

// type
echo "<tr><td><strong>Type: </strong></td><td>\n";
$query = "SELECT * FROM eso_opt_equipTypes;";
$result = mysql_query($query) or die("Error in query: $query<br />ERROR: ".mysql_error());
echo '<select name="type_id" id="type_id" onChange="updateAddEquipForm()">'."\n";
while($row = mysql_fetch_assoc($result))
{
    echo '<option value="'.$row['et_id'].'" ';
    if(isset($_GET['type']) && $_GET['type'] == $row['et_id']){ echo 'selected="selected" ';}
    echo '>'.$row['et_name'].'</option>'."\n";
}
echo '</select>'."\n";
echo '</td></tr>'."\n";

// manufacturer
if(isset($_GET['type']))
{
    $typeID = (int)$_GET['type'];
    echo "<tr><td><strong>Manufacturer: </strong></td><td>\n";
    $query = "SELECT em_id,em_name FROM eso_opt_mfr WHERE et_id=$typeID;";
    $result = mysql_query($query) or die("Error in query: $query<br />ERROR: ".mysql_error());
    echo '<select name="mfr_id" id="mfr_id" onChange="updateAddEquipForm()">'."\n";
    $lastID = 0;
    while($row = mysql_fetch_assoc($result))
    {
	echo '<option value="'.$row['em_id'].'" ';
	if(isset($_GET['mfr_id']) && $_GET['mfr_id'] == $row['em_id']){ echo 'selected="selected" ';}
	echo '>'.$row['em_name'].'</option>'."\n";
	$lastID = $row['em_id'];
    }
    if(mysql_num_rows($result) < 2){ $_GET['mfr_id'] = $lastID;} // hack to deal with 1 mfr for a type
    echo '</select>'."\n";
    echo '</td></tr>'."\n";
}

if(isset($_GET['mfr_id']))
{
    $mfrID = (int)$_GET['mfr_id'];
    echo "<tr><td><strong>Model: </strong></td><td>\n";
    $query = "SELECT emod_id,emod_name,emod_model_num FROM eso_opt_model WHERE emod_mfr_id=$mfrID ORDER BY emod_name;";
    $result = mysql_query($query) or die("Error in query: $query<br />ERROR: ".mysql_error());

    echo '<select name="emod_id" id="emod_id" onChange="updateAddEquipForm()">'."\n";
    $lastID = 0;
    while($row = mysql_fetch_assoc($result))
    {
	echo '<option value="'.$row['emod_id'].'" ';
	if(isset($_GET['emod_id']) && $_GET['emod_id'] == $row['emod_id']){ echo 'selected="selected" ';}
	echo '>'.$row['emod_name'];
	if(trim($row['emod_model_num']) != ""){ echo " (".$row['emod_model_num'].")";}
	echo '</option>'."\n";
	$lastID = $row['emod_id'];
    }
    if(mysql_num_rows($result) < 2){ $_GET['emod_id'] = $lastID;} // hack to deal with 1 model for a mfr
    echo '</select>'."\n";
    echo '</td></tr>'."\n";
}

if(isset($_GET['emod_id']))
{
    $emodID = (int)$_GET['emod_id'];
    $query = "SELECT emod_has_size FROM eso_opt_model WHERE emod_id=$emodID;";
    $result = mysql_query($query) or die("Error in query: $query<br />ERROR: ".mysql_error());
    $row = mysql_fetch_assoc($result);
    if($row['emod_has_size'] == 1)
    {
	// size not serial number
	echo '<tr><td><strong>Size: </strong></td>';
	echo '<td><input type="text" size="4" name="size" id="size" /></td></tr>'."\n";
    }
    else
    {
	// size not serial number
	echo '<tr><td><strong>Serial &#35;: </strong></td>';
	echo '<td><input type="text" size="20" name="serial" id="serial" /></td></tr>'."\n";
    }

    // comment
    echo '<tr><td><strong>Comment: </strong></td>';
    echo '<td><input type="text" size="30" name="comment" id="comment" /></td></tr>'."\n";
    echo '<tr><td colspan="2"><input type="submit" value="Add Item" /></td></tr>'."\n";
}

?>

</table>
