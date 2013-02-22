<?php

class SmsLogmo extends CI_Controller
{
	var $table_fields = array (
	0 => 'id',
	1 => 'msisdn', 
	2 => 'sms',
	3 => 'src',
	4 => 'client_id',
	5 => 'date',
	6 => 'time'
	);
    
    public function index(){
		$this->orca_auth->login_required();
		$this->load->view('smslogmo');
	}
	
	public function smsmopartner(){
		$this->orca_auth->login_required();
		$this->load->view('smslogmopartner');
	}
	
	public function all(){
		$this->orca_auth->login_required();
		
		header('Content-type: application/json; charset=UTF-8');
		$result = array('success' => true, 'rows' => array(), 'totalCount' => 0);
		
		$q = $this->input->post('q');
		$from = $this->input->post('from');
		$to = $this->input->post('to');
		
		$page = $this->input->post('page');
		$start = $this->input->post('start');
		$limit = $this->input->post('limit');
		$sort = $this->input->post('sort');
		$filter = $this->input->post('filter');
		if (!$limit) $limit = 20;
        
        $where = array();
		
		if ( $this->orca_auth->user->client_id )
		{
            $where[] = "client_id = {$this->orca_auth->user->client_id}";
		}
		
		$timezone = $this->orca_auth->user->timezone;
		
        $timezone = $timezone * 3600;
		
		if ($from)
            $where[] = "date = '".date('Y-m-d', strtotime($from)-$timezone)."'";
		if ($to)
            $where[] = "date = '".date('Y-m-d', strtotime($to)-$timezone)."'";
			
        $where[] = "is_read != '2'";

		if ($q)
		{
            $qx = $this->db->escape_like($q);
            $where[] = "msisdn LIKE '%$qx%'";
		}
        
		$where[] = parse_filter2( $filter, $this->table_fields, 'log_mo' );
        $where = array_filter(array_map('trim', $where));
        
        $sql = "SELECT COUNT(*) AS cnt FROM log_mo WHERE " . implode(" AND ", $where);
        $query = $this->db->query($sql);
        $totalCount = $query->row()->cnt;
		$result['totalCount'] = $totalCount;
		if ($totalCount > 0)
		{
            $check = json_decode($sort);
            foreach($check as $c)
            {
                if ($c->property == 'date')
                {
                    $newProp = new stdClass;
                    $newProp->property = 'time';
                    $newProp->direction = $c->direction;
                    $check[] = $newProp;
                    $sort = json_encode($check);
                    break;
                }
            }
            
			$sort = parse_sort2($sort, $this->table_fields);
            $start = intval($start);
            $limit = intval($limit);
            if (!$sort)
                $sort = " ORDER BY date DESC, time DESC LIMIT $start, $limit";
            
            $limit = intval($limit);
            $start = intval($start);
            
            $sql = "SELECT * FROM log_mo WHERE " . implode(" AND ", $where) . "  $sort LIMIT $start,$limit";
            $query = $this->db->query($sql);
			if ($query->num_rows() > 0)
			{
                $result['rows'] = array();
                foreach($query->result() as $r)
                {
                    $t = strtotime("{$r->date} {$r->time}");
                    $r->date = date('Y-m-d', $t+$timezone);
                    $r->time = date('H:i:s', $t+$timezone);
                    $result['rows'][] = $r;
                }
			}
		}
		
		echo json_encode($result);

	}
	
	public function wgdemo($client_id){
		//$this->orca_auth->login_required();
		
		$data['client_id'] = $client_id;
		
		//echo "test";
		$this->load->view('smslogmo_widgets_demo',$data);
	}
	
