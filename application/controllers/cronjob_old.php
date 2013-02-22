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
 * Description of cronjob
 *
 * @author ferdhie
 */
class Cronjob extends CI_Controller
{
    var $campaign_ids = array();
    var $start_time = 0;
    var $run_time = 0;
    
    function __construct() 
    {
        parent::__construct();
        
        $this->run_time = 5 * 60;
        set_time_limit($this->run_time);
        
        require_once APPPATH . 'Mail/class.pop3.php';
        require_once APPPATH . 'Mail/class.smtp.php';
        require_once APPPATH . 'Mail/class.phpmailer.php';
        
        $this->start_time = time();
    }
    
    function is_stop()
    {
        usleep(10000);
        return ((time() - $this->start_time) > ($this->run_time - 5)) ? true : false;
    }
    
    function log($s)
    {
        $date = date('Y-m-d H:i:s');
        echo "[$date] $s<br />\n";
    }
    
    function insertinvoice($row)
    {
        $admin_email = $this->config->item('admin_email');
        $site_name = $this->config->item('site_name');
        
        $t = strtotime('+7 days');
        $late = date('Y-m-d 00:00:00', $t);
        $price = 50000;
        
        $insert = array(
            'due_date' => $late,
            'client_id' => $row->client_id,
            'subtotal' => $price,
            'total' => $price,
            'discount' => 0,
            'tax' => 0,
            'to_name' => $row->name,
            'to_company' => $row->name,
            'to_address' => "{$row->address}, {$row->city}\n{$row->state}, {$row->country}, {$row->zip_code}",
            'status' => 0,
        );
        $this->db->insert('invoices', $insert);

        $invoice_id = $this->db->insert_id();

        $month = date('M/y');
        
        $insert = array(
            'invoice_id' => $invoice_id,
            'description' => "Tagihan bulan $month IndoCRM",
            'quantity' => 1,
            'price' => $price,
            'discount' => 0,
            'discount_percent' => 0,
            'tax' => 0,
            'tax_percent' => 0,
            'subtotal' => $price,
            'total' => $price,
            'status' => 0,
        );
        $this->db->insert('invoice_detail', $insert);
        
        $subject = "[IndoCRM.com] Tagihan bulan $month IndoCRM #$invoice_id";
        
        $body = "Halo {$row->name}\n\nBerikut adalah detail tagihan akun IndoCRM anda untuk bulan $month\n\n".
            "Invoice Number: #{$invoice_id}\n".
            "To: {$row->name}\n".
            "Address: {$row->address}, {$row->city}\n{$row->state}, {$row->country}, {$row->zip_code}\n\n".
            "Deskripsi: Tagihan IndoCRM Bulan $month\n".
            "Jumlah Tagihan: Rp. $price,-\n".
            "Due: ".date('d/M/Y', $t).",-\n\n".
            "Pembayaran dapat di transfer ke rekening:".
            "BCA\nan Joko Siswanto\n448.028.3339\n\n".
            "Mohon lakukan pembayaran paling lambat 7 hari setelah e-mail ini dikirimkan.\n\nTerima kasih telah menggunakan IndoCRM".
            "\n\n--\nPesan ini telah diproduksi dan didistribusikan oleh PT Sinar Media Tiga, Raya Sulfat 96c, Malang, Indonesia 65123 - 0341-406633";

        $this->log("Report: $subject<br />\n$body");
        
        @mail( $row->email, $subject, $body, "From: $admin_email\r\nTo: {$row->email}");
    }
    
    function lockmail($row)
    {
        $admin_email = $this->config->item('admin_email');
        $site_name = $this->config->item('site_name');
        
        $subject = "[IndoCRM.com] Notifikasi Non-Aktif Akun IndoCRM";
        
        $body = "Halo {$row->name}\n\nDengan berat hati kami informasikan bawah akun anda atas nama {$row->name} kami non-aktifkan untuk sementara waktu karena alasan administrasi. Untuk informasi lebih jelas mengenai administrasi mohon kunjungi http://www.indocrm.com/dashboard".
            "\n\n--\nPesan ini telah diproduksi dan didistribusikan oleh PT Sinar Media Tiga, Raya Sulfat 96c, Malang, Indonesia 65123 - 0341-406633";

        $this->log("Report: $subject<br />\n$body");
        
        @mail( $row->email, $subject, $body, "From: $admin_email\r\nTo: {$row->email}");
    }
    
