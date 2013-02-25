<?php

/*
 * Copyright 2012 by StarDev, Malang, ID
 * All rights reserved
 * 
 * Written By: Aryo Sanjaya, Santo Doni Romadhoni
 * aryo@stardev.web.id
 * http://id-stardev.com/
 */

/**
 * Description of API
 *
 * @author ferdhie
 */

/**
 * 

/api/customers
/api/customers/add
/api/customers/edit
/api/customers/delete

/api/campaign
/api/campaign/add

/api/sendsms

/api/profile
/api/profile/update

/api/company
/api/company/update

/api/customer

 * 
 */
 
class API extends CI_Controller 
{
	
    function index()
    {
		//show_error("Forbidden!", 403);
		$this->register();
    }
    
    public function register(){
		$this->orca_auth->login_required();
		$client_id = $this->orca_auth->user->client_id;
		$new = $this->input->get_post('new');
		
		$data['privatekey'] = '';
		$data['sisa'] = 0;
		
		$this->db->where('client_id', $this->orca_auth->user->client_id);
		$q = $this->db->get('clients');
		if ($q->num_rows()>0){
			$res = $q->result();
			$data['privatekey'] = $res[0]->privatekey;
		}
		
		if (empty($data['privatekey'])){
			if ($new){
				$privatekey = md5(uniqid());
				$this->db->where('client_id', $client_id);
				$this->db->update('clients', array('privatekey' => $privatekey));
				
				$this->db->where('client_id', $client_id);
				$q = $this->db->get('api_limit');
				if ($q->num_rows()>0){
					$this->db->where('client_id', $client_id);
					$this->db->update('api_limit', 
						array(
						'counter_limit' => 12000,
						'counter' => 0,
						)
					);
				}else{
					$this->db->insert('api_limit', array(
						'client_id' => $client_id,
						'counter' => 0,
					));
				}
				redirect('api/register', 'refresh');
			}
		}else{
			$resapi=array();
			$this->db->where('client_id', $client_id);
			$q = $this->db->get('api_limit');
			if ($q->num_rows()>0){
				$resapi=$q->result();
			}
			$counter = $resapi[0]->counter;
			
			$data['sisa'] = $counter;
		}
		
		$this->load->view('api_view', $data);
	}
	
	function generateHash($timer, $auth)
	{
		date_default_timezone_set('UTC');
		$sel = time()-$timer;
		if ($sel>(3600)) return "";
		$hash = md5($timer . 'INDOCRM' . $auth);
		return substr($hash, 5, 8);
    }
    
    public function login(){
		$this->load->view('api_login');
	}
	
	function _is_limited($client_id){
		$counter = 0;
		$counter_limit = 12000;
		
		$this->db->where('client_id', $client_id);
		
		$q = $this->db->get('api_limit');
		
		if ($q->num_rows()>0){
			$res = $q->result();
			
			$counter = $res[0]->counter;
			$counter_limit = $res[0]->counter_limit;
		}else{
			$this->db->insert('api_limit',array('client_id' => $client_id, 'counter_limit' => 12000));
		}
		
		if ($counter_limit <= $counter){
			return TRUE;
		}else{
			$this->db->query("UPDATE api_limit SET counter = counter + 1 WHERE client_id = '$client_id'");
			return FALSE;
		}
		
	}

