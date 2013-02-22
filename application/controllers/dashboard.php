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
 * Description of Dashboard
 *
 * @author ferdhie
 */
error_reporting(E_ALL);
 
class Dashboard extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->orca_auth->login_required();
        
		if ($this->orca_auth->user->group_id == 6)
        {
			$this->wgpenyiar();
		}
        else
        {
			$this->load->view('dashboard');
		}
	}
	
	public function checksms()
	{
		$this->orca_auth->login_required();
		
		header("Content-type: application/json; charset=UTF-8");
		
		$result = array('success'=>false,'allow'=>false,'error'=>'');
		
		$sms_count = 0;
		$sms_quota = 0;
		$is_active = 0;
		$sms_free = 0;

		if ( !isset($this->company) )
		{
			$this->company = $this->User_model->get_company($this->orca_auth->user->client_id);
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

		if ( $this->orca_auth->user->client_id == 0 || $sms_free > 0 || ( $is_active && ( $sms_quota == 0 || $sms_count < $sms_quota ) ) )
		{
			$diff = 180;
			$query = $this->db->query("SELECT * FROM adhoc_sms WHERE user_id = ? ORDER BY sent_time DESC", array($this->orca_auth->user->id));
			
			$html = '<form method="post" action="'.site_url('dashboard/sendsms').'">'.
					'<div><label for="id_msisdn">HP</label><input type="text" name="msisdn" id="id_msisdn" size="20" value="" placeholder="Nomer handphone" /></div>'.
					'<div id="smscontainer"><label for="adhocsms">Pesan SMS</label><span id="adhoclen"></span><textarea id="adhocsms" name="adhocsms" cols="20" rows="2"></textarea></div>'.
					'<div><input type="submit" id="dosendsms" class="btn btn.small" value="Kirim" /></div></form>';
			
			if ($query->num_rows())
			{
				$l = $query->row();
				$last_sent = strtotime( $l->sent_time );
				$diff = time() - $last_sent;
				$result['last_sent'] = $l->sent_time;
			}
			
			$allow=true;
			
			if ($diff < 180)
			{
				$html = '';
				$allow=false;
			}
			
			$result['success'] = true;
			$result['allow'] = $allow;
			$result['html'] = $html;
			$result['counter'] = $diff;
		}
		else
		{
			if ( $is_active )
			{
				die(json_encode(array( 'success'=>true,'allow'=>false,'error'=>'Quota SMS anda habis. Silahkan beli quota SMS tambahan' )));
				$result['error'] = 'Quota SMS anda habis. Silahkan beli quota SMS tambahan';
			}
			else
			{
				$result['error'] = 'Akun anda tidak aktif. Silahkan aktifkan akun anda dengan membayar tagihan anda.';
			}
			
			$result['success'] = true;
			$result['allow'] = false;
			$result['html'] = '';
		}
		
		echo json_encode($result);
	}
	
	public function sendsms()
	{
		$this->orca_auth->login_required();
		$result = array('success' => false, 'error'=>'','counter'=>180);
		
		header("Content-type: application/json; charset=UTF-8");
		
		/*$query = $this->db->query("SELECT * FROM limit_sms WHERE modem = 'modem6'");
		$limitsms = $query->result();
		
		if ($limitsms[0]->counter >= $limitsms[0]->counter_limit){
			
			$result= array('success' => false, 'error' => '<p>Maaf, SMS dibatasi</p>', 'counter'=>180);
			echo json_encode($result);
			exit;
		}
		*/
		$sms_count = 0;
		$sms_quota = 0;
		$is_active = 0;
		$sms_free = 0;
		
		$msisdn = preg_replace('~[^0-9,]+~', '', $this->input->get_post('msisdn'));
        $msisdn = array_filter(explode(',',$msisdn));
		$sms = substr($this->input->get_post('sms'),0,160);
		
		if (!$msisdn || !$sms)
		{
			$result['error'] = 'SMS atau HP salah';
		}
		else
		{
			if ( !isset($this->company) )
			{
				$this->company = $this->User_model->get_company($this->orca_auth->user->client_id);
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

			if ( $this->orca_auth->user->client_id == 0 || $sms_free > 0 || ( $is_active && ( $sms_quota == 0 || $sms_count < $sms_quota ) ) )
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
				
				$diff = 180;
				$now = date('Y-m-d H:i:s');
				$this->db->query("INSERT INTO adhoc_sms (user_id, sent_time) VALUES (?,?)
					ON DUPLICATE KEY UPDATE sent_time = ?", array($this->orca_auth->user->id,$now,$now));

				$html = '<form method="post" action="'.site_url('dashboard/sendsms').'">'.
						'<div><label for="id_msisdn">HP</label> <input type="text" name="msisdn" id="id_msisdn" size="20" value="" placeholder="Nomer handphone" /></div>'.
						'<div id="smscontainer"><label for="adhocsms">Pesan SMS</label><span id="adhoclen"></span><textarea id="adhocsms" name="adhocsms" cols="20" rows="2"></textarea></div>'.
						'<div><input type="submit" id="dosendsms" class="btn btn.small" value="Kirim" /></div></form>';

				$result['counter'] = $diff;
				
				$tanggal = date('Y-m-d H:i:s');
				
				foreach( $msisdn as $m )
				{
					if (!$m) continue;
					$this->db->query("INSERT INTO smsqueue ( tanggal, number, message ) VALUES ( ?, ?, ? )", array( $tanggal, $m, $sms ));
					$queue_id = $this->db->insert_id();

					$this->db->query("INSERT INTO smslog ( to_number, body_plain, campaign_id, sms_number, total_count, customer_id, client_id, queue_id ) VALUES ( ?, ?, 0, 0, 0, 0, ?, ? )", 
							array( $m ,$sms, $this->orca_auth->user->client_id, $queue_id ));
							
					if ($sms_free)
						$this->db->query("UPDATE clients SET sms_free = sms_free - 1 WHERE client_id = ?", array($this->orca_auth->user->client_id));
					else
						$this->db->query("UPDATE clients SET sms_count = sms_count + 1 WHERE client_id = ?", array($this->orca_auth->user->client_id));
					
					$this->db->query("UPDATE limit_sms SET counter = counter + 1 WHERE modem = 'modem6'");
				}
				
				$result['success'] = true;
				$result['counter'] = $diff;
			}
			else
			{
				if ( $is_active )
				{
					$result['error'] = '<p>maaf, quota anda udah abis</p>';
				}
				else
				{
					$result['error'] = '<p>Layanan ini dimatikan sementara waktu</p>';
				}
			}
		}
		
		echo json_encode($result);
	}
	
	public function wgpenyiar()
    {
		$this->orca_auth->login_required();
		$client_id = $this->orca_auth->user->client_id;
		
		//$data['key'] = $key;
		$data['client_id'] = $client_id;
		$arrFilter = $this->getfilter($client_id);
		
		$data['blacklistnumber'] = $arrFilter[0];
		$data['blacklistwords'] = $arrFilter[1];
		$limit = (empty($arrFilter[2][0]) ? 10 : $arrFilter[2][0]);
		//$limit =2;
		
		$arr = array();
		foreach($arrFilter[1] as $word)
        {
            $w = $this->db->escape_like_str("$word");
			$arr[]= "sms LIKE '%$w%'";
		}
	
		if (count($arr)>0)
        {
			$wordsblacklist = "AND NOT ( " .implode(" OR ", $arr) ." )";
		}
        else
        {
			$wordsblacklist = "";
		}
        
        $do = $this->input->get_post('do');
        $selaction = $this->input->get_post('selaction');
		$dofilter = $this->input->get_post('dofilter');
		$dofiltertgl = $this->input->get_post('dofiltertgl');
		$tanggal = $this->input->get_post('tanggal');
		$jam = $this->input->get_post('jam');
		
		$jamto = $this->input->get_post('jamto');
        $isread = $this->input->post('isread');
        if ( !is_array($isread) && is_string($isread) )
        {
            $isread = (array)json_decode($isread);
        }
        
        $segments = $this->uri->segment_array();
        foreach($segments as $k => $segment)
        {
            if ($segment == 't' && !$dofiltertgl)
            {
                $dofiltertgl = 't';
                $tanggal = isset($segments[$k+1]) ? $segments[$k+1] : false;
                if (!$tanggal) $dofiltertgl = '';
            }
            else if ($segment == 'j' && !$dofilter)
            {
                $dofilter = 'j';
                $jam = isset($segments[$k+1]) ? $segments[$k+1] : false;
                $jamto = isset($segments[$k+2]) ? $segments[$k+2] : false;
                if (!$jam && !$jamto) $dofilter = '';
            }
        }
        
        if ($do)
        {
            switch($selaction)
            {
                case 'Mark as read':
                    $isread = array_filter(array_map('intval', array_values($isread)));
                    $sql = "UPDATE log_mo SET is_read = '1' WHERE id IN (".implode(",", $isread).")";
                    $this->db->query($sql);
                    break;
                case 'Mark as unread':
                    $isread = array_filter(array_map('intval', array_values($isread)));
                    $sql = "UPDATE log_mo SET is_read = '0' WHERE id IN (".implode(",", $isread).")";
                    $this->db->query($sql);
                    break;
                case 'Delete':
                    $isread = array_filter(array_map('intval', array_values($isread)));
                    $sql = "UPDATE log_mo SET is_read = '2' WHERE id IN (".implode(",", $isread).")";
                    $this->db->query($sql);
                    break;
            }
        }
		
		//echo __LINE__.":".$tanggal;
		
        $curdate = date("Y-m-d");
		
		$strTimeFilter = "";
		$strTgl = ""; //"AND date = '$curdate'";
		
		if (!$dofiltertgl)
        {
			$tanggal = date('Y-m-d', strtotime('-7 hours'));
		}
		
		$tglbefore = $this->db->escape($tanggal);
		
        list($tanggal,$jam) = explode(" ",date('Y-m-d H:i:00',strtotime('-7 hours',strtotime("$tanggal $jam"))));
        $j = $this->db->escape($jam);
      
        list($tanggal2, $jamto) = explode(" ", date('Y-m-d H:i:59',strtotime('-7 hours',strtotime("$tanggal $jamto"))));
        $j1 = $this->db->escape($jamto);
        
        //$tanggal = date('Y-m-d',strtotime('-7 hours',strtotime("$tanggal $jam")));
        $dt = $this->db->escape($tanggal);
        
       // echo $tanggal;
        
        if ($dofilter)
        {
			if ($j > $j1)
            {
				$strTimeFilter = "AND (date = $dt AND time >= $j) OR (date = $tglbefore AND time <= $j1)";
				$strTgl = "";
			}
            else
            {
				$strTimeFilter = "AND (time >= $j AND time <= $j1)";
				$strTgl = "AND date = $dt";
			}
        }
        
        if ($dofiltertgl)
        {
			$strTgl = "AND date = $dt";
			if (!$dofilter){
				$strTimeFilter = "";
			}
		
        }

		$sql = "SELECT COUNT(*) as cnt FROM `log_mo` WHERE client_id = '$client_id' AND  is_read != '2' $strTgl $strTimeFilter AND msisdn NOT IN ('".implode("','",$arrFilter[0])."') ".$wordsblacklist;
		$query = $this->db->query($sql);
		//echo __LINE__.":".$this->db->last_query();
		$res = $query->result_array();
		$total = $res[0]['cnt'];
		$data['total'] = $total;
        
        $page = $this->input->get('page');
		
		require_once APPPATH . 'libraries/paging.php';
        
        $path = array();
        if ($dofiltertgl)
            $path[] = 't/'.rawurlencode($tanggal);
        if ($dofilter)
            $path[] = 'j/'.rawurlencode($jam).'/'.rawurlencode($jamto);
        $path = implode('/', $path);
        
        $paging = new Paging(array('page' => $page, 'offset' => $limit, 'page_param' => 'page', 'page_url' => site_url( 'dashboard/wgpenyiar/'.$path )));
        $pages = $paging->create_paging( $data['total'] );

		$sql = "SELECT * FROM `log_mo` WHERE client_id = '$client_id' AND is_read != '2' $strTgl $strTimeFilter AND msisdn NOT IN ('".implode("','",$arrFilter[0])."') ".$wordsblacklist." ORDER BY date DESC, time DESC LIMIT {$paging->start}, {$paging->offset}";
		
		$result = array();
		$query = $this->db->query($sql);
		//echo __LINE__.":".$this->db->last_query();
		//die;
		if ($query->num_rows()>0)
        {
			$result = $query->result_array();
		}
        
        /*
        if (!$result && !$client_id)
        {
            $sql = "SELECT COUNT(*) as cnt FROM `log_mo` WHERE is_read != '2' $strTimeFilter AND msisdn NOT IN ('".implode("','",$arrFilter[0])."') ".$wordsblacklist;
            $query = $this->db->query($sql);
            $res = $query->result_array();
            $total = $res[0]['cnt'];
            $data['total'] = $total;
            $pages = $paging->create_paging( $data['total'] );
            $sql = "SELECT * FROM `log_mo` WHERE msisdn NOT IN ('".implode("','",$arrFilter[0])."') ".$wordsblacklist." AND is_read != '2' $strTimeFilter ORDER BY date DESC, time DESC LIMIT {$paging->start}, {$paging->offset}";
            $result = array();
            $query = $this->db->query($sql);
            if ($query->num_rows()>0)
            {
                $result = $query->result_array();
            }
        }
        */
        
		/*$dofilter = $this->input->get_post('dofilter');
		$dofiltertgl = $this->input->get_post('dofiltertgl');
		$tanggal = $this->input->get_post('tanggal');
		$jam = $this->input->get_post('jam');
		$jamto = $this->input->get_post('jamto');*/
        
        $timezone = $this->orca_auth->user->timezone;
        
		$data['do'] = $do;
		$data['selaction'] = $selaction;
        $data['dofilter'] = $dofilter;
		$data['jam'] = $jam;
		$data['jamto'] = $jamto;
		$data['tanggal'] = $tanggal;
		$data['dofiltertgl'] = $dofiltertgl;
		$data['paging'] = $paging;
        foreach($result as $idx=>$item)
        {
            $result[$idx]['timestamp'] = strtotime($item['date'] . ' ' . $item['time']);
            $dttime = strtotime((($timezone<0) ? $timezone : '+'.$timezone).' hours', $result[$idx]['timestamp']);
            $result[$idx]['date'] = date('Y-m-d', $dttime);
            $result[$idx]['time'] = date('H:i:s', $dttime);
        }
		$data['result'] = $result;
        $data['isread'] = $isread;
        
        if ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' )
        {
            $this->load->view('wgview', $data);
        }
        else
        {
            $this->load->view('dashboard_penyiar', $data);
        }
	}
    
	function wg_load_cnt($client_id='-', $key='-', $since='')
    {
        $user = $this->orca_auth->get_current_user();
		if ($client_id == '-' && $user)
        {
            $client_id = $user->client_id;
		}
		
		$arrFilter = $this->getfilter($client_id);
		$arr = array();
		foreach($arrFilter[1] as $word)
        {
            $w = $this->db->escape_like_str("$word");
			$arr[]= "sms LIKE '%$w%'";
		}
	
		if (count($arr)>0)
        {
			$wordsblacklist = "AND NOT ( " .implode(" OR ", $arr) ." )";
		}
        else
        {
			$wordsblacklist = "";
		}
		
        $curdate = date('Y-m-d');
        $jam2 = date('H:i:s');
        $jam1 = date('H:i:s', strtotime('-1 hour'));
        
		$strclient = $client_id == 0 ? "1=1" : "client_id = '$client_id'";
        $where = $since ? " AND id > '$since'" : " AND date = '$curdate' AND time BETWEEN '$jam1' AND '$jam2'";
        
		$sql = "SELECT COUNT(*) as cnt FROM `log_mo` WHERE $strclient  AND is_read != '2' $where AND msisdn NOT IN ('".implode("','",$arrFilter[0])."') ".$wordsblacklist;
		$query = $this->db->query($sql);
		$result= $query->result_array();
		$cnt = $result[0]['cnt'];
        
		$sql = "SELECT MAX(id) as cnt FROM `log_mo` WHERE $strclient  AND is_read != '2' AND msisdn NOT IN ('".implode("','",$arrFilter[0])."') ".$wordsblacklist . " ORDER BY date DESC, time DESC";
		$query = $this->db->query($sql);
		$result= $query->result_array();
		$last_id = $result[0]['cnt'];
        
        header('Content-type: application/x-json');
        echo json_encode(array( 'success' => true, 'last_id' => $last_id, 'server_time' => $jam2, 'count' => $cnt ));
	}
	
	function getfilter($clientid)
    {
		$arrNumber = array();
		$arrWords = array();
		$perpage = array();
		
		$this->db->where('client_id', $clientid);
		$query = $this->db->get('log_mo_widgets_options');
		//echo $this->db->last_query();
		if ($query->num_rows()>0)
        {
			foreach($query->result_array() as $row)
            {
				$arrmob = explode(",", $row['blacklistnumber']);
				foreach($arrmob as $number)
                {
					$tmp = trim($number);
					if (empty($tmp)) continue;
					$arrNumber[] = $tmp;
				}
				
				$arrkata = explode(",", $row['blacklistwords']);
				foreach($arrkata as $kata)
                {
					$tmp = trim($kata);
					if (empty($tmp)) continue;
					$arrWords[] = $tmp;
				}
			}
			$perpage = array($row['view']);
		}
		
		return array($arrNumber, $arrWords, $perpage);
	}
}

