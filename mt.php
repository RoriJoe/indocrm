<?php

define('BASEPATH', dirname(__FILE__));
include "application/config/database.php";

$conn = mysql_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password']);
mysql_select_db($db['default']['database'], $conn);

$salt = sha1('!bismilah2012!');

$auth = isset($_GET['auth']) ? $_GET['auth'] : '';
$msisdn = isset($_GET['msisdn']) ? $_GET['msisdn'] : '';
$sms = isset($_GET['sms']) ? $_GET['sms'] : '';

$msisdn = preg_replace('~[^0-9]+~', '', $msisdn);
$sms = substr($sms, 0, 160);

$result = array( 'success' => false );
if ($auth == $salt && $msisdn && $sms)
{
    $result['success'] = true;
    
    $msisdn = mysql_real_escape_string($msisdn, $conn);
    $sms = mysql_real_escape_string($sms, $conn);
    
    mysql_query("INSERT INTO  `k9071857_indocrm`.`smsqueue` (`id` ,`tanggal` ,`number` ,`message` ,`status`) VALUES (NULL, CURRENT_TIMESTAMP, '$msisdn', '$sms',  '0')");
    $result['id'] = mysql_insert_id();
    
}

echo json_encode($result);
