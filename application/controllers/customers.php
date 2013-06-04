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
 * Description of customers
 *
 * @author ferdhie
 */
class Customers extends CI_Controller
{
	var $table_fields  = array (
		0 => 'customer_id',
		2 => 'first_name',
		3 => 'last_name',
		4 => 'address',
		5 => 'city',
		6 => 'state',
		7 => 'zip_code',
		8 => 'phone',
		9 => 'mobile',
		10 => 'photo',
		11 => 'email',
		12 => 'facebook',
		13 => 'twitter',
		14 => 'bb_pin',
		15 => 'website',
		16 => 'client_id',
		17 => 'create_date',
		18 => 'country',
		19 => 'category',
	);
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$this->orca_auth->login_required();
		$this->load->view('customers');
	}
	
	public function uploadresult()
	{
		$this->orca_auth->login_required();
		
		$id = $this->input->get_post('id');
		$campaign_id = $this->input->get_post('campaign');
		
		if ($id)
		{
			$filename = APPPATH . 'cache/'.$id;
			if ( is_file($filename) )
			{
				$tmparray = unserialize(file_get_contents($filename));
				
				if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
				{
					$category = $this->input->post('category');
					$new_category = $this->input->post('new_category');
					
					if ($new_category)
					{
						$new_category = strtolower($new_category);
						$this->db->query("INSERT INTO customer_categories ( category, client_id ) VALUES (?,?) ON DUPLICATE KEY UPDATE category = ?",
								array( $new_category, $this->orca_auth->user->client_id, $new_category ));
						$category_id = $this->db->insert_id();
					}
					
					if (!$category) $category = $new_category;
					
					$keys = isset($_POST['key']) ? $_POST['key'] : array();
					$fields = isset($_POST['field']) ? $_POST['field'] : array();
					
					$mapping = array();
					foreach($keys as $i => $key)
					{
						if (isset($fields[$i]) && $fields[$i])
						{
							if (!isset($mapping[$i]))
								$mapping[$i] = array($fields[$i]);
							else $mapping[$i][] = $key;
						}
					}
					
					$insert = array();
					//$tmp_category = uniqid();
					$tmp_category = $category;
					$tf = array_flip($this->table_fields);
					
					foreach( $tmparray['data'] as $cnt => $row )
					{
						$entry = array();
						
						foreach($row as $i => $value)
						{
							if (!isset($mapping[$i])) continue;
							
							foreach($mapping[$i] as $field_name)
							{
								if (!isset($tf[$field_name]))
								{
									continue;
								}
								
								if ( !isset($entry[ $field_name ]) || !$entry[ $field_name ] ) 
								{
									$entry[ $field_name ] = trim($value);
								}
							}
						}
						
						if (!$entry )
							continue;
						
						if ( isset($entry['phone']) )
						{
							$entry['phone'] = substr(preg_replace('~[^0-9]~', '', $entry['phone']), 0, 20);
						}
						
						if ( isset($entry['mobile']) )
						{
							$entry['mobile'] = substr(preg_replace('~[^0-9]~', '', $entry['mobile']), 0, 20);
						}
						
						$entry['client_id'] = $this->orca_auth->user->client_id;
						$entry['category'] = $tmp_category;
						
						$insert[] = $entry;
						
						
						$this->db->insert('customers', $entry);
						echo "query : " .$this->db->last_query()." ".mysql_error();
						$cust_id = $this->db->insert_id();
						
						$array = array('customer_id' => $cust_id, 'category_id' => $category_id);
						
						$this->db->insert('customer_details', $array);
					}
					
				//	die;
					
					flashmsg_set(count($insert) .  ' pelanggan telah disimpan');
					
					if ($campaign_id)
					{
						$qry = $this->db->get_where('campaign', array('campaign_id' => $campaign_id), 1);
						if ($qry->num_rows()) 
						{
							$cmp = $qry->row();
							if ($cmp->campaign_type == 'sms')
							{
								$this->db->query("DELETE FROM campaign_details WHERE campaign_id = {$campaign_id}");
								$this->db->query("INSERT INTO campaign_details (campaign_id, customer_id)  SELECT '$campaign_id' AS campaign_id, customer_id FROM customers WHERE category = ? AND mobile IS NOT NULL AND mobile <> '' GROUP BY mobile", array($tmp_category));
								//echo $this->db->last_query();
								//die;
								if ($cmp->is_direct == '1'){
									redirect(site_url('campaign/broadcast_sms'));
								}else{
									redirect(site_url('campaign/plaintext?id='.$campaign_id));
								}
								return;
							}
						}
						
						$this->db->query("DELETE FROM campaign_details WHERE campaign_id = {$campaign_id}");
						$this->db->query("INSERT INTO campaign_details (campaign_id, customer_id)  SELECT '$campaign_id' AS campaign_id, customer_id FROM customers WHERE category = ? AND email IS NOT NULL AND email <> '' GROUP BY email", array($tmp_category));
						
						redirect(site_url('campaign/templates?id='.$campaign_id));
					}
					else
					{
						if ($category)
							$this->db->where('category', $tmp_category)->update('customers', array('category' => $category));
						else
							$this->db->where('category', $tmp_category)->update('customers', array('category' => NULL));
						
						redirect(site_url('customers/upload'));
					}
				}
				
				$tmparray['campaign_id'] = $campaign_id;
				
				$this->load->view('upload_customer_result', $tmparray);
			}
			else
			{
				flashmsg_set("Upload failed");
			}
		}
		else
		{
			show_404();
		}
	}
	
	public function _doupload()
	{
		$result = array('success' => false, 'id' => null);
		
		$fname = isset($_FILES['file']['name']) ? $_FILES['file']['name'] : '';
		$tmp = isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : '';
		$ext = strtolower(strrchr($fname, '.'));

		if ( is_uploaded_file($tmp) )
		{
			$base = uniqid();
			$dir = APPPATH."cache/$base";

			if ($ext == ".zip")
			{
				$zip = zip_open($tmp);
				if ($zip) 
				{
					if (!is_dir($dir)) mkdir($dir, 0777);

					while ($zip_entry = zip_read($zip)) 
					{
						$zip_name = basename(zip_entry_name($zip_entry));
						$ext2 = strtolower(strrchr($zip_name, '.'));
						if ($ext2 == '.csv')
						{
							if (zip_entry_open($zip, $zip_entry, "r")) 
							{
								$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
								file_put_contents("$dir/$zip_name", $buf);
								zip_entry_close($zip_entry);
								$id = $this->_upload_csv("$dir/$zip_name");
								$result['id'] = $id;
								$result['success'] = true;
								break;
							}
						}
						else if ($ext2 == '.xls')
						{
							if (zip_entry_open($zip, $zip_entry, "r")) 
							{
								$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
								file_put_contents("$dir/$zip_name", $buf);
								zip_entry_close($zip_entry);

								$id = $this->_upload_excel("$dir/$zip_name");
								$result['id'] = $id;
								$result['success'] = true;
								break;
							}
						}
					}
				}
				zip_close($zip);
			}
			else if ( $ext == '.xls' )
			{
				$id = $this->_upload_excel($tmp);
				$result['id'] = $id;
				$result['success'] = true;
			}
			else if ($ext == '.csv')
			{
				$id = $this->_upload_csv($tmp);
				$result['id'] = $id;
				$result['success'] = true;
			}
		}
		
		return $result;
	}
	
	public function doupload()
	{
		$this->orca_auth->login_required();
		
		$result = array('success' => false);
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
		{
			$result = $this->_doupload();
		}
		
		echo json_encode($result);
	}
	
	public function _upload_csv($tmp)
	{
		if (($handle = fopen($tmp, "rb")) !== FALSE)
		{
			$headers = array();
			$rows = array();
			$is_header = true;
			while (($data = fgetcsv($handle, 2048, ",")) !== FALSE)
			{
				if ($is_header)
				{
					$headers[] = $data;
					$is_header = false;
				}
				else
				{
					$rows[] = $data;
				}
			}
			fclose($handle);
			
			//filter header, cari yg diperlukan saya
			$valid_headers = isset($headers[0]) ? $headers[0] : array();
			$valid_rows = $rows;
		   
			if ($headers)
			{
				$valid_header_keys = array();
				$valid_headers = array();
				foreach($headers[0] as $i => $header)
				{
					if ( preg_match('~(name|nama|fullname|alamat|first|middle|last|e-?mail|surel|mail|phone|telp|tele?pon|hp|mobile|web|address|website|country|city|state|zip|prop|propinsi|kota|negara|ngr)~i', $header)  )
					{
						$valid_header_keys[$header] = $i;
						$valid_headers[] = $header;
					}
				}

				$valid_rows = array();

				foreach($rows as $row)
				{
					$valid_row = array();
					foreach($valid_header_keys as $header => $index)
					{
						$valid_row[] = isset($row[$index]) ? $row[$index] : '';
					}
					$valid_rows[] = $valid_row;
				}
			}
			
			$tmparray = array('headers' => array($valid_headers), 'data' => $valid_rows);
			$id = uniqid();
			$filename = APPPATH . 'cache/'.$id;
			file_put_contents($filename, serialize($tmparray));
			
			return $id;
		}
		return false;
	}
	
	public function _upload_excel($tmp)
	{
		require_once APPPATH.'Excel/reader.php';
		
		$current_error = error_reporting(E_ALL ^ E_NOTICE);
		$data = new Spreadsheet_Excel_Reader();
		$data->setOutputEncoding('CP1251');
		$data->read($tmp);
		
		$headers = array();
		$rows = array();

		foreach ($data->boundsheets as $k=>$sheet)
		{
			$numrows = $data->sheets[$k]['numRows'];
			$numcols = $data->sheets[$k]['numCols'];
			$is_header = true;
			$cnt = 0;
			for ($i = 1; $i <= $numrows; $i++)
			{
				for ($j = 1; $j <= $numcols; $j++)
				{
					$info = isset($data->sheets[$k]['cellsInfo'][$i][$j]) ? $data->sheets[$k]['cellsInfo'][$i][$j] : false;
					if ( $info && $info['type'] == 'date' )
					{
						$cell = date('Y-m-d', $info['raw']);
					}
					else
					{
						$cell = $data->sheets[$k]['cells'][$i][$j];
					}

					$cell = trim($cell);
					if ( $is_header )
					{
						$headers[$k][$j-1] = $cell;
					}
					else
					{
						$rows[$cnt][$j-1] = $cell;
					}
				}
				
				if (!$is_header)
				{
					$cnt++;
				}
				
				$is_header = false;
			}
		}

		error_reporting($current_error);
		
		$valid_headers = isset($headers[0]) ? $headers[0] : array();
		$valid_rows = $rows;

		//filter header, cari yg diperlukan saya
		if ($headers)
		{
			$valid_header_keys = array();
			$valid_headers = array();
			foreach($headers[0] as $i => $header)
			{
				if ( preg_match('~(name|nama|fullname|alamat|first|middle|last|e-?mail|surel|mail|phone|telp|hp|tele?pon|mobile|web|address|website|country|city|state|zip|prop|propinsi|kota|negara|ngr)~i', $header)  )
				{
					$valid_header_keys[$header] = $i;
					$valid_headers[] = $header;
				}
			}

			$valid_rows = array();

			foreach($rows as $row)
			{
				$valid_row = array();
				foreach($valid_header_keys as $header => $index)
				{
					$valid_row[] = isset($row[$index]) ? $row[$index] : '';
				}
				$valid_rows[] = $valid_row;
			}
		}
		
		$tmparray = array('headers' => $valid_headers, 'data' => $valid_rows);
		$id = uniqid();
		$filename = APPPATH . 'cache/'.$id;
		file_put_contents($filename, serialize($tmparray));
		
		return $id;
	}
	
	public function upload()
	{
		$this->orca_auth->login_required();
		$this->load->view('upload_form');
	}
	
	public function all()
	{
		$this->orca_auth->login_required();
		
		header('Content-type: application/json; charset=UTF-8');
		$result = array('success' => true, 'rows' => array(), 'totalCount' => 0);

		$q = $this->input->post('q');
		$page = $this->input->post('page');
		$start = intval($this->input->post('start'));
		$limit = $this->input->post('limit');
		$sort = $this->input->post('sort');
		$filter = $this->input->post('filter');
		if (!$limit) $limit = 20;
		
		$where = 'is_delete = 0';
		if ( $this->orca_auth->user->client_id )
		{
			$where .= ($this->orca_auth->user->client_id == 139 ? " AND client_id IN (45,46,47,70,139)" : (" AND client_id = ".$this->orca_auth->user->client_id . " "));
		}
		
		if ($q)
		{
			$where .= $this->_build_query($q);
		}
		
		$where .= " " . parse_filter2( $filter, $this->table_fields, 'customers' );
		$totalCount = $this->db->query("SELECT COUNT(*) AS cnt FROM customers WHERE $where")->row()->cnt;
		$result['totalCount'] = $totalCount;
		if ($totalCount > 0)
		{
			$where = 'is_delete = 0';
			if ( $this->orca_auth->user->client_id )
			{
				$where .= ($this->orca_auth->user->client_id == 139 ? " AND client_id IN (45,46,47,70,139)" : (" AND client_id = ".$this->orca_auth->user->client_id . " "));
			}

			if ($q)
			{
				$where .= $this->_build_query($q);
			}
			
			$where .= " ".parse_filter2( $filter, $this->table_fields, 'customers' );
			$order = parse_sort2($sort, $this->table_fields);
			$query = $this->db->query("SELECT customers.* FROM customers WHERE $where $order LIMIT $start,$limit");
			
			if ($query->num_rows() > 0)
			{
				$result['rows'] = $query->result();
			}
		}
		
		echo json_encode($result);
	}
	
	public function contact_notes(){
		
		$this->orca_auth->login_required();
		
		$this->load->view('contact_notes');
	}
	
	public function save_notes(){
		$this->orca_auth->login_required();
		
		$customer_id = $this->input->post('customer_note');
		$note_id = $this->input->post('note_id');
		$notes = $this->input->post('notes');
		$date_posted = $this->input->post('nd');
		
		$client_id = $this->orca_auth->user->client_id;
		
		if (empty($note_id)){
		    
		    $data = array(
		                'customer_id' => $customer_id,
		                'client_id' => $client_id,
		                'notes' => nl2br( addslashes($notes) ),
		                'date_posted' => date('Y-m-d',strtotime($date_posted)),
		                'time_posted' => date('H:i:s', mktime(date('H')+6,date('i'),date('s'),date('m'),date('d'),date('Y'))),
		                 );
		    
		    $this->db->insert( 'customer_notes', $data );
		    
		}else{
			$this->db->query("UPDATE customer_notes SET notes = '".nl2br( addslashes($notes) )."' WHERE note_id = '$note_id'");
		}
		
		echo json_encode(array('success' => true, 'customer_id' => $customer_id));
	}
	
	public function all_notes(){
		
		$this->orca_auth->login_required();
		
		header('Content-type: application/json; charset=UTF-8');
		$result = array('success' => true, 'rows' => array(), 'totalCount' => 0);
		
		$cust_id = $this->input->get_post('cust_id');
		$limit = $this->input->post('limit');
		$start = $this->input->post('start');
		
		if( !$limit )
		    $limit = 10;
		
		$result['total'] = $this->db
		              ->where('customer_id', $cust_id)
		              ->count_all_results('customer_notes');  
		
		//$result['total'] = $query->num_rows();
		//$result['query'] = $this->db->last_query();
		
		if ($result['total'] > 0){
		    
		    $query = $this->db
    		              ->where('customer_id', $cust_id)
    		              ->order_by('note_id', 'desc')
    		              ->get('customer_notes',$limit, $start)->result_array();
		    
			//$datas = $query->result_array();
			foreach( $query as $data) {
			    $date = date('Y-m-d',strtotime($data['date_posted']))." ".$data['time_posted'];
			    
			    $data['is_delete'] = 'Hapus';
			    $data['notes'] = stripslashes($data['notes']);
			    $data['date_posted'] = date( 'd-m-Y H:i:s', strtotime( $date ));
			    $result['rows'][] = $data;
			}
		} 
		
		echo json_encode($result);
	}
	
	
	/*public function save_notes() {
	    
	    $this->orca_auth->login_required();
		
		header('Content-type: application/json; charset=UTF-8');
		
		$tgl = $this->input->post('nd');
		$notes = $this->input->post('ket');
		
		echo json_encode( array('success' => true) );
		
	}*/
	
	public function info(){
		
		$this->orca_auth->login_required();
		
		header('Content-type: application/json; charset=UTF-8');
		$result = array('success' => true, 'rows' => array(), 'totalCount' => 0);
		
		$cust_id = $this->input->get_post('id');
		
		$this->db->where('customer_id', $cust_id);
		$query = $this->db->get('customers');
		
		if ($query->num_rows()>0){
			$result['rows'] = $query->result();
			$result['rows'] = $result['rows'][0];
		}
		
		echo json_encode($result);
	}
	
	public function delete_notes(){
		$this->orca_auth->login_required();
		
		$id = $this->input->get_post('id');
		
		$this->db->where('note_id', $id);
		$this->db->delete('customer_notes');
		
		echo json_encode(array('success' => true));
	}
	
	private function _build_query( $query )
	{
		$comps = explode(' ', $query);
		$where = '';
		foreach($comps as $qry)
		{
			$query = $this->db->escape("%$qry%");
			$where = " AND ( customers.first_name LIKE $query OR customers.last_name LIKE $query OR customers.email LIKE $query OR customers.mobile LIKE $query)";			
		}
		return $where;
	}
	
	public function query()
	{
		$this->orca_auth->login_required();
		
		$result = array('success' => true, 'totalCount' => 0, 'rows' => array());
		
		$page = $this->input->post('page');
		$query = $this->input->post('query');

		$where = 'is_delete = 0';
		if ( $this->orca_auth->user->client_id )
		{
			$where .= " AND client_id = {$this->orca_auth->user->client_id}";
		}
		
		if ($query)
		{
			$query = strtoupper($query);
			$where .= $this->_build_query($query);
		}
		
		$rs = $this->db->query("SELECT * FROM customers WHERE $where");
		$result['totalCount'] = $rs->num_rows();
		if ($result['totalCount'] > 0)
		{
			$result['rows'] = $rs->result();
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
			$this->db->where('client_id', $this->orca_auth->user->client_id)->where_in('is_delete', 1);
			$this->db->update('customers', array('is_delete' => 2));
			
			$this->db->where('client_id', $this->orca_auth->user->client_id)->where_in('customer_id', $data);
			$this->db->update('customers', array('is_delete' => 1));
		}
		
		echo json_encode(array('success' => true));
	}
	
	function save()
	{
		$this->orca_auth->login_required();

		$data = array();
		foreach( $this->table_fields as $field )
		{
			$data[$field] = $this->input->post($field);
		}
		
		if (!isset($data['first_name']) || !$data['first_name'])
		{
			if (isset($data['email']) && $data['email'])
			{
				//extract firstname from email
				$pos = strpos($data['email'], '@');
				$data['first_name'] = substr($data['email'], 0, $pos);
			}
		}
		
		if (!$data['country']) $data['country'] = null;
		if (!$data['state']) $data['state'] = null;
		if (!$data['city']) $data['city'] = null;
		
		if (!$data['category']){
			//$data['category'] = null;
			$data['category'] = 'uncategorized';
			$this->db->query("INSERT INTO customer_categories (category, client_id) VALUES (?,?) ON DUPLICATE KEY UPDATE category = ?", array($data['category'], $this->orca_auth->user->client_id, $data['category']));
		}else{
			
			$cats = explode(",",$data['category']);
			foreach($cats as $cat){
				if (empty($cat)) continue;
				$this->db->query("INSERT INTO customer_categories ( category, client_id ) VALUES (?,?) ON DUPLICATE KEY UPDATE category = ?", 
					array( trim($cat), $this->orca_auth->user->client_id, trim($cat) ));
			}
		}
		
		if ($this->orca_auth->user->client_id)
			$data['client_id'] = $this->orca_auth->user->client_id;
		else 
			$data['client_id'] = intval($data['client_id']);

		if ( !isset($data['customer_id']) || !$data['customer_id'] )
		{
			$data['customer_id'] = null;
			$this->db->insert( 'customers', $data );
			$data['customer_id'] = $this->db->insert_id();
			
			//dapetin category_id
			$cats = array();
			$tmp = explode(",",trim($data['category']));
			foreach($tmp as $cat){
				if (empty($cat)) continue;
				$cats[]= trim($cat);
			}
			$sql = "INSERT INTO customer_details (category_id, customer_id) SELECT category_id, '".$data['customer_id']."' FROM customer_categories WHERE client_id = '$data[client_id]' AND category IN ('".implode("','",$cats)."')";
			
			$this->db->query($sql);
			//echo '<script language="javascript">alert('.$this->db->last_query().')</script>';
			
		}
		else
		{
			$this->db->where('customer_id', $data['customer_id']);
			$this->db->update( 'customers', $data );
			
			//hapus semua category_id dari customer_id ini
			$this->db->where('customer_id', $data['customer_id']);
			$this->db->delete('customer_details');
			
			//insert baru category_id
			$cats = array();
			$tmp = explode(",",trim($data['category']));
			foreach($tmp as $cat){
				if (empty($cat)) continue;
				$cats[]= trim($cat);
			}
			
			$sql = "INSERT INTO customer_details (category_id, customer_id) SELECT category_id, '".$data['customer_id']."' FROM customer_categories WHERE client_id = '$data[client_id]' AND category IN ('".implode("','",$cats)."')";
			$this->db->query($sql);
			//echo '<script language="javascript">alert('.$this->db->last_query().')</script>';
			//die;
		}
		
		$query = $this->db->get_where( 'customers', array('customer_id'=>$data['customer_id']), 1 );
		$data = $query->row();
		
		echo json_encode(array('success' => true, 'data' => $data));
	}
	
	function categories()
	{
		$this->orca_auth->login_required();
		
		$result = array('success' => true, 'totalCount' => 0, 'rows' => array());
		$query = $this->input->post('query');
		
		$arrCat = array();
		
		$where = "client_id = " . $this->orca_auth->user->client_id;
		
		$sql = "SELECT category, COUNT(*) as cnt FROM customer_details a JOIN customer_categories b ON a.category_id = b.category_id WHERE $where GROUP BY b.category";
	
		$query = $this->db->query($sql);
		$this->db->last_query();
		if ($query->num_rows()>0){
			foreach($query->result() as $row){
				$counts[$row->category]=$row->cnt;
			}
		}
		
		/*if ($query)
		{
			$query = strtoupper($query);
			$where .= " AND category LIKE " . $this->db->escape("%$query%");
		}
		
		$query = $this->db->query("SELECT category,COUNT(*) AS cnt FROM customers WHERE is_delete = 0 AND client_id = {$this->orca_auth->user->client_id} GROUP BY category");
		if ($query->num_rows())
		{
			foreach($query->result() as $row)
				$counts[$row->category] = $row->cnt;
		}*/
		
		$query = $this->db->query("SELECT * FROM customer_categories WHERE $where ORDER BY category");
		$result['totalCount'] = $query->num_rows();
		if ($result['totalCount'] > 0)
		{
			foreach($query->result() as $row)
			{
				$row->customer_count = isset($counts[$row->category]) ? $counts[$row->category] : 0;
				$result['rows'][] = $row;
			}
		}
		
		header('Content-Type: application/json; charset=UTF-8');
		echo json_encode($result);
	}
	
	public function list_categories(){
		$this->orca_auth->login_required();
		
		$this->load->view('categories');
	}
}

