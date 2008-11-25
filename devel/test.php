<?php
// this is a comment
$varUsed = 0;
$varUnused = 1;
global $varNeverDefined;
usedFunction();
if($varUsed == 0)
{
    $varUsed = 1;
}

function usedFunction()
{
    echo "hello";
}
function UNusedFunction()
{
    echo "never gets here.";
}
?>