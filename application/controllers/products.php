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
 * Description of products
 *
 * @author ferdhie
 */
class Products extends CI_Controller
{
    var $table_fields  = array (
        0 => 'product_id',
        2 => 'product_name',
        3 => 'product_description',
        4 => 'product_images',
        5 => 'product_price',
        6 => 'tax',
        7 => 'discount',
        8 => 'category',
        9 => 'client_id'
    );
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $this->orca_auth->login_required();
        $this->load->view('products');
    }
    
    private function _build_query( $query )
    {
        $query = $this->db->escape("%$query%");
        $where = " AND ( products.product_name LIKE $query )";
        return $where;
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
        
        $where = 'is_delete = 0';
        if ( $this->orca_auth->user->client_id )
        {
            $where .= " AND client_id = ".$this->orca_auth->user->client_id . " ";
        }
        
        if ($q)
        {
            $where .= $this->_build_query($q);
        }
        
        $where .= " " . parse_filter2( $filter, $this->table_fields, 'products' );
        $totalCount = $this->db->query("SELECT COUNT(*) AS cnt FROM products WHERE $where")->row()->cnt;
        $result['totalCount'] = $totalCount;
        if ($totalCount > 0)
        {
            $where = 'is_delete = 0';
            if ( $this->orca_auth->user->client_id )
            {
                $where .= " AND client_id = ".$this->orca_auth->user->client_id . " ";
            }

            if ($q)
            {
                $where .= $this->_build_query($q);
            }
            
            $where .= " ".parse_filter2( $filter, $this->table_fields, 'products' );
            $order = parse_sort2($sort, $this->table_fields);
            $query = $this->db->query("SELECT products.* FROM products WHERE $where $order LIMIT $start,$limit");
            
            if ($query->num_rows() > 0)
            {
                $result['rows'] = $query->result();
            }
        }
        
        echo json_encode($result);
    }

    public function lookup()
    {
        $this->orca_auth->login_required();
        
        header('Content-type: application/json; charset=UTF-8');
        $result = array('success' => true, 'rows' => array(), 'totalCount' => 0);

        $where = 'is_delete = 0';
        if ( $this->orca_auth->user->client_id )
        {
            $where .= " AND client_id = ".$this->orca_auth->user->client_id . " ";
        }
            
        $q = $this->input->post('query');
        if ($q)
        {
            $where .= " AND (product_code like " . $this->db->escape("%$q%") . "  OR product_name like " . $this->db->escape("%$q%") . ") ";
        }
            
        $query = $this->db->query("SELECT products.* FROM products WHERE $where");

        $result['totalCount'] = $query->num_rows();
        if ($result['totalCount'] > 0)
        {
            $result['rows'] = $query->result();
        }
        
        echo json_encode($result);
    }
    
    function categories()
    {
        $this->orca_auth->login_required();
        
        $result = array('success' => true, 'totalCount' => 0, 'rows' => array());
        $query = $this->input->post('query');
        
        $where = "client_id = " . $this->orca_auth->user->client_id;
        
        if ($query)
        {
            $query = strtoupper($query);
            $where .= " AND category LIKE " . $this->db->escape("%$query%");
        }
        
        $query = $this->db->query("SELECT category,COUNT(*) AS cnt FROM products WHERE is_delete = 0 AND client_id = {$this->orca_auth->user->client_id} GROUP BY category");
        if ($query->num_rows())
        {
            foreach($query->result() as $row)
                $counts[$row->category] = $row->cnt;
        }
        
        $query = $this->db->query("SELECT * FROM products_categories WHERE $where ORDER BY category");
        $result['totalCount'] = $query->num_rows();
        if ($result['totalCount'] > 0)
        {
            foreach($query->result() as $row)
            {
                $row->products_count = isset($counts[$row->category]) ? $counts[$row->category] : 0;
                $result['rows'][] = $row;
            }
        }
        
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($result);
    }
    
    function save()
    {
        $this->orca_auth->login_required();
        
        $data = array();
        foreach( $this->table_fields as $field )
        {
            $data[$field] = $this->input->post($field);
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
                ($img['image_width'] > 600 ) ? $config2['width'] = 600 : $config2['width'] = $img['image_width'];
                ($img['image_height'] > 400) ? $config2['height'] = 400 : $config2['height'] = $img['image_height'];
                $this->load->library('image_lib', $config2);
                if($this->image_lib->resize())
                {
                    $data['product_images'] = $img['file_name'];
                    
                    $config2 = array();
                    $config2['image_library'] = 'gd2';
                    $config2['source_image'] = $img['full_path'];
                    $config2['maintain_ratio'] = FALSE;
                    $config2['width'] = 64;
                    $config2['height'] = 64;
                    $config2['new_image'] = 'thumb_'.$img['file_name'];
                    $this->image_lib->initialize($config2);
                    if($this->image_lib->resize())
                    {
                        $thumb = substr($this->image_lib->full_dst_path, strlen($this->image_lib->dest_folder));
                        $data['product_thumb'] = $thumb;
                    }
                } 
            }
        }
        
        if (!$data['discount']) 
            $data['discount'] = 0;
        
        if (!$data['category']) 
            $data['category'] = null;
        else
            $this->db->query("INSERT INTO products_categories ( category, client_id ) VALUES (?,?) ON DUPLICATE KEY UPDATE category = ?", 
                    array( $data['category'], $this->orca_auth->user->client_id, $data['category'] ));
        
        if ($this->orca_auth->user->client_id)
            $data['client_id'] = $this->orca_auth->user->client_id;
        else 
            $data['client_id'] = intval($data['client_id']);

        if ( !isset($data['product_id']) || !$data['product_id'] )
        {
            $data['product_id'] = null;
            $client = $this->db->select('name')
                            ->from('clients')
                            ->where('client_id',$this->orca_auth->user->client_id)
                            ->get()->row();
            
            $data['product_code'] = auto_code(substr($client->name,0,3).$data['client_id']); 
            $this->db->insert( 'products', $data );
            $data['product_id'] = $this->db->insert_id();
        }
        else
        {
            $this->db->where('product_id', $data['product_id']);
            $this->db->update( 'products', $data );
        }
        
        $query = $this->db->get_where( 'products', array('product_id'=>$data['product_id']), 1 );
        $data = $query->row();
        
        echo json_encode(array('success' => true, 'data' => $data));
    }
    
    function delete()
    {
        $this->orca_auth->login_required();
        
        $data = array_filter(array_map('intval', $this->input->post('data')));
        if ( $data )
        {
            $this->db->where('client_id', $this->orca_auth->user->client_id)->where_in('is_delete', 1);
            $this->db->update('products', array('is_delete' => 2));
            
            $this->db->where('client_id', $this->orca_auth->user->client_id)->where_in('product_id', $data);
            $this->db->update('products', array('is_delete' => 1));
        }
        
        echo json_encode(array('success' => true));
    }
    
}