	public function wg($client_id, $key,$h="-",$m="-",$ht="-",$mt="-", $tgl="-"){
		//$this->orca_auth->login_required();
		
		$confirm_key = $this->get_key_valid($client_id, $key);
		if (!$confirm_key)
		{
			$data['notvalid'] = true;
		}else{
			$data['notvalid'] = false;
		}
		
		
		$data['key'] = $key;
		$data['client_id'] = $client_id;
		$arrFilter = $this->getfilter($client_id);
		
		$data['blacklistnumber'] = $arrFilter[0];
		$data['blacklistwords'] = $arrFilter[1];
		$limit = (empty($arrFilter[2][0]) ? 10 : $arrFilter[2][0]);
		//$limit =2;
		
		$arr = array();
		
		foreach($arrFilter[1] as $word){
			$arr[]= "sms LIKE '%$word%'";
		}
	
		if (count($arr)>0){
			$wordsblacklist = "AND NOT ( " .implode(" OR ", $arr) ." )";
		}else{
			$wordsblacklist = "";
		}
		
		$filter = $this->input->post('filter');
		$dofilter = $this->input->post('dofilter');
		$dofiltertgl = $this->input->post('dofiltertgl');
		$tanggal = $this->input->post('tanggal');
		$jam = $this->input->post('jam');
		$menit = $this->input->post('menit');
		$jamto = $this->input->post('jamto');
		$menitto = $this->input->post('menitto');
		
		if ($filter){
			if ($dofilter){
				$h = $jam;
				$m = $menit;
				$ht = $jamto;
				$mt = $menitto;
			}
			
			if ($dofiltertgl){
				$tgl = $tanggal;
			}
		}
		
		
		$strTimeFilter = "";
		$strTgl = "AND date=CURDATE()";
		
		if ($filter){
			if ($dofilter){
				$strTimeFilter = "AND time between '".$h.":".$m."' AND '".$ht.":".$mt."'";
			}
			
			if ($dofiltertgl){
				$strTgl = "AND date = '$tgl'";
			}
			
			if ($dofilter && $dofiltertgl){
				$strTimeFilter = "AND time between '".$h.":".$m."' AND '".$ht.":".$mt."'";
				$strTgl = "AND date = '$tgl'";
			}
		}
		
		
		$sql = "SELECT COUNT(*) as cnt FROM `log_mo` WHERE client_id = '$client_id' AND msisdn NOT IN ('".implode("','",$arrFilter[0])."') ".$wordsblacklist. " AND is_read != '2' $strTimeFilter $strTgl";
		
		$query = $this->db->query($sql);
		//echo __LINE__.":".$this->db->last_query();
		$res = $query->result_array();
		$total = $res[0]['cnt'];
		$data['total'] = $total;
		
		$now = $this->uri->segment(10);
		$now = intval($now);
		
		$sql = "SELECT * FROM `log_mo` WHERE client_id = '$client_id' AND msisdn NOT IN ('".implode("','",$arrFilter[0])."') ".$wordsblacklist." AND is_read != '2' $strTimeFilter $strTgl ORDER BY time,date DESC LIMIT $now, $limit ";
		
		$result = array();

		$query = $this->db->query($sql);
		//echo __LINE__.":".$this->db->last_query();
		//die;
		if ($query->num_rows()>0){
			$result = $query->result_array();
		}
		
		$data['result'] = $result;
		
		
		$this->load->library('pagination');
		$config['base_url'] = base_url().'index.php/smslogmo/wg/'.$client_id.'/'.$key.'/'.$h.'/'.$m.'/'.$ht.'/'.$mt.'/'.$tgl;
		$config['total_rows'] = $total;
		$config['per_page'] = $limit;
		$config['uri_segment'] = 10;
		$config['first_link'] = '<<';
		$config['last_link'] = '>>';
		$config['full_tag_open'] = '<p>';
		$config['full_tag_close'] = '</p>';
		
		$this->pagination->initialize($config);
		
		$data['page'] = $this->pagination->create_links();
		/**/
		//$data['page'] = $this->quickpaging($limit,$p, $total, $client_id);
		
		$this->load->view('smslogmo-widgets', $data);
	}
	
