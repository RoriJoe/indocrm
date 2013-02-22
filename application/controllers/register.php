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
 * Description of register
 *
 * @author ferdhie
 */
error_reporting(E_ALL);

class Register extends CI_Controller 
{
    function __construct() 
    {
        parent::__construct();
        if (!isset($_SESSION)) session_start();
    }
    
    function index()
    {
        $this->load->library('form_validation');
        $this->load->helper('form');
        
        $this->load->helper('recaptcha');
        
        $pubkey = $this->config->item('recaptcha_public_key');
        $privkey = $this->config->item('recaptcha_private_key');
        $admin_email = $this->config->item('admin_email');
        $site_name = $this->config->item('site_name');
        $recaptcha_error = false;
        
        ob_start();
        include FCPATH . '/simple-php-captcha/simple-php-captcha.php';
        ob_get_clean();
        
        $config = array(
               array(
                     'field'   => 'username', 
                     'label'   => 'Username', 
                     'rules'   => 'trim|required|alpha_dash|callback_check_user|is_unique[users.username]|xss_clean'
                  ),
               array(
                     'field'   => 'password', 
                     'label'   => 'Password', 
                     'rules'   => 'trim|required|matches[password2]'
                  ),
               array(
                     'field'   => 'password2', 
                     'label'   => 'Konfirmasi Password', 
                     'rules'   => 'trim|required'
                  ),   
               array(
                     'field'   => 'email', 
                     'label'   => 'Email', 
                     'rules'   => 'trim|required|valid_email|is_unique[users.email]'
                  ),
               array(
                     'field'   => 'company', 
                     'label'   => 'Nama Bisnis', 
                     'rules'   => 'trim|required'
                  ),
               array(
                     'field'   => 'address', 
                     'label'   => 'Alamat Kantor', 
                     'rules'   => 'trim|required'
                  ),
               array(
                     'field'   => 'city', 
                     'label'   => 'Kota', 
                     'rules'   => 'trim'
                  ),
               array(
                     'field'   => 'state', 
                     'label'   => 'Propinsi', 
                     'rules'   => 'trim'
                  ),
               array(
                     'field'   => 'country', 
                     'label'   => 'Negara', 
                     'rules'   => 'trim|required'
                  ),
               array(
                     'field'   => 'zip_code', 
                     'label'   => 'Kode Pos', 
                     'rules'   => 'trim'
                  ),
               array(
                     'field'   => 'phone', 
                     'label'   => 'Telepon', 
                     'rules'   => 'trim|required'
                  ),
               array(
                     'field'   => 'recaptcha_challenge_field', 
                     'label'   => 'Gambar Teks', 
                     'rules'   => 'trim|required'
                  ),
               array(
                     'field'   => 'recaptcha_response_field', 
                     'label'   => 'Gambar Teks', 
                     'rules'   => 'trim'
                  ),
            );
        
        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        
        $frontpage = $this->input->post('frontpage');

        if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
        {
            if ($this->form_validation->run())
            {
                if (!$frontpage)
                {
                    $captcha_code = isset($_SESSION['captcha']['code']) ? $_SESSION['captcha']['code'] : '';
                    if ($captcha_code != $this->input->post('recaptcha_challenge_field'))
                    {
                        $recaptcha_error = 'Maaf, teks tidak sama dengan gambar, mohon ulangi kembali';
                    }
                    /*
                    $recaptcha_response = recaptcha_check_answer($privkey, 
                            $_SERVER['REMOTE_ADDR'], 
                            $_POST['recaptcha_challenge_field'], 
                            $_POST['recaptcha_response_field']);

                    if (!$recaptcha_response->is_valid )
                    {
                        $recaptcha_error = $recaptcha_response->error;
                    }*/
                }

                if (!$recaptcha_error)
                {
                    $fields = array('username','password','email','company','address', 'phone', 'city','state', 'zip_code', 'country', 'client_type');
                    foreach($fields as $f)
                        $$f = $this->input->post($f);

                    $password = $this->orca_auth->make_hash( $password, '', true );
                    $confirm_hash = $this->orca_auth->make_hash( uniqid(mt_rand()), '', true );
                    
                    $mail_quota = $this->config->item('first_mail_quota');
                    $sms_quota = $this->config->item('first_sms_quota');

                    $query = $this->db->get_where('countries', array('Code'=> $country), 1);
                    if ($query->num_rows()) 
                        $country = $query->row()->Country;

                    $this->db->insert('clients', array(
                        'name'=>$company, 
                        'email'=>$email, 
                        'mail_quota' => $mail_quota, 
                        'sms_quota' => $sms_quota, 
                        'is_active' => 1,
                        'country' => $country,
                        'address' => $address,
                        'city' => $city,
                        'state' => $state,
                        'zip_code' => $zip_code,
                        'phone' => $phone,
                        'sms_free' => $sms_quota,
                        'mail_free' => $mail_quota,
                        'client_type' => $client_type,
                        ));
                    $client_id = $this->db->insert_id();
                    
                    $this->db->insert('users', array(
                        'username'=>$username,
                        'password'=>$password,
                        'email'=>$email,
                        'confirm_hash'=>$confirm_hash,
                        'client_id'=>$client_id,
                        'timezone' => '7',
                        'timezone_id' => 'Asia/Jakarta'
                        ));
                    $user_id = $this->db->insert_id();
                    
                    if ($client_type >= 0){
						$client = $this->db->get_where('groups', array('group_name' => 'clients'), 1)->row();
						
						$this->db->insert('user_groups', array('user_id'=>$user_id,'group_id'=>$client->group_id));
					}else{
						$client = $this->db->get_where('groups', array('group_name' => 'free'), 1)->row();
						
						$this->db->insert('user_groups', array('user_id'=>$user_id,'group_id'=>$client->group_id));
					}

                    //kirim konfirmasi email
                    $confirm_url = site_url('register/confirm?x='.rawurlencode($confirm_hash));

					$strclient = "";
					switch ($client_type){
						case -1: 
							$strclient = "Anda telah mendaftar FREE user. Anda hanya mendapatkan quota 50 SMS perhari. Kami menawarkan Anda untuk mendaftar paket Personal, Professional atau Corporate untuk mendapatkan service yang lebih baik. Termasuk broadcast SMS ke banyak nomor.";
						case 0:
							$strclient = "Anda telah mendaftar untuk paket Personal dengan biaya Rp. 100.000,- per bulan. Maka Anda akan mendapatkan quota sebesar 1.000 SMS dan 500 Email.";
							break;
						case 1:
							$strclient = "Anda telah mendaftar untuk paket Professional dengan biaya Rp. 250.000,- per bulan. Maka Anda akan mendapatkan quota sebesar 1.000 SMS dan 500 Email perbulan. Plus Anda bisa mengatur signature Anda sendiri.";
							break;
						case 2:
							$strclient = "Anda telah mendaftar untuk paket Corporate dengan biaya Rp. 500.000,- per bulan. Maka Anda akan mendapatkan quota SMS tak terbatas. Anda juga bisa mengatur signature pada setiap pesan SMS Anda. Dan Anda pun bisa menggunakan nomor Anda sendiri";
							break;
					}
					
					if ($client_type >= 0){
						$pesanclient = "Untuk mengaktifkan akun, silakan Anda melakukan pembayaran ke BCA\nan Joko Siswanto\n448.028.3339\n\n".
                        "Jangan lupa untuk menghubungi kami setelah Anda melakukan pembayaran ke email: ferdhie@simetri.web.id atau ke joko@simetri.web.id dengan Subject [KONFIRMASI PEMBAYARAN INDOCRM atas Nama ".$company." dengan username ".$username."]";
					}else{
						$pesanclient = "Mohon kunjungi alamat dibawah untuk mengkonfirmasikan account anda dan mulai menggunakan Simetri CRM\n\n$confirm_url\n\n";
					}
					
					
                    @mail( $email, "$site_name: Konfirmasi Pendaftaran", "Salam dari Simetri CRM,\n\n".
                        /*"Terima kasih untuk mendaftar. Sekarang Anda dapat mulai menggunakan SimetriCRM. Anda tidak akan dikenakan biaya sampai Anda mulai menggunakan layanan - dan Anda hanya akan membayar untuk apa yang Anda gunakan.\n\n".*/
     /*                   "Mohon kunjungi alamat dibawah untuk mengkonfirmasikan account anda dan mulai menggunakan Simetri CRM\n\n".
                        "$confirm_url\n\n".*/
                        "Terima kasih telah mendaftar.".
						"\n\n".
						$strclient."\n\n".
                        $pesanclient.
                        "Terima kasih atas partisipasi anda\n\n".
                        "Hormat kami,\n\n".
                        "Simetri CRM\n\n".
                        "--\n".
                        "Pesan ini telah diproduksi dan didistribusikan oleh PT Sinar Media Tiga, Raya Sulfat 96c, Malang, Indonesia 65123 - 0341-406633",
                        "From: $admin_email\r\n".
                        "To: $username <{$email}>");


					list($y,$m,$d) = explode('-', date('Y-m-d'));
					$due_date = date('Y-m-d', mktime(0,0,0,$m,$d+7,$y));
                    //save tagihan  
					$this->db->insert('tagihan_client', array(
							'client_id' => $client_id,
							'paket_id' => ($client_type+1),
							'due_date' => $due_date,
							'paid' => 0
							));
                    //registrasi sukses

                    redirect(site_url('register/success'));
                    return;
                }
            }
        }

        $_SESSION['captcha'] = captcha();
        
        $err = ($recaptcha_error) ? '<div class="error">Maaf, kata yg anda masukkan tidak cocok</div>' : '';
        

        //$recaptcha = recaptcha_get_html($pubkey, $recaptcha_error);
        $recaptcha = '
            <label for="id_captcha">Validasi</label>
            <div style="float:left;">
                <img src="'.$_SESSION['captcha']['image_src'].'" alt="jawab"><br />
                <small>Masukkan teks yg tertera di gambar diatas</small></br />
                <input type="text" id="id_captcha" name="recaptcha_challenge_field" value="" />
                '.$err.'
            </div>
            <br class="cl" />
        ';
        
        $this->load->view('register', array('recaptcha' => $recaptcha));
    }
    
    function confirm()
    {
        $confirm_hash = $this->input->get('x');
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
        
        $this->db->where('id', $user->id);
        $this->db->update('users', array('is_confirmed' => 1));
        $user->is_confirmed = 1;
        $this->orca_auth->set_login($user);

        redirect(site_url('dashboard/'));
    }
    
    function success()
    {
        $this->load->view('registersuccess');
    }
    
    function check_user($str)
    {
        $invalid_names = array_flip(array( 'root', 'admin', 'toor', 'ftp', 'www', 'www-data', 'nobody', 'postfix', 'postmaster', 'mail' ));
        if (isset($invalid_names[strtolower($str)])) return false;
        return $str;
    }
    
}

