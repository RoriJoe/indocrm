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
 * Description of validate
 *
 * @author ferdhie
 */
class Validate extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $this->orca_auth->login_required();
        $this->load->view('validate');
    }
    
    public function update()
    {
        $this->orca_auth->login_required();
        
        header('Content-type: application/json; charset=UTF-8');
        $result = array('success' => false, 'msg' => '');
        
        $voucher_id = $this->input->post('voucher_id');
        if ($voucher_id)
        {
            $query = $this->db->get_where('vouchers', array('voucher_id'=> $voucher_id), 1);
            if ($query->num_rows())
            {
                $this->db->where('voucher_id', $voucher_id)->update('vouchers', array('use_date'=> date('Y-m-d H:i:s')));
                $result['success'] = true;
            }
        }
        
        echo json_encode($result);
    }
}

