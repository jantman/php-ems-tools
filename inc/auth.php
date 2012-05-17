<?php


// returns true on success, false otherwise
function member_auth($EMTid, $password)
{

}

// returns true on success, false otherwise
function member_auth_MySQL($EMTid, $password)
{
    global $dbName;
    $conn = mysql_connect();
    mysql_select_db($dbName);
    $query = "SELECT * FROM roster WHERE EMTid='".mysql_real_escape_string($EMTid)."' AND password='".mysql_real_escape_string($password)."';";
    $result = mysql_query($query);
    if(mysql_num_rows($result) > 0){ return true;}
    return false;
}

// returns true on success, false otherwise
function member_exists($EMTid)
{
    global $dbName;
    $conn = mysql_connect();
    mysql_select_db($dbName);
    $query = "SELECT * FROM roster WHERE EMTid='".mysql_real_escape_string($EMTid)."';";
    //echo '<p>'.$query.'</p>';
    $result = mysql_query($query);
    if(mysql_num_rows($result) > 0){ return true;}
    return false;
}

// returns true on success, false otherwise
function get_member_status($EMTid)
{
    global $dbName;
    $conn = mysql_connect();
    mysql_select_db($dbName);
    $query = "SELECT status FROM roster WHERE EMTid='".mysql_real_escape_string($EMTid)."';";
    //echo '<p>'.$query.'</p>';
    $result = mysql_query($query);
    if(mysql_num_rows($result) < 1){ return false;}
    $row = mysql_fetch_assoc($result);
    return $row['status'];
}

?>