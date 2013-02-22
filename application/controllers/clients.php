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
 * Description of clients
 *
 * @author ferdhie
 */
class Clients extends CI_Controller
{
    var $table_fields = array (
    0 => 'client_id',
    1 => 'name',
    2 => 'address',
    3 => 'city',
    4 => 'state',
    5 => 'zip_code',
    6 => 'country',
    7 => 'phone',
    8 => 'email',
    9 => 'website',
    10 => 'image',
    11 => 'is_active',
    12 => 'reg_date',
    13 => 'mail_count',
    14 => 'mail_quota',
    15 => 'sms_count',
    16 => 'sms_quota',
    17 => 'sms_free',
    18 => 'mail_free',
    19 => 'client_type',
    );
    
    function index()
    {
        $this->orca_auth->login_required();
        $this->load->view('clients');
    }
    
    function save()
    {
        $this->orca_auth->login_required();

        $data = array();
        foreach( $this->table_fields as $field )
        {
            $val = $this->input->post($field);
            if (!is_null($val))
                $data[$field] = $val;
        }
        
        if ( isset($_FILES['file']['tmp_name']) )
        {
            $config = array();
            $config['upload_path'] = './u/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['overwrite'] = false;
            $this->load->library('upload', $config);

            if ($this->upload->do_upload('file'))
            {
                $img = $this->upload->data();
                $config2 = array();
                $config2['image_library'] = 'gd2';
                $config2['source_image'] = $img['full_path'];
                $config2['maintain_ratio'] = TRUE;
                $config2['width'] = 128;
                $config2['height'] = 128;
                $this->load->library('image_lib', $config2);
                if($this->image_lib->resize())
                {
                    $data['image'] = $img['file_name'];
                }
            }
        }
        
        $fields = array('client_id','sms_count','sms_quota','mail_count', 'mail_quota', 'is_active', 'sms_free', 'mail_free', 'client_type');
        foreach($fields as $f)
            if ( is_null($data[$f]) ) unset($data[$f]);
            else $data[$f] = intval($data[$f]);
            
        if ( !isset($data['client_id']) || !$data['client_id'] )
        {
            //
        }
        else
        {
            $this->db->where('client_id', $data['client_id']);
            $this->db->update('clients', $data );
        }

        $query = $this->db->get_where( 'clients', array('client_id'=>$data['client_id']), 1 );
        $data = $query->row();
        
        echo json_encode(array('success' => true, 'data' => $data));
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
        
        if ($q)
        {
            $this->_build_query($q);
        }
        
        parse_filter( $filter, $this->table_fields, 'clients' );
        
        $totalCount = $this->db->count_all_results('clients');
        $result['totalCount'] = $totalCount;
        if ($totalCount > 0)
        {
            if ($q)
            {
                $this->_build_query($q);
            }
            
            parse_sort($sort, $this->table_fields);
            parse_filter( $filter, $this->table_fields, 'clients' );
            
            $query = $this->db->get( 'clients', $limit, $start );
            if ($query->num_rows() > 0)
            {
                $result['rows'] = $query->result();
            }
        }
        
        echo json_encode($result);
    }
    
    private function _build_query( $query )
    {
        $this->db->like('clients.name', $query);
        $this->db->or_like('clients.email', $query);
        $this->db->or_like('clients.address', $query );
        $this->db->or_like('clients.city', $query );
        $this->db->or_like('clients.state', $query );
    }
    
    public function tagihan(){
		$this->orca_auth->login_required();
		
		//stop only admin can do it :)
		if ($this->orca_auth->user->group_id != 1)
			show_404();
			
		$this->load->view('tagihan');
	}
    
    public function all_tagihan(){
		$this->orca_auth->login_required();
		
		//stop only admin can do it :)
		if ($this->orca_auth->user->group_id != 1)
			show_404();
		
		header('Content-type: application/json; charset=UTF-8');
		
		$result = array('success' => true, 'data' => array(), 'totalCount' => 0);
		
		$this->db->select('tagihan_client.id as id, tagihan_client.paket_id as paket_id, tagihan_client.client_id as client_id, clients.name as name, nama_paket, due_date, paid');
		$this->db->join('paket','paket.id = tagihan_client.paket_id','INNER');
		$this->db->join('clients','tagihan_client.client_id = clients.client_id','INNER');
		$query = $this->db->get('tagihan_client');
		//echo $this->db->last_query();
		$result['totalCount'] = $query->num_rows();
		if ($result['totalCount'] > 0){
			
			$result['data'] = $query->result();
		}
		
		echo json_encode($result);
	}
	