	public function contacts($param=''){
		
		$token = $this->input->get_post('t');
		
		header('Content-type: application/json; charset=UTF-8');
		
		list($timer, $client_id, $hash) = explode('-', $token);
		if (!$hash)
		{
			$this->terminate ("Invalid token!");
			return;
		}
		
		$this->company = $this->User_model->get_company($client_id);
		if (!$this->company)
		{
			$this->terminate ("Invalid client ID!");
			return;
		}
		
		$checkhash = $this->generateHash($timer, $this->company->privatekey);

		if (!$checkhash)
		{
			$this->terminate ("Invalid hash!");
			return;
		}

		if (strcasecmp($hash, $checkhash))
		{
			$this->terminate ("Invalid submitted token!");
			return;
		}
		
		if ($this->_is_limited($client_id)){
			$this->terminate("API Limit reached");
			return;
		}
		
		$result = array('success' => false, 'rows' => array(), 'totalCount' => 0, 'error' => 'Error');
		
		if ($param == ''){
			$this->db->limit(10);
			$this->db->order_by('customer_id');
			$this->db->where('client_id', $client_id);
			$this->db->where('is_delete', 0);
			$this->db->select("first_name,last_name,mobile,email,category");
			$q = $this->db->get('customers');
			if ($q->num_rows()>0){
				$result['success'] = true;
				$result['totalCount'] = $q->num_rows();
				$result['rows'] = $q->result();
				$result['error'] = 'OK';
			}
			$this->_save_log($client_id, 'contacts/'.$param, '', json_encode($result));
		} else if ($param == 'add'){
			$name = $this->input->post('name');
			$mobile = $this->input->post('mobile');
			$email = $this->input->post('email');
			$category = $this->input->post('category');
			
			$arrVars = array('name' => $name, 'mobile' => $mobile, 'email' => $email, 'category' => $category);
			
			if (empty($mobile)){
				$this->terminate('Mobile Empty');
				return;
			}
			
			$category = (empty($category) ? 'uncategorized' : $category);
			
			$this->db->where('client_id', $client_id);
			$this->db->where('is_delete', 0);
			$this->db->where('mobile', $mobile);
			$this->db->where('category', $category);
			$q = $this->db->get('customers');
			if ($q->num_rows()==0){
			
				$this->db->insert('customers',
					array(
						'client_id' => $client_id,
						'first_name' => $name,
						'mobile' => $mobile,
						'email' => $email,
						'category' => $category
					));
				
				$this->db->query("INSERT INTO customer_categories (category, client_id) VALUES (?,?) ON DUPLICATE KEY UPDATE category = ?", array($category, $client_id, $category));
				
				$sql = "INSERT INTO customer_details (category_id, customer_id) SELECT category_id, '".$this->db->insert_id()."' FROM customer_categories WHERE client_id = '$client_id' AND category IN ('$category')";
				
				$this->db->query($sql);
				
			}
			$this->db->limit(10);
			$this->db->order_by('customer_id');
			$this->db->where('client_id', $client_id);
			$this->db->where('is_delete', 0);
			$this->db->select('first_name,mobile,email,category');
			$q = $this->db->get('customers');
			if ($q->num_rows()>0){
				$result['success'] = true;
				$result['totalCount'] = $q->num_rows();
				$result['rows'] = $q->result();
				$result['error'] = 'Add OK';
			}
			$this->_save_log($client_id, 'contacts/'.$param, json_encode($arrVars) , json_encode($result));
		}else if ($param == 'edit') {
			$id = $this->input->post('id');
			$name = $this->input->post('name');
			$mobile = $this->input->post('mobile');
			$email = $this->input->post('email');
			$category = $this->input->post('category');
			
			$arrVars = array('id' => $id, 'name' => $name, 'mobile' => $mobile, 'email' => $email, 'category' => $category);
			
			if (empty($mobile)){
				$this->terminate('Mobile Empty');
				return;
			}
			
			$arrFields = array('name' => $name,'mobile' => $mobile, 'email' => $email, 'category' => $category );
			
			$array = array();
			foreach($arrFields as $key => $val){
				if (!empty($val)){
					$array[$key] = $val;
				}
			}
			
			$this->db->where('client_id', $client_id);
			$this->db->where('customer_id', $id);
			$this->db->update('customers', $array);
			
			$this->db->where('client_id', $client_id);
			$this->db->where('category', $category);
			$q = $this->db->get('customer_categories');
			if ($q->num_rows()==0){
				$this->db->query("INSERT INTO customer_categories (category, client_id) VALUES (?,?) ON DUPLICATE KEY UPDATE category = ?", array($category, $client_id, $category));
			}
			
			/*
			 * Delete customer details first
			 */
			
			$this->db->where('customer_id', $id);
			$this->db->delete('customer_details');
			
			/*
			 * Insert new Data customer details
			 * */
			$sql = "INSERT INTO customer_details (category_id, customer_id) SELECT category_id, '".$this->db->insert_id()."' FROM customer_categories WHERE client_id = '$client_id' AND category IN ('$category')";
			
			$this->db->query($sql);
			
			$this->db->where('customer_id', $id);
			$this->db->select('first_name,mobile,email,category');
			$q = $this->db->get('customers');
			if ($q->num_rows()>0){
				$result['success'] = true;
				$result['totalcount'] = $q->num_rows();
				$result['rows'] = $q->result();
				$result['error'] = 'Edit OK';
			}
			
			$this->_save_log($client_id, 'contacts/'.$param, json_encode($arrVars) , json_encode($result));
		}else  if ($param == 'delete') {
			$id = $this->input->get_post('id');
			
			$arrVars = array('id' => $id);
			
			$this->db->where('customer_id', $id);
			$this->db->where('is_delete', 0);
			$q = $this->db->get('customers');
			if ($q->num_rows()>0){
				
				$this->db->where('client_id', $client_id);
				$this->db->where('customer_id', $id);
				$this->db->update('customers', array('is_delete' => 1));
				
				$this->db->where('customer_id', $id);
				$this->db->delete('customer_details');
				
				$result['success'] = true;
				$result['totalCount'] = 0;
				$result['rows'] = null;
				$result['error'] = 'Delete OK';
			}else{
				$result['success'] = false;
				$result['totalCount'] = 0;
				$result['rows'] = null;
				$result['error'] = 'Customer Not Found';
			}
			$this->_save_log($client_id, 'contacts/'.$param, json_encode($arrVars) , json_encode($result));
		}else if ($param == 'search'){
			$mobile = $this->input->post('mobile');
			$email = $this->input->post('email');
			
			$arrVars = array('mobile' => $mobile, 'email' => $email);
			
			if ($mobile != ''){
				$this->db->like('mobile',$mobile);
			}
			
			if ($email != ''){
				$this->db->like('email',$email);
			}
			$this->db->where('client_id', $client_id);
			$q=$this->db->get('customers');
			
			echo $this->db->last_query();
			
			//die;
			if ($q->num_rows()>0){
				$result['success'] = true;
				$result['totalCount'] = $q->num_rows();
				$result['rows'] = $q->result();
				$result['error'] = 'Search OK';
			}else{
				$result['success'] = false;
				$result['totalCount'] = 0;
				$result['rows'] = null;
				$result['error'] = 'Customer Not Found';
			}
			$this->_save_log($client_id, 'contacts/'.$param, json_encode($arrVars) , json_encode($result));
		}else if ($param='categories'){
			$arr=array();
			$this->db->where('client_id', $client_id);
			$this->db->select('category_id,category');
			$q = $this->db->get('customer_categories');
			$result['totalCount'] = $q->num_rows();
			if ($q->num_rows()>0){
				$x =0;
				foreach($q->result() as $row){
					$arr[$x]['category_id'] = $row->category_id;
					$arr[$x]['category'] = $row->category;
					$this->db->where('category_id',$row->category_id);
					$this->db->from('customer_details');
					$arr[$x]['count'] = $this->db->count_all_results();
					//echo $this->db->last_query();
					$x++;
				}
				//$arr[] = $q->result();
			}
			
			$result['success'] = false;
			$result['rows'] = $arr;
			$result['error'] = 'Ok';
			$this->_save_log($client_id, 'contacts/'.$param, '' , json_encode($result));
		}
		
		echo json_encode($result);
	}
	
