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
 * Description of design
 *
 * @author ferdhie
 */
class Design extends CI_Controller 
{
    function index()
    {
        $this->orca_auth->login_required();
        
        $templates = array();
        $my_templates = array();
        
        $client_ids = array(0);
        if ( $this->orca_auth->user->client_id )
            $client_ids[] = $this->orca_auth->user->client_id;
        
        $this->db->where('is_delete', 0);
        $this->db->where_in('client_id', $client_ids);
        
        $query = $this->db->get('mailtemplates');
        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                if ($row->client_id)
                {
                    $my_templates[] = $row;
                }
                else
                {
                    $templates[] = $row;
                }
            }
        }
        
        $this->load->view('design', array('my_templates' => $my_templates, 'templates' => $templates));
    }
    
    function create()
    {
        $this->orca_auth->login_required();
        
        $template_id = $this->input->get('id');
        $name = '';
        $template = '';
        $client_id = 0;
        
        if ($template_id)
        {
            $this->db->where_in('client_id', array(0, $this->orca_auth->user->client_id));
            $this->db->where('template_id', $template_id);
            $query = $this->db->get('mailtemplates', 1);
            if ($query->num_rows() > 0)
            {
                $row = $query->row();
                $name = $row->name;
                $template = $row->template;
                $client_id= $row->client_id;
            }
        }
        
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
        {
            $fields = array('template_id', 'name', 'txt');
            foreach($fields as $f) 
                $$f = $this->input->post($f);
            
            if ($client_id != $this->orca_auth->user->client_id)
                $template_id = 0;
            
            do
            {
                if (!$name)
                {
                    flashmsg_set('Nama kosong');
                    break;
                }
                
                if (!$txt)
                {
                    flashmsg_set('Desain kosong');
                    break;
                }
                
                $data = array('name' => $name, 'client_id' => $this->orca_auth->user->client_id, 'template' => $txt);
                
                if ($template_id)
                {
                    $this->db->where('template_id',$template_id);
                    $this->db->update('mailtemplates', $data);
                }
                else
                {
                    $this->db->insert('mailtemplates', $data);
                    $template_id = $this->db->insert_id();
                }
                
                $template = $this->db->get_where( 'mailtemplates', array( 'template_id' => $template_id ), 1 )->row();
                $thumbnail = $this->get_thumb($template_id);
                if ($thumbnail)
                    $this->db->where('template_id',$template_id)->update('mailtemplates', array('thumbnail' => $thumbnail));
                
                $template = $txt;
            }
            while(0);
        }

        $this->load->view('create_design', array('name'=>$name,'template'=>$template,'template_id'=>$template_id));
    }
    
    function get_thumb( $template_id )
    {
        $path = FCPATH.'u/';
        
        if (!extension_loaded('curl')) 
            dl('curl.so');
        
        $url = "http://api.thumbalizr.com/?api_key=9baa7d76b70f95dba6dd71f853d21177&url=".rawurlencode(site_url('design/gettemplate?id='.$template_id));
        $curl_config = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 60
        );

        $curl = curl_init( $url );
        curl_setopt_array( $curl , $curl_config );
        $response = curl_exec( $curl );
        
        if ($response !== false && strlen($response) != 4243)
        {
            $filename = uniqid($template_id) . '.jpg';
            file_put_contents($path . $filename, $response);
            return $filename;
        }
        
        return false;
    }
    
    function imagelist()
    {
        $this->orca_auth->login_required();
        
        $imagelist = array();
        $query = $this->db->get_where('assets', array('client_id' => $this->orca_auth->user->client_id));
        if ( $query->num_rows() > 0 )
        {
            foreach($query->result() as $row)
            {
                $imagelist[] = array($row->image, base_url('u/'.$row->image));
            }
        }
        
        header('Content-type: text/javascript; charset=UTF-8');
        echo 'var tinyMCEImageList = ' . json_encode($imagelist) . ';';
    }
    
    function assets()
    {
        $this->orca_auth->login_required();
        
        $result = array('success' => true, 'rows' => array(), 'totalCount' => 0);
        $query = $this->db->get_where('assets', array('client_id' => $this->orca_auth->user->client_id));
        $result['totalCount'] = $query->num_rows();
        if ( $result['totalCount'] > 0 )
        {
            $result['rows'] = $query->result();
        }
        
        header('Content-type: application/json; charset=UTF-8');
        echo json_encode($result);
    }
    
    function templatelist()
    {
        $this->orca_auth->login_required();
        
        $templatelist = array();
        
        $client_ids = array(0);
        if ( $this->orca_auth->user->client_id )
            $client_ids[] = $this->orca_auth->user->client_id;
        
        $this->db->where('is_delete', 0);
        $this->db->where_in('client_id', $client_ids);
        
        $query = $this->db->get('mailtemplates');
        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $templatelist[] = array($row->name, site_url('design/gettemplate?id='.$row->template_id), '');
            }
        }
        
        header('Content-type: text/javascript; charset=UTF-8');
        echo 'var tinyMCETemplateList = ' . json_encode($templatelist) . ';';
    }
    
    function gettemplate()
    {
        $id = $this->input->get_post('id');
        if (!$id)
        {
            show_404();
            return;
        }
        
        $client_ids = array(0);
        
        if ( $this->orca_auth->is_logged_in() )
        {
            if ( $this->orca_auth->user->client_id )
                $client_ids[] = $this->orca_auth->user->client_id;
        }
        
        $this->db->where('is_delete', 0);
        $this->db->where_in('client_id', $client_ids);
        $this->db->where('template_id', $id);
        
        $query = $this->db->get('mailtemplates', 1);
        if ($query->num_rows() > 0)
        {
            $row = $query->row();
            echo $row->template;
        }
        else
        {
            show_404();
        }
        
    }
    
    //list all uploaded images as HTML
    function uploadedimages()
    {
        $this->orca_auth->login_required();
        $this->load->view('uploadedimages');
    }
    
    function delasset() 
    {
        $this->orca_auth->login_required();
        $result = array('success' => false);
        
        $id = $this->input->get_post('id');
        if ($id)
        {
            $query = $this->db->get_where('assets', array('client_id' => $this->orca_auth->user->client_id, 'asset_id' => $id), 1);
            if ($query->num_rows())
            {
                $img = $query->row();
                $this->db->where(array('client_id' => $this->orca_auth->user->client_id, 'asset_id' => $id));
                $this->db->delete('assets');
                
                $p = str_replace(FCPATH, '\\', '/');
                @unlink($p . 'u/' . $img->image);
                @unlink($p . 'u/' . $img->thumb);
            }
        }
        
        echo json_encode($result);
    }
    
    function doupload()
    {
        $this->orca_auth->login_required();
        $result = array('success' => false, 'image' => null);
        
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
                $image = $img['file_name'];
                $result['image'] = base_url('u/'.$image);
                $result['success'] = true;

                $config2 = array();
                $config2['image_library'] = 'gd2';
                $config2['source_image'] = $img['full_path'];
                $config2['maintain_ratio'] = TRUE;
                $config2['width'] = 64;
                $config2['height'] = 64;
                $config2['create_thumb'] = true;
                $this->load->library('image_lib', $config2);
                
                $thumb = '';
                if($this->image_lib->resize())
                {
                    $thumb = substr($this->image_lib->full_dst_path, strlen($this->image_lib->dest_folder));
                    $result['thumb'] = base_url('u/'.$thumb);
                }

                $this->db->insert( 'assets', array('client_id' => $this->orca_auth->user->client_id, 'image' => $image, 'thumb' => $thumb) );
            }
        }
        
        echo json_encode($result);
    }
    
    public function query()
    {
        $this->orca_auth->login_required();
        
        $result = array('success' => true, 'totalCount' => 0, 'rows' => array());
        
        $page = $this->input->post('page');
        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        $query = $this->input->post('query');
        if (!$limit) $limit = 20;
        
        $this->db->select('template_id,name,is_delete,client_id');
        $this->db->where('is_delete', 0);
        
        if ( $this->orca_auth->user->client_id )
        {
            $this->db->where_in('client_id', array(0,$this->orca_auth->user->client_id));
        }
        
        if ($query)
        {
            $query = strtoupper($query);
            $this->like('name', $query);
        }
        
        $result['totalCount'] = $this->db->count_all_results('mailtemplates');
        if ($result['totalCount'] > 0)
        {
            $this->db->select('template_id,name,is_delete,client_id');
            $this->db->where('is_delete', 0);

            if ( $this->orca_auth->user->client_id )
            {
                $this->db->where_in('client_id', array(0,$this->orca_auth->user->client_id));
            }
            
            if ($query)
            {
                $this->db->like('name', $query);
            }
            
            $query = $this->db->get( 'mailtemplates', $limit, $start );
            
            if ($query->num_rows() > 0)
            {
                $result['rows'] = $query->result();
            }
        }
        
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($result);
    }
}

