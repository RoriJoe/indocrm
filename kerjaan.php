<?php
header("Content-type: text/plain");
$conn = mysql_connect("localhost", "k9071857_indocrm", 'sulfat*96');
if (!$conn){
	die('what???');
}


mysql_select_db('k9071857_indocrm');


$sql = "SELECT client_id FROM clients";
$rs = mysql_query($sql,$conn) or die(mysql_error());
while (false != $row = mysql_fetch_array($rs)){
	$clientid = $row['client_id'];
	
	
	$arrCat = array();
	$sql2 = "SELECT category_id, category FROM customer_categories WHERE client_id = '$clientid'";
	echo __LINE__.":".$sql2."\n";
	$rs2 = mysql_query($sql2,$conn) or die(mysql_error());
	while (false != $row2 = mysql_fetch_array($rs2)){
		$arrCat[$row2['category_id']]=$row2['category'];
	}
	
	foreach($arrCat as $key => $cat){
		$custId = '';
		$sql3 = "SELECT customer_id FROM customers WHERE category = '".trim($cat)."' AND client_id = '$clientid' AND is_delete <> 1";
		echo __LINE__.":".$sql3."\n";
		$rs3 = mysql_query($sql3,$conn) or die(mysql_error());
		while (false != $row3 = mysql_fetch_array($rs3)){
			$custId = $row3['customer_id'];
			$sql4 = "INSERT IGNORE INTO customer_details (customer_id, category_id) VALUES ('$custId', '$key')";
			echo __LINE__.":".$sql4."\n";
			mysql_query($sql4,$conn) or die(mysql_error());
		}
	}
	
}


?>