    function sms()
    {        
		$msisdn = preg_replace('~[^0-9,]+~', '', $this->input->get_post('msisdn'));
        $msisdn = array_filter(explode(',',$msisdn));
		$sms = substr($this->input->get_post('sms'),0,160);    

		if (!$msisdn||!$sms)
		{
			$this->terminate("Invalid params!");
			return;
		}

        $token = $this->input->get_post('t');
        
        $arrVars = array('msisdn' => $msisdn, 'sms' => $sms);
        
		header("Content-type: application/json; charset=UTF-8");
		
		$result = array('success'=>false,'error'=>'');
		
		$sms_count = 0;
		$sms_quota = 0;
		$is_active = 0;
		$sms_free = 0;
		
		list($timer, $client_id, $hash) = explode('-', $token);
		if (!$hash)
		{
			$this->terminate ("Invalid token!");
			return;
		}
		
		$this->company = $this->User_model->get_company($client_id);
		if (!$this->company)
		{
			$this->terminate ("Invalid client ID!");
			return;
		}
		
		$checkhash = $this->generateHash($timer, $this->company->privatekey);

		if (!$checkhash)
		{
			$this->terminate ("Invalid hash!");
			return;
		}

		if (strcasecmp($hash, $checkhash))
		{
			$this->terminate ("Invalid submitted token!");
			return;
		}
		
		if ($this->_is_limited($client_id)){
			$this->terminate("API Limit reached");
			return;
		}
		
		if ($this->company)
		{
			$sms_count = $this->company->sms_count; 
			$sms_quota = $this->company->sms_quota; 
			$is_active = $this->company->is_active;
			$sms_free = $this->company->sms_free; 
		}
		
		$result['sms_count'] = $sms_count;
		$result['sms_quota'] = $sms_quota;
		$result['is_active'] = $is_active;
		$result['sms_free'] = $sms_free;

		if ( $sms_free > 0 || ( $is_active && ( $sms_quota == 0 || $sms_count < $sms_quota ) ) )
		{
			if ($this->company)
			{
				$postfix = "\n\nPengirim: {$this->company->phone}";
				
				if ($this->company->client_type == 0)
				{
					//khusus untuk cims.
					if (in_array($this->orca_auth->user->client_id, array(48, 36)))
                    {
						$postfix .= "\nmalang.sbp.net.id\nvia IndoCRM.com";
					}
                    elseif (in_array($this->orca_auth->user->client_id, array(106)))
                    {
						$postfix .= "\n".$this->company->signature;
					}
					else
                    {
						$postfix .= "\nSMS GRATIS IndoCRM.com";
					}
				}else{
					$postfix .= "\n".$this->company->signature;
				}
                
				if (in_array($this->orca_auth->user->client_id, array(48, 36)))
                {
					$postfix = "";
				}
				
				if (strlen($sms) + strlen($sms) > 160)
				{
					$sms = substr($sms, 0, 160-strlen($postfix));
				}
				
				$sms .= $postfix;
			}
			
			$now = date('Y-m-d H:i:s');			
			$tanggal = date('Y-m-d H:i:s');			
			foreach( $msisdn as $m )
			{
				if (!$m) continue;
				$this->db->query("INSERT INTO smsqueue ( tanggal, number, message ) VALUES ( ?, ?, ? )", array( $tanggal, $m, $sms ));
				$queue_id = $this->db->insert_id();
				$this->db->query("INSERT INTO smslog ( to_number, body_plain, campaign_id, sms_number, total_count, customer_id, client_id, queue_id ) VALUES ( ?, ?, 0, 0, 0, 0, ?, ? )", 
						array( $m ,$sms, $client_id, $queue_id ));
						
				if ($sms_free)
					$this->db->query("UPDATE clients SET sms_free = sms_free - 1 WHERE client_id = ?", array($client_id));
				
				$this->db->query("UPDATE limit_sms SET counter = counter + 1 WHERE modem = 'modem6'");
			}
			
			$result['success'] = true;
			$result['counter'] = $diff;
			$result['error'] = 'Send OK';
		}
		else
		{
			$this->terminate("Quota limit exceeded!");
			return;
		}
		
		$this->_save_log($client_id, 'sms', json_encode($arrVars) , json_encode($result));
		
		echo json_encode($result);        
    }
	
