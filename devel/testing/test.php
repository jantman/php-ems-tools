<?php
// this is a comment
$varUsed = 0;
$varUnused = 1;
global $varNeverDefined;
usedFunction();
if($varUsed == 0)
{
    $varUsed = yetAnotherUsed() + 1;
}
usedFunction(1);
function usedFunction()
{
    echo "hello";
}
anotherUsed();
function UNusedFunction()
{
    echo "never gets here.";
}

function anotherUsed()
{
    echo $something;
}

function yetAnotherUsed()
{
    echo;
}
function unusedTwo()
{
    echo;
}
function includedInOtherScript($i)
{
    echo;
    $i = $i + 1;
    return $i;
}
?>