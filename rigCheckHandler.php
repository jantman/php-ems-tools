<?php

// form to select which rig check to do


require_once('./config/config.php');
?>

<html>
<head>

<?php
echo '<title>'.$shortName.' - PHP EMS Tools Index</title>';
global $serverWebRoot;
//echo '<link rel="stylesheet" href="'.$serverWebRoot.'php_ems.css" type="text/css">'; // the location of the CSS file for the schedule
?>

<body>
<h1 align=center>Rig Check Selection</h1><br>
<table border=1 align=center>
<tr><td><b>Fill in</b></td><td><b>Print Blank</b></td>
<?php
// generate table
global $rigChecks;
foreach($rigChecks as $key => $subarray)
{
    echo '<tr><td align=center><b>';
    echo '<a href="rigCheck.php?index='.$key.'">'.$subarray['name'].'</a>';
    echo '</b></td><td align=center><b>';
    echo '<a href="blankRigCheck.php?index='.$key.'">'.$subarray['name'].'</a>';
    echo '</b></td></tr>';
}
?>
</table>
</body>
</html>