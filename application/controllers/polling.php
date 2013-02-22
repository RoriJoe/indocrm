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
class Polling extends CI_Controller
{
	private $table_name = 'polling';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index(){
		$this->orca_auth->login_required();
		
		$err = $this->input->get_post('err');
		
		$data['err'] ='';
		$data['result'] = array();
		$this->db->where('client_id', $this->orca_auth->user->client_id);
		
		$kolom = $this->input->post('kolom');
		$query = $this->input->post('query');
		$cari = $this->input->post('cari');
		
		if (!empty($query)){
			$this->db->where($kolom, $query);
		}
		
		$q = $this->db->get($this->table_name);
		if ($q->num_rows()>0){
			$data['result'] = $q->result();
		}
		
		switch ($err){
			case 'insert':
				$data['err'] = 'Data telah ditambahkan';
			break;
			case 'update':
				$data['err'] = 'Data telah diupdate';
			break;
			case 'delete':
				$data['err'] = 'Data telah dihapus';
			break;
		}
		
		$this->load->view('polling',$data);
	}
	
	public function save(){
		$array_fields = array('judul_polling', 'pilihan1', 'pilihan2', 'pilihan3', 'pilihan4', 'pilihan5', 'start_date', 'end_date', 'client_id');
		$column = array();
		foreach($array_fields as $field){
			$column[$field] = $this->input->post($field);
		}
		
		$polling_id = $this->input->get_post('polling_id');
		$mode = $this->input->get_post('mode');
		$save = $this->input->post('save');
		$keyword = $this->input->post('keyword');
		$keywordnew = $this->input->post('keywordnew');
		$err = $this->input->get_post('err');
		
		$data['polling_id'] = $polling_id;
		$data['mode'] = $mode;
		
		if ($err=='exists'){
			$data['err'] = 'Keyword sudah dipakai';
		}else if ($err=='keyword'){
			$data['err'] = 'Pilih salah satu keyword atau buat yang baru';
		}else if ($err=='empty'){
			$data['err'] = 'Isian tidak boleh kosong';
		}
		
		if ($save){
			if (empty($column['judul_polling']) && empty($column['pilihan1']) && empty($column['pilihan2']) && empty($column['start_date']) && empty($column['end_date'])){
				redirect('polling/save?err=empty&mode=edit&polling_id='.$polling_id, 'refresh');
			}else{
				if ($mode == 'edit'){
					if ($this->_check_keyword($keyword,$polling_id)){
						redirect('polling/save?err=exists&mode=edit&polling_id='.$polling_id,'refresh');
					}else{
						if (!empty($keyword)){
							$this->db->where('polling_id', $polling_id);
							$this->db->update($this->table_name, array(
								'judul_polling' => $column['judul_polling'],
								'keyword' => $keyword,
								'pilihan1' => $column['pilihan1'],
								'pilihan2' => $column['pilihan2'],
								'pilihan3' => trim($column['pilihan3']),
								'pilihan4' => trim($column['pilihan4']),
								'pilihan5' => trim($column['pilihan5']),
								'start_date' => $column['start_date'],
								'end_date' => $column['end_date'])
							);
							
						//echo $this->db->last_query();
						//die;
							redirect('polling?err=update', 'refresh');
						}else{
							redirect('polling/save?err=keyword&mode=edit&polling_id='.$polling_id, 'refresh');
						}
					}
				}else{
					if ($this->_check_keyword($keyword)){
						redirect('polling/save?err=exists','refresh');
					}else{
						$this->db->insert($this->table_name, 
						array(
							'polling_id' => null,
							'keyword' => $keyword,
							'judul_polling' => $column['judul_polling'],
							'pilihan1' => $column['pilihan1'],
							'pilihan2' => $column['pilihan2'],
							'pilihan3' => trim($column['pilihan3']),
							'pilihan4' => trim($column['pilihan4']),
							'pilihan5' => trim($column['pilihan5']),
							'start_date' => $column['start_date'],
							'end_date' => $column['end_date'],
							'client_id' => $this->orca_auth->user->client_id)
						);
						$newid = mysql_insert_id();
						$this->db->insert('polling_result', array(
							'result_id' => null,
							'polling_id' => $newid,
							'pilihan1' => '0',
							'pilihan2' => '0',
							'pilihan3' => '0',
							'pilihan4' => '0',
							'pilihan5' => '0'
						));
						redirect('polling?err=insert','refresh');
					}
				}
			}
		}
		
		$this->load->view('update_polling',$data);
	}
	
