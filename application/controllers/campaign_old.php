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
 * Description of campaign
 *
 * @author ferdhie
 */
class Campaign extends CI_Controller
{
    var $table_fields = array (
    0 => 'campaign_id',
    1 => 'client_id',
    3 => 'sent_date',
    4 => 'create_date',
    5 => 'campaign_title',
    6 => 'campaign_description',
    7 => 'is_delete',
    8 => 'campaign_source',
    9 => 'campaign_type',
    10 => 'is_sent',
    );
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $this->orca_auth->login_required();
        
        //check adhoc campaign
        if ($this->db->where('client_id', $this->orca_auth->user->client_id)->where('campaign_title', 'adhoc')->count_all_results('campaign') == 0)
        {
            $this->db->insert('campaign', array('campaign_title' => 'adhoc', 'client_id' => $this->orca_auth->user->client_id));
        }
        
        $this->load->view('campaign');
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
        
        $this->db->where('is_delete', 0);
        if ( $this->orca_auth->user->client_id )
        {
            $this->db->where('client_id', $this->orca_auth->user->client_id);
        }
        
        if ($q)
        {
            $this->_build_query($q);
        }
        
        parse_filter( $filter, $this->table_fields, 'campaign' );
        
        $totalCount = $this->db->count_all_results('campaign');
        $result['totalCount'] = $totalCount;
        if ($totalCount > 0)
        {
            $this->db->where('is_delete', 0);
            if ( $this->orca_auth->user->client_id )
            {
                $this->db->where('client_id', $this->orca_auth->user->client_id);
            }
            
            if ($q)
            {
                $this->_build_query($q);
            }
            
            parse_sort($sort, $this->table_fields);
            parse_filter( $filter, $this->table_fields, 'campaign' );
            
            $query = $this->db->get( 'campaign', $limit, $start );
            
            if ($query->num_rows() > 0)
            {
                $result['rows'] = $query->result();
            }
        }
        
