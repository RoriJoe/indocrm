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
class Keywords extends CI_Controller
{
	private $table_keywords = 'keywords';
	private $table_params = 'keyword_params';
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index(){
		$this->orca_auth->login_required();
		$limit = 20;
		
		///$data['keywords'] = $this->_show_all();
		
		$data['keywords'] = array();
		
		$mode = $this->input->get_post('mode');
		$search = $this->input->get_post('search');
		$data['search'] = $search;
		$p = $this->input->get_post('p');
		
		$p = (empty($p) ? 0 : $p);
		
		if (!empty($search)){
			$this->db->like('keyword', $search);
		}
		
		if ($this->orca_auth->user->id != 1){
			$this->db->where('client_id', $this->orca_auth->user->client_id);
		}
		$this->db->order_by("keyword_id", "DESC");
		$q = $this->db->get($this->table_keywords,$limit,$p);
		
		$result = $q->num_rows();
		if ($result>0){
			$data['keywords']=$q->result();
		}
		
		if (!empty($search)){
			$this->db->like('keyword', $search);
		}
		if ($this->orca_auth->user->id != 1){
			$this->db->where('client_id', $this->orca_auth->user->client_id);
		}
		$this->db->from($this->table_keywords);
		$totalRows = $this->db->count_all_results();
		
		$link = '';
		if ($search){
			$link = '&search='.$search;
		}
		
		$data['paging'] = $this->_page($totalRows,$limit,$p,$link);
		
		$err = $this->input->get_post('err');
		switch ($err) {
		case 'insert':
			$data['flash'] = 'Keyword telah disimpan';
			break;
		case 'update':
			$data['flash'] = 'Keyword telah diupdate';
			break;
		case 'param': 
			$data['flash'] = 'Param telah disimpan';
			break;
		case 'delete':
			$data['flash'] = 'Data telah dihapus';
			break;
		case 'exists' :
			$data['flash'] = 'Data sudah ada';
			break;
		}
		
		$this->load->view('keywords', $data);
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
	
	function _show_all(){
		$result = array();
		$this->db->order_by('keyword_id', 'desc');
		$q = $this->db->get($this->table_keywords);
		if ($q->num_rows()>0){
			$result = $q->result();
		}
		
		return $result;
	}
	
	public function params(){
		$this->orca_auth->login_required();
		$client_id = $this->orca_auth->user->client_id;
		$limit = 20;
		
		$keyword_id = $this->input->get_post('keyword_id');
		$param_id = $this->input->get_post('param_id');
		$mode = $this->input->get_post('mode');
		$err = $this->input->get_post('err');
		
		$data['keyword'] = '';
		$data['flash'] = '';
		$data['mode'] = $mode;
		$data['keyword_id'] = $keyword_id;
		$data['param_id'] = $param_id;
		$data['keywordclient'] = '';
		
		$this->db->where('keyword_id', $keyword_id);
		$this->db->select('keyword');
		$q = $this->db->get($this->table_keywords);
		if ($q->num_rows()>0){
			$row = $q->result();
			$data['keyword'] = $row[0]->keyword;
			$data['keywordclient'] = $row[0]->client_id;
		}
		
		$data['replies'] = array();
		
		switch ($err) {
			case 'insert':
				$data['flash'] = 'Keyword telah disimpan';
				break;
			case 'param': 
				$data['flash'] = 'Param telah disimpan';
				break;
			case 'delete':
				$data['flash'] = 'Data telah dihapus';
				break;
			}
		
		$data['param'] = '';
		$data['reply'] = '';
		
		if ($mode == 'edit'){
			$this->db->where($this->table_params.'.keyword_id',$keyword_id);
			$this->db->where('param_id',$param_id);
			$this->db->where('client_id', $this->orca_auth->user->client_id);
			$this->db->select($this->table_params.".*");
			$this->db->from($this->table_params);
			$this->db->join($this->table_keywords, $this->table_keywords.".keyword_id = ".$this->table_params.".keyword_id");
			$q = $this->db->get();
			
			if ($q->num_rows()>0){
				$res = $q->result();
				$data['param'] = $res[0]->param;
				$data['reply'] = $res[0]->reply;
			}
		}
		
		if ($this->_permited_client($client_id, $keyword_id) || $this->orca_auth->user->group_id == 1){
		
			$result = array();
			$q = $this->db->query("SELECT ".$this->table_keywords.".keyword_id, ".$this->table_keywords.".keyword, ".$this->table_params.".param, ".$this->table_params.".reply, ".$this->table_params.".param_id 
			FROM ".$this->table_keywords." LEFT JOIN ".$this->table_params." ON ".$this->table_keywords.".keyword_id = ".$this->table_params.".keyword_id 
			WHERE ".$this->table_params.".keyword_id = '$keyword_id'");
	
			if ($q->num_rows()>0){
				$result = $q->result();
			}
			$data['replies'] = $result;
		}else{
			$data['replies'] = FALSE;
		}

		$this->load->view('params', $data);
	}
	
	public function save(){
		$this->orca_auth->login_required();
		
		$arrfields = array('keyword', 'description', 'active');
		$keyword_id = $this->input->get_post('keyword_id');
		$save = $this->input->post('save');
		$mode = $this->input->get_post('mode');
		$column = array();
		$data['mode'] = $mode;
		$data['keyword_id'] = $keyword_id;
		$data['client_id'] = $this->orca_auth->user->client_id;
		foreach($arrfields as $field){
			$column[$field] = $this->input->post($field);
		}
		$column['client_id'] = $this->orca_auth->user->client_id;
		$column['keyword'] = trim(strtolower($column['keyword']));
		
		$this->db->where('keyword_id', $keyword_id);
		$q = $this->db->get($this->table_keywords);
		$data['result'] = $q->result();
		
		if ($save){
			if ($mode != 'edit' || empty($mode)){
				
				if ($this->_keyword_exists($column['keyword'])){
					redirect('keywords?err=exists','refresh');
				}else{
					$this->db->insert( $this->table_keywords,$column);
					redirect('keywords?err=insert', 'refresh');
				}
			}else{
				$this->db->where('keyword_id', $data['keyword_id']);
				$this->db->update($this->table_keywords, array(
					'keyword' => trim(strtolower($data['keyword'])),
					'description' => $data['description'],
					'active' => $data['active']
					));
					redirect('keywords?err=keyword', 'refresh');
			}
		}
		
		$this->load->view('update_keywords',$data);
	}
	
	public function save_param(){
		$this->orca_auth->login_required();
		$client_id = $this->orca_auth->user->client_id;

		$keyword_id = $this->input->post('keyword_id');
		$param_id = $this->input->post('param_id');
		$param = $this->input->post('param');
		$reply = $this->input->post('reply');
		$save = $this->input->post('save');
		$mode = $this->input->post('mode');
		
		if ($save){
			if ($this->_param_exists($keyword_id,$param)){
				redirect('keywrods/params?err=exists','refresh');
			}else{
				if ($mode == 'edit' && $param_id != ''){
					$this->db->where('keyword_id', $keyword_id);
					$this->db->where('param_id', $param_id);
					$this->db->update($this->table_params, array(
						'reply' => $reply
					));
				}else{
					$this->db->insert($this->table_params, array(
					'keyword_id' => $keyword_id,
					'param' => trim(strtolower($param)),
					'reply' => $reply
					));
					//echo $this->db->last_query();
					//die;
				}
				redirect('keywords/params?err=param&keyword_id='.$keyword_id,'refresh');
			}
		}
		
	}
	
	public function delete_param(){
		$param_id = $this->input->get_post('param_id');
		$keyword_id = $this->input->get_post('keyword_id');
		$this->db->where('param_id', $param_id);
		$this->db->delete($this->table_params);
		redirect('keywords/params?err=delete&keyword_id='.$keyword_id);
	}
	
	public function delete_keyword(){
		$keyword_id = $this->input->get_post('keyword_id');
		$this->db->where('keyword_id', $keyword_id);
		$this->db->delete($this->table_keywords);
		redirect('keywords?err=delete');
	}
	
	public function all_keywords(){
		
		$this->orca_auth->login_required();
		
		header('Content-type: application/json; charset=UTF-8');
		$result = array('success' => false, 'rows' => array(), 'totalCount' => 0);
		
		$q = $this->input->post('q');
		$page = $this->input->post('page');
		$start = $this->input->post('start');
		$limit = $this->input->post('limit');
		$sort = $this->input->post('sort');
		$filter = $this->input->post('filter');
		if (!$limit) $limit = 20;
		
		$query = $this->db->get($table_keywords);
		$result['totalCount'] = $query->num_rows();
		if ($totalCount>0){
			$result['rows'] = $query->result();
		}else{
			$result['success'] = false;
		}
		
		echo json_encode($result);
	}
	
	function _keyword_exists($keyword){
		$this->db->where('keyword', $keyword);
		$q = $this->db->get($this->table_keywords);
		if ($q->num_rows()>0){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	function _param_exists($keyword_id,$param){
		$this->db->where('keyword_id',$keyword_id);
		$this->db->where('param',$param);
		$q = $this->db->get($this->table_params);
		if ($q->num_rows()>0){
			return TRUE;
		}else{
			return FALSE;
		}
	}

	function _permited_client($client_id, $keyword_id){
		
		if ($this->orca_auth->user->id === 1){
			return TRUE;
		}else{
			$this->db->where($this->table_keywords.'.keyword_id', $keyword_id);
			$this->db->where('client_id', $this->orca_auth->user->client_id);
			$this->db->from($this->table_keywords);
			$this->db->join($this->table_params,$this->table_keywords.".keyword_id = ".$this->table_params.".keyword_id");
			$q = $this->db->get();
		//	echo $this->db->last_query();
			//die;
			if ($q->num_rows()>0){
				return TRUE;
			}else{
				return FALSE;
			}
		}
	}
	
	function _search_keyword($keyword){
		$this->db->where_like('keyword', $keyword);
		$q = $this->db->get($this->table_keywords);
		if($q->num_rows()>0){
			return $q->result();
		}else{
			return FALSE;
		}
	}
	
}

