<?php
	include 'config.php';
	
	/*	region sms masking */
	
	// login
	$link_login = $site_url.'/index.php/auth/login?next=http%3A%2F%2Fwww.indocrm.com%2Findex.php%2Fdashboard';
	$link_next = $site_url.'/index.php/dashboard';
	$param_login = array( 'username' => 'lintasgps', 'password' => 'lintas*96', 'next' => $link_next, 'remember' => 'yes' );
	$reponse_login = $curl->post($link_login, $param_login);
	
	// send masking sms
	$link_send = $site_url.'/index.php/dashboard/sendsms';
	$param_send = array( 'msisdn' => '081232769000', 'usemask' => 1, 'sms' => "send masking\n".date('Y-m-d H:i:s') );
	$reponse_send = $curl->post($link_send, $param_send);
	$array_data = json_decode($reponse_send);
	WriteLog('send masking sms : '.json_encode($array_data), true);
	
	/*	end region sms masking */
	
	
	
	/*	region broadcast */
	
	// login user test
	$link_login = $site_url.'/index.php/auth/login?next=http%3A%2F%2Fwww.indocrm.com%2Findex.php%2Fdashboard';
	$link_next = $site_url.'/index.php/dashboard';
	$param_login = array( 'username' => 'tester', 'password' => 'test123', 'next' => $link_next, 'remember' => 'yes' );
	$reponse_login = $curl->post($link_login, $param_login);
	
	// sent sms broadcast
	$link_broadcast = $site_url.'/index.php/campaign/save_sms';
	$param_broadcast = array(
		'categories' => '[{"category_id":208,"category":"test","client_id":128,"customer_count":3}]',
		'customers' => '[]',
		'campaign_id' => '',
		'client_id' => 128,
		'is_direct' => 1,
		'plaintemplate' => "send broadcast\n".date('Y-m-d H:i:s'),
		'signature' => 'test',
		'campaign_type' => 'sms',
		'sent_date' => date("Y-m-d"),
		'sent_time' => date("H:i:s"),
		'campaign_source' => 'category',
		'allcustomer' => 0,
		'is_crontab' => 0,
		'tgl_start' => date("Y-m-d"),
		'tgl_end' => date("Y-m-d"),
		'repeat_by' => 0,
		'counter_limit' => 1,
		'ckbln' => 0,
		'is_repeat' => 1,
		'kirimbc' => 2
	);
	$reponse_broadcast = $curl->post($link_broadcast, $param_broadcast);
	$array_broadcast = json_decode($reponse_broadcast);
	WriteLog('send broadcast sms : '.json_encode($array_broadcast), false, true);
	
	/*	end region broadcast */
	
	
	
	/*	region sms rri */
	
	// setting
	$regex = '/\"sender\"\>\d+XXX[a-z0-9\<\>\/\s]+\=\"date\"\>([a-zA-Z0-9\<\>\/\s\,\:]+)\=\"sms\"\>([a-zA-Z0-9\<\>\/\s\,\:\.\?\&\;\(\)\+]+)\<\/div\>/i';
	
	// login user rri 1
	$link_login = $site_url.'/index.php/auth/login?next=http%3A%2F%2Fwww.indocrm.com%2Findex.php%2Fdashboard';
	$link_next = $site_url.'/index.php/dashboard';
	$param_login = array( 'username' => 'rri1', 'password' => '1234', 'next' => $link_next, 'remember' => 'yes' );
	$reponse_login = $curl->post($link_login, $param_login);
	
	// get sms rri 1
	$array_rri = array();
	$link_send = $site_url.'/index.php/dashboard/wgpenyiar';
	$reponse_send = $curl->post($link_send);
	preg_match_all($regex, $reponse_send, $match);
	if (is_array($match[1])) {
		foreach ($match[1] as $key => $raw_date) {
			$array_rri['rri1'][] = array( 'date' => trim(strip_tags($raw_date)), 'sms' => trim(strip_tags($match[2][$key])) );
			break;
		}
	}
	
	// login user rri 2
	$link_login = $site_url.'/index.php/auth/login?next=http%3A%2F%2Fwww.indocrm.com%2Findex.php%2Fdashboard';
	$link_next = $site_url.'/index.php/dashboard';
	$param_login = array( 'username' => 'rri2', 'password' => '1234', 'next' => $link_next, 'remember' => 'yes' );
	$reponse_login = $curl->post($link_login, $param_login);
	
	// get sms rri 2
	$link_send = $site_url.'/index.php/dashboard/wgpenyiar';
	$reponse_send = $curl->post($link_send);
	preg_match_all($regex, $reponse_send, $match);
	if (is_array($match[1])) {
		foreach ($match[1] as $key => $raw_date) {
			$array_rri['rri2'][] = array( 'date' => trim(strip_tags($raw_date)), 'sms' => trim(strip_tags($match[2][$key])) );
			break;
		}
	}
	
	// login user rri 4
	$link_login = $site_url.'/index.php/auth/login?next=http%3A%2F%2Fwww.indocrm.com%2Findex.php%2Fdashboard';
	$link_next = $site_url.'/index.php/dashboard';
	$param_login = array( 'username' => 'rri4', 'password' => 'rri1234', 'next' => $link_next, 'remember' => 'yes' );
	$reponse_login = $curl->post($link_login, $param_login);
	
	// get sms rri 4
	$link_send = $site_url.'/index.php/dashboard/wgpenyiar';
	$reponse_send = $curl->post($link_send);
	preg_match_all($regex, $reponse_send, $match);
	if (is_array($match[1])) {
		foreach ($match[1] as $key => $raw_date) {
			$array_rri['rri4'][] = array( 'date' => trim(strip_tags($raw_date)), 'sms' => trim(strip_tags($match[2][$key])) );
			break;
		}
	}
	
	// generate content
	$message = '';
	foreach ($array_rri as $user => $content) {
		$message .= '
			<h3>'.$user.'</h3>
			<div>'.$content[0]['date'].' => '.$content[0]['sms'].'</div>
		';
	}
	
	// send mail
	$to  = 'Herry <herry@simetri.in>, Asri Kusuma <asri@simetri.web.id>';
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: IndoCRM <noreply@indocrm.com>' . "\r\n";
	mail($to, '[IndoCRM] autocek', $message, $headers);

	/*	end region sms rri */
	
	echo 'done';
?>