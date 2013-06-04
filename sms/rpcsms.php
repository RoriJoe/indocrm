<?php

$db_host = "localhost";
$db_username = "k9071857_indocrm";
$db_passwd = "sulfat*96";
$db_name = "k9071857_indocrm";

$conn = @mysql_connect($db_host, $db_username, $db_passwd) or die("Tidak dapat konek ke database: " . mysql_error());
mysql_select_db($db_name) or die("Database $db_name tidak dapat dibuka...");

$act = $_GET['act'];

/*** prevent limit sms ****/

$arrModem = array();
$arrCounter = array();
$arrLimit = array();

$rs = mysql_query("SELECT * FROM limit_sms");
while (false !== $row = mysql_fetch_array($rs)){
	$arrModem[] = $row['modem'];
	$arrCounter[] = $row['counter'];
	$arrLimit[] = $row['counter_limit'];
}


switch($act)
{
	case 'get':
		/*if ($arrCounter[0] >= $arrLimit[0] || $arrCounter[1] >= $arrLimit[1])
		{
			//echo json_encode(array('num'=>0, 'items'=>array()));
			echo "PULSA HABIS";
			die();
			break;
		}else{*/
			$q = array();
			$rs = mysql_query("SELECT id, number, message, usemask FROM smsqueue WHERE status = 0 ORDER BY id LIMIT 20");
			while($r = mysql_fetch_assoc($rs))
			{
				$q[$r['id']] = array('number'=>$r['number'], 'message'=>preg_replace('/[^(\xA+\x20-\x7F)]*/','', $r['message']), 'usemask'=>$r['usemask']);
			}
			
			$rs = mysql_query("SELECT id, number, message, usemask FROM smsqueue_partner WHERE status = 0 ORDER BY id LIMIT 20");
			while($r = mysql_fetch_assoc($rs))
			{
				$q['p'.$r['id']] = array('number'=>$r['number'], 'message'=>preg_replace('/[^(\xA+\x20-\x7F)]*/','', $r['message']), 'usemask'=>$r['usemask']);
			}
			echo json_encode(array('num'=>count($q), 'items'=>$q));
			die();
			break;
		//}
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
				$ls = explode(',', $data);
				foreach($ls as $id)
				{
						if (substr($id,0,1) == 'p'){
								mysql_query("UPDATE smsqueue_partner SET status=2 WHERE id = '".substr($id,1)."'");
						}else{
								mysql_query("UPDATE smsqueue SET status=2 WHERE id ='$id'");
						}
				}
		die('OK');
		break;
}
