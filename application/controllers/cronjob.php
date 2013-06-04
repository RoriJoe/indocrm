<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

/**
 * Description of cronjob
 *
 * @author ferdhie
 */
class Cronjob extends CI_Controller
{
	var $campaign_ids = array();
	var $start_time = 0;
	var $run_time = 0;
	
	var $datetime = '';
	
	var $cron_fields = array(
		0 => 'repeat',
		1 => 'tgl_start',
		2 => 'time_start',
		3 => 'tgl_end',
		4 => 'time_end',
		5 => 'repeat_by',
		6 => 'counter_limit',
		7 => 'minggu',
		8 => 'senin',
		9 => 'selasa',
		10 => 'rabu',
		11 => 'kamis',
		12 => 'jumat',
		13 => 'sabtu',
		14 => 'ckbln',
		15 => 'is_repeat',
	);
	
	function __construct() 
	{
		parent::__construct();
		
		$this->datetime = date('Y-m-d H:i:s', strtotime('+7 hours'));
		
		$this->run_time = 5 * 60;
		set_time_limit($this->run_time);
		
		require_once APPPATH . 'Mail/class.pop3.php';
		require_once APPPATH . 'Mail/class.smtp.php';
		require_once APPPATH . 'Mail/class.phpmailer.php';
		
		$this->start_time = time();
	}
	
	function is_stop()
	{
		usleep(10000);
		return ((time() - $this->start_time) > ($this->run_time - 5)) ? true : false;
	}
	
	function log($s)
	{
		$date = date('Y-m-d H:i:s');
		echo "[$date] $s<br />\n";
	}
	
	function insertinvoice($row)
	{
		$admin_email = $this->config->item('admin_email');
		$site_name = $this->config->item('site_name');
		
		$t = strtotime('+7 days');
		$late = date('Y-m-d 00:00:00', $t);
		$price = 50000;
		
		$insert = array(
			'due_date' => $late,
			'client_id' => $row->client_id,
			'subtotal' => $price,
			'total' => $price,
			'discount' => 0,
			'tax' => 0,
			'to_name' => $row->name,
			'to_company' => $row->name,
			'to_address' => "{$row->address}, {$row->city}\n{$row->state}, {$row->country}, {$row->zip_code}",
			'status' => 0,
		);
		$this->db->insert('invoices', $insert);

		$invoice_id = $this->db->insert_id();

		$month = date('M/y');
		
		$insert = array(
			'invoice_id' => $invoice_id,
			'description' => "Tagihan bulan $month IndoCRM",
			'quantity' => 1,
			'price' => $price,
			'discount' => 0,
			'discount_percent' => 0,
			'tax' => 0,
			'tax_percent' => 0,
			'subtotal' => $price,
			'total' => $price,
			'status' => 0,
		);
		$this->db->insert('invoice_detail', $insert);
		
		$subject = "[IndoCRM.com] Tagihan bulan $month IndoCRM #$invoice_id";
		
		$body = "Halo {$row->name}\n\nBerikut adalah detail tagihan akun IndoCRM anda untuk bulan $month\n\n".
			"Invoice Number: #{$invoice_id}\n".
			"To: {$row->name}\n".
			"Address: {$row->address}, {$row->city}\n{$row->state}, {$row->country}, {$row->zip_code}\n\n".
			"Deskripsi: Tagihan IndoCRM Bulan $month\n".
			"Jumlah Tagihan: Rp. $price,-\n".
			"Due: ".date('d/M/Y', $t).",-\n\n".
			"Pembayaran dapat di transfer ke rekening:".
			"BCA\nan Joko Siswanto\n448.028.3339\n\n".
			"Mohon lakukan pembayaran paling lambat 7 hari setelah e-mail ini dikirimkan.\n\nTerima kasih telah menggunakan IndoCRM".
			"\n\n--\nPesan ini telah diproduksi dan didistribusikan oleh PT Sinar Media Tiga, Raya Sulfat 96c, Malang, Indonesia 65123 - 0341-406633";

		$this->log("Report: $subject<br />\n$body");
		
		@mail( $row->email, $subject, $body, "From: $admin_email\r\nTo: {$row->email}");
	}
	
	function lockmail($row)
	{
		$admin_email = $this->config->item('admin_email');
		$site_name = $this->config->item('site_name');
		
		$subject = "[IndoCRM.com] Notifikasi Non-Aktif Akun IndoCRM";
		
		$body = "Halo {$row->name}\n\nDengan berat hati kami informasikan bawah akun anda atas nama {$row->name} kami non-aktifkan untuk sementara waktu karena alasan administrasi. Untuk informasi lebih jelas mengenai administrasi mohon kunjungi http://www.indocrm.com/dashboard".
			"\n\n--\nPesan ini telah diproduksi dan didistribusikan oleh PT Sinar Media Tiga, Raya Sulfat 96c, Malang, Indonesia 65123 - 0341-406633";

		$this->log("Report: $subject<br />\n$body");
		
		@mail( $row->email, $subject, $body, "From: $admin_email\r\nTo: {$row->email}");
	}
	
	function lockmember()
	{
		$this->log("LOCKING LATE PAY MEMBER!");
		
		//hajar yg belum membayar
		/*
		$sql = "SELECT * FROM clients WHERE active_date IS NOT NULL AND DATE_ADD(active_date, INTERVAL 40 DAY) > CURDATE() AND invoice_status = 1";
		$query = $this->db->query($sql);
		if ($query->num_rows())
		{
			foreach($query->result() as $row)
			{
				if ($this->is_stop())
				{
					$this->log("time top stop, break!!");
					exit;
				}
				
				$this->log("disable client {$row->name}");
				$this->db->query("UPDATE clients SET is_active = 0, invoice_status = 2 WHERE client_id = {$row->client_id}");
				$this->lockmail($row);
			}
		}
		
		//member baru
		$sql = "SELECT * FROM clients WHERE active_date IS NULL AND DATE_ADD(DATE(reg_date), INTERVAL 40 DAY) = CURDATE() AND invoice_status = 1";
		$query = $this->db->query($sql);
		if ($query->num_rows())
		{
			foreach($query->result() as $row)
			{
				if ($this->is_stop())
				{
					$this->log("time top stop, break!!");
					exit;
				}
				
				$this->log("disable client {$row->name}");
				$this->db->query("UPDATE clients SET is_active = 0, invoice_status = 2 WHERE client_id = {$row->client_id}");
				$this->lockmail($row);
			}
		}
		*/
	}
	
	function geninvoice()
	{
		$this->log("GENERATING INVOICE!");
		//old member
		//Joko Siswanto
		//448.028.3339
		//BCA Cabang KCP Sukun
		
		$sql = "SELECT * FROM clients WHERE active_date IS NOT NULL AND DATE_ADD(active_date, INTERVAL 1 MONTH) = CURDATE() AND invoice_status = 0 AND client_type = 1";
		$query = $this->db->query($sql);
		if ($query->num_rows())
		{
			foreach($query->result() as $row)
			{
				if ($this->is_stop())
				{
					$this->log("time top stop, break!!");
					exit;
				}
			
				$this->log("generate invoice for {$row->name}");
				$this->insertinvoice($row);
				
				$this->db->query("UPDATE clients SET invoice_status = 1 WHERE client_id = {$row->client_id}");
			}
		}
		
		//member baru
		/*
		$sql = "SELECT * FROM clients WHERE active_date IS NULL AND (DATE_ADD(DATE(reg_date), INTERVAL 1 MONTH) = CURDATE() OR mail_count > 0 OR sms_count > 0) AND invoice_status = 0";
		$query = $this->db->query($sql);
		if ($query->num_rows())
		{
			foreach($query->result() as $row)
			{
				if ($this->is_stop())
				{
					$this->log("time top stop, break!!");
					exit;
				}
				
				$this->log("generate invoice for {$row->name}");
				$this->insertinvoice($row);
				
				$this->db->query("UPDATE clients SET invoice_status = 1 WHERE client_id = {$row->client_id}");
			}
		}
		*/
	}
	
