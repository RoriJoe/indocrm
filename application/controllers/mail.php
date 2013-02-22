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
 * Description of mail
 *
 * @author ferdhie
 */
class Mail extends CI_Controller
{
    var $table_fields = array (
    0 => 'job_id',
    1 => 'title',
    2 => 'sent_date',
    3 => 'campaign_id',
    4 => 'client_id',
    5 => 'is_delete',
    6 => 'is_sent',
    7 => 'create_date',
    8 => 'template_id',
    );
    
    function index()
    {
        $this->orca_auth->login_required();
        $this->load->view('mail');
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
        
        $this->db->where('mailjob.is_delete', 0);
        if ( $this->orca_auth->user->client_id )
        {
            $this->db->where('mailjob.client_id', $this->orca_auth->user->client_id);
        }
        
        if ($q)
        {
            $this->_build_query($q);
        }
        
        parse_filter( $filter, $this->table_fields, 'mailjob' );
        
        $totalCount = $this->db->count_all_results('mailjob');
        $result['totalCount'] = $totalCount;
        if ($totalCount > 0)
        {
            $this->db->where('mailjob.is_delete', 0);
            if ( $this->orca_auth->user->client_id )
            {
                $this->db->where('mailjob.client_id', $this->orca_auth->user->client_id);
            }
            
            if ($q)
            {
                $this->_build_query($q);
            }
            
            parse_sort($sort, $this->table_fields);
            parse_filter( $filter, $this->table_fields, 'mailjob' );
            
            $this->db->select('mailjob.*, campaign.campaign_title, mailtemplates.name AS template_name');
            $this->db->from('mailjob')->join('campaign', 'mailjob.campaign_id = campaign.campaign_id', 'left');
            $this->db->join('mailtemplates', 'mailjob.template_id = mailtemplates.template_id');
            
            $query = $this->db->get( '', $limit, $start );
            
            if ($query->num_rows() > 0)
            {
                $result['rows'] = $query->result();
            }
        }
        
        echo json_encode($result);
    }
    
    private function _build_query( $query )
    {
        $this->db->like('title', $query);
    }
    
    function delete()
    {
        $this->orca_auth->login_required();
        
        $data = array_filter(array_map('intval', $this->input->post('data')));
        if ( $data )
        {
            $this->db->where('client_id', $this->orca_auth->user->client_id)->where('is_delete', 1);
            $this->db->update('mailjob', array('is_delete' => 2));
            
            $this->db->where('client_id', $this->orca_auth->user->client_id)->where_in('job_id', $data);
            $this->db->update('mailjob', array('is_delete' => 1));
        }
        
        echo json_encode(array('success' => true));
    }
    
    function save()
    {
        $this->orca_auth->login_required();
        
        $data = array();
        foreach( $this->table_fields as $field )
        {
            if ($field == 'create_date' || $field == 'is_delete' || $field == 'is_sent')
                continue;
            
            $data[$field] = $this->input->post($field);
        }
        
        if ($this->orca_auth->user->client_id)
            $data['client_id'] = $this->orca_auth->user->client_id;
        else 
            $data['client_id'] = intval($data['client_id']);
        
        if ( !isset($data['job_id']) || !$data['job_id'] )
        {
            $data['job_id'] = null;
            $this->db->insert( 'mailjob', $data );
            $data['job_id'] = $this->db->insert_id();
        }
        else
        {
            $this->db->where('job_id', $data['job_id']);
            $this->db->update( 'mailjob', $data );
        }
        
        $query = $this->db->get_where( 'mailjob', array('job_id' => $data['job_id']) , 1 );
        $data = $query->row();
        
        echo json_encode(array('success' => true, 'data' => $data));
    }
}

