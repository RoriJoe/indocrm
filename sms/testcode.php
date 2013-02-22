<?php
header("Content-type:text/plain");

$db_host = "localhost";
$db_username = "k9071857_indocrm";
$db_passwd = "sulfat*96";
$db_name = "k9071857_indocrm";

$conn = @mysql_connect($db_host, $db_username, $db_passwd) or die("Tidak dapat konek ke database: " . mysql_error());
mysql_select_db($db_name) or die("Database $db_name tidak dapat dibuka...");

$arr = array();

$sql = "SELECT category_id, category, client_id FROM customer_categories";
$rs = mysql_query($sql,$conn) or die(__LINE__.":".mysql_error());
while(false !== $row =mysql_fetch_array($rs)){
	$sql2= "SELECT customer_id, category, client_id FROM customers WHERE client_id = '".$row['client_id']."'";
	$rs2 = mysql_query($sql2,$conn) or die(__LINE__.":".mysql_error());
	while (false !== $row2 = mysql_fetch_array($rs2)){
		//$arr[$row['client_id']][] = array($row2['customer_id'], $row2['category']);
		$arr[$row2['category']][]= array($row2['customer_id'],$row2['client_id']);
	}
}

echo '<pre>';
print_r($arr);
echo '</pre>';




/*$act = $_GET['act'];

/*** prevent limit sms ****

$arrModem = array();
$arrCounter = array();
$arrLimit = array();

$rs = mysql_query("SELECT * FROM limit_sms");
while (false !== $row = mysql_fetch_array($rs)){
	$arrModem[] = $row['modem'];
	$arrCounter[] = $row['counter'];
	$arrLimit[] = $row['counter_limit'];
}


switch ($act){
	case 'get':
	if (($arrCounter[0] >= $arrLimit[0]) || ($arrCounter[1] >= $arrLimit[1]) ){
		echo "PULSA HABIS";
		die();
		break;
	}else{
		echo "Yey ada!";
	}	
	break;
	case 'send':
	echo "what ever";
	break;
}

/**** prevent limit sms ****/