	public function campaign($param=''){
		
		$token = $this->input->get_post('t');
		
		header('Content-type: application/json; charset=UTF-8');
		
		list($timer, $client_id, $hash) = explode('-', $token);
		if (!$hash)
		{
			$this->terminate ("Invalid token!");
			return;
		}
		
		$this->company = $this->User_model->get_company($client_id);
		if (!$this->company)
		{
			$this->terminate ("Invalid client ID!");
			return;
		}
		
		$checkhash = $this->generateHash($timer, $this->company->privatekey);

		if (!$checkhash)
		{
			$this->terminate ("Invalid hash!");
			return;
		}

		if (strcasecmp($hash, $checkhash))
		{
			$this->terminate ("Invalid submitted token!");
			return;
		}
		
		if ($this->_is_limited($client_id)){
			$this->terminate("API Limit reached");
			return;
		}
		
		$result = array('success' => false, 'rows' => array(), 'totalCount' => 0, 'error' => 'Error');
		$arrVars = array();
		if ($param == ''){
			
			$this->db->order_by('campaign_id','desc');
			$this->db->where('client_id', $client_id);
			$this->db->where('campaign_type','sms'); //sementara just for SMS
			$this->db->select('campaign_id,client_id,create_date,campaign_title,campaign_description,signature,sent_date,template,plaintemplate,is_sent,log_message');
			$q = $this->db->limit(10);
			$q = $this->db->get('campaign');
			if ($q->num_rows()>0){
				$result['success'] = true;
				$result['totalCount'] = $q->num_rows();
				$result['rows'] = $q->result();
				$result['error'] = 'OK';
			}
		}elseif($param='add'){
			/*
			 * sementara just for SMS
			 * */
			 
			$type = $this->input->post('type');// all, category 
			$group = $this->input->post('group'); // category_id (integer) dipisahkan dengan koma
			$sent_date = $this->input->post('sent_date');
			$campaign_title = $this->input->post('title');
			$campaign_description = $this->input->post('description');
			$signature = $this->input->post('signature');
			$plaintemplate = $this->input->post('content');
			
			$arrVars = array(
				'type' => $type,
				'group' => $group,
				'sent_date' => $sent_date,
				'title' => $campaign_title,
				'description' => $campaign_description,
				'signature' => $signature,
				'content' => $plaintemplate
			);
			
			if (empty($type)){
				$this->terminate('Tipe diisi all atau sesuai group contact');
				return;
			}
			
			if (!empty($type)){
				if (empty($group)){
					$this->terminate('Group contact tidak boleh kosong');
					return;
				}
			}
			
			if (empty($sent_date)){
				$sent_date = date('Y-m-d 00:00:00');
			}
			
			if (empty($campaign_title)){
				$this->terminate('Judul broadcast tidak boleh kosong');
				return;
			}
			
			if (empty($plaintemplate)){
				$this->terminate('Isi broadcast tidak boleh kosong');
				return;
			}
			
			$limit = 500;
			
			switch($type){
				case "all":
					$campaign_source = 'all';
					
					$this->db->where('client_id', $client_id);
					$q = $this->db->get('clients');
					$resclient = $q->result();
					$counter_limit = $resclient[0]->counter_limit;
					
					if ($counter_limit <= $limit){
						$this->db->insert('campaign', 
							array(
							'campaign_title' => $campaign_title,
							'campaign_description' => $campaign_description,
							'sent_date' => $sent_date,
							'signature' => $signature,
							'plaintemplate' => $plaintemplate,
							'is_sent' => 0
							)
						);
						
						$campaign_id = $this->db->insert_id();
						/***
						 * get all customers where client id is $client_id
						 */ 
						
						$this->db->where('client_id', $client_id);
						$this->db->from('customers');
						$totalContact = $this->db->count_all_results();
						
						if ($totalContact < $limit){
							$insert = "INSERT INTO campaign_details ( customer_id, campaign_id )
							SELECT a.customer_id, {$campaign_id} AS campaign_id FROM customers a JOIN customer_details b ON a.customer_id = b.customer_id WHERE a.client_id = {$client_id} AND a.is_delete = 0 AND mobile IS NOT NULL AND mobile <> '' GROUP BY mobile";
							$this->db->query($insert);
							
							$this->db->where('campaign_id', $campaign_id);
							$q = $this->db->get('campaign');
							
							$res = $q->result();
							$rows = $res[0];
							
							$result = array('success' => true, 'rows' => $rows, 'totalCount' => 1, 'error' => 'Add campaign OK');
						}else{
							$this->terminate('Quota harian terlampaui: Limit '.$limit.', tidak cukup untuk '.$totalContact.' SMS');
							return;
						}
					}
				break;
				case "group":
					$this->db->where('client_id', $client_id);
					$q = $this->db->get('clients');
					$resclient = $q->result();
					
					if ($resclient[0]->counter_limit <= $limit){
						$this->db->insert('campaign', 
							array(
							'campaign_title' => $campaign_title,
							'campaign_description' => $campaign_description,
							'sent_date' => $sent_date,
							'signature' => $signature,
							'plaintemplate' => $plaintemplate,
							'is_sent' => 0
							)
						);
						
						$campaign_id = $this->db->insert_id();
						/***
						 * get all customers where client id is $client_id
						 */ 
						
						$arrCat = explode(",", $group);
						$arr = array();
						foreach($arrCat as $cat){
							$arr[] = $cat;
						}
						
						$rescount = $this->db->query("SELECT COUNT(*) as cnt FROM customer_details WHERE category_id IN ('".imlode(",",$arr)."'");
						
						$totalContact = $rescount[0]->cnt;
						
						if ($totalContact < $limit){
						
							$insert = "INSERT INTO campaign_details ( customer_id, campaign_id ) 
							SELECT a.customer_id, {$campaign_id} AS campaign_id FROM customers a JOIN customer_details b ON a.customer_id = b.customer_id WHERE a.client_id = {$client_id} AND a.is_delete = 0 AND mobile IS NOT NULL AND mobile <> '' AND b.category_id IN ('".implode(",", $arr)."') GROUP BY mobile";
							$this->db->query($insert);
							
							$this->db->where('campaign_id', $campaign_id);
							$q = $this->db->get('campaign');
							
							$res = $q->result();
							$rows = $res[0];
							
							$result = array('success' => true, 'rows' => $rows, 'totalCount' => 1, 'error' => 'Add campaign OK');
						}else{
							$this->terminate('Quota harian terlampaui: Limit '.$limit.', tidak cukup untuk '.$totalContact.' SMS');
							return;
						}
					}
				break;
			}
		}
		
		$this->_save_log($client_id, 'campaign/'.$param, json_encode($arrVars) , json_encode($result));
		echo json_encode($result);
	}
	