        echo json_encode($result);
    }
    
    private function _build_query( $query )
    {
        $this->db->like('campaign_title', $query);
        $this->db->or_like('campaign_description', $query);
    }
    
    public function query()
    {
        $this->orca_auth->login_required();
        
        $result = array('success' => true, 'totalCount' => 0, 'rows' => array());
        
        $page = $this->input->post('page');
        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        $query = $this->input->post('query');
        if (!$limit) $limit = 20;
        
        $this->db->where('is_delete', 0);
        
        if ( $this->orca_auth->user->client_id )
        {
            $this->db->where('client_id', $this->orca_auth->user->client_id);
        }
        
        if ($query)
        {
            $query = strtoupper($query);
            $this->_build_query($query);
            $result['totalCount'] = $this->db->count_all_results('campaign');
            if ($result['totalCount'] > 0)
            {
                $this->db->where('is_delete', 0);

                if ( $this->orca_auth->user->client_id )
                {
                    $this->db->where('client_id', $this->orca_auth->user->client_id);
                }
                $this->_build_query($query);
                $query = $this->db->get( 'campaign', $limit, $start );
                if ($query->num_rows() > 0)
                {
                    $result['rows'] = $query->result();
                }
            }
        }
        
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($result);
    }

    function delete()
    {
        $this->orca_auth->login_required();
        
        $data = array_filter(array_map('intval', $this->input->post('data')));
        if ( $data )
        {
            $this->db->where('client_id', $this->orca_auth->user->client_id)->where('is_delete', 1);
            $this->db->update('campaign', array('is_delete' => 2));
            
            $this->db->where('client_id', $this->orca_auth->user->client_id)->where_in('campaign_id', $data);
            $this->db->update('campaign', array('is_delete' => 1));
            
            $this->db->where_in('campaign_id', $data);
            $this->db->delete('campaign_details');
        }
        
        echo json_encode(array('success' => true));
    }
    
    function getcustomers()
    {
        $this->orca_auth->login_required();
        $campaign_id = $this->input->get_post('campaign_id');
        
        if ($campaign_id)
        {
            $result = array('success' => true, 'rows' => array());
            if ($this->orca_auth->user->client_id)
                $this->db->where('customers.client_id', $this->orca_auth->user->client_id);
            
            $this->db->where('campaign_details.campaign_id', $campaign_id);
            
            $this->db->select('customers.*')->from('customers')->join('campaign_details', 'customers.customer_id = campaign_details.customer_id');
            $query = $this->db->get('');
            
            if ($query->num_rows() > 0)
            {
                $result['rows'] = $query->result();
            }
            header('Content-type: application/json; charset=UTF-8');
            echo json_encode($result);
            return;
        }
        show_404();
    }
    
    function create()
    {
        $this->orca_auth->login_required();
        $id = $this->input->get_post('id');
        $campaign = null;
        if ($id)
        {
            $query = $this->db->get_where('campaign', array('campaign_id'=> $id, 'client_id' => $this->orca_auth->user->client_id), 1);
            if ($query->num_rows())
            {
                $campaign = $query->row();
            }
            
            if (!$campaign)
            {
                show_404();
                return;
            }
        }
        
        $this->load->view('create_campaign', array('campaign' => $campaign));
    }
    
    function plaintext()
    {
        $this->orca_auth->login_required();
        $id = $this->input->get_post('id');
        $txt = $this->input->post('txt');
        $selesai = $this->input->get_post('selesai'); 
        $test = $this->input->get_post('test'); 
        $phone = $this->input->get_post('phone');
        
        $campaign = null;
        if ($id)
        {
            $params = array('campaign_id'=> $id);
            
            if ($this->orca_auth->user->client_id)
            {
                $params['client_id'] = $this->orca_auth->user->client_id;
            }
            
            $query = $this->db->get_where('campaign', $params, 1);
            
            if ($query->num_rows())
            {
                $campaign = $query->row();
                if (!$campaign->plaintemplate && $campaign->campaign_type != 'sms')
                {
                    $campaign->plaintemplate = html2plain($campaign->template);
                    $this->db->where('campaign_id', $campaign->campaign_id)->update('campaign', array('plaintemplate' => $campaign->plaintemplate));
                }
                
                $txt = $this->input->post('txt');
                if ($txt)
                {
                    $this->db->where('campaign_id', $campaign->campaign_id)->update('campaign', array('plaintemplate'=>$txt));
                    flashmsg_set("Template telah disimpan dengan sukses");
                    $campaign->plaintemplate = $txt;
                }
                
                if (!$campaign->plaintemplate && $campaign->campaign_type == 'sms')
                {
                    flashmsg_set("Isi template SMS diperlukan, mohon ulangi kembali");
                    $this->load->view('campaign_plaintemplates', array('campaign' => $campaign));
                    return;
                }
                
                if ($test)
                {
                    if ($campaign->plaintemplate)
                    {
                        $this->sendsms( $campaign );
                        flashmsg_set("SMS test telah dikirim");
                    }
                    else
                    {
                        flashmsg_set("SMS tidak terkirim karena telepon kosong atau SMS kosong");
                    }
                }
                
                if ($selesai)
                {
                    redirect(site_url('campaign/'));
                }
                
                $this->load->view('campaign_plaintemplates', array('campaign' => $campaign));
                return;
            }
        }
        
        show_404();
        return;
    }
    
    function templates()
    {
        $this->orca_auth->login_required();
        $id = $this->input->get_post('id');
        $template_id = $this->input->get_post('template'); 
        $test = $this->input->get_post('test'); 
        $selesai = $this->input->get_post('selesai'); 
        
        $campaign = null;
        if ($id)
        {
            $params = array('campaign_id'=> $id);
            
            if ($this->orca_auth->user->client_id)
            {
                $params['client_id'] = $this->orca_auth->user->client_id;
            }
            
            $query = $this->db->get_where('campaign', $params, 1);
            
            if ($query->num_rows())
            {
                $campaign = $query->row();
                
                if ($template_id && $template_id != $campaign->template_id)
                {
                    $qry = $this->db->get_where('mailtemplates', array('template_id' => $template_id), 1);
                    if ($qry->num_rows() > 0)
                    {
                        $r = $qry->row();
                        $update = array('template_id' => $r->template_id, 'template' => $r->template);
                        $this->db->where('campaign_id', $campaign->campaign_id)->update('campaign', $update);
                        redirect(site_url('campaign/templates?id='.$id));
                    }
                }
                
                if (!$campaign->template_id || !$campaign->template)
                {
                    $client_ids = array(0);
                    if ( $this->orca_auth->user->client_id )
                        $client_ids[] = $this->orca_auth->user->client_id;

                    $this->db->where('is_delete', 0);
                    $this->db->where_in('client_id', $client_ids);
                    $query = $this->db->get('mailtemplates');
                    if ($query->num_rows() > 0)
                    {
                        $tpl = $query->row();
                        $campaign->template_id = $tpl->template_id;
                        $campaign->template = $tpl->template;
                        $update = array('template_id' => $tpl->template_id, 'template' => $tpl->template);
                        $this->db->where('campaign_id', $campaign->campaign_id)->update('campaign', $update);
                    }
                }
                
                $txt = $this->input->post('txt');
                if ($txt)
                {
                    $this->db->where('campaign_id', $campaign->campaign_id)->update('campaign', array('template'=>$txt));
                    flashmsg_set("Template telah disimpan dengan sukses");
                    $campaign->template = $txt;
                }
                
                if ($test)
                {
                    $r = $this->sendmail( $campaign, $this->orca_auth->user->email );
                    if ($r['success'])
                    {
                        flashmsg_set("Kirim e-mail test sukses. Silahkan cek e-mail anda.");
                    }
                    else
                    {
                        flashmsg_set("Kirim e-mail gagal, mohon ulangi kembali.");
                    }
                }
                
                if ($selesai)
                {
                    redirect(site_url('campaign/plaintext?id='.$campaign->campaign_id));
                }
                
                $this->load->view('campaign_templates', array('campaign' => $campaign));
                return;
            }
        }
        
        show_404();
        return;
    }
    
    function sendsms($campaign)
    {
        $this->orca_auth->login_required();
        
        $query = $this->db->get_where('clients', array('client_id'=> $this->orca_auth->user->client_id), 1);
        $client = $query->row();
        
        $customer = new stdClass();
        $customer->first_name = $this->orca_auth->user->username;
        $customer->last_name = '';
        $customer->email = $this->orca_auth->user->email;
        $customer->phone = $client->phone;
        $customer->mobile = $client->phone;
        $customer->website = 'http://www.indocrm.com/';
        
        $phone = $client->phone;
        
        $parser = new template_parser();
        $context = $parser->make_context($customer, $campaign, $client);
        $body_plain = $parser->parse($campaign->plaintemplate, $context);
        
        if (!$phone || !preg_match('~[0-9]+~', $phone) || !$body_plain)
        {
            return false;
        }

        $postfix = "\n\nPengirim: {$client->phone}";
        $maxsmslen = 160*4;
        if (strlen($body_plain) + strlen($postfix) > $maxsmslen)
        {
            $body_plain = substr($body_plain, 0, $maxsmslen-strlen($postfix));
        }

        $body_plain .= $postfix;
        
        $now = date('Y-m-d H:i:s');
        $this->db->query("INSERT INTO smsqueue ( tanggal, number, message ) VALUES ( '$now', ?, ? )", array( $phone, $body_plain ));
        return true;
    }
    
    function sendmail($campaign, $email)
    {
        $this->orca_auth->login_required();
        
        require_once APPPATH . 'Mail/class.smtp.php';
        require_once APPPATH . 'Mail/class.phpmailer.php';
        
        ob_start();
        
        $smtp = new PHPMailer();
        $smtp->From = $email;
        $smtp->FromName = $this->orca_auth->user->username;
        $smtp->XMailer = 'IndoCRM/1.0 (http://www.indocrm.com)';

        $query = $this->db->get_where('mailconfig', array('client_id'=> $this->orca_auth->user->client_id), 1);
        if ($query->num_rows())
        {
            $client = $query->row();
            if ($client->host && $client->port)
            {
                $smtp->SMTPSecure = ($client->ssl ? 'ssl' : ($client->tls ? 'tls' : ''));
                $smtp->Host = $client->host;
                $smtp->Port = $client->port;
                $smtp->Username = $client->username;
                $smtp->Password = $client->password;
                $smtp->SMTPAuth = true;
                $smtp->Mailer = 'smtp';
                $smtp->SMTPDebug = 2;
                
                $smtp->From = $client->username;
                $smtp->FromName = $client->mail_name;
            }
        }
        
        $query = $this->db->get_where('clients', array('client_id'=> $this->orca_auth->user->client_id), 1);
        $cl = $query->row();
        
        $customer = new stdClass();
        $customer->first_name = $smtp->FromName;
        $customer->last_name = '';
        $customer->email = $smtp->From;
        $customer->phone = $cl->phone;
        $customer->website = $cl->website;
        
        $parser = new template_parser();
        $context = $parser->make_context($customer, $campaign, $cl);
        
        $smtp->IsHTML(true);
        $smtp->Body = $parser->parse($campaign->template, $context);
        $smtp->AltBody = $parser->parse($campaign->plaintemplate ? $campaign->plaintemplate : html2plain($campaign->template), $context);
        $smtp->Subject = "[Test Campaign] " . $campaign->campaign_title;
        
        $smtp->AddAddress($email);
        
        if (!$smtp->Send())
        {
            $result['success'] = false;
        }
        else
        {
            $result['success'] = true;
        }
        
        $result['data']= ob_get_contents();
        ob_end_clean();
        
        return $result;
    }
    
    function publish()
    {
        $this->orca_auth->login_required();
        $id = $this->input->get_post('id');
        if ($id)
        {
            $query = $this->db->get_where('campaign', array('campaign_id' => $id), 1);
            if ($query->num_rows())
            {
                $row = $query->row();
                if (( $row->campaign_type == 'sms' && !trim($row->plaintemplate) ) || ( $row->campaign_type != 'sms' && !trim($row->template) ))
                {
                    header('Content-type: application/json; charset=UTF-8');
                    echo json_encode(array('success'=>false));
                    return;
                }
                
                
                $this->db->where('campaign_id', $id)->update('campaign', array('is_sent' => 1));
                header('Content-type: application/json; charset=UTF-8');
                echo json_encode(array('success'=>true));
                return;
            }
        }
        
        show_404();
    }
    
    function save()
    {
        $this->orca_auth->login_required();
        
        $json = $this->input->post('customers');
        $customers = array();
        if ($json)
        {
            $customers = json_decode($json);
            foreach($customers as $k => $cust)
            {
                //security
                if ($this->orca_auth->user->client_id && $cust->client_id != $this->orca_auth->user->client_id)
                    unset($customers[$k]);
            }
        }
        
        $json = $this->input->post('categories');
        $categories = array();
        if ($json)
        {
            $categories = json_decode($json);
            foreach($categories as $k => $cat)
            {
                //security
                if ($this->orca_auth->user->client_id && $cat->client_id != $this->orca_auth->user->client_id)
                    unset($categories[$k]);
            }
        }

        $data = array();
        foreach( $this->table_fields as $field )
        {
            $data[$field] = $this->input->post($field);
        }
        
        if ($this->orca_auth->user->client_id)
            $data['client_id'] = $this->orca_auth->user->client_id;
        else 
            $data['client_id'] = intval($data['client_id']);
        
        //$data['campaign_count'] = intval($data['campaign_count']);
        
        unset($data['create_date']);
        
        if ( !isset($data['campaign_id']) || !$data['campaign_id'] )
        {
            $data['campaign_id'] = null;
            $this->db->insert( 'campaign', $data );
            $data['campaign_id'] = $this->db->insert_id();
        }
        else
        {
            $this->db->where('campaign_id', $data['campaign_id']);
            $this->db->update( 'campaign', $data );
        }
        
        $query = $this->db->get_where( 'campaign', array('campaign_id' => $data['campaign_id']) , 1 );
        $data = $query->row();
        
        $campaign_source = $data->campaign_source;
        $next = null;

        if ( $this->input->get_post('editing') )
        {
            echo json_encode(array('success' => true, 'data' => $data, 'next' => $next));
            return;
        }
        
        if ($campaign_source == 'upload')
        {
            require_once APPPATH . 'controllers/customers.php';
            $customers = new Customers();
            $result = $customers->_doupload();
            if ($result['success'])
            {
                $next = '/customers/uploadresult?campaign='.$data->campaign_id.'&id='.rawurlencode( $result['id'] );
            }
        }
        else if ($campaign_source == 'category')
        {
            $this->db->query("DELETE FROM campaign_details WHERE campaign_id = {$data->campaign_id}");
            $cats = array();
            foreach($categories as $c)
            {
                $cats[] = $this->db->escape($c->category);
            }
            $strCategories = implode(",", $cats);
            
            if ($data->campaign_type == 'sms')
            {
                $this->db->query("INSERT INTO campaign_details ( customer_id, campaign_id ) SELECT customer_id, {$data->campaign_id} AS campaign_id FROM customers WHERE client_id = {$this->orca_auth->user->client_id} AND is_delete = 0 AND category IN ($strCategories) AND mobile IS NOT NULL AND mobile <> '' GROUP BY mobile");
            }
            else
            {
                $this->db->query("INSERT INTO campaign_details ( customer_id, campaign_id ) SELECT customer_id, {$data->campaign_id} AS campaign_id FROM customers WHERE client_id = {$this->orca_auth->user->client_id} AND is_delete = 0 AND category IN ($strCategories) AND email IS NOT NULL AND email <> '' GROUP BY email");
            }
        }
        else if ($campaign_source == 'selected')
        {
            $this->db->query("DELETE FROM campaign_details WHERE campaign_id = {$data->campaign_id}");
            foreach($customers as $customer)
            {
                $this->db->query("INSERT INTO campaign_details ( campaign_id, customer_id ) VALUES (?, ? ) ON DUPLICATE KEY UPDATE customer_id = ?",
                        array( $data->campaign_id, $customer->customer_id, $customer->customer_id ));
            }
        }
        else
        {
            $allcustomer = $this->input->post('allcustomer');
            if ($allcustomer)
            {
                $this->db->query("DELETE FROM campaign_details WHERE campaign_id = {$data->campaign_id}");
                
                if ($data->campaign_type == 'sms')
                {
                    $this->db->query("INSERT INTO campaign_details (customer_id, campaign_id) SELECT customer_id, '{$data->campaign_id}' AS campaign_id FROM customers WHERE client_id = {$this->orca_auth->user->client_id} AND is_delete = 0 AND mobile IS NOT NULL AND mobile <> '' GROUP BY mobile");
                }
                else
                {
                    $this->db->query("INSERT INTO campaign_details (customer_id, campaign_id) SELECT customer_id, '{$data->campaign_id}' AS campaign_id FROM customers WHERE client_id = {$this->orca_auth->user->client_id} AND is_delete = 0 AND email IS NOT NULL AND email <> '' GROUP BY email");
                }
            }
        }
            
        echo json_encode(array('success' => true, 'data' => $data, 'next' => $next));
    }
}

