<?php

function echoLink($url, $title)
{
    if(strpos($_SERVER["REQUEST_URI"], "auth/") != false && strpos($_SERVER["REQUEST_URI"], "admin/") == false)
    {
	echo '<a href="htdocs/'.$url.'">'.$title.'</a>';
    }
    else
    {
	echo '<a href="'.$url.'">'.$title.'</a>';
    }
}

function mysql_make_array_insert($tblName, $arr)
{
    $s = "INSERT INTO $tblName SET ";
    foreach($arr as $key => $val)
    {
	$s .= $key."='".$val."',";
    }
    $s = trim($s, ",");

    $s .= " ON DUPLICATE KEY UPDATE ";
    foreach($arr as $key => $val)
    {
	$s .= $key."='".$val."',";
    }
    $s = trim($s, ",");

    $s .= ";";
    return $s;
}


?>