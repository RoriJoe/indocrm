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
 * Description of abuse
 *
 * @author ferdhie
 */
class Abuse extends CI_Controller 
{
    function index()
    {
        $mail_id = $this->input->get_post('id'); 
        $this->load->view('abuse', array('mail_id' => $mail_id));
    }
    
    function report()
    {
        $mail_id = $this->input->get_post('id'); 
        $name = $this->input->get_post('name'); 
        $email = $this->input->get_post('email'); 
        $reason = $this->input->get_post('reason');
        if ($mail_id)
        {
            $exists = $this->db->get_where('abuse_report', array('mail_id' => $mail_id), 1)->num_rows();
            if (!$exists)
                $this->db->insert('abuse_report', array('mail_id' => $mail_id, 'status' => 0, 'email' => $email, 'name' => $name, 'reason' => $reason));
            flashmsg_set("Laporan anda sudah kami terima dan akan kami proses segera. Mohon maaf atas ketidaknyamanannya. Kami akan hubungi anda untuk perkembangan selanjutnya.");
            $this->load->view('abuse_reported');
            return;
        }
        
        $this->load->view('abuse', array('mail_id' => $mail_id, 'name' => $name, 'email' => $email, 'reason' => $reason));
    }
    
    function campaignid()
    {
        $this->load->view('campaignid');
    }
}
