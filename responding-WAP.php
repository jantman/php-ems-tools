<?php
// Time-stamp: "2009-12-13 22:48:34 jantman"
// page to show current schedule and responding members
// $Id$
require_once('/srv/www/htdocs/inc/responding.php.inc'); // this provides the functions for everything
// TODO: redo all of this with a timed DHTML refresh of the DIVs so the page itself doesn't reload
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php echo '<title>Crew & Responding Members</title>'; ?>
</head>

<body>
<h1>Current Crew & Responding Members</h1>
<?php

genRespWAPDiv();

genCrewWAPDiv();

?>

<br /><br /><br />

<p>Call <a href="tel:2014787160">201-478-7160</a> to respond or <a href="respond.php">click here</a>.</p>

</body>

</html>
