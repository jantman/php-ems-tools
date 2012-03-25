<?php
// devel/php-trim.php
// a quick little script to find unused functions and variables in code
// TESTING ONLY
// | $LastChangedRevision:: 155                                         $ |
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/devel/php-trim#$ |
// +----------------------------------------------------------------------+


/*
 * T_ML_COMMENT does not exist in PHP 5.
 * The following three lines define it in order to
 * preserve backwards compatibility.
 *
 * The next two lines define the PHP 5 only T_DOC_COMMENT,
 * which we will mask as T_ML_COMMENT for PHP 4.
 */
if (!defined('T_ML_COMMENT')){ define('T_ML_COMMENT', T_COMMENT); } else { define('T_DOC_COMMENT', T_ML_COMMENT); }

doFile("test.php");

function doFile($fileName)
{
    $lines = file($fileName); // NOTE - array starts at 0, so index is (lineNumber - 1)
    $source = file_get_contents($fileName);
    $tokens = token_get_all($source);
    
    $tokentypes = array(); // DEBUG
    
    foreach ($tokens as $token)
    {
	if(is_string($token)){ continue;} // a string - a simple 1-character token, don't worry about it
	list($id, $text) = $token;
	$type = token_name($id);
	//echo $id." - ".$type."\n"; // DEBUG
	$tokentypes[$type] = $type; // DEBUG
	$lineNum = $token[2];

	// DEBUG
	if($lineNum == 6)
	{
	    echo "LINE 6: ".var_dump($token)."\n";
	}
	// END DEBUG

	switch ($type)
	{
	    case "T_FUNCTION":
		echo "Function on Line".$lineNum.": ".trim($lines[$lineNum-1])."\n";
		break;
	    case "T_VARIABLE":
		echo "Variable on Line".$lineNum.": ".trim($lines[$lineNum-1])."\n";
	    default:
		// do nothing
	}
    }
    
    // DEBUG
    foreach($tokentypes as $type)
    {
	echo $type."\n";
    }
    // END DEBUG
}
?>