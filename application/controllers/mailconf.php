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
 * Description of mailconf
 *
 * @author ferdhie
 */
class MailConf extends CI_Controller 
{
    var $table_fields = array (
        0 => 'client_id',
        1 => 'mail_name',
        2 => 'username',
        3 => 'password',
        4 => 'host',
        5 => 'port',
        6 => 'bcc',
        7 => 'cc',
        8 => 'ssl',
        9 => 'tls',
        10 => 'popauth',
        );
    
    function index()
    {
        $this->orca_auth->login_required();
        $this->load->view('mailconf');
    }
    
    function test()
    {
        $this->orca_auth->login_required();
        
        header("Content-type: application/json; charset=UTF-8");
        
        $result = array('success'=>false);
        
        $host = $this->input->post('host');
        $port = $this->input->post('port');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $ssl = $this->input->post('ssl');
        $tls = $this->input->post('tls');

        require_once APPPATH . 'Mail/class.pop3.php';
        require_once APPPATH . 'Mail/class.smtp.php';
        require_once APPPATH . 'Mail/class.phpmailer.php';
        
        ob_start();
        
        try {
            $smtp = new PHPMailer(true);
            $smtp->IsSMTP();
            $smtp->SMTPSecure = $ssl ? 'ssl' : ($tls ? 'tls' : '');
            $smtp->Host = $host;
            $smtp->Port = $port;
            $smtp->Username = $username;
            $smtp->Password = $password;
            $smtp->SMTPAuth = true;
            $smtp->SMTPDebug = 2;

            $smtp->SmtpConnect();
            if (!$smtp->IsError()) {
                $smtp->SmtpClose();
                $result['success'] = true;
            }
        } catch (phpmailerException $e) {
            $result['error'] = $e->errorMessage();
        } catch (Exception $e) {
            $result['error'] = $e->getMessage();
        }

        $output = ob_get_contents();
        ob_end_clean();
        
        $result['debug'] = $output;
        
        echo json_encode($result);
    }
    
    function info()
    {
        $this->orca_auth->login_required();
        
        $result = array('success'=>false,'data'=>array());
        
        $query = $this->db->get_where('mailconfig', array('client_id'=> $this->orca_auth->user->client_id), 1);
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
        
        foreach( $this->table_fields as $f )
        {
            $$f = $this->input->post($f);
        }
        
        $ssl = $ssl ? 1 : 0;
        $tls = $tls ? 1 : 0;
        $popauth = $popauth ? 1 : 0;

        $port = intval($port);
        
        $this->db->query("INSERT INTO mailconfig ( client_id, mail_name, username, password, host, port, bcc, cc, `ssl`, `tls`, `popauth` )
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                          ON DUPLICATE KEY UPDATE mail_name=?, username=?, password=?, host=?, port=?, bcc=?, cc=?, `ssl`=?, `tls`=?, `popauth`=?",
                        array($this->orca_auth->user->client_id, $mail_name, $username, $password, $host, $port, $bcc, $cc, $ssl, $tls, $popauth,
                            $mail_name, $username, $password, $host, $port, $bcc, $cc, $ssl, $tls, $popauth));
        
        $query = $this->db->get_where('mailconfig', array('client_id'=> $this->orca_auth->user->client_id), 1);
        if ($query->num_rows())
        {
            $client = $query->row();
            $result['data'] = $client;
            $result['success'] = true;
        }
        
        echo json_encode($result);
    }
}

