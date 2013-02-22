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
 * Description of unsubscribe
 *
 * @author ferdhie
 */
class Unsubscribe extends CI_Controller 
{
    function index()
    {
        $client_id = $this->input->get_post('c'); 
        $email = $this->input->get_post('email'); 
        $form = false;
        
        if ( $email )
        {
             $client_id = intval($client_id);
            
            $this->db->query("INSERT INTO whitelist_email (email, client_id, ip_address) VALUES (?,?,?)
                    ON DUPLICATE KEY UPDATE client_id = ?, ip_address = ?",
                    array($email, $client_id, $_SERVER['REMOTE_ADDR'], $client_id, $_SERVER['REMOTE_ADDR']));
            flashmsg_set("Alamat e-mail $email telah ditambahkan di halaman whitelist kami, jadi sistem kami tidak akan mengirimkan e-mail lagi ke e-mail anda. Mohon maaf atas ketidaknyamannya.");
        }
        else
        {
            $form = true;
        }
        
        $this->load->view('unsub', array('client_id' => $client_id, 'email' => $email, 'form'=>$form));
    }
}

