<?php
// inc/serviceCheckFuncs.php
//  functions to check status of various functions directly via PHP
// Copyright 2009 Jason Antman (unless otherwise noted).
//


$response_array = array("100" => "Continue", "101" => "Switching Protocols", "200" => "OK", "201" => "Created", "203" => "Non-Authoritative Information", "204" => "No Content", "205" => "Reset Content", "206" => "Partial Content", "300" => "Multiple Choices", "301" => "Moved Permanently", "302" => "Found", "303" => "See Other", "304" => "Not Modified", "305" => "Use Proxy", "306" => "(Unused)", "307" => "Temporary Redirect", "400" => "Bad Request", "401" => "Unauthorized", "402" => "Payment Required", "403" => "Forbidden", "404" => "Not Found", "405" => "Method Not Allowed", "406" => "Not Acceptable", "407" => "Proxy Authentication Required", "408" => "Request Timeout", "409" => "Conflict", "410" => "Gone", "411" => "Length Required", "412" => "Precondition Failed", "413" => "Request Entity Too Large", "414" => "Request-URI Too Long", "415" => "Unsupported Media Type", "416" => "Requested Range Not Satisfiable", "417" => "Expectation Failed", "500" => "Internal Server Error", "501" => "Not Implemented", "502" => "Bad Gateway", "503" => "Service Unavailable", "504" => "Gateway Timeout","505" => "HTTP Version Not Supported");

// TODO: modify this so it returns a numerical status code, 0 if the page doesnt exist, -1 if we can't connect
function getStatusCode($url)
{
    // this function grabbed from: http://www.davewooding.com/php-script-for-http-response-codes/
    // written by Dave Wooding, slightly modified by Jason Antman on 2009-01-30
    $url = strip_tags($_POST["url"]);
    $info = @parse_url($url);
    $fp = @fsockopen( $info["host"], 80, $errno, $errstr, 10 );
    if (!$fp)
    {
        $message = "<strong>FAIL</strong>, ".$url." does not exist";
    }
    else
    {
        if(empty($info["path"]))
	{
            $info["path"] = "/";
        }
        $query = "";
        if(isset($info["query"]))
	{
            $query = "?".$info["query"]."";
        }
        $out  = "HEAD ".$info["path"]."".$query." HTTP/1.0\r\n";
        $out .= "Host: ".$info["host"]."\r\n";
        $out .= "Connection: close \r\n";
        $out .= "User-Agent: PHP-Status-Code-Test/1.0\r\n\r\n";
        fwrite( $fp, $out );
        $html = "";
        while ( !feof( $fp ) )
	{
            $html .= fread( $fp, 8192 );
        }
        fclose( $fp );
    }
    if(!$html)
    {
        $message = "FAIL, ".$url." does not exist";
    }
    else
    {
        $headers = explode("\r\n", $html);
        unset($html);
        for($i=0;isset($headers[$i]);$i++ )
	{
            if(preg_match("/HTTP\/[0-9A-Za-z +]/i" ,$headers[$i]))
	    {
                $status = preg_replace("/http\/[0-9]\.[0-9]/i","",$headers[$i]);
            }
        }
        $message = $status." ".$response_array[$status];
    }

}    

function do_ping($host)
{
    // pings a host, returns an array with elements 'loss' and 'time' (avg)
    $ping = Net_Ping::factory();
    if(PEAR::isError($ping))
    {
	echo $ping->getMessage();
    }
    else
    {
	$ping->setArgs(array('count' => 2));
	$temp =  $ping->ping($host);
	$loss = $temp->_loss;
	$time = $temp->_round_trip;
	$final = array('loss' => $loss, 'time' => $time['avg']);
	return $final;
    }
}

function get_snmp_plain($host, $community, $oid)
{
    // returns the plain value of the OID
    snmp_set_valueretrieval(SNMP_VALUE_PLAIN);
    $result = snmpget($host, $community, $oid);
    return $result;
}

function get_snmp_library($host, $community, $oid)
{
    // returns the library value of the OID, as shown by snmpget or snmpwalk
    snmp_set_valueretrieval(SNMP_VALUE_LIBRARY);
    $result = snmpget($host, $community, $oid);
    return $result;
}

function get_snmp_timeticks($host, $community, $oid)
{
    // pulls the textual timetick representation out of a LIBRARY value
    $result = get_snmp_library($host, $community, $oid);
    $result = trim(substr($result, strpos($result, ")")+1));
    return $result;
}

function format_timeticks($ticks)
{
    // formats timeticks into a textual representation
    $ticks = (int)($ticks / 100);
    $final = "";
    if($ticks > 86400) // > 1 day
    {
	$days = (int)($ticks / 86400);
	$final .= $days."d ";
	$ticks = ((int)$ticks % 86400);
    }
    if($ticks > 3600) // 1 hour
    {
	$hours = (int)($ticks / 3600);
	$final .= $hours."h ";
	$ticks = (int)($ticks % 3600);
    }
    if($ticks > 60) // 1 minute
    {
	$min = (int)($ticks / 60);
	$final .= $min."m ";
	$ticks = (int)($ticks % 60);
    }
    $final .= $ticks."s";
    return $final;
}


?>