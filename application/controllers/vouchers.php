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
 * Description of vouchers
 *
 * @author ferdhie
 */
class Vouchers extends CI_Controller 
{
    var $table_fields = array (
        0 => 'voucher_id',
        1 => 'client_id',
        2 => 'voucher_code',
        3 => 'create_date',
        4 => 'use_date',
        5 => 'customer_id',
        6 => 'campaign_id',
        7 => 'valid_from',
        8 => 'valid_thru',
        9 => 'voucher_value',
        10 => 'is_sent',
    );

    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $this->orca_auth->login_required();
        
        //check adhoc campaign
        if ($this->db->where('client_id', $this->orca_auth->user->client_id)->where('campaign_title', 'adhoc')->count_all_results('campaign') == 0)
        {
            $this->db->insert('campaign', array('campaign_title' => 'adhoc', 'client_id' => $this->orca_auth->user->client_id));
        }
        
        $this->load->view('vouchers');
    }

    public function generate()
    {
        $this->orca_auth->login_required();
        
        header('Content-type: application/json; charset=UTF-8');
        
        $result = array('success' => true, 'voucher_code' => '');
        $voucher_code = unique_id('a');
        $result['voucher_code'] = $voucher_code;
        
        echo json_encode($result);
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
        
        $strclient = '';
        if ( $this->orca_auth->user->client_id )
        {
            $strclient = " AND vouchers.client_id = {$this->orca_auth->user->client_id}";
        }
        
        if ($q)
        {
            $qx = $this->db->escape_like($q);
            $strclient .= " AND (vouchers.voucher_code LIKE '%$qx%' OR customers.first_name LIKE '%$qx%' OR customers.last_name LIKE '%$qx%'  OR campaign.campaign_title LIKE '%$qx%')";
        }
        
        //$strclient .= " " . parse_filter2($filter, $this->table_fields, 'vouchers');
        
        $sql = 'SELECT COUNT(*) AS cnt FROM vouchers LEFT JOIN customers ON vouchers.customer_id=vouchers.customer_id
                        LEFT JOIN campaign ON vouchers.campaign_id=campaign.campaign_id
                        WHERE vouchers.is_delete = 0 '.$strclient;
        $query = $this->db->query($sql);
        $row = $query->row();
        $totalCount = $row->cnt;
        $result['totalCount'] = $totalCount;
        if ($totalCount > 0)
        {
            $start = intval($start);
            $limit = intval($limit);
            
            //$strclient .= parse_sort2($sort, $this->table_fields, 'vouchers');
            $query = $this->db->query('SELECT vouchers.*, customers.first_name, customers.last_name, campaign.campaign_title 
                        FROM vouchers
                        LEFT JOIN customers ON vouchers.customer_id=customers.customer_id
                        LEFT JOIN campaign ON vouchers.campaign_id=campaign.campaign_id
                        WHERE vouchers.is_delete = 0 '.$strclient." LIMIT $start,$limit");
            if ($query->num_rows() > 0)
            {
                $result['rows'] = $query->result();
            }
        }
        
        echo json_encode($result);
    }
    
    private function _build_query( $query )
    {
        $this->db->like('vouchers.voucher_code', $query);
        $this->db->or_like('customers.first_name', $query);
        $this->db->or_like('customers.last_name', $query );
        $this->db->or_like('campaign.campaign_title', $query );
    }
    
    public function query()
    {
        require_once APPPATH . 'libraries/verhoeff.php';
        
        $this->orca_auth->login_required();
        
        $result = array('success' => true, 'totalCount' => 0, 'rows' => array());
        
        $page = $this->input->post('page');
        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        $query = $this->input->post('query');
        if (!$limit) $limit = 20;
        
        do
        {
            if ( strlen($query) < 3 )
            {
                $result['msg'] = 'length < 3';
                break;
            }

            if (!verhoeff::validate($query))
            {
                $result['msg'] = 'checksum error';
                break;
            }

            if ($query)
            {
                $this->db->where('vouchers.is_delete', 0);

                if ( $this->orca_auth->user->client_id )
                {
                    $this->db->where('vouchers.client_id', $this->orca_auth->user->client_id);
                }

                $query = strtoupper($query);

                $this->db->where('vouchers.voucher_code', $query);

                $this->db->select('vouchers.*,customers.first_name,customers.last_name,campaign.campaign_title')
                        ->from('vouchers')
                        ->join('customers', 'vouchers.customer_id=customers.customer_id', 'left')
                        ->join('campaign', 'vouchers.campaign_id=campaign.campaign_id', 'left');

                $qry = $this->db->get( '', 1 );
                if ($qry->num_rows() > 0)
                {
                    $result['rows'] = $qry->result();
                    $result['totalCount'] = count($result['rows']);
                }
            }
        }
        while(0);
        
        $result['sql'] = $this->db->queries;
        
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
            $this->db->update('vouchers', array('is_delete' => 2));
            
            $this->db->where('client_id', $this->orca_auth->user->client_id)->where_in('voucher_id', $data);
            $this->db->update('vouchers', array('is_delete' => 1));
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
        
        if ($this->orca_auth->user->client_id)
            $data['client_id'] = $this->orca_auth->user->client_id;
        else 
            $data['client_id'] = intval($data['client_id']);
        
        if (isset($data['valid_from']) && !$data['valid_from'])
            $data['valid_from'] = null;

        if (isset($data['valid_thru']) && !$data['valid_thru'])
            $data['valid_thru'] = null;
        
        if (isset($data['use_date']) && !$data['use_date'])
            $data['use_date'] = null;
        
        if (isset($data['create_date']) && !$data['create_date'])
            $data['create_date'] = null;
        
        $voucher_count = $this->input->post('voucher_count');
        if ($voucher_count > 1 && !$data['voucher_id'])
        {
            unset($data['voucher_id']);
            $batch = array();
            for($i = 0; $i<$voucher_count; $i++)
            {
                $data['voucher_code'] = unique_id('a');
                $batch[] = $data;
            }
            $this->db->insert_batch('vouchers', $batch);
            $data = true;
        }
        else
        {
            if ( !isset($data['voucher_id']) || !$data['voucher_id'] )
            {
                $data['voucher_id'] = null;
                $this->db->insert( 'vouchers', $data );
                $data['voucher_id'] = $this->db->insert_id();
            }
            else
            {
                $this->db->where('voucher_id', $data['voucher_id']);
                $this->db->update('vouchers', $data );
            }

            $query = $this->db->get_where( 'vouchers', array('voucher_id'=>$data['voucher_id']), 1 );
            $data = $query->row();
        }
        
        echo json_encode(array('success' => true, 'data' => $data));
    }
    
    function download()
    {
        $this->orca_auth->login_required();
        $this->load->view('vouchers_download_form');
    }
    
    function requestdownload()
    {
        if (!isset($_SESSION)) session_start();
        $this->orca_auth->login_required();
        
        $fields = array('from', 'to', 'customer_id', 'campaign_id', 'status', 'sent');
        $data = array();
        foreach($fields as $f)
            $data[$f] = $this->input->post($f);
        
        $_SESSION['vr'] = $data;
        
        $result = array('success'=>true,'msg'=>'');
        
        echo json_encode($result);
    }
    
    function getdownload()
    {
        if (!isset($_SESSION)) session_start();
        $this->orca_auth->login_required();
        
        $data = isset($_SESSION['vr']) ? $_SESSION['vr'] : null;
        
        if (is_null($data))
        {
            show_404();
            return;
        }
        
        $fields = array('from', 'to', 'customer_id', 'campaign_id', 'status', 'sent');
        foreach($fields as $f)
            $$f = isset($data[$f]) ? $data[$f] : null;
        
        if ($this->orca_auth->user->client_id)
            $this->db->where('vouchers.client_id', $this->orca_auth->user->client_id);
        $this->db->where('vouchers.is_delete', 0);
        
        if ($from)
            $this->db->where('vouchers.create_date >= ', "$from 00:00:00");
        
        if ($to)
            $this->db->where('vouchers.create_date <= ', "$to 23:59:59");
        
        if ($customer_id)
            $this->db->where('vouchers.customer_id', $customer_id);
        
        if ($campaign_id)
            $this->db->where('vouchers.campaign_id', $campaign_id);
        
        switch($status) {
            case 1:
                $this->db->where('vouchers.use_date', '0000-00-00 00:00:00');
                break;
            case 2:
                $this->db->where('vouchers.use_date !=', '0000-00-00 00:00:00');
                break;
        }
        
        switch($sent) {
            case 1:
                $this->db->where('vouchers.is_sent', 0);
                break;
            case 2:
                $this->db->where('vouchers.is_sent', 1);
                break;
        }
        
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment;filename="sicrm-voucher-'.date('YmdHis').'.csv"');
        
        $this->db->select('vouchers.*,customers.first_name,customers.last_name,campaign.campaign_title')
                ->from('vouchers')
                ->join('customers', 'vouchers.customer_id=customers.customer_id', 'left')
                ->join('campaign', 'vouchers.campaign_id=campaign.campaign_id', 'left');

        $query = $this->db->get();
        if ($query->num_rows())
        {
            $rows = $query->result_array();
            $handle = fopen('php://output', 'w');
            foreach($rows as $row)
            {
                //voucher_id	client_id	voucher_code	create_date	use_date	customer_id	campaign_id	valid_from	valid_thru	voucher_value	is_delete	is_sent	first_name	last_name	campaign_title
                unset($row['voucher_id']);
                unset($row['client_id']);
                unset($row['customer_id']);
                unset($row['campaign_id']);
                unset($row['is_delete']);
                unset($row['is_sent']);
                
                if (!isset($header))
                {
                    $header = true;
                    fputcsv($handle, array_keys($row), ',', '"');
                }
                fputcsv($handle, array_values($row), ',', '"');
            }
        }
    }
}

