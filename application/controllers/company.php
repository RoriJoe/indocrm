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
 * Description of company
 *
 * @author ferdhie
 */
class Company extends CI_Controller 
{
    var $table_fields = array (
        1 => 'name',
        2 => 'address',
        3 => 'city',
        4 => 'state',
        5 => 'zip_code',
        6 => 'country',
        7 => 'phone',
        8 => 'mobile',
        9 => 'email',
        10 => 'website',
        12 => 'signature'
    );
    
    function index()
    {
        $this->orca_auth->login_required();
        $this->load->view('company');
    }
    
    function info()
    {
        $this->orca_auth->login_required();
        
        $result = array('success'=>false,'data'=>array());
        
        $query = $this->db->get_where('clients', array('client_id'=> $this->orca_auth->user->client_id), 1);
        if ($query->num_rows())
        {
            $client = $query->row();
            $result['data'] = $client;
            $result['success'] = true;
        }
        
        echo json_encode($result);
    }
    
    function save()
    {
        $this->orca_auth->login_required();
        $result = array('success'=>false,'data'=>array());
        
        $data = array();
        
        foreach( $this->table_fields as $f )
        {
            $data[$f] = $this->input->post($f);
        }
        
        if ( isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name']) )
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
                else
                {
                    $result['error'] = "resize gagal";
                    echo json_encode($result);
                    return;
                }
            }
            else
            {
                $result['error'] = $this->upload->display_errors('','');
                echo json_encode($result);
                return;
            }
        }
        
        $query = $this->db->get_where('clients', array('client_id'=> $this->orca_auth->user->client_id), 1);
        if ($query->num_rows())
        {
            $this->db->where('client_id', $this->orca_auth->user->client_id)->update( 'clients', $data );
        }
        else
        {
            $data['client_id'] = '0';
            $this->db->insert('clients', $data );
        }
        
        $query = $this->db->get_where('clients', array('client_id'=> $this->orca_auth->user->client_id), 1);
        if ($query->num_rows())
        {
            $client = $query->row();
            $result['data'] = $client;
            $result['success'] = true;
        }
        
        echo json_encode($result);
    }
}

