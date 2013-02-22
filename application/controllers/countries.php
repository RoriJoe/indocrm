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
 * Description of countries
 *
 * @author ferdhie
 */
class Countries extends CI_Controller 
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        header('Content-type: application/json; charset=UTF-8');
        
        $q = $this->input->get_post('query');
        if ($q)
        {
            $this->db->like('Country', $q);
        }
        
        $query = $this->db->order_by('Country')->get('countries'); 
        
        echo json_encode( array('success' => true, 'rows' => ($query->num_rows() > 0 ? $query->result() : array())) );
    }
    
    public function states()
    {
        header('Content-type: application/json; charset=UTF-8');
        $country = $this->input->get_post('country');
        $result = array('success' => true, 'rows' => array());
        if ($country)
        {
            $this->db->where('Country', $country);
            
            $q = $this->input->get_post('query');
            if ($q)
            {
                $this->db->like('State', $q);
            }

            $query = $this->db->select('Country,State')->order_by('State')->group_by('State')->get('cities');
            if ($query->num_rows())
            {
                $result['rows'] = $query->result();
            }
        }
        echo json_encode( $result );
    }
    
    public function cities()
    {
        header('Content-type: application/json; charset=UTF-8');
        $country = $this->input->get_post('country');
        $state = $this->input->get_post('state');
        $result = array('success' => true, 'rows' => array());
        if ($country)
        {
            $this->db->where('Country', $country);
            
            if ($state)
            {
                $this->db->where('State', $state);
            }
            
            $q = $this->input->get_post('query');
            if ($q)
            {
                $this->db->like('City', $q);
            }
            
            $query = $this->db->select('City')->order_by('City')->get('cities');
            
            if ($query->num_rows())
            {
                $result['rows'] = $query->result();
            }
        }
        echo json_encode( $result );
    }
}

