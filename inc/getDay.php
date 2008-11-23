<?php
// getDay.php
//
// script to get the content of one day, for refreshing when signing on
//

$ts = (int)$_GET['ts'];

require_once('../config/config.php');
require_once('../config/scheduleConfig.php');
require_once('sched.php');
require_once('global.php');
echo getCellContent($ts, $ts);




?>