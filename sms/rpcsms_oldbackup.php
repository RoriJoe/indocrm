<?php

$db_host = "localhost";
$db_username = "k9071857_indocrm";
$db_passwd = "sulfat*96";
$db_name = "k9071857_indocrm";

$conn = @mysql_connect($db_host, $db_username, $db_passwd) or die("Tidak dapat konek ke database: " . mysql_error());
mysql_select_db($db_name) or die("Database $db_name tidak dapat dibuka...");

$act = $_GET['act'];

switch($act)
{
	case 'get':
		$rs = mysql_query("SELECT * FROM smsqueue WHERE status = 0 ORDER BY id LIMIT 20");
		$q = array();
		while($r = mysql_fetch_assoc($rs))
		{
			$q[$r['id']] = array('number'=>$r['number'], 'message'=>$r['message']);
		}
		echo json_encode(array('num'=>count($q), 'items'=>$q));
		die();
		break;

	case 'send':
		$number = mysql_real_escape_string($_REQUEST['number']);
		$message = mysql_real_escape_string($_REQUEST['message']);
		mysql_query("INSERT INTO smsqueue(`number`, message) VALUES('$number', '$message')");
		$newid = mysql_insert_id();
		die($newid);
		break;

	case 'status':
		$id = mysql_real_escape_string($_GET['id']);
		$rs = mysql_query("SELECT * FROM smsqueue WHERE id = '$id' LIMIT 1");
		if($r = mysql_fetch_assoc($rs))
		{
			echo $r['status'];
		}
		else
			echo 'NOTFOUND';
		die();
		break;
				
	case 'submit':
		$data = mysql_real_escape_string($_POST['data']);
		mysql_query("UPDATE smsqueue SET status=2 WHERE id IN ($data)");
		die('OK');
		break;
}