    function lockmember()
    {
        $this->log("LOCKING LATE PAY MEMBER!");
        
        //hajar yg belum membayar
        /*
        $sql = "SELECT * FROM clients WHERE active_date IS NOT NULL AND DATE_ADD(active_date, INTERVAL 40 DAY) > CURDATE() AND invoice_status = 1";
        $query = $this->db->query($sql);
        if ($query->num_rows())
        {
            foreach($query->result() as $row)
            {
                if ($this->is_stop())
                {
                    $this->log("time top stop, break!!");
                    exit;
                }
                
                $this->log("disable client {$row->name}");
                $this->db->query("UPDATE clients SET is_active = 0, invoice_status = 2 WHERE client_id = {$row->client_id}");
                $this->lockmail($row);
            }
        }
        
        //member baru
        $sql = "SELECT * FROM clients WHERE active_date IS NULL AND DATE_ADD(DATE(reg_date), INTERVAL 40 DAY) = CURDATE() AND invoice_status = 1";
        $query = $this->db->query($sql);
        if ($query->num_rows())
        {
            foreach($query->result() as $row)
            {
                if ($this->is_stop())
                {
                    $this->log("time top stop, break!!");
                    exit;
                }
                
                $this->log("disable client {$row->name}");
                $this->db->query("UPDATE clients SET is_active = 0, invoice_status = 2 WHERE client_id = {$row->client_id}");
                $this->lockmail($row);
            }
        }
		*/
    }
    
    function geninvoice()
    {
        $this->log("GENERATING INVOICE!");
        //old member
        //Joko Siswanto
        //448.028.3339
        //BCA Cabang KCP Sukun
        
        $sql = "SELECT * FROM clients WHERE active_date IS NOT NULL AND DATE_ADD(active_date, INTERVAL 1 MONTH) = CURDATE() AND invoice_status = 0 AND client_type = 1";
        $query = $this->db->query($sql);
        if ($query->num_rows())
        {
            foreach($query->result() as $row)
            {
                if ($this->is_stop())
                {
                    $this->log("time top stop, break!!");
                    exit;
                }
            
                $this->log("generate invoice for {$row->name}");
                $this->insertinvoice($row);
                
                $this->db->query("UPDATE clients SET invoice_status = 1 WHERE client_id = {$row->client_id}");
            }
        }
        
        //member baru
        /*
        $sql = "SELECT * FROM clients WHERE active_date IS NULL AND (DATE_ADD(DATE(reg_date), INTERVAL 1 MONTH) = CURDATE() OR mail_count > 0 OR sms_count > 0) AND invoice_status = 0";
        $query = $this->db->query($sql);
        if ($query->num_rows())
        {
            foreach($query->result() as $row)
            {
                if ($this->is_stop())
                {
                    $this->log("time top stop, break!!");
                    exit;
                }
                
                $this->log("generate invoice for {$row->name}");
                $this->insertinvoice($row);
                
                $this->db->query("UPDATE clients SET invoice_status = 1 WHERE client_id = {$row->client_id}");
            }
        }
        */
    }
    
    function resetquota()
    {
        $this->log("reset quota e-mail dan sms");
        $this->db->query("UPDATE clients SET mail_free = 50, sms_free = 50");
    }
    
    function resetmonthlyquota()
    {
        //$this->log("reset quota SMS");
        //$this->db->query("UPDATE clients SET sms_count = 0 WHERE is_active = 1");
    }
    
    function is_over_quota($count, $quota, $free)
    {
        if ($free > 0)
        {
            return false;
        }
        
        if ( $quota > 0 && $count >= $quota )
        {
            return true;
        }
        
        return false;
    }
    
