<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

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

//escape string
$msisdn = mysql_real_escape_string($msisdn, $conn);
$src = mysql_real_escape_string($src, $conn);
$sms = mysql_real_escape_string($sms, $conn);

$client_id = get_client($msisdn,$src);

$sql = "INSERT INTO $table (id, msisdn, sms, src, client_id, date, time) values ('', '$msisdn', '$sms', '$src', '$client_id', '$date', '$time')";
echo "$msisdn|$src|$sms|$client_id|insert log_mo sukses";
mysql_query($sql,$conn) or die(mysql_error());

function reply($msisdn,$src,$sms){
	global $_conn;
	
	$tanggal = date('Y-m-d H:i:s');
	
	$tmp = explode(" ",$sms);
	$keyword = $tmp[0];
	$param = $tmp[1];
	
	$sql = "SELECT * FROM keywords WHERE keyword = '$keyword' AND active = 1 LIMIT 1";
	$res = mysql_query($sql,$_conn);
	if (mysql_num_rows($res)>0){
		if (false !== $row = mysql_fetch_array($res)){
			$sql2 = "SELECT * FROM keyword_params WHERE param = '$param' LIMIT 1";
			$rs = mysql_query($sql,$_conn);
			if (mysql_num_rows($rs)>0){
				if (false !== $row2 = mysql_fetch_array($rs)){
					reply_sms($msisdn, $row2['reply'], $client_id);
				}
			}else{
			}
		}
	}else{
		reply_sms($msisdn, 'Keyword tidak dikenal', $client_id);
	}
}

function reply_sms($msisdn, $reply, $client_id){
	global $_conn;
	
	$tanggal = date('Y-m-d H:i:s');
	
	$this->db->query("INSERT INTO smsqueue ( tanggal, number, message ) VALUES ( ?, ?, ? )", array( $tanggal, $msisdn, $reply));

	$queue_id = mysql_insert_id();

	$this->db->query("INSERT INTO smslog ( to_number, body_plain, campaign_id, sms_number, total_count, customer_id, client_id, queue_id ) VALUES ( ?, ?, 0, 0, 0, 0, ?, ? )", 
	array( $msisdn , $reply ,$client_id, $queue_id ));

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

