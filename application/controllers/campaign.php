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
 * Description of campaign
 *
 * @author ferdhie
 */
class Campaign extends CI_Controller
{
	var $table_fields = array (
		0 => 'campaign_id',
		1 => 'client_id',
		2 => 'sent_date',
		3 => 'create_date',
		4 => 'campaign_title',
		5 => 'campaign_description',
		6 => 'signature',
		7 => 'is_delete',
		8 => 'campaign_source',
		9 => 'campaign_type',
		10 => 'is_sent',
		11 => 'plaintemplate',
		12 => 'is_direct',
		13 => 'is_crontab',
	);
	
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
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->orca_auth->login_required();
		
		//check adhoc campaign
		if ($this->db->where('client_id', $this->orca_auth->user->client_id)->where('campaign_title', 'adhoc')->count_all_results('campaign') == 0)
		{
			$this->db->insert('campaign', array('campaign_title' => 'adhoc', 'client_id' => $this->orca_auth->user->client_id));
		}
		
		$this->load->view('campaign');
	}
	
	public function all()
	{
		$this->orca_auth->login_required();
		
		header('Content-type: application/json; charset=UTF-8');
		$result = array('success' => true, 'rows' => array(), 'totalCount' => 0);

		$q = $this->input->post('q');
		$page = $this->input->post('page');
		$start = $this->input->post('start');
		$limit = $this->input->post('limit');
		$sort = $this->input->post('sort');
		$filter = $this->input->post('filter');
		if (!$limit) $limit = 20;
		
		$this->db->where('is_delete', 0);
		if ( $this->orca_auth->user->client_id )
		{
			$this->db->where('client_id', $this->orca_auth->user->client_id);
		}
		
		if ($q)
		{
			$this->_build_query($q);
		}
		
		parse_filter( $filter, $this->table_fields, 'campaign' );
		
		$totalCount = $this->db->count_all_results('campaign');
		$result['totalCount'] = $totalCount;
		if ($totalCount > 0)
		{
			$this->db->where('is_delete', 0);
			if ( $this->orca_auth->user->client_id )
			{
				$this->db->where('client_id', $this->orca_auth->user->client_id);
			}
			
			if ($q)
			{
				$this->_build_query($q);
			}
			
			parse_sort($sort, $this->table_fields);
			parse_filter( $filter, $this->table_fields, 'campaign' );
			
			$query = $this->db->get( 'campaign', $limit, $start );
			
			if ($query->num_rows() > 0)
			{
				$result['rows'] = $query->result();
			}
		}
		
		echo json_encode($result);
	}
	
	public function smslog_campaign(){
		$this->orca_auth->login_required();
		
		header('Content-type: application/json; charset=UTF-8');
		$result = array('success' => true, 'rows' => array(), 'totalCount' => 0);
		
		$campaign_id = $this->input->get_post('id');
		$q = $this->input->post('q');
		$page = $this->input->post('page');
		$start = $this->input->post('start');
		$limit = $this->input->post('limit');
		$sort = $this->input->post('sort');
		$filter = $this->input->post('filter');
		if (!$limit) $limit = 20;
		

		$this->db->where('campaign_id', $campaign_id);
		
		parse_filter( $filter, $this->table_fields, 'smslog' );
		
		$totalCount = $this->db->count_all_results('smslog');
		$result['totalCount'] = $totalCount;
		if ($totalCount > 0)
		{
			$this->db->where('campaign_id', $campaign_id);
				
			parse_sort($sort, $this->table_fields);
			parse_filter( $filter, $this->table_fields, 'smslog' );
			
			$query = $this->db->get( 'smslog', $limit, $start );
			
			if ($query->num_rows() > 0)
			{
				$result['rows'] = $query->result();
			}
		}
		echo json_encode($result);
	}
	
	public function log_campaign(){
		$this->orca_auth->login_required();
		
		$id = $this->input->get_post('id');
		if ($id){
			$campaign_id = $id;
			$this->load->view('log_campaign');
			return;
		}
		show_404();
		return;
	}
	
	private function _build_query( $query )
	{
		$this->db->like('campaign_title', $query);
		$this->db->or_like('campaign_description', $query);
	}
	
	public function query()
	{
		$this->orca_auth->login_required();
		
		$result = array('success' => true, 'totalCount' => 0, 'rows' => array());
		
		$page = $this->input->post('page');
		$start = $this->input->post('start');
		$limit = $this->input->post('limit');
		$query = $this->input->post('query');
		if (!$limit) $limit = 20;
		
		$this->db->where('is_delete', 0);
		
		if ( $this->orca_auth->user->client_id )
		{
			$this->db->where('client_id', $this->orca_auth->user->client_id);
		}
		
		if ($query)
		{
			$query = strtoupper($query);
			$this->_build_query($query);
			$result['totalCount'] = $this->db->count_all_results('campaign');
			if ($result['totalCount'] > 0)
			{
				$this->db->where('is_delete', 0);

				if ( $this->orca_auth->user->client_id )
				{
					$this->db->where('client_id', $this->orca_auth->user->client_id);
				}
				$this->_build_query($query);
				$query = $this->db->get( 'campaign', $limit, $start );
				if ($query->num_rows() > 0)
				{
					$result['rows'] = $query->result();
				}
			}
		}
		
		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode($result);
	}

	function delete()
	{
		$this->orca_auth->login_required();
		
		$data = array_filter(array_map('intval', $this->input->post('data')));
		if ( $data )
		{
			$this->db->where('client_id', $this->orca_auth->user->client_id)->where('is_delete', 1);
			$this->db->update('campaign', array('is_delete' => 2));
			
			$this->db->where('client_id', $this->orca_auth->user->client_id)->where_in('campaign_id', $data);
			$this->db->update('campaign', array('is_delete' => 1));
			
			$this->db->where_in('campaign_id', $data);
			$this->db->delete('campaign_details');
		}
		
		echo json_encode(array('success' => true));
	}
	
	function getcustomers()
	{
		$this->orca_auth->login_required();
		$campaign_id = $this->input->get_post('campaign_id');
		
		if ($campaign_id)
		{
			$result = array('success' => true, 'rows' => array());
			if ($this->orca_auth->user->client_id)
				$this->db->where('customers.client_id', $this->orca_auth->user->client_id);
			
			$this->db->where('campaign_details.campaign_id', $campaign_id);
			
			$this->db->select('customers.*')->from('customers')->join('campaign_details', 'customers.customer_id = campaign_details.customer_id');
			$query = $this->db->get('');
			
			if ($query->num_rows() > 0)
			{
				$result['rows'] = $query->result();
			}
			header('Content-type: application/json; charset=UTF-8');
			echo json_encode($result);
			return;
		}
		show_404();
	}
	
	function create()
	{
		$this->orca_auth->login_required();
		$id = $this->input->get_post('id');
		$campaign = null;
		if ($id)
		{
			$query = $this->db->get_where('campaign', array('campaign_id'=> $id, 'client_id' => $this->orca_auth->user->client_id), 1);
			if ($query->num_rows())
			{
				$campaign = $query->row();
			}
			
			if (!$campaign)
			{
				show_404();
				return;
			}
		}
		
		$this->load->view('create_campaign', array('campaign' => $campaign));
	}
	
	function plaintext()
	{
		$this->orca_auth->login_required();
		$id = $this->input->get_post('id');
		$txt = $this->input->post('txt');
		$selesai = $this->input->get_post('selesai'); 
		$test = $this->input->get_post('test'); 
		$phone = $this->input->get_post('phone');
		
		$campaign = null;
		$datasms = null;
		if ($id)
		{
			$params = array('campaign_id'=> $id);
			
			if ($this->orca_auth->user->client_id)
			{
				$params['campaign.client_id'] = $this->orca_auth->user->client_id;
			}
			
			$this->db->select('campaign.*,clients.client_type');
			$this->db->join('clients', 'campaign.client_id = clients.client_id');
			
			$query = $this->db->get_where('campaign', $params, 1);
			//echo '<br><br><br><br>'.$this->db->last_query();
			if ($query->num_rows())
			{
				$campaign = $query->row();
				
				if (!$campaign->plaintemplate && $campaign->campaign_type != 'sms')
				{
					$campaign->plaintemplate = html2plain($campaign->template);
					$this->db->where('campaign_id', $campaign->campaign_id)->update('campaign', array('plaintemplate' => $campaign->plaintemplate));
				}
				
				$txt = $this->input->post('txt');
				if ($txt)
				{
					$this->db->where('campaign_id', $campaign->campaign_id)->update('campaign', array('plaintemplate'=>$txt));
					flashmsg_set("Template telah disimpan dengan sukses");
					$campaign->plaintemplate = $txt;
				}
				
				if (!$campaign->plaintemplate && $campaign->campaign_type == 'sms')
				{
					flashmsg_set("Isi template SMS diperlukan, mohon ulangi kembali");
					$this->load->view('campaign_plaintemplates', array('campaign' => $campaign));
					return;
				}
				
				if ($test)
				{
					if ($campaign->plaintemplate)
					{
						$this->sendsms( $campaign );
						flashmsg_set("SMS test telah dikirim");
					}
					else
					{
						flashmsg_set("SMS tidak terkirim karena telepon kosong atau SMS kosong");
					}
				}
				
				if ($selesai)
				{
					redirect(site_url('campaign/'));
				}
				
				
				$this->load->view('campaign_plaintemplates', array('campaign' => $campaign));
				return;
			}
		}
		
		show_404();
		return;
	}
	
	function templates()
	{
		$this->orca_auth->login_required();
		$id = $this->input->get_post('id');
		$template_id = $this->input->get_post('template'); 
		$test = $this->input->get_post('test'); 
		$selesai = $this->input->get_post('selesai'); 
		
		$campaign = null;
		if ($id)
		{
			$params = array('campaign_id'=> $id);
			
			if ($this->orca_auth->user->client_id)
			{
				$params['client_id'] = $this->orca_auth->user->client_id;
			}
			
			$query = $this->db->get_where('campaign', $params, 1);
			
			if ($query->num_rows())
			{
				$campaign = $query->row();
				
				if ($template_id && $template_id != $campaign->template_id)
				{
					$qry = $this->db->get_where('mailtemplates', array('template_id' => $template_id), 1);
					if ($qry->num_rows() > 0)
					{
						$r = $qry->row();
						$update = array('template_id' => $r->template_id, 'template' => $r->template);
						$this->db->where('campaign_id', $campaign->campaign_id)->update('campaign', $update);
						redirect(site_url('campaign/templates?id='.$id));
					}
				}
				
				if (!$campaign->template_id || !$campaign->template)
				{
					$client_ids = array(0);
					if ( $this->orca_auth->user->client_id )
						$client_ids[] = $this->orca_auth->user->client_id;

					$this->db->where('is_delete', 0);
					$this->db->where_in('client_id', $client_ids);
					$query = $this->db->get('mailtemplates');
					if ($query->num_rows() > 0)
					{
						$tpl = $query->row();
						$campaign->template_id = $tpl->template_id;
						$campaign->template = $tpl->template;
						$update = array('template_id' => $tpl->template_id, 'template' => $tpl->template);
						$this->db->where('campaign_id', $campaign->campaign_id)->update('campaign', $update);
					}
				}
				
				$txt = $this->input->post('txt');
				if ($txt)
				{
					$this->db->where('campaign_id', $campaign->campaign_id)->update('campaign', array('template'=>$txt));
					flashmsg_set("Template telah disimpan dengan sukses");
					$campaign->template = $txt;
				}
				
				if ($test)
				{
					$r = $this->sendmail( $campaign, $this->orca_auth->user->email );
					if ($r['success'])
					{
						flashmsg_set("Kirim e-mail test sukses. Silahkan cek e-mail anda.");
					}
					else
					{
						flashmsg_set("Kirim e-mail gagal, mohon ulangi kembali.");
					}
				}
				
				if ($selesai)
				{
					redirect(site_url('campaign/plaintext?id='.$campaign->campaign_id));
				}
				
				$this->load->view('campaign_templates', array('campaign' => $campaign));
				return;
			}
		}
		
		show_404();
		return;
	}
	
	function sendsms($campaign)
	{
		$this->orca_auth->login_required();
		
		$query = $this->db->get_where('clients', array('client_id'=> $this->orca_auth->user->client_id), 1);
		$client = $query->row();
		
		$customer = new stdClass();
		$customer->first_name = $this->orca_auth->user->username;
		$customer->last_name = '';
		$customer->email = $this->orca_auth->user->email;
		$customer->phone = $client->phone;
		$customer->mobile = $client->phone;
		$customer->website = 'http://indocrm.com/';
		
		$phone = $client->phone;
		
		$parser = new template_parser();
		$context = $parser->make_context($customer, $campaign, $client);
		$body_plain = $parser->parse($campaign->plaintemplate, $context);
		
		if (!$phone || !preg_match('~[0-9]+~', $phone) || !$body_plain)
		{
			return false;
		}

		$postfix = "\n\nPengirim: {$client->phone}";
		$maxsmslen = 160*4;
		if (strlen($body_plain) + strlen($postfix) > $maxsmslen)
		{
			$body_plain = substr($body_plain, 0, $maxsmslen-strlen($postfix));
		}

		$body_plain .= $postfix;
		
		$now = date('Y-m-d H:i:s');
		$this->db->query("INSERT INTO smsqueue ( tanggal, number, message ) VALUES ( '$now', ?, ? )", array( $phone, $body_plain ));
		return true;
	}
	
	function sendmail($campaign, $email)
	{
		$this->orca_auth->login_required();
		
		require_once APPPATH . 'Mail/class.smtp.php';
		require_once APPPATH . 'Mail/class.phpmailer.php';
		
		ob_start();
		
		$smtp = new PHPMailer();
		$smtp->From = $email;
		$smtp->FromName = $this->orca_auth->user->username;
		$smtp->XMailer = 'IndoCRM/1.0 (http://www.indocrm.com)';

		$query = $this->db->get_where('mailconfig', array('client_id'=> $this->orca_auth->user->client_id), 1);
		if ($query->num_rows())
		{
			$client = $query->row();
			if ($client->host && $client->port)
			{
				$smtp->SMTPSecure = ($client->ssl ? 'ssl' : ($client->tls ? 'tls' : ''));
				$smtp->Host = $client->host;
				$smtp->Port = $client->port;
				$smtp->Username = $client->username;
				$smtp->Password = $client->password;
				$smtp->SMTPAuth = true;
				$smtp->Mailer = 'smtp';
				$smtp->SMTPDebug = 2;
				
				$smtp->From = $client->username;
				$smtp->FromName = $client->mail_name;
			}
		}
		
		$query = $this->db->get_where('clients', array('client_id'=> $this->orca_auth->user->client_id), 1);
		$cl = $query->row();
		
		$customer = new stdClass();
		$customer->first_name = $smtp->FromName;
		$customer->last_name = '';
		$customer->email = $smtp->From;
		$customer->phone = $cl->phone;
		$customer->website = $cl->website;
		
		$parser = new template_parser();
		$context = $parser->make_context($customer, $campaign, $cl);
		
		$smtp->IsHTML(true);
		$smtp->Body = $parser->parse($campaign->template, $context);
		$smtp->AltBody = $parser->parse($campaign->plaintemplate ? $campaign->plaintemplate : html2plain($campaign->template), $context);
		$smtp->Subject = "[Test Campaign] " . $campaign->campaign_title;
		
		$smtp->AddAddress($email);
		
		if (!$smtp->Send())
		{
			$result['success'] = false;
		}
		else
		{
			$result['success'] = true;
		}
		
		$result['data']= ob_get_contents();
		ob_end_clean();
		
		return $result;
	}
	
	function publish()
	{
		$this->orca_auth->login_required();
		$id = $this->input->get_post('id');
		if ($id)
		{
			$query = $this->db->get_where('campaign', array('campaign_id' => $id), 1);
			if ($query->num_rows())
			{
				$row = $query->row();
				if (( $row->campaign_type == 'sms' && !trim($row->plaintemplate) ) || ( $row->campaign_type != 'sms' && !trim($row->template) ))
				{
					header('Content-type: application/json; charset=UTF-8');
					echo json_encode(array('success'=>false));
					return;
				}
				
				
				$this->db->where('campaign_id', $id)->update('campaign', array('is_sent' => 1));
				header('Content-type: application/json; charset=UTF-8');
				echo json_encode(array('success'=>true));
				return;
			}
		}
		
		show_404();
	}
	
	function _publish_sms($id){
		$query=$this->db->get_where('campaign', array('campaign_id' => $id),1);
		if ($query->num_rows()>0){
			$this->db->where('campaign_id',$id);
			$this->db->update('campaign', array('is_sent' => 1));
			return;
		}
		show_404();
	}
	
	function save()
	{
		$this->orca_auth->login_required();
		
		$json = $this->input->post('customers');
		$customers = array();
		if ($json)
		{
			$customers = json_decode($json);
			foreach($customers as $k => $cust)
			{
				//security
				if ($this->orca_auth->user->client_id && $cust->client_id != $this->orca_auth->user->client_id)
					unset($customers[$k]);
			}
		}
		
		$json = $this->input->post('categories');
		$categories = array();
		if ($json)
		{
			$categories = json_decode($json);
			foreach($categories as $k => $cat)
			{
				//security
				if ($this->orca_auth->user->client_id && $cat->client_id != $this->orca_auth->user->client_id)
					unset($categories[$k]);
			}
		}

		$data = array();
		foreach( $this->table_fields as $field )
		{
			if ($field == 'sent_date'){
				$data['sent_date'] = $this->input->post('sent_date').' '.$this->input->post('sent_time');
			}else{
				if ($field == 'plaintemplate')  //dicegat memakai javascript kalau kosong
					continue;
				else
					$data[$field] = $this->input->post($field);
			}
		}
		
		$crontab = array();
		foreach($this->cron_fields as $field){
			$crontab[$field] = $this->input->post($field);
		}
		
		if ($this->orca_auth->user->client_id)
			$data['client_id'] = $this->orca_auth->user->client_id;
		else 
			$data['client_id'] = intval($data['client_id']);
		
		//$data['campaign_count'] = intval($data['campaign_count']);
		
		unset($data['create_date']);
		
		if ( !isset($data['campaign_id']) || !$data['campaign_id'] )
		{
			$data['campaign_id'] = null;
			
			$this->db->insert( 'campaign', $data );
			$data['campaign_id'] = $this->db->insert_id();
		}
		else
		{
			$this->db->where('campaign_id', $data['campaign_id']);
			$this->db->update( 'campaign', $data );
		}
		
		/*
		 * begin menyimpan crontab
		 * */
		
		if ($data['is_crontab'] == 1){
			
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
		}
		
		/*
		End 
		*/
		
		$query = $this->db->get_where( 'campaign', array('campaign_id' => $data['campaign_id']) , 1 );
		$data = $query->row();
		
		$campaign_source = $data->campaign_source;
		$next = null;

		if ( $this->input->get_post('editing') )
		{
			echo json_encode(array('success' => true, 'data' => $data, 'next' => $next));
			return;
		}
		
		if ($campaign_source == 'upload')
		{
			require_once APPPATH . 'controllers/customers.php';
			$customers = new Customers();
			$result = $customers->_doupload();
			if ($result['success'])
			{
				$next = '/customers/uploadresult?campaign='.$data->campaign_id.'&id='.rawurlencode( $result['id'] );
			}
		}
		else if ($campaign_source == 'category')
		{
			$this->db->query("DELETE FROM campaign_details WHERE campaign_id = {$data->campaign_id}");
			$cats = array();
			foreach($categories as $c)
			{
				$cats[] = $c->category;
			}
			
			$arrCatId = array();
			$this->db->where_in('category', $cats);
			$this->db->where('client_id', $this->orca_auth->user->client_id);
			$this->db->select('category_id');
			$query = $this->db->get('customer_categories');
			if ($query->num_rows()>0){
				foreach($query->result() as $cat){
					$arrCatId[] = $cat->category_id;
				}
			}
			
			if ($data->campaign_type == 'sms')
			{
				$sql = "INSERT INTO campaign_details ( customer_id, campaign_id ) SELECT a.customer_id, {$data->campaign_id} AS campaign_id FROM customers a JOIN customer_details b ON a.customer_id = b.customer_id WHERE a.client_id = {$this->orca_auth->user->client_id} AND a.is_delete = 0 AND category_id IN ('".implode("','",$arrCatId)."') AND mobile IS NOT NULL AND mobile <> '' GROUP BY mobile";
				$this->db->query($sql);
				//echo "<script language='javascript'>alert('".$this->db->last_query()."   ".$sql."')</script>";
				//die;
			}
			else
			{
				
				$this->db->query("INSERT INTO campaign_details ( customer_id, campaign_id ) SELECT a.customer_id, {$data->campaign_id} AS campaign_id FROM customers a JOIN customer_details b ON a.customer_id = b.customer_id WHERE a.client_id = {$this->orca_auth->user->client_id} AND a.is_delete = 0 AND category_id IN ('".implode("','",$arrCatId)."') AND email IS NOT NULL AND email <> '' GROUP BY email");
			}
		}
		else if ($campaign_source == 'selected')
		{
			$this->db->query("DELETE FROM campaign_details WHERE campaign_id = {$data->campaign_id}");
			foreach($customers as $customer)
			{
				$this->db->query("INSERT INTO campaign_details ( campaign_id, customer_id ) VALUES (?, ? ) ON DUPLICATE KEY UPDATE customer_id = ?",
						array( $data->campaign_id, $customer->customer_id, $customer->customer_id ));
			}
		}
		else
		{
			$allcustomer = $this->input->post('allcustomer');
			if ($allcustomer)
			{
				$this->db->query("DELETE FROM campaign_details WHERE campaign_id = {$data->campaign_id}");
				
				if ($data->campaign_type == 'sms')
				{
					$this->db->query("INSERT INTO campaign_details (customer_id, campaign_id) SELECT customer_id, '{$data->campaign_id}' AS campaign_id FROM customers WHERE client_id = {$this->orca_auth->user->client_id} AND is_delete = 0 AND mobile IS NOT NULL AND mobile <> '' GROUP BY mobile");
				}
				else
				{
					$this->db->query("INSERT INTO campaign_details (customer_id, campaign_id) SELECT customer_id, '{$data->campaign_id}' AS campaign_id FROM customers WHERE client_id = {$this->orca_auth->user->client_id} AND is_delete = 0 AND email IS NOT NULL AND email <> '' GROUP BY email");
				}
			}
		}
			
		echo json_encode(array('success' => true, 'data' => $data, 'next' => $next));
	}
	
	function save_sms(){
		$this->orca_auth->login_required();
		$success = true;
		
		$json = $this->input->post('customers');
		$customers = array();
		if ($json)
		{
			$customers = json_decode($json);
			foreach($customers as $k => $cust)
			{
				//security
				if ($this->orca_auth->user->client_id && $cust->client_id != $this->orca_auth->user->client_id)
					unset($customers[$k]);
			}
		}
		
		$json = $this->input->post('categories');
		$categories = array();
		if ($json)
		{
			$categories = json_decode($json);
			foreach($categories as $k => $cat)
			{
				//security
				if ($this->orca_auth->user->client_id && $cat->client_id != $this->orca_auth->user->client_id)
					unset($categories[$k]);
			}
		}

		$data = array();
		foreach( $this->table_fields as $field )
		{
			if ($field == 'signature'){
				$date['signature'] = ($this->input->post('signature') == '') ? '' : $this->input->post('signature');
			}
			
			if ($field == 'sent_date'){
				$data['sent_date'] = $this->input->post('sent_date').' '.$this->input->post('sent_time');
			}else{
				if ($field == 'is_sent'){
					$data[$field] = '1';
				}else{
					$data[$field] = $this->input->post($field);
				}
			}
		}
		
		if ($this->orca_auth->user->client_id)
			$data['client_id'] = $this->orca_auth->user->client_id;
		else 
			$data['client_id'] = intval($data['client_id']);
		
		//$data['campaign_count'] = intval($data['campaign_count']);
		
		unset($data['create_date']);
		
		if ( !isset($data['campaign_id']) || !$data['campaign_id'] )
		{
			$data['campaign_id'] = null;
			$this->db->insert( 'campaign', $data );
			$data['campaign_id'] = $this->db->insert_id();
		}
		else
		{
			$this->db->where('campaign_id', $data['campaign_id']);
			$this->db->update( 'campaign', $data );
		}
		
		/*
		 * begin menyimpan crontab
		 * */
		 
		$crontab = array();
		foreach($this->cron_fields as $field){
			$crontab[$field] = $this->input->post($field);
		}
		
		if ($data['is_crontab'] == 1){
			$sql = "INSERT INTO cron_schedule ";
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
							WHERE campaign_id = '".$data['campaign_id']."' AND schedule_id = '".$crontabp['schedule_id']."'";
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
					if ($data['is_repeat'] == 1){
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
					if ($data['is_repeat'] == 1){
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
						$sql .= "(campaign_id, tgl, bln, tgl_start, time_start, tgl_end, time_end, once_a_year, is_repeat) 
						VALUES ('".$data['campaign_id']."','$d', '$m', '".$crontab['tgl_start']."', '".$crontab['time_start']."','".$crontab['tgl_end']."','".$crontab['time_end']."','1','".$crontab['is_repeat']."')";
					}else{
						$sql .= "(campaign_id, tgl, bln, tgl_start, time_start, tgl_end, time_end, once_a_year, counter_limit, is_repeat) 
						VALUES ('".$data['campaign_id']."','$d', '$m', '".$crontab['tgl_start']."', '".$crontab['time_start']."','".$crontab['tgl_end']."','".$crontab['time_end']."','1','".$crontab['counter_limit']."','".$crontab['is_repeat']."')";
					}
					break;
			}
			
			$this->db->query($sql);
		}
		
		/*
		End 
		*/
		
		
		
		$query = $this->db->get_where( 'campaign', array('campaign_id' => $data['campaign_id']) , 1 );
		$data = $query->row();
		
		$campaign_source = $data->campaign_source;
		$next = null;

		if ( $this->input->get_post('editing') )
		{
			echo json_encode(array('success' => true, 'data' => $data, 'next' => $next));
			return;
		}
		
		if ($campaign_source == 'upload')
		{
			require_once APPPATH . 'controllers/customers.php';
			$customers = new Customers();
			$result = $customers->_doupload();
			if ($result['success'])
			{
				$next = '/customers/uploadresult?campaign='.$data->campaign_id.'&id='.rawurlencode( $result['id'] );
			}
			$this->_publish_sms($data->campaign_id);
		}
		else if ($campaign_source == 'category')
		{
			$this->db->query("DELETE FROM campaign_details WHERE campaign_id = {$data->campaign_id}");
			$cats = array();
			foreach($categories as $c)
			{
				$cats[] = $c->category;
			}
			
			$arrCatId = array();
			$this->db->where_in('category', $cats);
			$this->db->where('client_id', $this->orca_auth->user->client_id);
			$this->db->select('category_id');
			$query = $this->db->get('customer_categories');
			if ($query->num_rows()>0){
				foreach($query->result() as $cat){
					$arrCatId[] = $cat->category_id;
				}
			}
			
			if ($data->campaign_type == 'sms')
			{
				$sql = "INSERT INTO campaign_details ( customer_id, campaign_id ) SELECT a.customer_id, {$data->campaign_id} AS campaign_id FROM customers a JOIN customer_details b ON a.customer_id = b.customer_id WHERE a.client_id = {$this->orca_auth->user->client_id} AND a.is_delete = 0 AND category_id IN ('".implode("','",$arrCatId)."') AND mobile IS NOT NULL AND mobile <> '' GROUP BY mobile";
				$this->db->query($sql);
				//echo "<script language='javascript'>alert('".$this->db->last_query()."   ".$sql."')</script>";
				//die;
			}
			else
			{
				
				$this->db->query("INSERT INTO campaign_details ( customer_id, campaign_id ) SELECT a.customer_id, {$data->campaign_id} AS campaign_id FROM customers a JOIN customer_details b ON a.customer_id = b.customer_id WHERE a.client_id = {$this->orca_auth->user->client_id} AND a.is_delete = 0 AND category_id IN ('".implode("','",$arrCatId)."') AND email IS NOT NULL AND email <> '' GROUP BY email");
			}
			$this->_publish_sms($data->campaign_id);
		}
		else if ($campaign_source == 'selected')
		{
			$this->db->query("DELETE FROM campaign_details WHERE campaign_id = {$data->campaign_id}");
			foreach($customers as $customer)
			{
				$this->db->query("INSERT INTO campaign_details ( campaign_id, customer_id ) VALUES (?, ? ) ON DUPLICATE KEY UPDATE customer_id = ?",
						array( $data->campaign_id, $customer->customer_id, $customer->customer_id ));
			}
			$this->_publish_sms($data->campaign_id);
		}
		else
		{
			$this->db->where('client_id', $this->orca_auth->user->client_id);
			$this->db->select('counter_limit');
			$q = $this->db->get('clients');
			$res = $q->result();
			$limit = 500;
			if ($res[0]->counter_limit >= $limit){
				
			}else{
				
				$this->db->where('client_id', $this->orca_auth->user->client_id);
				$q = $this->db->get('customers');
				$total = $this->db->count_all_results();
				
				if ($total >= $limit){
					$success = false;
				}else{
					$this->db->query("DELETE FROM campaign_details WHERE campaign_id = {$data->campaign_id}");
					
					$this->db->query("INSERT INTO campaign_details (customer_id, campaign_id) SELECT customer_id, '{$data->campaign_id}' AS campaign_id FROM customers WHERE client_id = {$this->orca_auth->user->client_id} AND is_delete = 0 AND mobile IS NOT NULL AND mobile <> '' GROUP BY mobile");
				}
			}
		}
			
		echo json_encode(array('success' => $success, 'data' => $data, 'next' => $next));
	}
	
	function get_client_type($client){
		$group = 1;
		$sql = "SELECT client_type FROM clients WHERE client_id = '$client'";
		$query  = $this->db->query($sql);
		if ($query->num_rows()>0){
			$result = $query->result_array();
			$group = $result[0]['client_type'];
		}
		return $group;
	}
	
		public function download_log($id){
		$this->orca_auth->login_required();
		
		header('Content-type: text/plain; charset=UTF-8');
		header('Content-disposition: attachment; filename="Download_campaign_'.$id.'.csv"');
		
		$this->db->where('campaign_id', $id);
		$query = $this->db->get( 'smslog' );
		
		if ($query->num_rows() > 0)
		{
			echo "Tanggal;Ke;Isi;Terkirim\n";
			foreach($query->result() as $row){
				echo $row->sent_date.";'".$row->to_number."';".$row->body_plain.";".(($row->is_sent == 0) ? "Belum" : "Terkirim")."\n";
			}
		}
	}
	
	public function retry_campaign(){
		$this->orca_auth->login_required();
		
		$id = $this->input->get_post('id');
		
		$arrGagal = $this->_campaign_failed($id);
		
		//eksekusi
		
		$date = date('Y-m-d');
		
		if (count($arrGagal)>0){
			$sql = "INSERT INTO `campaign` (client_id, start_date, end_date, create_date, campaign_title, campaign_description, signature, campaign_source, campaign_type, template_id, template, plaintemplate) 
			SELECT client_id, start_date, end_date, '$date', concat('Retry ($date) : ',campaign_title ,' Tgl :',create_date), campaign_description, signature, campaign_source, campaign_type, template_id, template, plaintemplate FROM `campaign` WHERE campaign_id = '$id'"; 
			$this->db->query($sql);
			$newid = mysql_insert_id();
			
			$arr = array();
			
			$sql = "INSERT INTO `campaign_details` (campaign_id, customer_id) VALUES ";
			foreach($arrGagal as $val){
				$arr[]= "('$newid','$val')";
			}
			$str = implode(", ", $arr);
			$sql = $sql.$str;
			
			$this->db->query($sql);
		}
		
		redirect(site_url('campaign/'));
	}
	
	function _campaign_failed($id){
		
		$arrGagal = array();
		$this->db->select('customer_id');
		$this->db->where('campaign_id', $id);
		$this->db->where('is_sent !=', '1');
		$query = $this->db->get('smslog');
		//echo $this->db->last_query();
		if ($query->num_rows()>0){
			$result = $query->result_array();
			foreach($result as $row){
				$arrGagal[] = $row['customer_id'];
			}
		}
		
		return $arrGagal;
	}
	
	public function is_campaign_failed(){
		$id = $this->input->get_post('id');
		
		header('Content-type: application/json; charset=UTF-8');
		$result = array('success' => true, 'rows' => array(), 'totalCount' => 0);
		
		$arrGagal = array();
		
		$sql = "SELECT * FROM smslog WHERE campaign_id = '$id' AND is_sent != 1";
		$query = $this->db->query($sql);
		$result['totalCount'] = $query->num_rows();
		if ($query->num_rows()>0){
			$result['rows'] = $query->result();
		}
		
		echo json_encode($result);
	}
	
	public function all_sms()
	{
		$this->orca_auth->login_required();
		header('Content-type: application/json; charset=UTF-8');
		$result = array('success' => true, 'rows' => array(), 'totalCount' => 0);

		$q = $this->input->post('q');
		$page = $this->input->post('page');
		$start = $this->input->post('start');
		$limit = $this->input->post('limit');
		$sort = $this->input->post('sort');
		$filter = $this->input->post('filter');
		if (!$limit) $limit = 20;
		
		$this->db->where('is_delete', 0);
		$this->db->where('campaign_type', 'sms');
		if ( $this->orca_auth->user->client_id )
		{
			$this->db->where('client_id', $this->orca_auth->user->client_id);
		}
		
		if ($q)
		{
			$this->_build_query($q);
		}
		
		parse_filter( $filter, $this->table_fields, 'campaign' );
		
		$totalCount = $this->db->count_all_results('campaign');
		$result['totalCount'] = $totalCount;
		if ($totalCount > 0)
		{
			$this->db->where('is_delete', 0);
			$this->db->where('campaign_type', 'sms');
			if ( $this->orca_auth->user->client_id )
			{
				$this->db->where('client_id', $this->orca_auth->user->client_id);
			}
			
			if ($q)
			{
				$this->_build_query($q);
			}
			
			parse_sort($sort, $this->table_fields);
			parse_filter( $filter, $this->table_fields, 'campaign' );
			
			$query = $this->db->get( 'campaign', $limit, $start );
			
			if ($query->num_rows() > 0)
			{
				$result['rows'] = $query->result();
			}
		}
		
		echo json_encode($result);
	}
	
	public function broadcast_sms(){
		
		$this->load->view('broadcastsms');
	}
	
	function create_broadcast()
	{
		$this->orca_auth->login_required();
		$id = $this->input->get_post('id');
		$campaign = null;
		if ($id)
		{
			$query = $this->db->get_where('campaign', array('campaign_id'=> $id, 'client_id' => $this->orca_auth->user->client_id), 1);
			if ($query->num_rows())
			{
				$campaign = $query->row();
			}
			
			if (!$campaign)
			{
				show_404();
				return;
			}
		}
		
		$this->load->view('create_broadcast', array('campaign' => $campaign));
	}
}

