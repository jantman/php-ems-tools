#!/usr/bin/php
<?php

// also need to check submitted TS on the call

$cmd = 'find /srv/www/htdocs/newcall/filledCalls -mmin -5 -iname "*.pdf"';
$out = shell_exec($cmd);
$files = explode("\n", $out);
foreach($files as $i => $f)
{
    if(trim($f) == ""){ unset($files[$i]);}
}


foreach($files as $file)
{
    echo $file."\n";
}

?>