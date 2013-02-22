<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-type: text");
$conn = connect();

include_once "scripts/polling.php";

$msisdn = (isset($_REQUEST['msisdn']) ? $_REQUEST['msisdn'] : '');
$sms = (isset($_REQUEST['sms']) ? $_REQUEST['sms'] : '');
$src = (isset($_REQUEST['src']) ? $_REQUEST['src'] : ''); // nomer modem yg terima sms
$date = date('Y-m-d');
$time = date('H:i:s');

$table = 'log_mo';

//set prefix mobile number
$msisdn = (substr($msisdn,0,2) == '62') ? '0'.(substr($msisdn,2)) : $msisdn;
$src = (substr($src,0,2) == '62') ? '0'.(substr($src,2)) : $src;

//escape string
$msisdn = mysql_real_escape_string($msisdn, $conn);
$src = mysql_real_escape_string($src, $conn);
$sms = mysql_real_escape_string($sms, $conn);

$client_id = get_client($msisdn,$src);

$sql = "INSERT INTO $table (id, msisdn, sms, src, client_id, date, time) values ('', '$msisdn', '$sms', '$src', '$client_id', '$date', '$time')";
echo "$msisdn|$src|$sms|$client_id|insert log_mo sukses";
mysql_query($sql,$conn) or die(mysql_error());

if ($src == '081330332222'){
//if (!in_array($src, array('6281555600994', '6281555600919', '6281555600946'))){
	reply($msisdn,$src,$sms);
}


function reply($msisdn,$src,$sms){
	global $_conn;
	
	$tanggal = date('Y-m-d H:i:s');
	
	$tmp = explode(" ",$sms);
	$keyword = strtolower($tmp[0]);
	$param = (isset($tmp[1]) ? strtolower($tmp[1]) : '');
	
	//space kosong ini nanti untuk menghandle perilaku parameter dan keyword
	//sisanya lakukan reply
	
	echo "\nKEYWORD = $keyword ";
	echo "PARAMETER = $param";
	
	$sql = "SELECT * FROM keywords WHERE keyword = '$keyword' AND active = 1 LIMIT 1";
	echo $sql;
	$res = mysql_query($sql,$_conn);
	if (mysql_num_rows($res)>0){
		if (cek_polling($keyword) === false){ // cek apakah keyword termasuk polling
			//echo "masuk sini";
			if (false !== $row = mysql_fetch_array($res)){
				$sql2 = "SELECT * FROM keyword_params WHERE param = '$param' AND keyword_id = '".$row['keyword_id']."' LIMIT 1";
				$rs = mysql_query($sql2,$_conn);
				if (mysql_num_rows($rs)>0){
					if (false !== $row2 = mysql_fetch_array($rs)){
						reply_sms($msisdn, $row2['reply'], $row['client_id']);
					}
				}
			}
		}else{
			$polling_id = cek_parameter($keyword);
			echo "POLLING ID = ".$polling_id;
			count_polling($polling_id, $param);
		}
	}/*else{
		reply_sms($msisdn, 'Keyword tidak dikenal', 1);
	}*/
}

function reply_sms($msisdn, $reply, $client_id){
	global $_conn;
	
	$signature = get_signature($client_id);
	
	$tanggal = date('Y-m-d H:i:s');
	
	mysql_query("INSERT INTO smsqueue ( tanggal, number, message ) VALUES ( '$tanggal', '$msisdn', '".$reply.$signature."' )",$_conn);

	$queue_id = mysql_insert_id();

	mysql_query("INSERT INTO smslog ( to_number, body_plain, campaign_id, sms_number, total_count, customer_id, client_id, queue_id ) VALUES ( '$msisdn', '".$reply.$signature."', 0, 0, 0, 0, '$client_id', '$queue_id' )", $_conn);
	
	echo 'Message Reply = '.$reply.'\n
	Destination:'.$msisdn.'\n
	Status: Sukses';

}

function get_signature($client_id){
	global $_conn;
	
	$signature = "\n\nDikirim oleh: INDOCRM.com";
	
	$sql = "SELECT signature, client_type FROM clients WHERE client_id = '$client_id'";
	$rs = mysql_query($sql,$_conn);
	if (false !== $row = mysql_fetch_array($rs)){
		if ($row['client_type'] >0){
			$signature .= "\n".$row['signature'];
		}
	}
}