	public function company($param=''){
		
		$token = $this->input->get_post('t');
		
		header('Content-type: application/json; charset=UTF-8');
		
		list($timer, $client_id, $hash) = explode('-', $token);
		if (!$hash)
		{
			$this->terminate ("Invalid token!");
			return;
		}
		
		$this->company = $this->User_model->get_company($client_id);
		if (!$this->company)
		{
			$this->terminate ("Invalid client ID!");
			return;
		}
		
		$checkhash = $this->generateHash($timer, $this->company->privatekey);

		if (!$checkhash)
		{
			$this->terminate ("Invalid hash!");
			return;
		}

		if (strcasecmp($hash, $checkhash))
		{
			$this->terminate ("Invalid submitted token!");
			return;
		}
		
		if ($this->_is_limited($client_id)){
			$this->terminate("API Limit reached");
			return;
		}
		
		$result = array('success' => false, 'rows' => array(), 'totalCount' => 0, 'error' => 'Error');
		$arrVars = array();
		$rows = array();
		
		$namecl = $statecl = $addresscl = $citycl = $countrycl = $mobilecl = $emailcl = $default_signaturecl = '';
		
		$this->db->where('client_id', $client_id);
		$q = $this->db->get('clients');
		if ($q->num_rows()>0){
			$resclient = $q->result();
			$rows = $resclient[0];
			$namecl = $resclient[0]->name;
			$addresscl = $resclient[0]->address;
			$statecl = $resclient[0]->city;
			$countrycl = $resclient[0]->country;
			$emailcl = $resclient[0]->email;
			$mobilecl = $resclient[0]->mobile;
			$default_signaturecl = $resclient[0]->default_signature;
			
			$error = 'OK';
		}
		
		if ($param == 'edit'){
			$name = $this->input->get_post('name');
			$address = $this->input->get_post('address');
			$city = $this->input->get_post('city');
			$state = $this->input->get_post('state');
			$country = $this->input->get_post('country');
			$mobile = $this->input->get_post('mobile');
			$email = $this->input->get_post('email');
			$default_signature = $this->input->get_post('default_signature');
			
			$arrVars = array(
				'name' => $name,
				'addres' => $address, 
				'city' => $city,
				'state' => $state,
				'country' => $country,
				'mobile' => $mobile,
				'email' => $email,
				'default_signature' => $default_signature
			);
			
			$name = (empty($name) ? $namecl : $name);
			$address = (empty($address) ? $addresscl : $address);
			$city = (empty($city) ? $citycl : $city);
			$state = (empty($state) ? $statecl : $state);
			$country = (empty($country) ? $countrycl : $country);
			$email = (empty($email) ? $emailcl : $email);
			$mobile = (empty($mobile) ? $mobilecl : $mobile);
			$default_signature = (empty($default_signature) ? $default_signaturecl : $default_signature);
			
			if (!preg_match('/^[a-zA-Z0-9]+[a-zA-Z0-9\._]+\@+[a-zA-Z0-9\._]+[a-zA-Z]$/i', $email)){
				$this->terminate('Email tidak sesuai');
				return;
			}
			
			if (!preg_match('/[0-9]/i',$mobile)){
				$this->terminate('Mobile harus angka');
				return;
			}
			
			$array = array(
			
				'name' => $name,
				'address' => $address,
				'city' => $city,
				'state' => $state,
				'country' => $country,
				'mobile' => $mobile,
				'email' => $email,
				'signature' => $default_signature,
			);
			
			$this->db->where('client_id', $client_id);
			$this->db->update('clients', $array);
			
			$this->db->where('client_id', $client_id);
			$q = $this->db->get('clients');
			$resclient = $q->result();
			$rows = $resclient[0];
			$error = 'Update OK';
		}
		
		$result = array('success' => true, 'rows' => $rows, 'totalCount' => 0, 'error' => $error );
		
		$this->_save_log($client_id, 'company/'.$param, json_encode($arrVars) , json_encode($result));
		echo json_encode($result);
	}
	
