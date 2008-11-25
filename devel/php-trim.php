<?php
// devel/php-trim.php
// a quick little script to find unused functions and variables in code
// TESTING ONLY
// $Id$

/*
 * T_ML_COMMENT does not exist in PHP 5.
 * The following three lines define it in order to
 * preserve backwards compatibility.
 *
 * The next two lines define the PHP 5 only T_DOC_COMMENT,
 * which we will mask as T_ML_COMMENT for PHP 4.
 */
if (!defined('T_ML_COMMENT')){ define('T_ML_COMMENT', T_COMMENT); } else { define('T_DOC_COMMENT', T_ML_COMMENT); }

$source = file_get_contents('test.php');
$tokens = token_get_all($source);

$tokentypes = array(); // DEBUG

foreach ($tokens as $token)
{
    if(is_string($token)){ continue;} // a string - a simple 1-character token, don't worry about it
    list($id, $text) = $token;
    $type = token_name($id);
    //echo $id." - ".$type."\n"; // DEBUG
    $tokentypes[$type] = $type; // DEBUG
    switch ($type)
    {
	case "T_FUNCTION":
	    echo "Function: \n";
	    echo var_dump($token);
	    break;
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

?>