	public function delete(){
		$this->orca_auth->login_required();
		$polling_id = $this->input->get_post('polling_id');
		
		$this->db->where('polling_id', $polling_id);
		$this->db->delete('polling_result');
		
		$this->db->where('polling_id', $polling_id);
		$this->db->delete($this->table_name);
		redirect('polling?err=delete', 'refresh');
	}
	
	public function result(){
		$this->orca_auth->login_required();
		$polling_id = $this->input->get_post('polling_id');
		$data['result'] = array();
		
		if ($this->_my_polling($polling_id)){
			
			$sql = "SELECT 
			judul_polling, a.pilihan1 as text1, a.pilihan2 as text2, a.pilihan3 as text3, a.pilihan4 as text4, a.pilihan5 as text5, b.pilihan1, b.pilihan2, b.pilihan3, b.pilihan4, b.pilihan5
			FROM ".$this->table_name." a JOIN ".$this->table_name."_result b ON a.polling_id = b.polling_id
			WHERE a.polling_id = '$polling_id'";
			$q = $this->db->query($sql);
			if ($q->num_rows()>0){
				$data['result'] = $q->result();
			}
		}else{
			show_404();
		}
		$this->load->view('polling_result',$data);
	}
	
	function mobile_count(){
		$this->orca_auth->login_required();
		
		$limit = 10;
		
		$polling_id = $this->input->get_post('polling_id');
		$p = $this->input->get_post('p');
		$p = (empty($p) ? 0 : $p);
		$data['judul_polling'] = '';
		$data['result'] = array();
		if ($this->_my_polling($polling_id))
		{
			$this->db->select('keyword,judul_polling');
			$this->db->where('polling_id',$polling_id);
			$q = $this->db->get($this->table_name);
			$rs= $q->result();
			$keyword = $rs[0]->keyword;
			$data['judul_polling'] = $rs[0]->judul_polling;
			
			$sql = "SELECT DISTINCT msisdn FROM log_mo WHERE sms LIKE '".$keyword."%'";
			$qcount = $this->db->query($sql);
			$totalRows = $qcount->num_rows();
			
			$sql = "SELECT msisdn, COUNT(*) as cnt FROM log_mo WHERE sms like '".$keyword."%' GROUP BY 1 ORDER BY 2 DESC LIMIT $p,$limit";
			$query = $this->db->query($sql);
			if ($query->num_rows()>0){
				$data['result'] = $query->result();
			}
			
			$data['paging'] = $this->_page($totalRows, $limit, $p, '&polling_id='.$polling_id);
		}else{
			show_404();
		}
		
		$this->load->view('polling_mobil_result', $data);
	}
	
	function _page($total, $limit, $now,$link=''){
		
		$nbefore = ($now == 0) ? 0 : ($now - $limit);
		$nnext = ($now == $total) ? $total : ($now + $limit);
		
		$strFirst = 'First';
		$strbefore = 'Prev';
		$strnext = 'Next';
		$strEnd = 'End';
		
		if ($nbefore > 0 || $now >0){
			$strbefore = anchor('keywords?p='.$nbefore.$link,$strbefore);
		}
		
		if ($nnext <= $total){
			$strnext = anchor('keywords?p='.$nnext.$link,$strnext);
		}
		
		return $strbefore.'&nbsp;&nbsp;|&nbsp;&nbsp;'.$strnext;
	}
	
	function _my_polling($id){
		$this->db->where('polling_id', $id);
		$this->db->where('client_id', $this->orca_auth->user->client_id);
		$q = $this->db->get($this->table_name);
		if ($q->num_rows()>0){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	function _keyword_exists($keyword){
		$this->db->where('keyword', $keyword);
		$q = $this->db->get('keywords');
		if ($q->num_rows()>0){
			$result = $q->result();
			return $result[0]->client_id;
		}else{
			return FALSE;
		}
	}
	
	function _check_keyword($keyword,$id=''){
		
		if (!empty($id)){
			$this->db->where('polling_id !=', $id);
		}
		$this->db->where('keyword', $keyword);
		$q = $this->db->get($this->table_name);
		if ($q->num_rows()>0){
			return TRUE;
		}else{
			return FALSE;
		}
	}
}