	function wg_load_cnt($client_id='-', $key='-', $before, $h, $m, $ht, $mt, $tgl='-'){
		
		if ($client_id=='-'){
			$client_id = $this->orca_auth->user->client_id;
		}
		
		$arrFilter = $this->getfilter($client_id);
		
		$arr = array();
		
		foreach($arrFilter[1] as $word){
			$arr[]= "sms LIKE '%$word%'";
		}
	
		if (count($arr)>0){
			$wordsblacklist = "AND NOT ( " .implode(" OR ", $arr) ." )";
		}else{
			$wordsblacklist = "";
		}
		
		$strTimeFilter = "";
		$strTgl = "";
		
		
		if ($h !='-' && $m != '-' && $ht != '-' && $mt != '-' && $tgl == '-'){
			$strTimeFilter = "AND time BETWEEN '".$h.":".$m."' AND '".$ht.":".$mt."'";
		}
		
		if ($h =='-' && $m == '-' && $ht == '-' && $mt == '-' && $tgl != '-'){
			$strTgl = "AND date = '$tgl'";
		}
		
		if ($h !='-' && $m != '-' && $ht != '-' && $mt != '-' && $tgl != '-'){
			$strTimeFilter = "AND time BETWEEN '".$h.":".$m."' AND '".$ht.":".$mt."'";
			$strTgl = "AND date = '$tgl'";
		}
		
		if ($tgl == '-'){
			$strTgl = "AND date = CURDATE()";
		}
		
		$sql = "SELECT COUNT(*) as cnt FROM `log_mo` WHERE client_id = '$client_id' AND msisdn NOT IN ('".implode("','",$arrFilter[0])."') ".$wordsblacklist. " AND is_read != '2' $strTimeFilter $strTgl";
		
		$query = $this->db->query($sql);
		//echo $this->db->last_query();
		$result= $query->result_array();
		$cnt = $result[0]['cnt'];
		
		$new = $cnt - $before;
		$npath = ($key == '-') ? 'dashboard/wgpenyiar/' : 'smslogmo/wg/';
		echo '<h3><strong>'.$new.'</strong> Pesan Baru '.anchor($npath.$client_id.'/'.$key.'/'.$h.'/'.$m.'/'.$ht.'/'.$mt.'/'.$tgl, "klik untuk refresh").'</h3>';
	}
	
	public function allpartner(){
		$this->orca_auth->login_required();
		
		header('Content-type: application/json; charset=UTF-8');
		$result = array('success' => true, 'rows' => array(), 'totalCount' => 0);
		
		$q = $this->input->post('q');
		$from = $this->input->post('from');
		$to = $this->input->post('to');
		
		$page = $this->input->post('page');
		$start = $this->input->post('start');
		$limit = $this->input->post('limit');
		$sort = $this->input->post('sort');
		$filter = $this->input->post('filter');
		if (!$limit) $limit = 20;
		
		$mobileset = true;
		if ( $this->orca_auth->user->client_id )
		{
			
			$arrMobGW = $this->get_mobile_client_partner($this->orca_auth->user->client_id);
			//print_r($arrMobGW);

			$this->db->where_in('src', $arrMobGW['mobile']);
			if (empty($arrMobGW))
				$mobileset = false;
		}
		
		if ($q)
		{
			$this->db->like('msisdn', $q);
		}
		
		if ($from)
			$this->db->where('date >=', $from);
		if ($to)
			$this->db->where('date <=', $to);
		
		$this->db->where('is_read !=',2);
		$this->db->order_by('date', 'desc');
		
		parse_filter( $filter, $this->table_fields, 'log_mo' );
		$totalCount = $this->db->count_all_results('log_mo');
		//echo $this->db->last_query();
		$result['totalCount'] = $totalCount;
		if ($totalCount > 0 && $mobileset)
		{
			if ( $this->orca_auth->user->client_id )
			{
				$arrMobGW = $this->get_mobile_client_partner($this->orca_auth->user->client_id);
				$this->db->where_in('src', $arrMobGW['mobile']);
			}

			if ($q)
			{
				$this->db->like('msisdn', $q);
			}

			if ($from)
				$this->db->where('date >=', $from);
			if ($to)
				$this->db->where('date <=', $to);
			
			parse_sort($sort, $this->table_fields);
			parse_filter( $filter, $this->table_fields, 'log_mo' );
			
			$query = $this->db->get( 'log_mo', $limit, $start );
			//echo $this->db->last_query();
			if ($query->num_rows() > 0)
			{
				$result['rows'] = $query->result();
			}
		}
		
		echo json_encode($result);
	}
	
	public function getdata($mode){
		if ($mode == 'json'){
			header('Content-type: application/json; charset=UTF-8');
			$result = array('success' => true, 'rows' => array(), 'totalCount' => 0);
		}else{
			header('Content-type: application/xml; charset=UTF-8');
			
		}
		
		$page = (isset($_GET['p']) ? $_GET['p'] : 0);
		
/*		$page = $this->input->post('page');
		$start = $this->input->post('start');
        $limit = $this->input->post('limit');
		if (!$limit) $limit = 20;*/
		
		$limit = 20;
		$curpage = $page * $limit;
		
		/*
		 * query for filter
		 * */
		
		
		
		
		$query = $this->db->get('log_mo',$limit,$curpage);
		
		if ($mode == 'json'){
			if ($query->num_rows() >0){
				$result['rows'] = $query->result();
			}
			echo json_encode($result);
		}
		else{
			$result = array();
			
			if ($query->num_rows()>0){
				$result = $query->result_array();
			}
			
			echo '<xml>';
			
			foreach($result as $row){
				echo '<row>';
				echo '<id>'.$row['id'].'</id>';
				echo '<msisdn>'.$row['msisdn'].'</msisdn>';
				echo '<sms>'.$row['sms'].'</sms>';
				echo '<src>'.$row['src'].'</src>';
				echo '<date>'.$row['date'].'</date>';
				echo '<time>'.$row['date'].'</time>';
				echo '</row>';
			}
			echo '</xml>';
			/*echo '<pre>';
			print_r($result);
			echo '</pre>';
			die;
			
			$xml = new SimpleXMLElement('<root/>');
			array_walk_recursive(array_flip($result), array ($xml, 'addChild'));
			echo $xml->asXML();*/
			
			//print $result;
		}
	}

