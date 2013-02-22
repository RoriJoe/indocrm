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
 * Description of lupapassword
 *
 * @author ferdhie
 */
class LupaPassword extends CI_Controller
{
    function index()
    {
        $this->load->helper('recaptcha');
        
        $pubkey = $this->config->item('recaptcha_public_key');
        $privkey = $this->config->item('recaptcha_private_key');
        $admin_email = $this->config->item('admin_email');
        $site_name = $this->config->item('site_name');
        $recaptcha_error = null;
        
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
        {
            $recaptcha_response = recaptcha_check_answer($privkey, 
                    $_SERVER['REMOTE_ADDR'], 
                    $_POST['recaptcha_challenge_field'], 
                    $_POST['recaptcha_response_field']);
            
            if (!$recaptcha_response->is_valid)
            {
                $recaptcha_error = $recaptcha_response->error;
            }
            else
            {
                $email = $this->input->post('email');
                if ($email)
                {
                    $query = $this->db->get_where('users', array('email' => $email), 1);
                    if ($query->num_rows() > 0)
                    {
                        $user = $query->row();
                        $confirm_hash = $this->orca_auth->make_hash(uniqid(), '', FALSE);
                        $this->db->where('id', $user->id);
                        $this->db->update('users', array('confirm_hash' => $confirm_hash));

                        $confirm_url = site_url('lupapassword/resetpass?x='.rawurlencode($confirm_hash));

                        $name = $user->name ? $user->name : $user->username;

                        @mail( $user->email, "$site_name: Bantuan Password", "Hi {$name},\n\n".
                            "Kami menerima permintaan untuk me-reset password yang terkait dengan alamat e-mail. Jika Anda yang membuat permintaan ini, silakan ikuti petunjuk di bawah ini.\n\n".
                            "Klik link di bawah untuk mereset password Anda:\n\n".
                            "$confirm_url\n\n".
                            "Jika Anda tidak merasa meminta kami untuk me-reset password Anda abaikan saja email ini. Yakinlah account pelanggan Anda aman.\n\n".
                            "Jika link diatas tidak bisa diklik, Anda dapat menyalin dan menyisipkan link ke jendela alamat browser Anda, atau mengetik ulang di sana. Setelah Anda kembali ke $site_name, kami akan memberikan instruksi untuk mereset password Anda.\n\n".
                                "--\n".
                                "Simetri tidak pernah akan mengirimkan e-mail ke anda Anda dan meminta Anda untuk mengungkapkan atau memverifikasi sandi Simetri, kartu kredit, atau nomor rekening perbankan. Jika Anda menerima email yang mencurigakan dengan link untuk memperbarui informasi account Anda, jangan klik link tersebut - silahlkan melaporkan e-mail ke Simetri untuk penyelidikan. Terima kasih telah mengunjungi SIMETRI!",
                            "From: $admin_email\r\n".
                            "To: {$user->email}");
                        redirect('lupapassword/done');
                    }
                    else
                    {
                        flashmsg_set("Maaf, e-mail tidak ada");
                    }
                }
                else
                {
                    flashmsg_set("Isikan e-mail anda");
                }
            }
        }
        
        $recaptcha = recaptcha_get_html($pubkey, $recaptcha_error);
        
        $this->load->view('lupapassword', array('recaptcha' => $recaptcha));
    }
    
    function done()
    {
        $this->load->view('passworddone');
    }
    
    function resetdone()
    {
        $this->load->view('resetdone');
    }
    
    function resetpass()
    {
        $confirm_hash = $this->input->get_post('x');
        if (!$confirm_hash)
        {
            show_404();
        }
        
        $query = $this->db->get_where('users', array('confirm_hash' => $confirm_hash), 1);
        if ($query->num_rows() == 0)
        {
            show_404();
        }
        
        $user = $query->row();
        
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
        {
            $password = $this->input->post('password');
            $password2 = $this->input->post('password2');
            
            if ($password && $password2 && $password == $password2)
            {
                $password = $this->orca_auth->make_hash($password, '', true);
                $this->db->where('id', $user->id);
                $this->db->update('users', array('password' => $password));
                
                $admin_email = $this->config->item('admin_email');
                $site_name = $this->config->item('site_name');
                
                $name = $user->name ? $user->name : $user->username;
                
                $login_url = site_url('auth/login');
                        
                @mail( $user->email, "$site_name: Ganti Password", "Hi {$name},\n\n".
                    "Password anda telah berubah. Berikut info akun anda.\n\n".
                    "Username: {$user->username}\n".
                    "Password: {$password2}\n".
                    "Silahkan login disini: $login_url\n".
                    "Jika link diatas tidak bisa diklik, Anda dapat menyalin dan menyisipkan link ke jendela alamat browser Anda, atau mengetik ulang di sana. Setelah Anda kembali ke $site_name, kami akan memberikan instruksi untuk mereset password Anda.\n\n".
                    "--\n".
                    "Simetri tidak pernah akan mengirimkan e-mail ke anda Anda dan meminta Anda untuk mengungkapkan atau memverifikasi sandi Simetri, kartu kredit, atau nomor rekening perbankan. Jika Anda menerima email yang mencurigakan dengan link untuk memperbarui informasi account Anda, jangan klik link tersebut - silahlkan melaporkan e-mail ke Simetri untuk penyelidikan. Terima kasih telah mengunjungi SIMETRI!",
                    "From: $admin_email\r\n".
                    "To: {$user->email}");
                
                redirect('lupapassword/resetdone');
            }
            else
            {
                flashmsg_set("Password kosong atau anda tidak memasukkan password dengan cocok, mohon ulangi kembali");
            }
        }
        
        $this->load->view('resetpass', array('user' => $user, 'confirm_hash'=>$confirm_hash));
    }
}

