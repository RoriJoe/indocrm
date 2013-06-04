<?php
	set_time_limit(300);
	
	include 'curl.php';
	$curl = new CURL();
	
	$is_devel = false;
	
	if ($is_devel) {
		$site_url = 'http://localhost:8666/indocrm/trunk';
	} else {
		$site_url = 'http://www.indocrm.com';
	}
	
function WriteLog($content, $is_new = false, $is_end = false) {
	if ($is_new) {
		$content = date("Y-m-d H:i:s")."\n".$content."\n";
	} else {
		$content = $content."\n";
	}
	
	if ($is_end) {
		$content = $content."\n";
	}
	
	$Handle = @fopen('log.txt', 'ab+');
	if ($Handle) {
		fputs($Handle, $content);
		fclose($Handle);
	}
}
?>