function cek_polling($keyword){
	global $_conn;
	
	$sql = "SELECT * FROM polling WHERE keyword = '$keyword' AND CURDATE() BETWEEN start_date AND end_date LIMIT 1";
	echo $sql."\n";
	$res = mysql_query($sql,$_conn);
	if (mysql_num_rows($res)>0){
		return true;
	}else{
		return false;
	}
}

function connect() 
{
    global $_conn;
    
    if (!isset($_conn) || !$_conn)
    {
        $_conn = mysql_connect('localhost','k9071857_indocrm','sulfat*96') or die('connect db gagal!');
        mysql_select_db('k9071857_indocrm') or die('select database gagal!');
    }
    
	return $_conn;
}

function get_client($msisdn,$src)
{
	$conn = connect();
	
	$client_id = '0';
	
	$sql = "SELECT client_id FROM clients WHERE mobile LIKE '$src'";
	$rs = mysql_query($sql,$conn) or die(mysql_error());
	while (false !== $row = mysql_fetch_array($rs))
    {
		$client_id = $row['client_id'];
		
		if (in_array($client_id, array(45,46,47,70)))
        {
			$namegroup = "pendengar";
			$nominal = 0;
			
			$sqlgroup = "SELECT category, COUNT(*) as cnt FROM customers WHERE client_id = '$client_id' AND category like '$namegroup%' GROUP BY 1 ORDER BY 2 ASC LIMIT 1";
			$rsg = mysql_query($sqlgroup,$conn) or die(mysql_error());
			while (false != $rgroup = mysql_fetch_array($rsg))
            {
				$group = $rgroup['category'];
				$tmp = explode($namegroup, $group);
				if (count($tmp)>1)
                {
					$nominal = $tmp[1];
				}
				$jmlh = $rgroup['cnt'];
				
				if ($jmlh >= 500)
                {
					$nominal++;
					$namegroup = $namegroup.$nominal;
					$sql3 = "SELECT * FROM customer_categories WHERE category = '$namegroup' AND client_id = '$client_id'";
					$res3 = mysql_query($sql3,$conn) or die(__LINE__.":".mysql_error());
					if (mysql_num_rows($res3)==0)
                    {
						$sql4 = "INSERT INTO customer_categories (category_id, category, client_id) VALUES ('', '$namegroup', '$client_id')";
						mysql_query($sql4,$conn) or die(__LINE__.":".mysql_error());
					}
				}
                else
                {
					$namegroup = $group;
					$sql3 = "SELECT * FROM customer_categories WHERE category = '$namegroup' AND client_id = '$client_id'";
					$res3 = mysql_query($sql3,$conn) or die(__LINE__.":".mysql_error());
					if (mysql_num_rows($res3)==0)
                    {
						$sql4 = "INSERT INTO customer_categories (category_id, category, client_id) VALUES ('', '$namegroup', '$client_id')";
						mysql_query($sql4,$conn) or die(__LINE__.":".mysql_error());
					}
				}
			}
			
			$sql2 = "SELECT * FROM customers WHERE mobile = '$msisdn' AND client_id = '$client_id' LIMIT 1";
			$rs2 = mysql_query($sql2,$conn) or die(mysql_error());
			if (mysql_num_rows($rs2) == 0)
            {
				$sqlinsertcustomer = "INSERT INTO customers (mobile, client_id, country, category) values ('$msisdn','$client_id','Indonesia','$namegroup')";
				mysql_query($sqlinsertcustomer,$conn) or die(mysql_error());
			}
		}
	}
	
	if ($client_id == 0)
    {
		$sql = "SELECT client_id FROM smslog WHERE to_number LIKE '$msisdn' ORDER BY sent_date DESC LIMIT 1";
		$rs = mysql_query($sql,$conn) or die(mysql_error());
		while (false !== $row = mysql_fetch_array($rs))
		{
			$client_id = $row['client_id'];
		}
	}
    
    echo "client_id=$client_id\n";

	return $client_id;
}