	function get_time_server()
    {
        /*-- date server 16:59
        * sama dengan WIB 23:59
        ---*/
        if(date("H:i")=='16:59')
        {
            echo "reset";
        }else{
            echo "gak onok";
        }
        
    }
    function resetquota()
	{
		//Kebijakan baru disable quota free
		/*$this->log("reset quota e-mail dan sms");
		$this->db->query("UPDATE clients SET mail_free = 50, sms_free = 50, counter_limit = 0");
		*/
        
        /*--- Reset tabel Clients 
        * kolom counter_limit
        * khusus untuk user rri client_id : 11,45,46,47,70
        * date server 16:59 sama dengan WIB 23:59
        ---*/

        //if(date("H:i")=='16:59')
       // {
            $this->db->query("UPDATE clients SET counter_limit = 0 WHERE  client_id IN (11,45,46,47,70)");
        //}

	//reset for API
		
		$this->db->query("UPDATE api_limit SET counter = 0");
		$this->tagihan();
	}
	
	function resetmonthlyquota()
	{
		//$this->log("reset quota SMS");
		//$this->db->query("UPDATE clients SET sms_count = 0 WHERE is_active = 1");
	}
	
	function is_over_quota($count, $quota, $free)
	{
		if ($free > 0)
		{
			return false;
		}
		
		if ( $quota > 0 && $count >= $quota )
		{
			return true;
		}
		
		return false;
	}
	