	public function save_tagihan(){
		$this->orca_auth->login_required();
		
		//stop only admin can do it :)
		if ($this->orca_auth->user->group_id != 1)
			show_404();
		
		header('Content-type: application/json; charset=UTF-8');
		
		$arr_fields = array(
			'0' => 'id',
			'1' => 'client_id',
			'2' => 'paket_id',
			'3' => 'due_date',
			'4' => 'paid'
			);
			
		$data = array();
		foreach($arr_fields as $field){
			$data[$field] = $this->input->post($field);
		}
		
		if (isset($data['id']) && !empty($data['id']) && isset($data['client_id']) && !empty($data['client_id'])){
			$this->db->where('id',$data['id']);
			$this->db->where('client_id',$data['client_id']);
			$this->db->update('tagihan_client',
				array(
					'paket_id' => $data['paket_id'],
					'due_date' => $data['due_date'],
					'paid' => $data['paid']
				));
				
				if ($data['paid'] == 1){
					$this->db->where('client_id',$data['client_id']);
					$this->db->limit(1);
					$q = $this->db->get('clients');
					$rsclient = $q->result();
					
					$this->db->where('id', $data['paket_id']);
					$query = $this->db->get('paket');
					$res = $query->result();
					
					list($y,$m,$d) = explode('-', $data['due_date']);
					$nextdate = date('Y-m-d', strtotime("+1 MONTH"));
					
					$contentmail = "Salam dari IndoCRM.\n".
					"Pesan ini dikirimkan karena Anda telah mendaftar untuk mengikuti layanan SMS gratis dan Broadcast Email dari IndoCRM. Anda tercatat pada paket ".strtoupper($res[0]->nama_paket)."\n\n".
					"Kami perlu memberitahukan bahwa tagihan Anda untuk bulan Ini sebesar Rp. ".number_format($res[0]->biaya, 2,",",".")." yang akan jatuh tempo pada tanggal ".$data['due_date']." telah LUNAS.\n".
					"Tagihan ini akan dikirim lagi terhitung 30 hari lagi yaitu pada tanggal ".$nextdate."\n\n".
					"Apabila layanan kami belum aktif setelah pembayaran ini silakan menghubungi ke email: ferdhie@simetri.web.id atau ke joko@simetri.web.id dengan Subject [LAYANAN BELUM AKTIF SETELAH PEMBAYARAN atas Nama ".$rsclient[0]->name." Tgl ".$data['due_date']."]\n\n".
					"Terima kasih atas partisipasi anda\n\n".
					"Hormat kami,\n\n".
					"Simetri CRM\n\n".
					"--\n".
					"Pesan ini telah diproduksi dan didistribusikan oleh PT Sinar Media Tiga, Raya Sulfat 96c, Malang, Indonesia 65123 - 0341-406633";
					//echo $contentmail;
					@mail($rsclient[0]->email, "Indocrm.com: Konfirmasi Pelunasan Tagihan", $contentmail, "from: info@indocrm.com\r\n");
					//$this->log("Sending email to ".$rsclient[0]->name." : ".$rsclient[0]->email."\n");
					
					$this->db->insert('tagihan_client',
						array(
							'id' => null,
							'client_id' => $data['client_id'],
							'paket_id' => $data['paket_id'],
							'due_date' => $nextdate,
							'paid' => '0'
							)
							);
				}
		}else{
			$this->db->insert('tagihan_client',
				array(
					'id' => null,
					'client_id' => $data['client_id'],
					'paket_id' => $data['paket_id'],
					'due_date' => $data['due_date'],
					'paid' => $data['paid']
				)
			);
		}
		
		echo json_encode(array('success' => true, 'data' => $data));
	}
	
	public function paket(){
		$this->orca_auth->login_required();
		header('Content-type: application/json; charset=UTF-8');
		
		$result = array('success' => true, 'data' => array(), 'totalCount' => 0);
		
		$query = $this->db->get('paket');
		
		$result['totalCount'] = $query->num_rows();
		
		if ($result['totalCount'] > 0){
			$result['data'] = $query->result();
		}
		
		echo json_encode($result['data']);
	}
}
