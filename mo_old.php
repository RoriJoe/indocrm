<?php
header("Content-type: text");
$conn = connect();

$msisdn = (isset($_REQUEST['msisdn']) ? $_REQUEST['msisdn'] : '');
$sms = (isset($_REQUEST['sms']) ? $_REQUEST['sms'] : '');
$src = (isset($_REQUEST['src']) ? $_REQUEST['src'] : ''); // nomer modem yg terima sms
$date = date('Y-m-d');
$time = date('H:i:s');

$table = 'log_mo';

//set prefix mobile number
$msisdn = (substr($msisdn,0,2) == '62') ? '0'.(substr($msisdn,2)) : $msisdn;
$src = (substr($src,0,2) == '62') ? '0'.(substr($src,2)) : $src;

$client_id = get_client($msisdn,$src);

$sql = "INSERT INTO $table (id, msisdn, sms, src, client_id, date, time) values ('', '$msisdn', '$sms', '$src', '$client_id', '$date', '$time')";
echo __LINE__.":".$sql;
echo "\ninsert log_mo sukses";
mysql_query($sql,$conn) or die(mysql_error());


function connect(){
	/**/
	$conn = mysql_connect('localhost','k9071857_indocrm','sulfat*96');
	mysql_select_db('k9071857_indocrm');
	//*/
	/**
	$conn = mysql_connect('localhost','root','');
	mysql_select_db('indocrm');
	//*/
	return $conn;
}

function get_client($msisdn,$src){
	
	$conn = connect();
	
	$client_id = '0';
	
	$sql = "SELECT client_id FROM clients WHERE mobile LIKE '%$src%'";
	$rs = mysql_query($sql,$conn) or die(mysql_error());
	while (false !== $row = mysql_fetch_array($rs)){
		$client_id = $row['client_id'];
	}
	
	if ($client_id == 0){
		$sql = "SELECT client_id FROM smslog WHERE to_number LIKE '%$msisdn%' ORDER BY sent_date DESC LIMIT 1";
		//echo $sql;
		$rs = mysql_query($sql,$conn) or die(mysql_error());
		while (false !== $row = mysql_fetch_array($rs))
		{
			$client_id = $row['client_id'];
		}
	}

	
	return $client_id;
}

?>