	public function profile($param=''){
		$token = $this->input->get_post('t');
		
		header('Content-type: application/json; charset=UTF-8');
		
		list($timer, $client_id, $hash) = explode('-', $token);
		if (!$hash)
		{
			$this->terminate ("Invalid token!");
			return;
		}
		
		$this->company = $this->User_model->get_company($client_id);
		if (!$this->company)
		{
			$this->terminate ("Invalid client ID!");
			return;
		}
		
		$checkhash = $this->generateHash($timer, $this->company->privatekey);

		if (!$checkhash)
		{
			$this->terminate ("Invalid hash!");
			return;
		}

		if (strcasecmp($hash, $checkhash))
		{
			$this->terminate ("Invalid submitted token!");
			return;
		}
		
		if ($this->_is_limited($client_id)){
			$this->terminate("API Limit reached");
			return;
		}
		
		$result = array('success' => false, 'rows' => array(), 'totalCount' => 0, 'error' => 'Error');
		$arrVars = array();
		$user_id = $this->input->post('user_id');
		
		$rows = array();
		
		$uemail = '';
		$utimezone = 0;
		$umobile = '';
		$ubdate = '';
		
		if ($param == ''){
			$this->db->where('id', $user_id);
			$this->db->where('client_id', $client_id);
			$this->db->select('username,email,timezone,bdate,mobile');
			$q = $this->db->get('users');
			if ($q->num_rows()>0){
				$res = $q->result();
				$rows = $res[0];
				
				$error = 'OK';
				$result = array('success' => true, 'rows' => $res, 'totalCount' => 1, 'error' => $error);
			}
			
		}elseif($param='edit'){
			
			$email = $this->input->post('email');
			$timezone = $this->input->post('timezone');
			$bdate = $this->input->post('bdate');
			$mobile = $this->input->post('mobile');
			
			$arrVars = array('email' => $email, 'timezone' => $timezone, 'bdate' => $bdate, 'mobile' => $mobile);
			
			$this->db->where('id', $user_id);
			$this->db->where('client_id', $client_id);
			$this->db->select('username,email,timezone,bdate,mobile');
			$q = $this->db->get('users');
			if ($q->num_rows()>0){
				$res = $q->result();
				$rows = $res[0];
				$uemail= $res[0]->email;
				$umobile= $res[0]->mobile;
				$utimezone= $res[0]->timezone;
				$ubdate= $res[0]->bdate;
			}
			
			$email = (empty($email) ? $uemail : $email);
			$timezone = (empty($timezone) ? $utimezone : $timezone);
			$bdate = (empty($bdate) ? $ubdate : $bdate);
			$mobile = (empty($mobile) ? $umobile : $mobile);
			
			if (!preg_match('/^[a-zA-Z0-9]+[a-zA-Z0-9\._]+\@+[a-zA-Z0-9\._]+[a-zA-Z]$/i', $email)){
				$this->terminate('Email tidak sesuai');
				return;
			}
			
			if (!preg_match('/[0-9]/i',$mobile)){
				$this->terminate('Mobile harus angka');
				return;
			}
			
			if (!ctype_digit($timezone) || (intval($timezone) <= -12 && intval($timezone) >= 12)){
				$this->terminate('Timezone harus angka range (-12 s/d 12)');
				return;
			}
			
			list($y,$m,$d) = explode('-', $bdate);
			if (!checkdate($m,$d,$y)){
				$this->terminate('Format tanggal tidak benar gunakan (yyyy-mm-dd)');
				return;
			}
			
			
			$this->db->where('id', $user_id);
			$this->db->update('users', 
					array(
					'email' => $email,
					'timezone' => $timezone,
					'bdate' => $bdate,
					'mobile' => $mobile
			));
			
			$this->db->where('id', $user_id);
			$this->db->select('username,email,timezone,bdate,mobile');
			$q = $this->db->get('users');
			if ($q->num_rows()>0){
				$res = $q->result();
				$rows = $res[0];
				$error = 'Update OK';
			}
			
			$result = array('success' => true, 'rows' => $rows, 'totalCount' => 0, 'error' => $error );
		}
		
		$this->_save_log($client_id, 'profile/'.$param, json_encode($arrVars) , json_encode($result));
		
		echo json_encode($result);
	}
	
