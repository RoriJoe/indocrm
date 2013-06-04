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
 * Description of maillog
 *
 * @author ferdhie
 */
class MailLog extends CI_Controller
{
    var $table_fields = array (
    0 => 'log_id',
    1 => 'sent_date',
    2 => 'from_name',
    3 => 'from_email',
    4 => 'to_name',
    5 => 'to_email',
    6 => 'body_html',
    7 => 'body_plain',
    8 => 'is_sent',
    9 => 'campaign_id',
    10 => 'email_number',
    11 => 'total_count',
    12 => 'customer_id',
    13 => 'is_success',
    );
    
    function index()
    {
        $this->orca_auth->login_required();
        $this->load->view('maillog');
    }

    public function all()
    {
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
        
        if ( $this->orca_auth->user->client_id )
        {
            $this->db->where($this->orca_auth->user->client_id == 139 ? "client_id IN (45,46,47,70,139)" : ("client_id = {$this->orca_auth->user->client_id}"));
        }
        
        if ($q)
        {
            $this->db->where('to_email', $q);
        }
        
        if ($from)
            $this->db->where('sent_date >=', $from);
        if ($to)
            $this->db->where('sent_date <=', $to);
        
        parse_filter( $filter, $this->table_fields, 'maillog' );
        $totalCount = $this->db->count_all_results('maillog');
        $result['totalCount'] = $totalCount;
        if ($totalCount > 0)
        {
            if ( $this->orca_auth->user->client_id )
            {
				$this->db->where($this->orca_auth->user->client_id == 139 ? "client_id IN (45,46,47,70,139)" : ("client_id = {$this->orca_auth->user->client_id}"));
            }

            if ($q)
            {
                $this->db->where('to_email', $q);
            }

            if ($from)
                $this->db->where('sent_date >=', $from);
            if ($to)
                $this->db->where('sent_date <=', $to);
            
            parse_sort($sort, $this->table_fields);
            parse_filter( $filter, $this->table_fields, 'maillog' );
            
            $query = $this->db->get( 'maillog', $limit, $start );
            if ($query->num_rows() > 0)
            {
                $result['rows'] = $query->result();
            }
        }
        
        echo json_encode($result);
    }
}