	function get_user_partner(){
		$sql = "SELECT user_id, client_id FROM user_groups a JOIN users b ON a.user_id = b.id WHERE a.group_id = 5";
		$query=$this->db->query($sql);
		
		//echo $this->db->last_query();
		$rows = array();
		if ($query->num_rows()>0){
			foreach($query->result_array() as $arr){
				$rows['client_id'][] = $arr['client_id'];
				$rows['user_id'][] = $arr['user_id'];
			}
		}
		
		/*echo '<pre>';
		print_r($rows);
		echo '</pre>';*/
		return $rows;
		
	}
	
	function get_mobile_client_partner($client_id){
		$sql = "SELECT mobile
		FROM clients WHERE client_id = '$client_id'";
		$query = $this->db->query($sql);
		
		//echo $this->db->last_query();
		$result = array();
		if ($query->num_rows()>0){
			$x=0;
			$arrX=array();
			foreach($query->result_array() as $row){
				$arr= explode(';', $row['mobile']);
				foreach($arr as $val){
					if (empty($val)) continue;
					$result['mobile'][] = $val;
				}
			}
			
		}
		return $result;
	}
	
	function widget_sms_logmo_edit(){
		$client_id = $this->orca_auth->user->client_id;
		
		$this->load->view('smslogmo-form-widgets');
	}

	function getfilter($clientid){
		$arrNumber = array();
		$arrWords = array();
		$perpage = array();
		
		$this->db->where('client_id', $clientid);
		$query = $this->db->get('log_mo_widgets_options');
		//echo $this->db->last_query();
		if ($query->num_rows()>0){
			foreach($query->result_array() as $row){
				$arrmob = explode(",", $row['blacklistnumber']);
				foreach($arrmob as $number){
					$tmp = trim($number);
					if (empty($tmp)) continue;
					$arrNumber[] = $tmp;
				}
				
				$arrkata = explode(",", $row['blacklistwords']);
				foreach($arrkata as $kata){
					$tmp = trim($kata);
					if (empty($tmp)) continue;
					$arrWords[] = $tmp;
				}
			}
			$perpage = array($row['view']);
		}
		
		return array($arrNumber, $arrWords, $perpage);
	}
	
	function quickpaging($limit, $p, $count, $client_id){
		
		$totalpage = $count%$limit;

		
		$x =$p-1;
		
		if ( $x== 0 || $x == 1){
			$str = '<<';
			
			$str .= '<';
		}else{
			$str =anchor('smslogmo/allwg/'.$client_id.'/?p=1', '<<');
			
			$str .= anchor('smslogmo/allwg/'.$client_id.'/?p='.$x, '<');
		}
		
		if ($totalpage >= 5){
			for($i=($p-2); $i<=($p+2); $i++){
				if ($i != $p){
					$str .= anchor('smslogmo/allwg/'.$client_id.'/?p='.$i, $i);
				}else{
					$str .= $i;
				}
			}
		}
		
		if ($p+1 >= $totalpage)
		{
			$str .= '>';
			$str .= '>>';
		}else{
			$str .= anchor('smslogmo/allwg/'.$client_id.'/?p='.($p+1), '>');
			$str .= anchor('smslogmo/allwg/'.$client_id.'/?p='.$totalpage, '>>');
		}
	
		return $str;
	}
	
	function get_key_valid($client_id, $key){
		$this->db->where('client_id', $client_id);
		$this->db->where('confirm_hash', $key);
		$query = $this->db->get('users');
		if ($query->num_rows()>0){
			return true;
		}else{
			return false;
		}
	}
}