	function terminate($message)
	{
			$result['success'] = false;
			$result['error'] = $message;
			echo json_encode($result);        
			die();
	}    
	
	public function help(){
		$this->orca_auth->login_required();
		
		$this->load->view('api_help');
	}
	
	function _save_log($client_id, $request, $params, $results){
		$this->db->insert('api_log', 
			array ( 'client_id' => $client_id,
				'tgl_akses' => date('Y-m-d'),
				'jam_akses' => date('H:i:s'), 
				'request' => $request, 
				'variables' => $params,
				'results' => $results
			));
	}
	
	function validation() {
		$token = $this->input->get_post('t');
		
		// Check Token
		list($timer, $client_id, $hash) = explode('-', $token);
		if (!$hash) {
			$this->terminate ("Invalid token!");
			exit;
		}
		
		// Check Client
		$Company = $this->User_model->get_company($client_id);
		if (!$Company) {
			$this->terminate ("Invalid client ID!");
			exit;
		}
		
		// Check Client Hash
		$checkhash = $this->generateHash($timer, $Company->privatekey);
		if (!$checkhash) {
			$this->terminate ("Invalid hash!");
			exit;
		}
		
		// Check Client Hast & Post Hast
		if (strcasecmp($hash, $checkhash)) {
			$this->terminate ("Invalid submitted token!");
			exit;
		}
		
		// Check Limit API
		if ($this->_is_limited($client_id)) {
			$this->terminate("API Limit reached");
			exit;
		}
		
		return array('client_id' => $client_id);
	}
	
