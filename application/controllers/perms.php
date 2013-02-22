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
 * Description of perms
 *
 * @author ferdhie
 */
class Perms extends CI_Controller
{
    function index()
    {
        $this->orca_auth->login_required();
        $this->load->view('perms');
    }
    
    function update()
    {
        $this->orca_auth->login_required();
        
        $delete = $this->input->post('delete');
        $selected = $this->input->post('selected');
        $new_name = $this->input->post('new_name');
        $new_path = $this->input->post('new_path');
        $new_public = $this->input->post('new_public');
        $new_order = $this->input->post('new_order');
        $new_class = $this->input->post('new_class');

        $perm_name = $this->input->post('perm_name');
        $perm_path = $this->input->post('perm_path');
        $public = $this->input->post('public');
        $ids = $this->input->post('id');
        $perm_order = $this->input->post('perm_order');
        $perm_class = $this->input->post('perm_class');

        if ( $delete )
        {
            if ( $selected && is_array($selected) )
            {
                $selected = array_filter( array_map('intval', $selected) );
                if ($selected)
                {
                    $selected = implode(",", $selected);
                    $sql = "DELETE FROM perms WHERE perm_id IN ($selected)";
                    $this->db->query($sql);
                }
            }
        }
        else
        {
            $parents = array();

            //new
            if ( $new_name && is_array($new_name) )
            {
                foreach( $new_name as $parent_id => $name )
                {
                    if ( !$name ) continue;
                    $query = $this->db->get_where('perms',array('perm_name' => $name), 1);
                    if ($query->num_rows() > 0)
                    {
                        flashmsg_set("Nama sudah terpakai");
                        continue;
                    }
                    else
                    {
                        $this->db->insert('perms', array('perm_name'=>$name,
                            'perm_path' => isset($new_path[$parent_id]) ? $new_path[$parent_id] : '',
                            'public' => isset($new_public[$parent_id]) ? intval($new_public[$parent_id]) : 0,
                            'perm_order' => isset($new_order[$parent_id]) ? intval($new_order[$parent_id]) : 0,
                            'perm_class' => isset($new_class[$parent_id]) ? $new_class[$parent_id] : NULL,
                            'parent_id' => $parent_id,
                            'children_count' => 0,
                            ));

                        if ($parent_id > 0)
                        {
                            $this->db->query("UPDATE perms SET children_count = children_count + 1 WHERE perm_id = ?", array($parent_id));
                        }
                    }
                }
            }

            if ( $ids && is_array($ids) )
            {
                foreach($ids as $perm_id)
                {
                    $name = isset( $perm_name[$perm_id] ) ? $perm_name[$perm_id] : '';
                    if ( !$name ) continue;

                    $this->db->where('perm_id', $perm_id)->update('perms', array('perm_name'=>$name,
                        'perm_path' => isset($perm_path[$perm_id]) ? $perm_path[$perm_id] : '',
                        'public' => isset($public[$perm_id]) ? intval($public[$perm_id]) : 0,
                        'perm_order' => isset($perm_order[$perm_id]) ? intval($perm_order[$perm_id]) : 0,
                        'perm_class' => isset($perm_class[$perm_id]) ? $perm_class[$perm_id] : NULL,
                        ));
                }
            }
        }
        
        redirect(site_url('perms/'));
    }
}

