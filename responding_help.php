<?php
// Time-stamp: "2009-01-29 21:08:14 jantman"
// $Id$
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php echo '<title>Help - Responding</title>'; ?>
<link rel="stylesheet" type="text/css" href="responding.css" />
</head>

<body>
<div class="mainTitle">Help - Responding</div>
<?php
require_once('/srv/www/htdocs/inc/maintenance.php');
if($maint_warn_html != ""){ echo '<div class="maintWarning">'.$maint_warn_html.'</div>'."\n";}
?>

<p>So, you&lsquo;re having problems? Here&lsquo;s the deal:</p>

<p>If you can see this page, that's good. It means that the primary web server at the building is running and serving pages. If you're not at the building and can see this, that's even better.</p>

<p><strong>If you're having problems calling in:</strong></p>
<ol>
<li>Did you try calling back? Or calling from a different phone to see if the system says anything at all?</li>
<li>Have a look at the <a href="systemStatus.php">System Status</a> page. Green is good, red is bad. If anything is red, Jason needs to be called (201-906-7347). If he doesn't answer, send him a *very* nasty text message.</li>
</ol>

<p><strong>If you're having problems with something web-based:</strong></p>
<ol>
<li>This will be written at some point...</li>
</ol>

</body>

</html>