	function domail()
	{
		$this->log("do mail!!");
		$now = date('Y-m-d H:i:s', strtotime('+7 hours'));
		
		$campaigns = $this->db->query("SELECT * FROM campaign WHERE sent_date IS NOT NULL AND sent_date <= '$now' AND is_sent = 1 AND is_delete = 0 AND campaign_type <> 'sms' ORDER BY sent_date");
		if ($campaigns->num_rows() == 0)
		{
			$this->log("Tidak ada campaign yang belum terkirim");
			return;
		}
		
		foreach($campaigns->result() as $campaign)
		{
			if ($this->is_stop())
			{
				$this->log("time top stop, break!!");
				exit;
			}
			
			if (in_array($campaign->client_id, array(11,45,46,47,70))){
				$query = $this->db->query("SELECT limit FROM clients WHERE clientid = {$campaign->client_id}");
				
				$res = $query->result();
				
				if ($res[0]->limit >= 2){
					$this->log("Limit 500 reach, break!!");
					exit;
				}
			}
			
			$this->db->query("UPDATE campaign SET is_sent = 2 WHERE campaign_id = {$campaign->campaign_id}");
			
			$query = $this->db->query("SELECT * FROM clients WHERE client_id = {$campaign->client_id}");
			
			$client = $query->row();
			
			if (!$client->is_active)
			{
				$this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, tidak aktif");
				$this->report($campaign, $client, 4, "Pengiriman campaign {$campaign->campaign_title} gagal karena akun anda tidak aktif. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
				continue;
			}

			if ( $this->is_over_quota($client->mail_count, $client->mail_quota, $client->mail_free) )
			{
				$this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, Quota = {$client->mail_count}, over quota");
				$this->report($campaign, $client, 4, "Pengiriman campaign {$campaign->campaign_title} gagal karena akun anda melebihi kuota. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
				continue;
			}

			$query = $this->db->query("SELECT customers.* FROM campaign_details LEFT JOIN customers ON campaign_details.customer_id = customers.customer_id WHERE campaign_details.campaign_id = {$campaign->campaign_id} AND customers.is_delete = 0");
			$campaign_count = $query->num_rows();
			if ($campaign_count == 0)
			{
				$this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, Count = {$campaign_count}, kosong karena tidak ada customer utk dikirim");
				$this->report($campaign, $client, 4, "Pengiriman campaign {$campaign->campaign_title} gagal karena tidak ada customer utk dikirim. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
				continue;
			}

			$customers = $query->result();

			$parser = new template_parser();

			$from_name = $client->name;
			$from_email = $client->email;

			$total_count = 0;

			foreach($customers as $customer)
			{
				if (!$customer->email)
					continue;
				
				$qx = $this->db->query("SELECT email FROM whitelist_email WHERE email = ? LIMIT 1", array( $customer->email ));
				if ($qx->num_rows())
				{
					$this->log("Customer {$customer->customer_id}, $to_name, $to_email WHITELISTED, SKIP!");
				}

				$context = $parser->make_context($customer, $campaign, $client);

				$body_html = $parser->parse($campaign->template, $context);
				$body_plain = $parser->parse($campaign->plaintemplate ? $campaign->plaintemplate.$campaign->signature : html2plain($campaign->template).$campaign->signature, $context);

				$to_name = $customer->first_name . ' ' . $customer->last_name;
				$to_email = $customer->email;
				if (!$to_email || !$body_html || !$body_plain)
				{
					$this->log("Customer {$customer->customer_id}, $to_name, $to_email no email, no content, SKIP!");
					continue;
				}

				$total_count++;

				$this->db->query("INSERT INTO maillog ( from_name, from_email, to_name, to_email, body_html, body_plain, campaign_id, email_number, total_count, customer_id, client_id )
								VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
						array( $from_name, $from_email, $to_name, $to_email, $body_html, $body_plain, $campaign->campaign_id, $total_count, 0, $customer->customer_id, $client->client_id ));
			}

			$this->db->query("UPDATE maillog SET total_count = $total_count WHERE campaign_id = {$campaign->campaign_id}");

			$this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, Terproses = {$total_count}, SUKSES!");
		}
	}
	
	function sendbatch()
	{
		$this->log("sendbatch begin");

        $now = date('Y-m-d H:i:s', strtotime('+7 hours'));
		
		$campaigns = $this->db->query("SELECT * FROM campaign WHERE sent_date IS NOT NULL AND sent_date <= '$now' AND is_sent = 2 AND is_delete = 0  AND campaign_type <> 'sms' ORDER BY sent_date LIMIT 1");
		if ($campaigns->num_rows() == 0)
		{
			$this->log("tidak ada campaign untuk dikirim");
			return;
		}
		
		foreach($campaigns->result() as $campaign)
		{
			if ($this->is_stop())
			{
				$this->log("time top stop, break!!");
				exit;
			}
			
			$this->log("Proses campaign: {$campaign->campaign_id} , {$campaign->campaign_title}");
			
			$query = $this->db->query("SELECT * FROM clients WHERE client_id = {$campaign->client_id}");
			$client = $query->row();
			
			if (!$client->is_active)
			{
				$this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, tidak aktif");
				$this->report($campaign, $client, 4, "Pengiriman campaign {$campaign->campaign_title} gagal karena akun tidak aktif. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
				continue;
			}

			if ($this->is_over_quota($client->mail_count, $client->mail_quota, $client->mail_free))
			{
				$this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, Quota = {$client->mail_count}, over quota");
				$this->report($campaign, $client, 4, "Pengiriman campaign {$campaign->campaign_title} gagal karena akun melebihi quota. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
				continue;
			}
			
			$query = $this->db->query("SELECT * FROM maillog WHERE campaign_id = {$campaign->campaign_id} AND is_sent = 0 ORDER BY email_number");
			if ($query->num_rows() == 0)
			{
				$this->report( $campaign, $client );
				continue;
			}

			$batches = $query->result();

			$mailconfig = null;
			$query = $this->db->get_where('mailconfig', array('client_id'=> $client->client_id), 1);
			if ($query->num_rows() > 0)
			{
				$mailconfig = $query->row();
			}

			if (!$mailconfig || !$mailconfig->host || !$mailconfig->port)
			{
				$this->db->query("UPDATE maillog SET is_success = 0 WHERE campaign_id = {$campaign->campaign_id} AND is_sent = 0");
				$this->report($campaign, $client, 4, "Pengiriman campaign {$campaign->campaign_title} gagal karena akun e-mail tidak disetting. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
				continue;
			}

			$this->log("###BEGIN SMTP LOG<pre>");

			$smtp = new PHPMailer(true);
			$smtp->IsSMTP();
			$smtp->SMTPSecure = $mailconfig->ssl ? 'ssl' : ($mailconfig->tls ? 'tls' : '');
			$smtp->Host = $mailconfig->host;
			$smtp->Port = $mailconfig->port;
			$smtp->Username = $mailconfig->username;
			$smtp->Password = $mailconfig->password;
			$smtp->SMTPAuth = true;
			$smtp->SMTPDebug = 2;
			$smtp->From = $client->email ? $client->email : $mailconfig->username;
			$smtp->FromName = $client->name ? $client->name : $mailconfig->mail_name;
			$smtp->SMTPKeepAlive = true;
			$smtp->XMailer = 'IndoCRM/1.0 (http://www.indocrm.com)';

			$this->log("Set SMTP: {$smtp->Host} : {$smtp->Port}, {$smtp->Username}, {$smtp->SMTPSecure}");
			
			foreach($batches as $batch)
			{
				$success = 0;
				
				$qx = $this->db->query("SELECT email FROM whitelist_email WHERE email = ? LIMIT 1", array( $batch->to_email ));
				if ($qx->num_rows())
				{
					$this->log("E-Mail {$batch->to_email} WHITELISTED, SKIP!");
				}
				else if ( !$batch->to_email || !$batch->body_html || !$batch->body_plain )
				{
					$this->log("E-Mail {$batch->to_email} EMPTY MAIL,HTML,BODY, SKIP!");
				}
				else
				{
					try {
						$smtp->IsHTML(true);

						$unsub_url = 'http://www.indocrm.com/unsubscribe?email='.rawurlencode($batch->to_email).'&c='.$client->client_id;
						
						$powered = ($client->client_type == 0) ? 'Powered by IndoCRM.com' : '';
						$signature_html = '<br /><br /><div style="font:11px/1.5 arial,helvetica,sans-serif; text-align:center; color:#888;">Anda menerima e-mail ini karena anda terdaftar dalam salah satu member '.strtoupper($client->name).'. <a href="'.$unsub_url.'">Klik link ini untuk berhenti</a><br /><b>'.$powered.'</b></div>';
						$signature_plain = "\n\n".'Anda menerima e-mail ini karena anda terdaftar dalam salah satu member '.strtoupper($client->name).'. Silahkan kunjungi '.$unsub_url.' untuk berhenti menerima e-mail'."\n$powered";

						$smtp->Body = utf8_decode($batch->body_html . $signature_html);
						$smtp->AltBody = utf8_decode($batch->body_plain . $signature_plain);
						$smtp->Subject = $campaign->campaign_title;
						$smtp->AddAddress($batch->to_email, $batch->to_name);
						$smtp->AddCustomHeader("X-CampaignID: {$batch->log_id}");
						$smtp->AddCustomHeader("X-CampaignInfoID: {$campaign->campaign_id}");
						$smtp->AddCustomHeader("X-Report-Abuse: Please report abuse to for this campaign here (http://www.indocrm.com/abuse?id={$batch->log_id})");

						$this->log("Kirim Email ke : {$batch->to_email} - {$campaign->campaign_title}");
						if ($smtp->Send()) 
						{
							$success = 1;
							
							if ( $client->mail_free > 0 )
							{
                                $client->mail_free--;
								$this->db->query("UPDATE clients SET mail_free = {$client->mail_free} WHERE client_id = {$client->client_id}");
							}
							else
							{
                                $client->mail_count++;
								$this->db->query("UPDATE clients SET mail_count = {$client->mail_count} WHERE client_id = {$client->client_id}");
							}
						}
					} catch (phpmailerException $e) {
						$success = 0;
						$this->log("Kirim Email ke : {$batch->to_email} - {$batch->Subject} - GAGAL: " . $e->errorMessage());
					} catch (Exception $e) {
						$this->log("Kirim Email ke : {$batch->to_email} - {$batch->Subject} - GAGAL: " . $e->getMessage());
					}

					$smtp->ClearAddresses();
					$smtp->ClearCustomHeaders();
				}
				
				$curdate = date('Y-m-d H:i:s');
				$this->db->query("UPDATE maillog SET is_success = {$success}, is_sent = 1, sent_date = '$curdate' WHERE log_id = {$batch->log_id}");

				if ($this->is_over_quota($client->mail_count, $client->mail_quota, $client->mail_free))
				{
					$this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, Quota = {$client->mail_count}, over quota");
					$this->report($campaign, $client, 4, "Pengiriman campaign {$campaign->campaign_title} gagal melebihi kuota. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
					$smtp->SmtpClose();
					continue 2;
				}

				if ( $batch->email_number >= $batch->total_count )
				{
					$smtp->SmtpClose();
					$this->log("###END SMTP LOG</pre>");
					$this->report( $campaign, $client );
					continue 2;
				}
			}

			$smtp->SmtpClose();
			$this->log("###END SMTP LOG</pre>");
			$this->report( $campaign, $client );
		}
	}
	
	function report($campaign, $client, $is_sent=3, $log_message='')
	{
		$this->db->query("UPDATE campaign SET is_sent = $is_sent, log_message = ? WHERE campaign_id = {$campaign->campaign_id}", array($log_message));
		
		$admin_email = $this->config->item('admin_email');
		$site_name = $this->config->item('site_name');
		
		$result = $this->db->query("SELECT is_sent, is_success, COUNT(*) AS cnt FROM maillog WHERE campaign_id = {$campaign->campaign_id} GROUP BY is_sent, is_success")->result();
		
		$reportstr = '';
		$total = 0;
		$success = 0;
		$failed = 0;
		$unsent = 0;
		foreach($result as $row)
		{
			if ($row->is_sent)
			{
				if ($row->is_success)
				{
					$success+=$row->cnt;
				}
				else
				{
					$failed+=$row->cnt;
				}
			}
			else
			{
				$unsent+=$row->cnt;
			}
			
			$total+=$row->cnt;
		}
		
		$reportstr .= "TOTAL MEMBER: {$total}\n";
		$reportstr .= "SUKSES: {$success}\n";
		$reportstr .= "GAGAL: {$failed}\n";
		$reportstr .= "TIDAK TERKIRIM: {$unsent}\n";
		
		if (!$log_message)
		{
			$log_message = "Campaign {$campaign->campaign_title} telah diproses dengan detail sbb\n$reportstr\n";
		}
		
		$subject = "$site_name: Report Campaign #$campaign->campaign_id \"$campaign->campaign_title\"";
		$body = $log_message.
			"\n\n--\n".
			"Pesan ini telah diproduksi dan didistribusikan oleh PT Sinar Media Tiga, Raya Sulfat 96c, Malang, Indonesia 65123 - 0341-406633";
		
		$this->log("Report: $subject<br />\n$body");
		@mail( $client->email, $subject, $body, "From: $admin_email\r\nTo: {$client->email}");
	}
	
	function dosms()
	{
		$crons = array();
		
		$now = $this->datetime;
		$sql = "SELECT * FROM campaign WHERE sent_date IS NOT NULL AND sent_date <= '$now' AND is_sent = 1 AND is_delete = 0  AND campaign_type = 'sms' AND is_crontab = 0 ORDER BY sent_date";
		
		$campaigns = $this->db->query($sql);
		if ($campaigns->num_rows() == 0)
		{
			$this->log("Tidak ada campaign yang belum terkirim");
			
		}
		
		/**
		 * Kirim ucapan Ultah
		 * **/
		
		$this->_send_birthday(); 
		
		
		/**  cek jadwal campaign **/
		$tmp = explode(" ",$now);
		$tanggalnow = $tmp[0];
		
		
		$crons = $this->db->query("SELECT * FROM campaign a JOIN cron_schedule b ON a.campaign_id = b.campaign_id WHERE a.is_crontab = 1 AND a.campaign_type = 'sms' AND a.is_delete = 0 AND b.tgl_end <= '$tanggalnow' AND b.exec_date <= '$tanggalnow' AND counter_limit <= counter");
		//echo $this->db->last_query();
		if ($crons->num_rows() == 0){
			$this->log("Tidak ada jadwal campaign");
			
		}
		
		if ($campaigns->num_rows()>0){
			$this->_sms_sending($campaigns);
		}
		
		if ($crons->num_rows()==0 && $campaigns->num_rows()==0){
			return;
		}
		
		
		/**** jalanin cron_schedule ******/
		
		
		$arrCampaignID = array();
		
		if (count($crons)>0)
		{
		
			$datetime = $this->datetime;
			
			$tmp = explode(" ",$datetime);
			$tanggalnow = $tmp[0];
			$timenow = $tmp[1];
			list($y,$m,$d) = explode("-", $tmp[0]);
			
			foreach($crons->result() as $row){
				
				if ($row->counter >= $row->counter_limit){
					continue;
				}
				
				if ($row->once_a_year == 1) //setahun sekali
				{
					$tglbln = $row->bln.'-'.$row->tgl;
					if ($tglbln == $m.'-'.$d){
						$arrCampaignID[] = $row->campaign_id;
						continue;
					}
				} else if ($row->hari == '*' && $row->tgl != '*' && $row->bln != '*' ) // sebulan sekali
				{
					list($y,$m,$d) = explode("-",$row->tgl_start);
					$hari = date('w', mktime(0,0,0,$m,$d,$y)); //cek hari tanggal asli
					
					$day = $d; //cek ini minggu ke berapa
					$week = $day/7;
					$week = round($week);
					
					if ($row->week_of_month == $week && $row->week_of_month != 0) //jika minggu sama
					{
						if ($hari == date('w', mktime(0,0,0,$m,$d,$y))) //jika hari ini sesuai dengan hari asli
						{
							$arrCampaignID[] = $row->campaign_id;
							continue;
						}
					}else{
						if ($row->tgl == $d && $row->bln == $m)
						{
							$arrCampaignID[] = $row->campaign_id;
							continue;
						}
					}
				} else if ($row->hari != '*' && $row->week_of_month == '0' && $row->once_a_year == '0') //seminggu sekali
				{
					$arrHari = explode(",", $row->hari);
					if (in_array(date('w', mktime(0,0,0,$m,$d,$y)), $arrHari)){
						$arrCampaignID[] = $row->campaign_id;
						continue;
					}
				} else {
					///harian
					$arrCampaignID[] = $row->campaign_id;
				}
			}
		}
		
		$sql = "SELECT * FROM campaign WHERE campaign_id IN ('". implode(",", $arrCampaignID) ."')";
		$query = $this->db->query($sql);
		
		$this->_sms_sending($query);
	}
	
	private function _sms_sending($campaigns){
		
		/*** digunakan sementara ***/
		
		//$this->_pulsa_report();
		
		/*** digunakan sementara ***/
		
		$now = $this->datetime;
		foreach($campaigns->result() as $campaign)
		{
			if ($this->is_stop())
			{
				$this->log("time top stop, break!!");
				exit;
			}
			
			
			//if ($campaign->client_id != 109){
			if (in_array($campaign->client_id, array(11,45,46,47,70)))
			{
				$query = $this->db->query("SELECT counter_limit FROM clients WHERE client_id = {$campaign->client_id} LIMIT 1");
				//echo $this->db->last_query();
				$rs = $query->result();
				if ($rs[0]->counter_limit >= 500)
				{
					$this->log("SMS LIMIT reached, break!!");
					exit;
				}
			}
			
			$this->db->query("UPDATE campaign SET is_sent = 2 WHERE campaign_id = {$campaign->campaign_id}");
			
			$query = $this->db->query("SELECT * FROM clients WHERE client_id = {$campaign->client_id}");
			$client = $query->row();

			if (!$client->is_active || !$client->phone)
			{
				$this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, tidak aktif");
				$errorx = "akun anda tidak aktif";
				if (!$client->phone)
					$errorx .= "nomer telepon profil anda tidak valid";
				$this->smsreport($campaign->campaign_id, 0, 4, "Pengiriman $campaign->campaign_title gagal karena $errorx. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
				continue;
			}
            
			if ($this->is_over_quota($client->sms_count, $client->sms_quota, $client->sms_free))
			{
				$this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, Quota = {$client->sms_count}, over quota");
				$this->smsreport($campaign->campaign_id, 0, 4, "Pengiriman $campaign->campaign_title gagal karena akun anda melebihi kuota. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
				continue;
			}
			
			$query = $this->db->query("SELECT customers.* FROM campaign_details LEFT JOIN customers ON campaign_details.customer_id = customers.customer_id WHERE campaign_details.campaign_id = {$campaign->campaign_id} AND customers.is_delete = 0");
			$campaign_count = $query->num_rows();
			if ($campaign_count == 0)
			{
				$this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, Count = {$campaign_count}, kosong karena tidak ada customer utk dikirim");
				$this->smsreport($campaign->campaign_id, 0, 4, "Pengiriman $campaign->campaign_title gagal karena tidak ada customer utk dikirim. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
				continue;
			}

			if ( !isset($this->company) )
			{
				$this->company = $this->User_model->get_company($campaign->client_id);
			}


			$customers = $query->result();

			$parser = new template_parser();

			$total_count = 0;
			$actual_count = 0;

			foreach($customers as $customer)
			{
            
                /*-- reset signature to null --*/
                $postfix ="";
                
				/*if ($this->_checkpulsa_sms() === TRUE){
				//	$this->_sending_report_pulsa_habis();
					break;
					
					//break if pulsa habis
				}*/
				$to_number = $customer->mobile;
				$context = $parser->make_context($customer, $campaign, $client);
				$body_plain = $parser->parse($campaign->plaintemplate, $context);
				
				if (!$to_number || !preg_match('~^[0-9]+$~', $to_number) || !$body_plain)
				{
					$this->log("Customer {$customer->customer_id}, $to_number no number or $body_plain empty, SKIP!");
					continue;
				}
				
				//$postfix = "\n\nPengirim: {$this->company->phone} ";
				
				/*if (in_array($client->client_id, array(48, 36))){
					$postfix = "";
				}*/
				
				/*
				if ($client->client_type == 0)
				{
					$maxsmslen = 160;
					if (in_array($client->client_id, array(48, 36)))
                    {
                        $postfix .= "\nmalang.sbp.net.id\nvia IndoCRM.com";
					}
                    elseif (in_array($client->client_id, array(106)))
                    {
						$postfix .= "\n".(empty($campaign->signature)? '' : $campaign->signature);
					}
					else
					{
						$postfix .= "\nSMS GRATIS IndoCRM.com";
					}
				}
				else
				{
					$maxsmslen = 160*4;
					if (in_array($client->client_id, array(48, 36)))
					{
						$postfix .= "\nmalang.sbp.net.id\nvia IndoCRM.com";
					}
					else
					{
						$postfix .= "\n".(empty($campaign->signature)? '' : $campaign->signature);
					}
				}
				
				if ($client->client_id == 109)
				{
					$postfix = '';
				}
				*/
				
				$maxsmslen = 160*4;
				$postfix .= "\n".(empty($campaign->signature)? '' : $campaign->signature);
				
				if (strlen($body_plain) + strlen($postfix) > $maxsmslen)
				{
					$body_plain = substr($body_plain, 0, $maxsmslen-strlen($postfix));
				}
				
				$body_plain .= $postfix;
                
                $total_sms = 1;

				if ($maxsmslen > 160)
				{
					$total_sms = ceil(strlen($body_plain)/160);
				}
				
				$quota = $client->sms_quota;
				$smscount = $client->sms_count;
				
			//	echo "QUOTA : $quota \n<br>";
			//	echo "SMSCOUNT : $smscount \n<br>";
				
				$clientsmsquota = $client->sms_quota;
				$clientsmsfree = $client->sms_free;
				
				if ($clientsmsquota > 0)
				{
					$clientsmsquota -= $total_sms;
					if ($clientsmsquota < 0)
					{
						$kelebihan = abs($clientsmsquota);
						$clientsmsquota += $kelebihan;
						$clientsmsfree -= $total_sms;
					}
				}
				else
				{
					$clientsmsfree -= $total_sms;
				}
				
				if ($clientsmsfree < 0) $clientsmsfree = 0;
				if ($clientsmsquota < 0) $clientsmsquota = 0;
				
				if ($this->is_over_quota($client->sms_count+$total_sms, $clientsmsquota, $clientsmsfree))
				{
					$this->db->query("UPDATE smslog SET total_count = $total_count WHERE campaign_id = {$campaign->campaign_id}");
					$this->db->query("UPDATE clients SET sms_count = {$client->sms_count}, sms_free = {$client->sms_free} WHERE client_id = {$client->client_id}");
					$this->log("Stop sending, QUOTA={$client->sms_quota},COUNT={$total_count},SMS_COUNT={$client->sms_count}, out of quota");
					$this->smsreport($campaign->campaign_id, $actual_count, 4, "Pengiriman $campaign->campaign_title gagal karena akun anda melebihi kuota. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
					continue 2;
				}
				
				$total_count++;
				
				if ($client->client_type == 2)
                {
					$this->db->query("INSERT INTO smsqueue_partner ( tanggal, number, message ) VALUES ( '$now', ?, ? )", array( $to_number, $body_plain ));
				}
                else
                {
					$this->db->query("INSERT INTO smsqueue ( tanggal, number, message ) VALUES ( '$now', ?, ? )", array( $to_number, $body_plain ));
				}
                
                $queue_id = $this->db->insert_id();
				$this->db->query("INSERT INTO smslog ( to_number, body_plain, campaign_id, sms_number, total_count, customer_id, client_id, queue_id ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )", 
						array( $to_number, $body_plain, $campaign->campaign_id, $total_count, 0, $customer->customer_id, $client->client_id, $queue_id ));
				
				if ($client->sms_quota > $total_sms)
				{
					
					/*$client->sms_quota -= $total_sms;
					
					if ($client->quota < 0){
						$kelebihan = abs($client->sms_quota);
						$client->sms_quota += $kelebihan;
						$client->sms_free -= $total_sms;
					}*/
					
					$client->sms_count += $total_sms;
					
				}
				else
				{
					if ($client->sms_free > $total_sms)
					{
						$client->sms_free -= $total_sms;
					}
					else if ($client->sms_free > 0)
					{
						$client->sms_free = 0;
						$client->sms_count+= abs($total_sms - $client->sms_free);
					}
					else
					{
						$client->sms_count += $total_sms;
					}
				}
				
				$this->db->query("UPDATE clients SET sms_count = {$client->sms_count}, sms_quota = {$client->sms_quota}, sms_free = {$client->sms_free} WHERE client_id = {$client->client_id}");
				
				$this->db->query("UPDATE limit_sms SET counter = counter + ". $total_sms ." WHERE modem = 'modem9'");
				
				
				$this->db->where('campaign_id', $campaign->campaign_id);
				$this->db->where('is_crontab', '1');
				$this->db->where('campaign_type', 'sms');
				$q = $this->db->get('campaign');
				if ($q->num_rows()>0)
				{
					$sql = "UPDATE cron_schedule SET counter = counter + ".$total_sms.", exec_date = '$tanggalnow', exec_time = '$timenow' WHERE counter_limit <> 0 AND campaign_id = {$campaign->campaign_id}";
					$this->db->query($sql);
					
					$sql = "UPDATE cron_schedule SET exec_date = '$tanggalnow', exec_time = '$timenow' WHERE counter_limit = 0 AND campaign_id = {$campaign->campaign_id}";
					$this->db->query($sql);
				}
			}
			
			$tmp = explode(" ",$this->datetime);
			$tanggalnow = $tmp[0];
			$timenow = $tmp[1];
			
			$this->db->query("UPDATE clients SET counter_limit = counter_limit + $total_count WHERE client_id = {$campaign->client_id}");

			$this->db->query("UPDATE smslog SET total_count = $total_count WHERE campaign_id = {$campaign->campaign_id}");
			$this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, Terproses = {$total_count}, SUKSES!");
			
		}
		
	}
	
	function checksmsstatus()
	{
		$this->log("checksms status");

		$queue_ids = array();
		$batch_limit = 100;
		
		$query = $this->db->query("SELECT * FROM smslog WHERE is_sent = 0");
		foreach($query->result() as $row)
		{
			$queue_ids[] = $row->queue_id;
			if (count($queue_ids) >= $batch_limit)
			{
				$this->update_queue($queue_ids);
				$queue_ids = array();
			}
		}
		
		if (count($queue_ids))
		{
			$this->update_queue($queue_ids);
		}
		
		$qry = $this->db->query("SELECT smslog.campaign_id,smslog.total_count,COUNT(*) AS cnt FROM smslog 
								LEFT JOIN campaign ON smslog.campaign_id = campaign.campaign_id
								WHERE smslog.is_sent = 1 AND campaign.is_sent = 2
								GROUP BY smslog.campaign_id");
		if ($qry->num_rows())
		{
			foreach($qry->result() as $row)
			{
				$this->log( "cek total count untuk Campaign #{$row->campaign_id}, Total={$row->total_count}, Success={$row->cnt}" );
				if ($row->total_count <= $row->cnt)
				{
					$this->smsreport($row->campaign_id, $row->total_count);
				}
			}
		}
		
	}
	
	function smsreport($campaign_id, $count, $is_sent=3, $log_message='')
	{
		$admin_email = $this->config->item('admin_email');
		$site_name = $this->config->item('site_name');
		
		$query = $this->db->query("SELECT * FROM campaign WHERE campaign_id = {$campaign_id}");
		if ($query->num_rows() == 0)
		{
			$this->log("Tidak ada campaign yang belum terkirim");
			return;
		}
		
		$campaign = $query->row();
		
		$this->db->query("UPDATE campaign SET is_sent = $is_sent, log_message = ? WHERE campaign_id = {$campaign->campaign_id}", array($log_message));
		$query = $this->db->query("SELECT * FROM clients WHERE client_id = {$campaign->client_id}");
		if ($query->num_rows() == 0)
		{
			$this->log("Client tidak ada");
			return;
		}
		
		$client = $query->row();
			
		if (!$log_message)
		{
			$reportstr = "Total terkirim: $count\n";
			$log_message = "Campaign {$campaign->campaign_title} telah terkirim dengan detail sbb\n$reportstr\n";
		}
		else if ($count)
		{
			$log_message .= "\nTotal terkirim: $count\n";
		}
		
		$subject = "$site_name: Report Campaign #$campaign->campaign_id \"$campaign->campaign_title\"";
		$body = $log_message.
			"\n\n--\n".
			"Pesan ini telah diproduksi dan didistribusikan oleh PT Sinar Media Tiga, Raya Sulfat 96c, Malang, Indonesia 65123 - 0341-406633";

		$this->log("Report: $subject<br />\n$body");
		
		@mail( $client->email, $subject, $body, "From: $admin_email\r\nTo: {$client->email}");
	}
	
	function update_queue( $queue_ids )
	{
		$updated = array();
		$query2 = $this->db->query("SELECT id FROM smsqueue WHERE id IN (".implode(",", $queue_ids).") AND status = 2");
		if ($query2->num_rows() > 0)
		{
			foreach($query2->result() as $row2)
			{
				$updated[] = $row2->id;
			}
			$this->db->query("UPDATE smslog SET is_sent = 1 WHERE queue_id IN ( ".implode(", " , $updated)." )");
		}
		$this->log("updating smslog ".count($queue_ids).", ".count($updated));
	}
	
	public function savecron(){
		$this->orca_auth->login_required();
		
		$data['campaign_id'] = $this->input->post('campaign_id');
		
		$crontab = array();
		
		foreach($this->cron_fields as $field){
			$crontab[$field] = $this->input->post($field);
		}
		
		
	if (empty($crontab['schedule_id']) || !isset($crontab['schedule_id']))
			$sql = "INSERT INTO cron_schedule ";
		else
			$sql = "UPDATE cron_schedule SET ";
			
		$arrHari = array($crontab['minggu'], $crontab['senin'], $crontab['selasa'], $crontab['rabu'], $crontab['kamis'], $crontab['jumat'], $crontab['sabtu']);
		foreach($arrHari as $hari){
			if (empty($hari) || !isset($hari)){
				continue;
			}else{
				$inputhari[] = $hari;
			}
		}
		$inputhari = implode(",", $inputhari);
		switch ($crontab['repeat_by']){
			case 0: //days
				$inputhari = "0,1,2,3,4,5,6";
				if ($crontab['is_repeat'] == 1){
					if (empty($crontab['schedule_id']) || !isset($crontab['schedule_id'])){
						$sql .= "(campaign_id, hari, tgl_start, time_start, tgl_end, time_end, is_repeat) 
						VALUES ('".$data['campaign_id']."', '".$inputhari."', '".$crontab['tgl_start']."', '".$crontab['time_start']."','".$crontab['tgl_end']."','".$crontab['time_end']."','".$crontab['is_repeat']."')";
					}else{
						$sql .= "hari = '".$inputhari."', tgl_start = '".$crontab['tgl_start']."', time_start = '".$crontab['time_start']."', tgl_end = '".$crontab['tgl_end']."', time_end = '".$crontab['time_end']."', is_repeat = '".$crontab['is_repeat']."'
						WHERE campaign_id = '".$data['campaign_id']."' AND schedule_id = '".$crontab['schedule_id']."'";
					}
				}else{
					if (empty($crontab['schedule_id']) || !isset($crontab['schedule_id'])){
						$sql .= "(campaign_id, hari, tgl_start, time_start, tgl_end, time_end, counter_limit, is_repeat) 
						VALUES ('".$data['campaign_id']."', '".$inputhari."', '".$crontab['tgl_start']."', '".$crontab['time_start']."','".$crontab['tgl_end']."','".$crontab['time_end']."','".$crontab['counter_limit']."','".$crontab['is_repeat']."')";
					}else{
						$sql .= "hari = '".$inputhari."', tgl_start = '".$crontab['tgl_start']."', time_start = '".$crontab['time_start']."', tgl_end = '".$crontab['tgl_end']."', time_end = '".$crontab['time_end']."', counter_limit = '".$crontab['counter_limit']."', is_repeat = '".$crontab['is_repeat']."'
						WHERE campaign_id = '".$data['campaign_id']."' AND schedule_id = '".$crontabp['schedule_id']."'";
					}
				}
				break;
			case 1: //weeks
				if ($crontab['is_repeat'] == 1){
					if (empty($crontab['schedule_id']) || !isset($crontab['schedule_id'])){
						$sql .= "(campaign_id, hari, tgl_start, time_start, tgl_end, time_end, is_repeat) 
						VALUES ('".$data['campaign_id']."','".$inputhari."', '".$crontab['tgl_start']."', '".$crontab['time_start']."','".$crontab['tgl_end']."','".$crontab['time_end']."','".$crontab['is_repeat']."')";
					}else{
						$sql = "hari = '".$inputhari."', tgl_start = '".$crontab['tgl_start']."', time_start='".$crontab['time_start']."', tgl_end ='".$crontab['tgl_end']."', time_end = '".$crontab['time_end']."', is_repeat = '".$crontab['is_repeat']."' 
						WHERE campaign_id = '".$data['campaign_id']."' AND schedule_id = '".$crontabp['schedule_id']."'";
					}
				}else{
					if (empty($crontab['schedule_id']) || !isset($crontab['schedule_id'])){
						$sql .= "(campaign_id, hari, tgl_start, time_start, tgl_end, time_end, counter_limit, is_repeat) 
						VALUES ('".$data['campaign_id']."','".$inputhari."', '".$crontab['tgl_start']."', '".$crontab['time_start']."','".$crontab['tgl_end']."','".$crontab['time_end']."','".$crontab['counter_limit']."','".$crontab['is_repeat']."')";
					}else{
						$sql = "hari = '".$inputhari."', tgl_start = '".$crontab['tgl_start']."', time_start = '".$crontab['time_start']."', tgl_end = '".$crontab['tgl_end']."', time_end = '".$crontab['time_end']."', counter_limit = '".$crontab['counter_limit']."', is_repeat = '".$crontab['is_repeat']."'
						WHERE campaign_id = '".$data['campaign_id']."' AND schedule_id = '".$crontabp['schedule_id']."'";
					}
				}
				break;
			case 2: //months
				list($y,$m,$d) = explode("-",$crontab['tgl_start']);
				$inputhari = date('w', mktime(0,0,0,$m,$d,$y));
				if ($crontab['is_repeat'] == 1){
					if ($crontab['ckbln'] == 1){
						$week = $d/7;
						$week = round($week);
						
						if (empty($crontab['schedule_id']) || !isset($crontab['schedule_id'])){
							$sql .= "(campaign_id, hari, tgl, bln, week_of_month, tgl_start, time_start, tgl_end, time_end, is_repeat) 
							VALUES ('".$data['campaign_id']."','".$inputhari."', '$d', '$m', '$week', '".$crontab['tgl_start']."', '".$crontab['time_start']."','".$crontab['tgl_end']."','".$crontab['time_end']."','".$crontab['is_repeat']."')";
						}else{
							$sql .= "hari = '".$inputhari."', tgl = '$d', bln = '$m', week_of_month = '$week', tgl_start = '".$crontab['tgl_start']."', time_start = '".$crontab['time_start']."', tgl_end = '".$crontab['tgl_end']."', time_end = '".$crontab['time_end']."'
							WHERE campaign_id = '".$data['campaign_id']."' AND schedule_id = '".$crontabp['schedule_id']."'";
						}
					}else{
						if (empty($crontab['schedule_id']) || !isset($crontab['schedule_id'])){
							$sql .= "(campaign_id, tgl,bln, tgl_start, time_start, tgl_end, time_end, is_repeat) 
							VALUES ('".$data['campaign_id']."','$d', '$m', '".$crontab['tgl_start']."', '".$crontab['time_start']."','".$crontab['tgl_end']."','".$crontab['time_end']."','".$crontab['is_repeat']."')";
						}else{
							$sql .= "tgl = '$d', bln = '$m', tgl_start = '".$crontab['tgl_start']."', time_start = '".$crontab['time_start']."', tgl_end = '".$crontab['tgl_end']."', time_end = '".$crontab['time_end']."'
							WHERE campaign_id = '".$data['campaign_id']."' AND schedule_id = '".$crontabp['schedule_id']."'";
						}
					}
				}else{
					if ($crontab['ckbln'] == 1){
						$week = $d/7;
						$week = round($week);
						if (empty($crontab['schedule_id']) || !isset($crontab['schedule_id'])){
							$sql .= "(campaign_id, hari, tgl, bln, week_of_month, tgl_start, time_start, tgl_end, time_end, counter_limit,is_repeat) 
							VALUES ('".$data['campaign_id']."','".$inputhari."', '$d', '$m', '$week', '".$crontab['tgl_start']."', '".$crontab['time_start']."','".$crontab['tgl_end']."','".$crontab['time_end']."','".$crontab['counter_limit']."','".$crontab['is_repeat']."')";
						}else{
							$sql .= "hari = '$inputhari', tgl = '$d', bln = '$m', week_of_month = '$week', tgl_start = '".$crontab['tgl_start']."', time_start = '".$crontab['time_start']."', tgl_end = '".$crontab['tgl_end']."', time_end = '".$crontab['time_end']."' , counter_limit = '".$crontab['counter_limit']."', is_repeat = '".$crontab['is_repeat']."'
							WHERE campaign_id = '".$data['campaign_id']."' AND schedule_id = '".$crontabp['schedule_id']."'";
						}
					}else{
						if (empty($crontab['schedule_id']) || !isset($crontab['schedule_id'])){
							$sql .= "(campaign_id, tgl,bln, tgl_start, time_start, tgl_end, time_end, counter_limit, is_repeat) 
							VALUES ('".$data['campaign_id']."','$d', '$m', '".$crontab['tgl_start']."', '".$crontab['time_start']."','".$crontab['tgl_end']."','".$crontab['time_end']."','".$crontab['counter_limit']."','".$crontab['is_repeat']."')";
						}else{
							$sql .= "tgl = '$d', bln = '$m', 
							tgl_start = '".$crontab['tgl_start']."', time_start = '".$crontab['time_start']."', tgl_end = '".$crontab['tgl_end']."', time_end = '".$crontab['time_end']."', counter_limit = '".$crontab['counter_limit']."', is_repeat = '".$crontab['is_repeat']."'
							WHERE campaign_id = '".$data['campaign_id']."' AND schedule_id = '".$crontabp['schedule_id']."'";
						}
					}
				}
				break;
			case 3: //years
				list($y,$m,$d) = explode("-",$crontab['tgl_start']);
				if ($crontab['is_repeat'] == 1){
					if (empty($crontab['schedule_id']) || !isset($crontab['schedule_id'])){
						$sql .= "(campaign_id, tgl, bln, tgl_start, time_start, tgl_end, time_end, once_a_year, is_repeat) 
						VALUES ('".$data['campaign_id']."','$d', '$m', '".$crontab['tgl_start']."', '".$crontab['time_start']."','".$crontab['tgl_end']."','".$crontab['time_end']."','1','".$crontab['is_repeat']."')";
					}else{
						$sql .="tgl = '$d', bln = '$m', tgl_start = '".$crontab['tgl_start']."', time_start = '".$crontab['time_start']."', tgl_end = '".$crontab['tgl_end']."', time_end = '".$crontab['time_end']."', once_a_year = 1, is_repeat = '".$crontab['is_repeat']."'
						WHERE campaign_id = '".$data['campaign_id']."' AND schedule_id = '".$crontabp['schedule_id']."'";
					}
				}else{
					if (empty($crontab['schedule_id']) || !isset($crontab['schedule_id'])){
						$sql .= "(campaign_id, tgl, bln, tgl_start, time_start, tgl_end, time_end, once_a_year, counter_limit, is_repeat) 
						VALUES ('".$data['campaign_id']."','$d', '$m', '".$crontab['tgl_start']."', '".$crontab['time_start']."','".$crontab['tgl_end']."','".$crontab['time_end']."','1','".$crontab['counter_limit']."','".$crontab['is_repeat']."')";
					}else{
						$sql .= "tgl = '$d', bln = '$m', tgl_start = '".$crontab['tgl_start']."', time_start = '".$crontab['time_start']."', tgl_end = '".$crontab['tgl_end']."', time_end = '".$crontab['time_end']."', once_a_year = 1, counter_limit = '".$crontab['counter_limit']."', is_repeat = '".$crontab['is_repeat']."'
						WHERE campaign_id = '".$data['campaign_id']."' AND schedule_id = '".$crontab['schedule_id']."'";
					}
				}
				break;
		}
		$this->db->query($sql);
		
		echo json_encode(array('success' => true));
	}
	
	public function cron_info(){
		$this->orca_auth->login_required();
		
		header('Content-type: application/json; charset=UTF-8');
		$result = array('success' => true, 'rows' => array(), 'totalCount' => 0);
		
		$campaign_id = $this->input->get_post('campaign_id');
		
		if (isset($campaign_id) && !empty($campaign_id)){
			$this->db->where('campaign_id', $campaign_id);
			$query = $this->db->get('cron_schedule');
			$result['totalCount'] = $query->num_rows();
			if ($query->num_rows()>0){
				$result['rows'] = $query->result();
				$result['rows'] = $result['rows'][0];
			}
		}
		
		echo json_encode($result);
	}
	
	public function cron_delete(){
		$this->orca_auth->login_required();
		
		$campaign_id = $this->input->get_post('campaign_id');
		
		header('Content-type: application/json; charset=UTF-8');
		
		$this->db->where('campaign_id', $campaign_id);
		$this->db->delete('cron_schedule');
		
		$this->db->where('campaign_id', $campaign_id);
		$this->db->update('campaign', array('is_crontab' => '0'));
		
		echo json_encode(array('success' => true));
	}
	
	private function _check_schedule(){
		
		$arrCampaign = array(); //mendapatkan campaign yang harus dijalankan saat ini.
		
		$sql = "SELECT * FROM cron_schedule WHERE tgl_end <= CURDATE()";
		
		$query = $this->db->query($sql);
		if ($query->num_rows()>0){
			$result = $query->result();
			foreach($result as $row){
				//masukin variabel
				
				$jam = $row->jam;
				$menit = $row->menit;
				$tgl = $row->tgl;
				$bln = $row->bln;
				$hari = $row->hari;
				$tgl_start = $row->tgl_start;
				$tgl_end = $row->tgl_end;
				$time_start = $row->time_start;
				$time_end = $row->time_end;
				$is_repeat = $row->is_repeat;
				
				//cek waktu
				
				
			}
		}
	}
	
	public function setting_cron(){
		$this->orca_auth->login_required();
		
		$campaign_id = $this->input->get_post('campaign_id');
		
		$this->load->view('setting_cron');
	}
	
	public function tagihan(){
		
		$sisahari = array(30,7,3,1,0);
		$arrclient = array();
		foreach($sisahari as $hari){
			$this->db->where('paid','0');
			$q = $this->db->get('tagihan_client');
			foreach($q->result() as $row){
				$tglnow = date('Y-m-d');
				$diff = abs(strtotime($tglnow) - strtotime($row->due_date));
				$years = floor($diff / (365*60*60*24));
				$days = floor(($diff - $years * 365*60*60*24)/ (60*60*24));
				if ($days == $hari){
					$arrclient[$hari][]= array($row->client_id, $row->paket_id, $row->due_date);
				}
			}
		}
		
		$arrPaket = $this->_paket();
		
		foreach($arrclient as $key => $arrVal){
			foreach($arrVal as $arr)
			{
				$resclient = $this->_get_client($arr[0]);
				
				$contentmail = "Salam dari IndoCRM.\n".
				"Pesan ini dikirimkan karena Anda telah mendaftar untuk mengikuti layanan SMS gratis dan Broadcast Email dari IndoCRM. Anda tercatat pada paket ".strtoupper($arrPaket[$arr[1]][0])."\n\n".
				"Kami perlu memberitahukan kepada Anda bahwa tagihan Anda untuk bulan Ini adalah Rp. ".number_format($arrPaket[$arr[1]][1], 2,",",".")." yang akan jatuh tempo pada tanggal ".$arr[2]." yaitu pada ".$key." hari lagi.\n".
				"Silakan Anda melakukan pembayaran ke BCA\nan Joko Siswanto\n448.028.3339\n\n".
				"Jangan lupa untuk menghubungi kami setelah Anda melakukan pembayaran ke email: ferdhie@simetri.web.id atau ke joko@simetri.web.id dengan Subject [KONFIRMASI PEMBAYARAN INDOCRM atas Nama ".$resclient->name." Tgl ".$arr[2]."]\n\n".
				"Terima kasih atas partisipasi anda\n\n".
				"Hormat kami,\n\n".
				"Simetri CRM\n\n".
				"--\n".
				"Pesan ini telah diproduksi dan didistribusikan oleh PT Sinar Media Tiga, Raya Sulfat 96c, Malang, Indonesia 65123 - 0341-406633";
				//echo $contentmail;
				@mail($resclient->email, "Indocrm.com: Tagihan", $contentmail, "from: info@indocrm.com\r\n");
				$this->log("Sending email to ".$resclient->name." : ".$resclient->email."\n");
			}
		}
	}
	
	function _paket(){
		$arr = array();
		$q = $this->db->get('paket');
		foreach($q->result() as $row){
			$arr[$row->id] = array($row->nama_paket, $row->biaya);
		}
		return $arr;
	}
	
	function _get_client($client_id){
		
		$this->db->where('client_id', $client_id);
		$q = $this->db->get('clients');
		$res = $q->result();
		
		return $res[0];
	}
	
	function _sending_report_pulsa_habis(){
		$sql = "SELECT * FROM smslog WHERE client_id = '32' AND body_plain = 'Pulsa modem 9 dengan MSISDN 085785238793 telah habis. Mohon diisi' AND sent_date = '".$this->datetime."'";
			
		$query = $this->db->query($sql);
		if ($query->num_rows() == 0){
			$tanggal = $this->datetime;
			
			$this->db->query("INSERT INTO smsqueue ( tanggal, number, message ) VALUES ( ?, ?, ? )", array( $tanggal, '081944981131', 'Pulsa modem 9 dengan MSISDN 085785238793 telah habis. Mohon diisi' ));
			
			$queue_id = $this->db->insert_id();

			$this->db->query("INSERT INTO smslog ( to_number, body_plain, campaign_id, sms_number, total_count, customer_id, client_id, queue_id ) VALUES ( ?, ?, 0, 0, 0, 0, ?, ? )", 
			array( '081944981131' ,'Pulsa modem 9 dengan MSISDN 085785238793 telah habis. Mohon diisi', 
			32, $queue_id ));
			
			$this->db->query("INSERT INTO smsqueue ( tanggal, number, message ) VALUES ( ?, ?, ? )", array( $tanggal, '085790932477', 'Pulsa modem 9 dengan MSISDN 085785238793 telah habis. Mohon diisi' ));
			
			$queue_id = $this->db->insert_id();

			$this->db->query("INSERT INTO smslog ( to_number, body_plain, campaign_id, sms_number, total_count, customer_id, client_id, queue_id ) VALUES ( ?, ?, 0, 0, 0, 0, ?, ? )", 
			array( '085790932477' ,'Pulsa modem 9 dengan MSISDN 085785238793 telah habis. Mohon diisi', 
			32, $queue_id ));
		}
	}
	
	function _checkpulsa_sms(){
		$q = $this->db->get('limit_sms');
		if ($q->num_rows()>0){
			foreach($q->result() as $row){
				$modem = $row->modem;
				$counter_limit = $row->counter_limit;
				$counter = $row->counter;
				if ($modem != 'modem9')
					continue;
			}
		}
		
		if ($counter >= $counter_limit){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	function test_sending($var){
		if ($var == 'pulsa'){
			$this->_pulsa_report();
		}else if ($var == 'habis'){
			$this->_sending_report_pulsa_habis();
		}
	}
	
	function _pulsa_report(){
		//$jam = date('H');
		$jam = intval(date('H')) + 7;
		if ($jam == 6 || $jam == 14 || $jam == 20){
			$arr = array();
			$q = $this->db->get('limit_sms');
			foreach($q->result() as $row){
				$arr[$row->phone] = $row->counter_limit - $row->counter;
			}
			
			$sql = "SELECT * FROM smslog WHERE client_id = '32' AND body_plain LIKE 'PEMBERITAHUAN HARIAN: %' AND DATE(sent_date) = CURDATE() AND HOUR(sent_date) = '".($jam-7)."'";
			
			$query = $this->db->query($sql);
			if ($query->num_rows() < 3 ){
				$str = "";
				foreach($arr as $key => $val){
					$str .= $key." Sisa $val SMS;";
				}
				$this->log("PEMBERITAHUAN SMS");
				$tanggal = date('Y-m-d H:i:s');

				$this->db->query("INSERT INTO smsqueue ( tanggal, number, message ) VALUES ( ?, ?, ? )", array( $tanggal, '081944981131', 'PEMBERITAHUAN HARIAN: '.$str ));
				
				$queue_id = $this->db->insert_id();

				$this->db->query("INSERT INTO smslog ( to_number, body_plain, campaign_id, sms_number, total_count, customer_id, client_id, queue_id ) VALUES ( ?, ?, 0, 0, 0, 0, ?, ? )", 
				array( '081944981131' ,'PEMBERITAHUAN HARIAN: '.$str, 32, $queue_id ));
				
				$this->db->query("INSERT INTO smsqueue ( tanggal, number, message ) VALUES ( ?, ?, ? )", array( $tanggal, '085790932477','PEMBERITAHUAN HARIAN: '.$str));
				
				$queue_id = $this->db->insert_id();

				$this->db->query("INSERT INTO smslog ( to_number, body_plain, campaign_id, sms_number, total_count, customer_id, client_id, queue_id ) VALUES ( ?, ?, 0, 0, 0, 0, ?, ? )", 
				array( '085790932477','PEMBERITAHUAN HARIAN: '.$str, 32, $queue_id ));
				
				$this->db->query("INSERT INTO smsqueue ( tanggal, number, message ) VALUES ( ?, ?, ? )", array( $tanggal, '08123382815','PEMBERITAHUAN HARIAN: '.$str));
				
				$queue_id = $this->db->insert_id();

				$this->db->query("INSERT INTO smslog ( to_number, body_plain, campaign_id, sms_number, total_count, customer_id, client_id, queue_id ) VALUES ( ?, ?, 0, 0, 0, 0, ?, ? )", 
				array( '08123382815','PEMBERITAHUAN HARIAN: '.$str, 32, $queue_id ));
				
			}
		}
	}
	
	function pulsa_habis(){
		
		$phone = $this->input->get_post('phone');
		if ($phone == 'all'){
			$phone = $this->_modem_phone('all');
		}
		
		if (is_array($phone)){
			
			$tanggal = $this->datetime;
			
			$this->db->query("INSERT INTO smsqueue ( tanggal, number, message ) VALUES ( ?, ?, ? )", array( $tanggal, '081944981131', 'URGENT: PULSA SEMUA MODEM HABIS. Mohon isi untuk nomor telpon '.explode(",", $phone) ));
			
			$queue_id = $this->db->insert_id();

			$this->db->query("INSERT INTO smslog ( to_number, body_plain, campaign_id, sms_number, total_count, customer_id, client_id, queue_id ) VALUES ( ?, ?, 0, 0, 0, 0, ?, ? )", 
			array( '081944981131' ,'URGENT: PULSA SEMUA MODEM HABIS. Mohon isi untuk nomor telpon '.explode(",", $phone), 
			32, $queue_id ));
			
			$this->db->query("INSERT INTO smsqueue ( tanggal, number, message ) VALUES ( ?, ?, ? )", array( $tanggal, '085790932477', 'URGENT: PULSA SEMUA MODEM HABIS. Mohon isi untuk nomor telpon '.explode(",", $phone) ));
			
			$queue_id = $this->db->insert_id();

			$this->db->query("INSERT INTO smslog ( to_number, body_plain, campaign_id, sms_number, total_count, customer_id, client_id, queue_id ) VALUES ( ?, ?, 0, 0, 0, 0, ?, ? )", 
			array( '085790932477' ,'URGENT: PULSA SEMUA MODEM HABIS. Mohon isi untuk nomor telpon '.explode(",", $phone), 
			32, $queue_id ));
		}else{
			$this->db->select('modem');
			$this->db->where('phone', $phone);
			$qu = $this->db->get('limit_sms');
			$result = $qu->result();
			$modem = $result[0]->modem;
		
			$sql = "SELECT * FROM smslog WHERE body_plain LIKE 'URGENT: PULSA $modem HABIS%' AND DATE(sent_date) = CURDATE()";
			$q = $this->db->query($sql);
			if ($q->num_rows()==0){
				$tanggal = $this->datetime;
				
				$this->db->query("INSERT INTO smsqueue ( tanggal, number, message ) VALUES ( ?, ?, ? )", array( $tanggal, '081944981131', 'URGENT: PULSA '.$modem.' HABIS. Mohon isi untuk nomor telpon '.$phone ));
				
				$queue_id = $this->db->insert_id();

				$this->db->query("INSERT INTO smslog ( to_number, body_plain, campaign_id, sms_number, total_count, customer_id, client_id, queue_id ) VALUES ( ?, ?, 0, 0, 0, 0, ?, ? )", 
				array( '081944981131' ,'URGENT: PULSA '.$modem.' HABIS. Mohon isi untuk nomor telpon '.$phone, 
				32, $queue_id ));
				
				$this->db->query("INSERT INTO smsqueue ( tanggal, number, message ) VALUES ( ?, ?, ? )", array( $tanggal, '085790932477', 'URGENT: PULSA '.$modem.' HABIS. Mohon isi untuk nomor telpon '.$phone ));
				
				$queue_id = $this->db->insert_id();

				$this->db->query("INSERT INTO smslog ( to_number, body_plain, campaign_id, sms_number, total_count, customer_id, client_id, queue_id ) VALUES ( ?, ?, 0, 0, 0, 0, ?, ? )", 
				array( '085790932477' ,'URGENT: PULSA '.$modem.' HABIS. Mohon isi untuk nomor telpon '.$phone, 
				32, $queue_id ));
			}
		}
		
		header("Content-type: json/text");
		echo json_encode(array('success' => true));
	}
	
	function update_modem(){
		$phone = $this->input->get_post('phone');
		$counter = $this->input->get_post('counter');
		
		$phone = '0'.substr($phone,2,strlen($phone));
		
		$this->db->where('phone', $phone);
		$this->db->update('limit_sms', array('counter' => $counter));
		
		header('Content-type: application/json; charset=UTF-8');
		echo json_encode(array('success' => true));
	}
	
	private function _modem_phone($modem){
		if ($modem == 'all'){
			$arrmodem = array();
			$q = $this->db->get('limit_sms');
			foreach($q->result() as $row){
				$arrmodem[] = $row->phone;
			}
			return $arrmodem;
		}else{
			$this->db->where('modem', $modem);
			$q = $this->db->get('limit_sms');
			$res = $q->result();
			$phone = $res[0]->phone;
			return $phone;
		}
	}
	
	private function _send_birthday(){
		
		$arrMobile = array();
		
		$this->db->where('bdate', date('Y-m-d'));
		$q = $this->db->get('users');
		if ($q->num_rows()>0){
			foreach($q->result() as $row){
				if ($row->mobile != ''){
					$arrMobile[] = $row->mobile;
				}
			}
		}
		
		foreach($arrMobile as $mobile){
			$sql = "SELECT * FROM smslog WHERE body_plain LIKE 'KAMI SEGENAP KRU indocrm.com%' AND DATE(sent_date) = CURDATE() AND to_number = '$mobile'";
			$q = $this->db->query($sql);
			//echo $this->db->last_query();
			if ($q->num_rows()==0){
				
				$this->db->query("INSERT INTO smsqueue ( tanggal, number, message ) VALUES ( ?, ?, ? )", array( $tanggal, $mobile, 'KAMI SEGENAP KRU indocrm.com MENGUCAPKAN SELAMAT ULANG TAHUN. SEMOGA SUKSES DAN SEHAT SELALU'));
				
				$queue_id = $this->db->insert_id();

				$this->db->query("INSERT INTO smslog ( to_number, body_plain, campaign_id, sms_number, total_count, customer_id, client_id, queue_id ) VALUES ( ?, ?, 0, 0, 0, 0, ?, ? )", 
				array( $mobile ,'KAMI SEGENAP KRU indocrm.com MENGUCAPKAN SELAMAT ULANG TAHUN. SEMOGA SUKSES DAN SEHAT SELALU', 1, $queue_id ));
				$this->log("Send Birthday To: $mobile");
			}
		}
		
		
	}
}