    function domail()
    {
        $this->log("do mail!!");
        
        $campaigns = $this->db->query("SELECT * FROM campaign WHERE sent_date IS NOT NULL AND sent_date <= CURDATE() AND is_sent = 1 AND is_delete = 0 AND campaign_type <> 'sms' ORDER BY sent_date");
        if ($campaigns->num_rows() == 0)
        {
            $this->log("Tidak ada campaign yang belum terkirim");
            return;
        }
        
        foreach($campaigns->result() as $campaign)
        {
            if ($this->is_stop())
            {
                $this->log("time top stop, break!!");
                exit;
            }
            
            $this->db->query("UPDATE campaign SET is_sent = 2 WHERE campaign_id = {$campaign->campaign_id}");
            
            $query = $this->db->query("SELECT * FROM clients WHERE client_id = {$campaign->client_id}");
            $client = $query->row();
            if (!$client->is_active)
            {
                $this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, tidak aktif");
                $this->report($campaign, $client, 4, "Pengiriman campaign {$campaign->campaign_title} gagal karena akun anda tidak aktif. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
                continue;
            }

            if ( $this->is_over_quota($client->mail_count, $client->mail_quota, $client->mail_free) )
            {
                $this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, Quota = {$client->mail_count}, over quota");
                $this->report($campaign, $client, 4, "Pengiriman campaign {$campaign->campaign_title} gagal karena akun anda melebihi kuota. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
                continue;
            }

            $query = $this->db->query("SELECT customers.* FROM campaign_details LEFT JOIN customers ON campaign_details.customer_id = customers.customer_id WHERE campaign_details.campaign_id = {$campaign->campaign_id} AND customers.is_delete = 0");
            $campaign_count = $query->num_rows();
            if ($campaign_count == 0)
            {
                $this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, Count = {$campaign_count}, kosong karena tidak ada customer utk dikirim");
                $this->report($campaign, $client, 4, "Pengiriman campaign {$campaign->campaign_title} gagal karena tidak ada customer utk dikirim. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
                continue;
            }

            $customers = $query->result();

            $parser = new template_parser();

            $from_name = $client->name;
            $from_email = $client->email;

            $total_count = 0;

            foreach($customers as $customer)
            {
                if (!$customer->email)
                    continue;
                
                $qx = $this->db->query("SELECT email FROM whitelist_email WHERE email = ? LIMIT 1", array( $customer->email ));
                if ($qx->num_rows())
                {
                    $this->log("Customer {$customer->customer_id}, $to_name, $to_email WHITELISTED, SKIP!");
                }

                $context = $parser->make_context($customer, $campaign, $client);

                $body_html = $parser->parse($campaign->template, $context);
                $body_plain = $parser->parse($campaign->plaintemplate ? $campaign->plaintemplate : html2plain($campaign->template), $context);

                $to_name = $customer->first_name . ' ' . $customer->last_name;
                $to_email = $customer->email;
                if (!$to_email || !$body_html || !$body_plain)
                {
                    $this->log("Customer {$customer->customer_id}, $to_name, $to_email no email, no content, SKIP!");
                    continue;
                }

                $total_count++;

                $this->db->query("INSERT INTO maillog ( from_name, from_email, to_name, to_email, body_html, body_plain, campaign_id, email_number, total_count, customer_id, client_id )
                                VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
                        array( $from_name, $from_email, $to_name, $to_email, $body_html, $body_plain, $campaign->campaign_id, $total_count, 0, $customer->customer_id, $client->client_id ));
            }

            $this->db->query("UPDATE maillog SET total_count = $total_count WHERE campaign_id = {$campaign->campaign_id}");

            $this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, Terproses = {$total_count}, SUKSES!");
        }
    }
    
    function sendbatch()
    {
        $this->log("sendbatch begin");
        
        $campaigns = $this->db->query("SELECT * FROM campaign WHERE sent_date IS NOT NULL AND sent_date <= CURDATE() AND is_sent = 2 AND is_delete = 0  AND campaign_type <> 'sms' ORDER BY sent_date LIMIT 1");
        if ($campaigns->num_rows() == 0)
        {
            $this->log("tidak ada campaign untuk dikirim");
            return;
        }
        
        foreach($campaigns->result() as $campaign)
        {
            if ($this->is_stop())
            {
                $this->log("time top stop, break!!");
                exit;
            }
            
            $this->log("Proses campaign: {$campaign->campaign_id} , {$campaign->campaign_title}");
            
            $query = $this->db->query("SELECT * FROM clients WHERE client_id = {$campaign->client_id}");
            $client = $query->row();
            
            if (!$client->is_active)
            {
                $this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, tidak aktif");
                $this->report($campaign, $client, 4, "Pengiriman campaign {$campaign->campaign_title} gagal karena akun tidak aktif. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
                continue;
            }

            if ($this->is_over_quota($client->mail_count, $client->mail_quota, $client->mail_free))
            {
                $this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, Quota = {$client->mail_count}, over quota");
                $this->report($campaign, $client, 4, "Pengiriman campaign {$campaign->campaign_title} gagal karena akun melebihi quota. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
                continue;
            }
            
            $query = $this->db->query("SELECT * FROM maillog WHERE campaign_id = {$campaign->campaign_id} AND is_sent = 0 ORDER BY email_number");
            if ($query->num_rows() == 0)
            {
                $this->report( $campaign, $client );
                continue;
            }

            $batches = $query->result();

            $mailconfig = null;
            $query = $this->db->get_where('mailconfig', array('client_id'=> $client->client_id), 1);
            if ($query->num_rows() > 0)
            {
                $mailconfig = $query->row();
            }

            if (!$mailconfig || !$mailconfig->host || !$mailconfig->port)
            {
                $this->db->query("UPDATE maillog SET is_success = 0 WHERE campaign_id = {$campaign->campaign_id} AND is_sent = 0");
                $this->report($campaign, $client, 4, "Pengiriman campaign {$campaign->campaign_title} gagal karena akun e-mail tidak disetting. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
                continue;
            }

            $this->log("###BEGIN SMTP LOG<pre>");

            $smtp = new PHPMailer(true);
            $smtp->IsSMTP();
            $smtp->SMTPSecure = $mailconfig->ssl ? 'ssl' : ($mailconfig->tls ? 'tls' : '');
            $smtp->Host = $mailconfig->host;
            $smtp->Port = $mailconfig->port;
            $smtp->Username = $mailconfig->username;
            $smtp->Password = $mailconfig->password;
            $smtp->SMTPAuth = true;
            $smtp->SMTPDebug = 2;
            $smtp->From = $client->email ? $client->email : $mailconfig->username;
            $smtp->FromName = $client->name ? $client->name : $mailconfig->mail_name;
            $smtp->SMTPKeepAlive = true;
            $smtp->XMailer = 'IndoCRM/1.0 (http://www.indocrm.com)';

            $this->log("Set SMTP: {$smtp->Host} : {$smtp->Port}, {$smtp->Username}, {$smtp->SMTPSecure}");
            
            foreach($batches as $batch)
            {
                $success = 0;
                
                $qx = $this->db->query("SELECT email FROM whitelist_email WHERE email = ? LIMIT 1", array( $batch->to_email ));
                if ($qx->num_rows())
                {
                    $this->log("E-Mail {$batch->to_email} WHITELISTED, SKIP!");
                }
                else if ( !$batch->to_email || !$batch->body_html || !$batch->body_plain )
                {
                    $this->log("E-Mail {$batch->to_email} EMPTY MAIL,HTML,BODY, SKIP!");
                }
                else
                {
                    try {
                        $smtp->IsHTML(true);

                        $unsub_url = 'http://www.indocrm.com/unsubscribe?email='.rawurlencode($batch->to_email).'&c='.$client->client_id;
                        
                        $powered = ($client->client_type == 0) ? 'Powered by IndoCRM.com' : '';
                        $signature_html = '<br /><br /><div style="font:11px/1.5 arial,helvetica,sans-serif; text-align:center; color:#888;">Anda menerima e-mail ini karena anda terdaftar dalam salah satu member '.strtoupper($client->name).'. <a href="'.$unsub_url.'">Klik link ini untuk berhenti</a><br /><b>'.$powered.'</b></div>';
                        $signature_plain = "\n\n".'Anda menerima e-mail ini karena anda terdaftar dalam salah satu member '.strtoupper($client->name).'. Silahkan kunjungi '.$unsub_url.' untuk berhenti menerima e-mail'."\n$powered";

                        $smtp->Body = $batch->body_html . $signature_html;
                        $smtp->AltBody = $batch->body_plain . $signature_plain;
                        $smtp->Subject = $campaign->campaign_title;
                        $smtp->AddAddress($batch->to_email, $batch->to_name);
                        $smtp->AddCustomHeader("X-CampaignID: {$batch->log_id}");
                        $smtp->AddCustomHeader("X-CampaignInfoID: {$campaign->campaign_id}");
                        $smtp->AddCustomHeader("X-Report-Abuse: Please report abuse to for this campaign here (http://www.indocrm.com/abuse?id={$batch->log_id})");

                        $this->log("Kirim Email ke : {$batch->to_email} - {$campaign->campaign_title}");
                        if ($smtp->Send()) 
                        {
                            $success = 1;
                            
                            if ( $client->mail_free > 0 )
                            {
                                $this->db->query("UPDATE clients SET mail_free = mail_free - 1 WHERE client_id = {$client->client_id}");
                            }
                            else
                            {
                                $this->db->query("UPDATE clients SET mail_count = mail_count + 1 WHERE client_id = {$client->client_id}");
                            }
                        }
                    } catch (phpmailerException $e) {
                        $success = 0;
                        $this->log("Kirim Email ke : {$batch->to_email} - {$batch->Subject} - GAGAL: " . $e->errorMessage());
                    } catch (Exception $e) {
                        $this->log("Kirim Email ke : {$batch->to_email} - {$batch->Subject} - GAGAL: " . $e->getMessage());
                    }

                    $smtp->ClearAddresses();
                    $smtp->ClearCustomHeaders();
                }
                
                if ($client->mail_free > 0)
                {
                    $client->mail_free--;
                }
                else
                {
                    $client->mail_count++;
                }
				
                $curdate = date('Y-m-d H:i:s');
                $this->db->query("UPDATE maillog SET is_success = {$success}, is_sent = 1, sent_date = '$curdate' WHERE log_id = {$batch->log_id}");

                if ($this->is_over_quota($client->mail_count, $client->mail_quota, $client->mail_free))
                {
                    $this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, Quota = {$client->mail_count}, over quota");
                    $this->report($campaign, $client, 4, "Pengiriman campaign {$campaign->campaign_title} gagal melebihi kuota. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
                    $smtp->SmtpClose();
                    continue 2;
                }

                if ( $batch->email_number >= $batch->total_count )
                {
                    $smtp->SmtpClose();
                    $this->log("###END SMTP LOG</pre>");
                    $this->report( $campaign, $client );
                    continue 2;
                }
            }

            $smtp->SmtpClose();
            $this->log("###END SMTP LOG</pre>");
            $this->report( $campaign, $client );
        }
    }
    
    function report($campaign, $client, $is_sent=3, $log_message='')
    {
        $this->db->query("UPDATE campaign SET is_sent = $is_sent, log_message = ? WHERE campaign_id = {$campaign->campaign_id}", array($log_message));
        
        $admin_email = $this->config->item('admin_email');
        $site_name = $this->config->item('site_name');
        
        $result = $this->db->query("SELECT is_sent, is_success, COUNT(*) AS cnt FROM maillog WHERE campaign_id = {$campaign->campaign_id} GROUP BY is_sent, is_success")->result();
        
        $reportstr = '';
        $total = 0;
        $success = 0;
        $failed = 0;
        $unsent = 0;
        foreach($result as $row)
        {
            if ($row->is_sent)
            {
                if ($row->is_success)
                {
                    $success+=$row->cnt;
                }
                else
                {
                    $failed+=$row->cnt;
                }
            }
            else
            {
                $unsent+=$row->cnt;
            }
            
            $total+=$row->cnt;
        }
        
        $reportstr .= "TOTAL MEMBER: {$total}\n";
        $reportstr .= "SUKSES: {$success}\n";
        $reportstr .= "GAGAL: {$failed}\n";
        $reportstr .= "TIDAK TERKIRIM: {$unsent}\n";
        
        if (!$log_message)
        {
            $log_message = "Campaign {$campaign->campaign_title} telah diproses dengan detail sbb\n$reportstr\n";
        }
        
        $subject = "$site_name: Report Campaign #$campaign->campaign_id \"$campaign->campaign_title\"";
        $body = $log_message.
            "\n\n--\n".
            "Pesan ini telah diproduksi dan didistribusikan oleh PT Sinar Media Tiga, Raya Sulfat 96c, Malang, Indonesia 65123 - 0341-406633";
        
        $this->log("Report: $subject<br />\n$body");
        @mail( $client->email, $subject, $body, "From: $admin_email\r\nTo: {$client->email}");
    }
    
    function dosms()
    {
        $now = date('Y-m-d H:i:s');
        $sql = "SELECT * FROM campaign WHERE sent_date IS NOT NULL AND sent_date <= '$now' AND is_sent = 1 AND is_delete = 0  AND campaign_type = 'sms' ORDER BY sent_date LIMIT 1";
        
        $campaigns = $this->db->query($sql);
        if ($campaigns->num_rows() == 0)
        {
            $this->log("Tidak ada campaign yang belum terkirim");
            return;
        }
        
        foreach($campaigns->result() as $campaign)
        {
            if ($this->is_stop())
            {
                $this->log("time top stop, break!!");
                exit;
            }
            
            $this->db->query("UPDATE campaign SET is_sent = 2 WHERE campaign_id = {$campaign->campaign_id}");

            $query = $this->db->query("SELECT * FROM clients WHERE client_id = {$campaign->client_id}");
            $client = $query->row();

            if (!$client->is_active || !$client->phone)
            {
                $this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, tidak aktif");
                $errorx = "akun anda tidak aktif";
                if (!$client->phone)
                    $errorx .= "nomer telepon profil anda tidak valid";
                $this->smsreport($campaign->campaign_id, 0, 4, "Pengiriman $campaign->campaign_title gagal karena $errorx. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
                continue;
            }

            if ($this->is_over_quota($client->sms_count, $client->sms_quota, $client->sms_free))
            {
                $this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, Quota = {$client->sms_count}, over quota");
                $this->smsreport($campaign->campaign_id, 0, 4, "Pengiriman $campaign->campaign_title gagal karena akun anda melebihi kuota. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
                continue;
            }

            $query = $this->db->query("SELECT customers.* FROM campaign_details LEFT JOIN customers ON campaign_details.customer_id = customers.customer_id WHERE campaign_details.campaign_id = {$campaign->campaign_id} AND customers.is_delete = 0");
            $campaign_count = $query->num_rows();
            if ($campaign_count == 0)
            {
                $this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, Count = {$campaign_count}, kosong karena tidak ada customer utk dikirim");
                $this->smsreport($campaign->campaign_id, 0, 4, "Pengiriman $campaign->campaign_title gagal karena tidak ada customer utk dikirim. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
                continue;
            }

            $customers = $query->result();

            $parser = new template_parser();

            $total_count = 0;
            $actual_count = 0;

            foreach($customers as $customer)
            {
                $to_number = $customer->mobile;
                $context = $parser->make_context($customer, $campaign, $client);
                $body_plain = $parser->parse($campaign->plaintemplate, $context);
                
                if (!$to_number || !preg_match('~^[0-9]+$~', $to_number) || !$body_plain)
                {
                    $this->log("Customer {$customer->customer_id}, $to_number no number or $body_plain empty, SKIP!");
                    continue;
                }
                
                $postfix = "\n\nPengirim: {$client->phone}";
                
                if ($client->client_type == 0)
                {
                    $maxsmslen = 160;
                    $postfix .= "\nSMS GRATIS IndoCRM.com";
                }
                else
                {
                    $maxsmslen = 160*4;
                }
                
                if (strlen($body_plain) + strlen($postfix) > $maxsmslen)
                {
                    $body_plain = substr($body_plain, 0, $maxsmslen-strlen($postfix));
                }
                
                $body_plain .= $postfix;

                if ($maxsmslen > 160)
                {
                    $actual_count+=ceil(strlen($body_plain)/160);
                }
                else
                {
                    $actual_count++;
                }
                
                $total_count++;

                $this->db->query("INSERT INTO smsqueue ( tanggal, number, message ) VALUES ( '$now', ?, ? )", array( $to_number, $body_plain ));
                $queue_id = $this->db->insert_id();

                $this->db->query("INSERT INTO smslog ( to_number, body_plain, campaign_id, sms_number, total_count, customer_id, client_id, queue_id ) VALUES ( ?, ?, ?, ?, ?, ?, ?, ? )", 
                        array( $to_number, $body_plain, $campaign->campaign_id, $total_count, 0, $customer->customer_id, $client->client_id, $queue_id ));

                if ($this->is_over_quota($client->sms_count+$actual_count, $client->sms_quota, $client->sms_free-$actual_count))
                {
                    $sms_free = $client->sms_free;
                    $sms_count = $client->sms_count;
                    if ($client->sms_free > 0)
                    {
                        $sms_free = $client->sms_free - $actual_count;
                    }
                    
                    if ($sms_free < 0)
                    {
                        $sms_count += $sms_free;
                        $sms_free = 0;
                    }
                    
                    $this->db->query("UPDATE smslog SET total_count = $total_count WHERE campaign_id = {$campaign->campaign_id}");
                    $this->db->query("UPDATE clients SET sms_count = $sms_count, sms_free = $sms_free WHERE client_id = {$client->client_id}");
                    $this->log("Stop sending, QUOTA={$client->sms_quota},COUNT={$total_count},SMS_COUNT={$client->sms_count}, out of quota");
                    $this->smsreport($campaign->campaign_id, $actual_count, 4, "Pengiriman $campaign->campaign_title gagal karena akun anda melebihi kuota. Mohon kontak info@indocrm.com untuk informasi lebih lanjut");
                    continue 2;
                }
            }
            
            $sms_free = $client->sms_free;
            $sms_count = $client->sms_count;
            if ($client->sms_free > 0)
            {
                $sms_free = $client->sms_free - $actual_count;
            }

            if ($sms_free < 0)
            {
                $sms_count += $sms_free;
                $sms_free = 0;
            }

            $this->db->query("UPDATE clients SET sms_count = $sms_count, sms_free = $sms_free WHERE client_id = {$client->client_id}");
            $this->db->query("UPDATE smslog SET total_count = $total_count WHERE campaign_id = {$campaign->campaign_id}");
            $this->log("Campaign = {$campaign->campaign_id}, Client = {$client->name}, Terproses = {$total_count}, SUKSES!");
        }
    }
    
    function checksmsstatus()
    {
        $this->log("checksms status");

        $queue_ids = array();
        $batch_limit = 100;
        
        $query = $this->db->query("SELECT * FROM smslog WHERE is_sent = 0");
        foreach($query->result() as $row)
        {
            $queue_ids[] = $row->queue_id;
            if (count($queue_ids) >= $batch_limit)
            {
                $this->update_queue($queue_ids);
                $queue_ids = array();
            }
        }
        
        if (count($queue_ids))
        {
            $this->update_queue($queue_ids);
        }
        
        $qry = $this->db->query("SELECT smslog.campaign_id,smslog.total_count,COUNT(*) AS cnt FROM smslog 
                                LEFT JOIN campaign ON smslog.campaign_id = campaign.campaign_id
                                WHERE smslog.is_sent = 1 AND campaign.is_sent = 2
                                GROUP BY smslog.campaign_id");
        if ($qry->num_rows())
        {
            foreach($qry->result() as $row)
            {
                $this->log( "cek total count untuk Campaign #{$row->campaign_id}, Total={$row->total_count}, Success={$row->cnt}" );
                if ($row->total_count <= $row->cnt)
                {
                    $this->smsreport($row->campaign_id, $row->total_count);
                }
            }
        }
    }
    
    function smsreport($campaign_id, $count, $is_sent=3, $log_message='')
    {
        $admin_email = $this->config->item('admin_email');
        $site_name = $this->config->item('site_name');
        
        $query = $this->db->query("SELECT * FROM campaign WHERE campaign_id = {$campaign_id}");
        if ($query->num_rows() == 0)
        {
            $this->log("Tidak ada campaign yang belum terkirim");
            return;
        }
        
        $campaign = $query->row();
        
        $this->db->query("UPDATE campaign SET is_sent = $is_sent, log_message = ? WHERE campaign_id = {$campaign->campaign_id}", array($log_message));
        $query = $this->db->query("SELECT * FROM clients WHERE client_id = {$campaign->client_id}");
        if ($query->num_rows() == 0)
        {
            $this->log("Client tidak ada");
            return;
        }
        
        $client = $query->row();
        
        if (!$log_message)
        {
            $reportstr = "Total terkirim: $count\n";
            $log_message = "Campaign {$campaign->campaign_title} telah terkirim dengan detail sbb\n$reportstr\n";
        }
        else if ($count)
        {
            $log_message .= "\nTotal terkirim: $count\n";
        }
        
        $subject = "$site_name: Report Campaign #$campaign->campaign_id \"$campaign->campaign_title\"";
        $body = $log_message.
            "\n\n--\n".
            "Pesan ini telah diproduksi dan didistribusikan oleh PT Sinar Media Tiga, Raya Sulfat 96c, Malang, Indonesia 65123 - 0341-406633";

        $this->log("Report: $subject<br />\n$body");
        
        @mail( $client->email, $subject, $body, "From: $admin_email\r\nTo: {$client->email}");
    }
    
    function update_queue( $queue_ids )
    {
        $updated = array();
        $query2 = $this->db->query("SELECT id FROM smsqueue WHERE id IN (".implode(",", $queue_ids).") AND status = 2");
        if ($query2->num_rows() > 0)
        {
            foreach($query2->result() as $row2)
            {
                $updated[] = $row2->id;
            }
            $this->db->query("UPDATE smslog SET is_sent = 1 WHERE queue_id IN ( ".implode(", " , $updated)." )");
        }
        $this->log("updating smslog ".count($queue_ids).", ".count($updated));
    }
}