	function customer() {
		$Validation = $this->validation();
		$action = $this->input->get_post('action');
		$Param = GetPost($this->input);
		
		if ($action == 'Update') {
			if (empty($Param['customer_category'])) {
				$this->terminate("Category required.");
				exit;
			}
			
			// Get Customer Category
			$ParamCategory = array( 'ForceInsert' => 1, 'category' => $Param['customer_category'], 'client_id' => $Validation['client_id'] );
			$CustomerCategory = $this->Customer_Category_model->GetByID($ParamCategory);
			
			// Insert / Update Customer
			$Param['client_id'] = $Validation['client_id'];
			$Result = $this->Customer_model->Update($Param);
			
			// Insert / Update Customer Detail
			$ParamCustomerDetail = array( 'customer_id' => $Result['customer_id'], 'category_id' => $CustomerCategory['category_id'] );
			$this->Customer_model->UpdateDetail($ParamCustomerDetail);
		} else if ($action == 'Delete') {
			$Result = $this->Customer_model->Delete($Param);
		} else {
			$this->terminate("No action");
			exit;
		}
		
		// Validation Result
		$Result['success'] = ($Result['QueryStatus'] == 1) ? true : false;
		if (isset($Result['QueryStatus']))
			unset($Result['QueryStatus']);
		
		$this->_save_log($Validation['client_id'], 'gereja', json_encode($Param) , json_encode($Result));
		echo json_encode($Result);      
	}
}