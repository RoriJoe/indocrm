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
class SmsLog extends CI_Controller
{
    var $table_fields = array (
    0 => 'log_id',
    1 => 'sent_date',
    2 => 'to_number',
    3 => 'body_plain',
    4 => 'is_sent',
    5 => 'campaign_id',
    6 => 'sms_number',
    7 => 'total_count',
    8 => 'customer_id',
    9 => 'is_success',
    10 => 'client_id',
    11 => 'queue_id',
    );
    
    function index()
    {
        $this->orca_auth->login_required();
        $this->load->view('smslog');
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
        
        $timezone = $this->orca_auth->user->timezone;
        
		$this->db->select("log_id, 
		(sent_date + INTERVAL ".$timezone." HOUR) as sent_date, 
		to_number, body_plain, is_sent, campaign_id,sms_number, total_count, customer_id, is_success,
		client_id, queue_id");
        
        if ( $this->orca_auth->user->client_id )
        {
            //$this->db->where('client_id', $this->orca_auth->user->client_id);
            $this->db->where($this->orca_auth->user->client_id == 139 ? "client_id IN (45,46,47,70,139)" : ("client_id = {$this->orca_auth->user->client_id}"));
        }
        
        if ($q)
        {
            $this->db->where('to_number', $q);
        }
        
        if ($from)
            $this->db->where('sent_date >=', $from);
        if ($to)
            $this->db->where('sent_date <=', $to);
        
        parse_filter( $filter, $this->table_fields, 'smslog' );
        $totalCount = $this->db->count_all_results('smslog');
        $result['totalCount'] = $totalCount;
        if ($totalCount > 0)
        {
			$this->db->select("log_id, 
			(sent_date + INTERVAL ".$timezone." HOUR) as sent_date, 
			to_number, body_plain, is_sent, campaign_id,sms_number, total_count, customer_id, is_success,
			client_id, queue_id");
            if ( $this->orca_auth->user->client_id )
            {
                //$this->db->where('client_id', $this->orca_auth->user->client_id);
				$this->db->where($this->orca_auth->user->client_id == 139 ? "client_id IN (45,46,47,70,139)" : ("client_id = {$this->orca_auth->user->client_id}"));
            }

            if ($q)
            {
                $this->db->where('to_number', $q);
            }

            if ($from)
                $this->db->where('sent_date >=', $from);
            if ($to)
                $this->db->where('sent_date <=', $to);
            
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
}

