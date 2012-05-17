<?php
// Time-stamp: "2010-03-13 16:30:53 jantman"
// page to show current schedule and responding members
// $Id$
require_once('/srv/www/htdocs/inc/responding.php.inc'); // this provides the functions for everything
require_once('/srv/www/htdocs/inc/respondingNotice.php');
// TODO: redo all of this with a timed DHTML refresh of the DIVs so the page itself doesn't reload
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php echo '<title>'.$shortName.' Crew & Responding Members</title>'; ?>
<link rel="stylesheet" type="text/css" href="bayResponding.css" />
<script language="javascript" type="text/javascript" src="inc/responding.js"></script>
<meta http-equiv="refresh" content="8">
</head>

<body>

<?php

require_once('/srv/www/htdocs/inc/maintenance.php');
if($maint_warn_html != "")
{
    echo '<div style="width: 100%;">&nbsp;</div>'."\n";
    echo '<div class="maintWarning">'.$maint_warn_html.'</div>'."\n";
    echo '<div class="clearing"></div>'."\n";
}

echo getRespondingNotice();

echo '<div class="timeDiv" id="timeDiv">'.genBayTimeDiv().'</div>'."\n";

// tones cell
echo '<div class="tonesDiv" id="tonesDiv">'.genTonesDiv(true).'</div>'."\n";

//responding cell
if($rmt_conn)
{
    echo '<div>'."\n";
    echo '<div class="spacer">&nbsp;</div>'."\n";
    echo '<div class="respTitle">Currently Responding Members</div>'."\n";
    echo '<div class="clearing"></div>'."\n";
    echo '</div>'."\n";
    echo '<div class="respDiv" id="respDiv">'.genRespDiv().'</div>';
}

?>

<div class="nextRig">
<?php echo genNextRigDiv(); ?>
</div>
<div class="bayMessage">

</div>

</body>

</html>
