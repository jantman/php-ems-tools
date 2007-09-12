<html>
<head>
<!-- Time-stamp: "2006-11-08 23:04:13 jantman" -->
<title>PHP EMS Tools Roster Help</title>
</head>
<body>
<h3>PHP EMS Tools Roster Help</h3>
<p>For the administrative view of the roster, please go to the PHP EMS Tools
index page and select Roster Administrative View, or replace the current URL
(address) of "roster.php" with "roster.php?adminView=1".</p>
<p>To sort the roster by any of those fields, click on the column title (link)
and the roster will be re-sorted by that column.</p>

<p>Membership Status Letters:
<table border=1 cellpadding=5>
<?php
require_once("../custom.php");

for($i = 0; $i < count($memberTypes); $i++)
{
    echo '<tr><td>';
    if($memberTypes[$i]['rosterName'] == '')
    {
	echo '&nbsp;';
    }
    else
    {
	echo $memberTypes[$i]['rosterName'];
    }
    echo '</td><td>';
    echo $memberTypes[$i]['name'];
    echo '</td>';
}
?>

</table>
</p>

<p>When using the normal roster, clicking <b>"Short View"</b> will display only the membership status letter, ID, and first and last name for each member. When viewing the Short View, clicking <b>"Normal View"</b> will bring you back to the normal (full) view. The short view cannot be used when in the administrative (adminView) mode.

<p>For full documentatin, please see <a href="index.html">the docs</a> or the
<a href="http://www.php-ems-tools.com">PHP EMS Tools Homepage</a></p>
</body>
</html>