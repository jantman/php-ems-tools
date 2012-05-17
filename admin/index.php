<?php
require_once('../inc/common.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<html>
<head>
<title>MPAC - Administrative tools on PCRserv</title>
<link rel="stylesheet" href="../php_ems.css" type="text/css">
</head>
<body>
<h2>MPAC - Administrative tools on PCRserv</h2>

<?php
$conn = mysql_connect();
mysql_select_db('pcr');
$query = "SELECT EMTid FROM roster ORDER BY lpad(EMTid,10,'0');";
$result = mysql_query($query);
$selectOptions = "";
while($row = mysql_fetch_assoc($result))
{
    $selectOptions .= '<option value="'.$row['EMTid'].'">'.$row['EMTid'].'</option>';
}

?>

<li><strong>Schedule</strong>
  <ul>
    <li><?php echoLink('changes.php?month='.date("m").'&year='.date("Y"), 'Schedule Change Log'); ?></li>
    <li><?php echoLink('randomRotation.php', 'Create Random Rotation of Members'); ?></li>
    <li><?php echoLink('saturdaySchedule.php', 'Create/Edit Saturday Night Schedule'); ?></li>
  </ul>
</li>

<li><strong>Roster</strong>
  <ul>
    <li><?php echoLink('checkRoster.php', 'Missing Roster Information'); ?></li>
    <li><?php echoLink('membersCSV.php', 'Membership List (CSV)'); ?></li>
    <li><?php echoLink('goldCross.php', 'Gold Cross List (CSV)'); ?></li>
    <li><?php echoLink('signinSheet.php', 'Printable Sign-In Sheet'); ?></li>
    <li><?php echoLink('nominationRoster.php', 'Nominations Roster'); ?></li>
  </ul>
</li>

<li><strong>Calls</strong>
  <ul>
    <li><?php echoLink('../newcall-stats/', 'Call Statistics'); ?></li>
    <li><nobr><?php echo '<a href="checkMonthMembers.php?year='.date("Y").'&month='.date("m").'">Check which members were on which calls this month</a></li>';?><span style="font-weight: bold; font-style: italic; color: red;">(not working for new call reports yet)</span></nobr></li>
    <li><form name="membDutyCalls" action="membDutyCalls.php" method="GET">Check Missing Duty Calls for <label for="EMTid">EMTid: </label><select name="EMTid" id="EMTid"><?php echo $selectOptions;?></select> <label for="year">Year: </label><select name="year" id="year"><?php yearOptions(); ?></select> <label for="month">Month: </label><select name="month" id="month"><?php monthOptions(); ?></select><input name="buttonGroup[btnSubmit]" value="Submit" type="submit" /><span style="font-weight: bold; font-style: italic; color: red;">(not working for new call reports yet)</span></form> 
</li>
    <li><?php echoLink('checkNewDuty.php', 'Check duty/gen for new calls'); ?></li>
    <li><?php echoLink('checkNewDuty2.php', 'Check duty/gen for new calls - Condensed Version'); ?></li>
    <li><?php echoLink('checkCalls.php', 'Check New Calls'); ?></li>
  </ul>
</li>

<li><strong>Building</strong>
  <ul>
    <li><?php echoLink('doorLogs.php', 'Door Access Logs'); ?></li>
  </ul>
</li>

<li><strong>Attendance</strong>
    <ul>
    <li><?php echoLink('attendance/listAttendance.php', 'List Attendance'); ?></li>
    <li><?php echoLink('attendance/yearlyAttendance.php?year='.date("Y"), 'Yearly Attendance'); ?></li>
    <li><?php echoLink('attendance/attendance.php', 'Take Attendance'); ?></li>
    </ul>
</li>

<li><strong>Misc. Administrative</strong>
  <ul>
    <li><?php echoLink('eso/', 'Equipment Sign-Out'); ?></li>
    <li><?php echoLink('respondingNotice.php', 'Responding Screen Notices'); ?></li>
    <li><?php echoLink('MPAC-Admin-Notes.html', 'Administrative Notes/Checklists'); ?></li>
    <li><?php echoLink('clothingAllowance.php', 'Clothing Allowance Calculation'); ?></li>
    <li><?php echoLink("emailList.php", 'MPAC E-Mail List'); ?></li>
    <li><?php echoLink("smsList.php", 'MPAC SMS List'); ?></li>
  </ul>
</li>

<li><strong>Technical Stuff</strong>
  <ul>
    <li><?php echoLink('MPAC-Network-Docs.html', 'Network Documentation'); ?></li>
    <li><?php echoLink('phpinfo.php', 'PHPinfo'); ?></li>
  </ul>
</li>

<li><strong>Unix Admin stuff:</strong>
  <ul>
    <li><?php echoLink('testing/', 'Testing/Devel'); ?></li>
    <li><?php echoLink('unixAdmin/', 'Unix Admin Tools'); ?></li>
  </ul>
</li>


</body>
</html>

<?php
function yearOptions()
{
    $i = (int)date("Y");
    for($x = $i - 3; $x < $i+4; $x++)
    {
	echo '<option value="'.$x.'"';
	if($x == $i){ echo ' selected="selected"';}
	echo '>'.$x.'</option>';
    }
}

function monthOptions()
{
    $i = (int)date("m");
    for($x = 1; $x < 13; $x++)
    {
	echo '<option value="'.$x.'"';
	if($x == $i){ echo ' selected="selected"';}
	echo '>'.$x.'</option>';
    }
}

?>