<?php

/**
 * This script for handling Polling request
 * author: MYRAF
 * copyright: SIMETRI
 * */

function cek_parameter($keyword){
	global $_conn;
	
	$polling_id = '';
	
	$sql = "SELECT * FROM polling WHERE keyword = '$keyword' AND CURDATE() BETWEEN start_date AND end_date LIMIT 1";
	echo $sql."\n";
	$res = mysql_query($sql,$_conn);
	if (mysql_num_rows($res)>0){
		if (false !== $row = mysql_fetch_array($res)){
			$polling_id = $row['polling_id'];
			return $polling_id;
		}
	}
}

function count_polling($polling_id, $parameter){
	global $_conn;
	
	$strSet = '';
	
	switch($parameter) {
		case "a":
			$strSet = "pilihan1 = pilihan1 + 1";
		break;
		case "b":
			$strSet = "pilihan2 = pilihan2 + 1";
		break;
		case "c": 
			$strSet = "pilihan3 = pilihan3 + 1";
		break;
		case "d": 
			$strSet = "pilihan4 = pilihan4 + 1";
		break;
		case "e": 
			$strSet = "pilihan5 = pilihan5 + 1";
		break;
	}
	
	$sql = "UPDATE polling_result SET ".$strSet." WHERE polling_id = '$polling_id'";
	//echo $sql;
	mysql_query($sql,$_conn);
}

?>
