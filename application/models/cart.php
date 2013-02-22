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
 * Description of carts
 *
 * @author ferdhie
 */
class Cart extends CI_Model
{
    var $table_name = 'carts';
    var $data = null;
    var $id = null;

    public function __construct()
    {
        parent::__construct();
    }
    
    public function load($cart_id, $key=null)
    {
        $this->data = array();
        $this->db->where('cart_id', $cart_id);
        $query = $this->db->get($this->table_name, 1, 0);
        if ( $query->num_rows() > 0 )
        {
            $cart = $query->row();
            $this->data = unserialize($cart->data);
            $this->id = $cart_id;
        }
        else
        {
            $this->save($cart_id);
        }

        return $key ? (isset($this->data)?$this->data:null): $this->data ;
    }
    
    public function save($cart_id=null)
    {
        if ($this->id)
        {
            //update
            $this->db->where( 'cart_id', $this->id );
            $data = array('data' => serialize($this->data));
            $this->db->update( $this->table_name, $data );
        }
        else
        {
            $data = array('data' => serialize($this->data), 'cart_id' => $cart_id);
            $this->db->insert( $this->table_name, $data );
            $this->id = $cart_id;
        }
        return $this->id;
    }
    
    public function clear($cart_id=null)
    {
        if ($this->id)
        {
            //update
            $this->db->where( 'cart_id', $cart_id ? $cart_id : $this->id );
            $this->db->delete( $this->table_name );
        }
        $this->data = array();
        $this->id = null;
    }
}
