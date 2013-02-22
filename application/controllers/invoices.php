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
 * Description of invoice
 *
 * @author ferdhie
 */
class Invoices extends CI_Controller 
{
    var $table_fields = array (
        0 => 'invoice_id',
        1 => 'create_date',
        2 => 'due_date',
        4 => 'to_name',
        5 => 'to_company',
        6 => 'to_email',
        7 => 'to_address',
        8 => 'customer_id',
        9 => 'subtotal',
        10 => 'discount',
        11 => 'discount_percent',
        12 => 'tax',
        13 => 'tax_percent',
        14 => 'total',
        15 => 'pay_total',
        16 => 'pay_date',
        17 => 'status',
    );
    
    function index()
    {
        $this->orca_auth->login_required();
        $this->load->view('client_invoices');
    }
    
    function design()
    {
        $this->orca_auth->login_required();
        
        $templates = array();
        $my_templates = array();
        
        $client_ids = array(0);
        if ( $this->orca_auth->user->client_id )
            $client_ids[] = $this->orca_auth->user->client_id;
        
        $this->db->where('is_delete', 0);
        $this->db->where_in('client_id', $client_ids);
        
        $query = $this->db->get('invoice_templates');
        if ($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                if ($row->client_id)
                {
                    $my_templates[] = $row;
                }
                else
                {
                    $templates[] = $row;
                }
            }
        }
        
        $this->load->view('design_invoice', array('my_templates' => $my_templates, 'templates' => $templates));
    }
    
    function create()
    {
        $this->orca_auth->login_required();
        
        $template_id = $this->input->get('id');
        $name = '';
        $template = '';
        $client_id = 0;
        
        if ($template_id)
        {
            $this->db->where_in('client_id', array(0, $this->orca_auth->user->client_id));
            $this->db->where('template_id', $template_id);
            $query = $this->db->get('invoice_templates', 1);
            if ($query->num_rows() > 0)
            {
                $row = $query->row();
                $name = $row->name;
                $template = $row->template;
                $client_id= $row->client_id;
            }
        }
        
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
        {
            $fields = array('template_id', 'name', 'txt');
            foreach($fields as $f) 
                $$f = $this->input->post($f);
            
            $template = $txt;
        
            if ($client_id != $this->orca_auth->user->client_id)
                $template_id = 0;
            
            do
            {
                if (!$name)
                {
                    flashmsg_set('Nama kosong');
                    break;
                }
                
                if (!$txt)
                {
                    flashmsg_set('Desain kosong');
                    break;
                }
                
                $data = array('name' => $name, 'client_id' => $this->orca_auth->user->client_id, 'template' => $txt);
                
                if ($template_id)
                {
                    $this->db->where('template_id',$template_id);
                    $this->db->update('invoice_templates', $data);
                }
                else
                {
                    $this->db->insert('invoice_templates', $data);
                    $template_id = $this->db->insert_id();
                }
                
                $template = $this->db->get_where( 'invoice_templates', array( 'template_id' => $template_id ), 1 )->row();
                $thumbnail = $this->get_thumb($template_id);
                if ($thumbnail)
                    $this->db->where('template_id',$template_id)->update('invoice_templates', array('thumbnail' => $thumbnail));
                
                $template = $txt;
                
                flashmsg_set('Simpan template sukses');
            }
            while(0);
        }

        $this->load->view('create_design_invoice', array('name'=>$name,'template'=>$template,'template_id'=>$template_id));
    }
    
    function gettemplate()
    {
        $id = $this->input->get_post('id');
        if (!$id)
        {
            show_404();
            return;
        }
        
        $client_ids = array(0);
        
        if ( $this->orca_auth->is_logged_in() )
        {
            if ( $this->orca_auth->user->client_id )
                $client_ids[] = $this->orca_auth->user->client_id;
        }
        
        $this->db->where('is_delete', 0);
        $this->db->where_in('client_id', $client_ids);
        $this->db->where('template_id', $id);
        
        $query = $this->db->get('invoice_templates', 1);
        if ($query->num_rows() > 0)
        {
            $row = $query->row();
            echo $row->template;
        }
        else
        {
            show_404();
        }
        
    }
    
    function get_thumb( $template_id )
    {
        $path = FCPATH.'u/';
        
        if (!extension_loaded('curl')) 
            dl('curl.so');
        
        $url = "http://api.thumbalizr.com/?api_key=9baa7d76b70f95dba6dd71f853d21177&url=".rawurlencode(site_url('invoices/gettemplate?id='.$template_id));
        $curl_config = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 60
        );

        $curl = curl_init( $url );
        curl_setopt_array( $curl , $curl_config );
        $response = curl_exec( $curl );
        
        if ($response !== false && strlen($response) != 4243)
        {
            $filename = uniqid($template_id) . '.jpg';
            file_put_contents($path . $filename, $response);
            return $filename;
        }
        
        return false;
    }
    
    function load_detail() 
    {
        $this->orca_auth->login_required();
        $in_no = $this->input->post('invoice_no');
        
        $result = array();
        
        if ($in_no)
        {
            $query = $this->db->select('client_invoices_details.*,client_invoices_details.product_code AS code')->where('invoice_id' ,$in_no)->get('client_invoices_details');
            if ($query->num_rows())
            {
                $result = $query->result(); 
            }
        }
        
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($result);

    }
    
    function querytemplates()
    {
        $this->orca_auth->login_required();
        
        $result = array('success' => true, 'totalCount' => 0, 'rows' => array());
        
        $page = $this->input->post('page');
        $start = $this->input->post('start');
        $limit = $this->input->post('limit');
        $query = $this->input->post('query');
        if (!$limit) $limit = 20;
        
        $this->db->select('template_id,name,is_delete,client_id');
        $this->db->where('is_delete', 0);
        
        if ( $this->orca_auth->user->client_id )
        {
            $this->db->where_in('client_id', array(0,$this->orca_auth->user->client_id));
        }
        
        if ($query)
        {
            $query = strtoupper($query);
            $this->db->like('name', $query);
        }
        
        $query = $this->db->get( 'invoice_templates' );
        if ($query->num_rows() > 0)
        {
            $result['totalCount'] = $query->num_rows();
            $result['rows'] = $query->result();
        }
        
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($result);
    }
    
    function sendemail()
    {
        $this->orca_auth->login_required();
        
        $id = $this->input->get_post('id');

        if ($id)
        {
            $this->db->select('client_invoices.*,clients.name,clients.address,clients.city,clients.state,clients.zip_code,clients.country,clients.phone,clients.email,clients.website,clients.image')
                            ->from('client_invoices')
                            ->join('clients','client_invoices.client_id = clients.client_id','left')
                            ->where('invoice_id',$id);
            
            if ($this->orca_auth->user->client_id)
                $this->db->where('client_invoices.client_id',$this->orca_auth->user->client_id);
            
            $query = $this->db->get();
            
            if ($query->num_rows())
            {
                $invoice = $query->row();
                
                if ($invoice->to_email)
                {
                    $query = $this->db->get_where('client_invoices_details', array('invoice_id'=>$id));
                    $details = $query->result();
                    $template = false;

                    if ($invoice->template_id)
                    {
                        $query2 = $this->db->get_where('invoice_templates', array('template_id' => $invoice->template_id), 1);
                        $template = $query2->row();
                    }

                    $tpl = new invoice_template();
                    $html = '';
                    if ($template)
                    {
                        $html = $template->template;
                    }
                    else
                    {
                        $html = $tpl->default_template();
                    }

                    $client = $this->db->get_where('clients',array('client_id'=> $invoice->client_id), 1)->row();
                    $html = $tpl->parse($html, $invoice, $client, $details);
                    $plain = $tpl->parse($tpl->plain_template(), $invoice, $client, $details);

                    require_once APPPATH . 'Mail/class.smtp.php';
                    require_once APPPATH . 'Mail/class.phpmailer.php';

                    ob_start();

                    $smtp = new PHPMailer();
                    $smtp->From = $client->email;
                    $smtp->FromName = $client->name;
                    $smtp->XMailer = 'IndoCRM/1.0 (http://www.indocrm.com)';

                    $query = $this->db->get_where('mailconfig', array('client_id'=> $this->orca_auth->user->client_id), 1);
                    if ($query->num_rows())
                    {
                        $mailconfig = $query->row();
                        if ($mailconfig->host && $mailconfig->port)
                        {
                            $smtp->SMTPSecure = ($mailconfig->ssl ? 'ssl' : ($mailconfig->tls ? 'tls' : ''));
                            $smtp->Host = $mailconfig->host;
                            $smtp->Port = $mailconfig->port;
                            $smtp->Username = $mailconfig->username;
                            $smtp->Password = $mailconfig->password;
                            $smtp->SMTPAuth = true;
                            $smtp->Mailer = 'smtp';
                            $smtp->SMTPDebug = 2;

                            $smtp->From = $mailconfig->username;
                            $smtp->FromName = $mailconfig->mail_name;
                        }
                    }

                    $smtp->IsHTML(true);
                    $smtp->Body = $html;
                    $smtp->AltBody = $plain;
                    $smtp->Subject = "Invoice #{$invoice->invoice_id} dari {$client->name}";

                    $smtp->AddAddress($invoice->to_email);

                    if (!$smtp->Send())
                    {
                        $result['success'] = false;
                    }
                    else
                    {
                        $result['success'] = true;
                    }

                    $result['data']= ob_get_contents();
                    $result['email'] = $invoice->to_email;
                    
                    ob_end_clean();
                    
                    echo json_encode($result);

                    return;
                }
            }
            
        }
        
        show_404();
    }
    
    function detail()
    {
        $this->orca_auth->login_required();
        $id = $this->input->get_post('id');
        $t = $this->input->get_post('t');
        
        if ($id)
        {
            $template = null;
            
            $this->db->select('client_invoices.*,clients.name,clients.address,clients.city,clients.state,clients.zip_code,clients.country,clients.phone,clients.email,clients.website,clients.image')
                            ->from('client_invoices')
                            ->join('clients','client_invoices.client_id = clients.client_id','left')
                            ->where('invoice_id',$id);
            
            if ($this->orca_auth->user->client_id)
                $this->db->where('client_invoices.client_id',$this->orca_auth->user->client_id);
            
            $query = $this->db->get();
            
            if ($query->num_rows())
            {
                if ($t)
                {
                    $query2 = $this->db->get_where('invoice_templates', array('template_id' => $t), 1);
                    if ($query2->num_rows())
                    {
                        $template = $query2->row();
                        $this->db->where('invoice_id', $id)->update( 'client_invoices', array('template_id' => $t) );
                    }
                }
                
                $invoice = $query->row();
                
                $query = $this->db->get_where('client_invoices_details', array('invoice_id'=>$id));
                $details = $query->result();
                
                $tpl = new invoice_template();
                $html = '';
                if ($template)
                {
                    $html = $template->template;
                }
                else
                {
                    $html = $tpl->default_template();
                }
                
                $client = $this->db->get_where('clients',array('client_id'=> $invoice->client_id), 1)->row();
                
                $html = $tpl->parse($html, $invoice, $client, $details);
                
                $this->load->view('client_invoice_detail',array('invoice_html' => $html, 'invoice' => $invoice));
                
                return;
            }
            
        }
        
        show_404();
    }
    
    public function save()
    {
        $this->orca_auth->login_required();
        
        header('Content-type: application/json; charset=UTF-8');
        
        $tdetail = array();
        $product = array();
        $fields = array('discount','due_date','invoice_date','invoice_id', 
            'jparam','subtotal','tax','total','to_address','to_email','to_name', 
            'customer_id', 'pay_date', 'pay_total');
        $invo_def = "[Invoice Baru]";
        $result = array('success'=>false);
        
        foreach($fields as $k)
        {
            $$k = $this->input->post($k);
        }
        
        $is_new = false;
            
        if ($invoice_id == $invo_def || !$invoice_id)
        {
            $is_new = true;
            $invoice_id = auto_code('INV-'.$this->orca_auth->user->client_id);
        }
        else
        {
            $this->db->query("DELETE FROM client_invoices_details WHERE invoice_id = ?", array($invoice_id));
        }
            
        $tdetail = json_decode($jparam);
        
        if (count($tdetail) > 0)
        {
            for ($i=0,$len=count($tdetail); $i<$len; $i++)
            {
                if($tdetail[$i]->{'product_id'} == 0)          
                { 
                    $product = array('product_code' => $tdetail[$i]->{'code'},
                                    'product_name' => $tdetail[$i]->{'description'},
                                    'product_price' => $tdetail[$i]->{'price'},
                                    'discount' => $tdetail[$i]->{'discount_percent'},
                                    'tax' => $tdetail[$i]->{'tax_percent'},
                                    'client_id' => $this->orca_auth->user->client_id);
                    $this->db->insert('products',$product);
                    $getID = $this->db->get_where('products',array('product_code' => $tdetail[$i]->{'code'}, 'client_id' => $this->orca_auth->user->client_id))->row();
                    $tdetail[$i]->{'product_id'} = $getID->product_id;
                }
                
                $detail = array('invoice_id' => $invoice_id,
                                'description' => $tdetail[$i]->{'description'},
                                'quantity' => $tdetail[$i]->{'quantity'},
                                'price' => $tdetail[$i]->{'price'},
                                'discount' => $tdetail[$i]->{'discount'},
                                'discount_percent' => $tdetail[$i]->{'discount_percent'},
                                'tax' => $tdetail[$i]->{'tax'},
                                'tax_percent' => $tdetail[$i]->{'tax_percent'},
                                'subtotal' => $tdetail[$i]->{'subtotal'},
                                'total' => $tdetail[$i]->{'total'},
                                'status' => 0,
                                'product_id' => $tdetail[$i]->{'product_id'},
                                'product_code' => $tdetail[$i]->{'code'}    
                                );
                $this->db->insert('client_invoices_details',$detail);                
            }
            
            if (!$customer_id)
            {
                $customer = array('first_name' => $to_name,
                                  'address' => $to_address,
                                  'email' => $to_email,
                                  'client_id' => $this->orca_auth->user->client_id);
                $this->db->insert('customers',$customer);
                $query = $this->db->get_where('customers',$customer)->row();                  
            }
            
            $status = 0;
            if ($pay_total >= $total )
                $status = 1;
            
            if ($is_new)
            {
                $grand = array('invoice_id' => $invoice_id,
                            'create_date' => $invoice_date,
                            'due_date' => $due_date,
                            'client_id' => $this->orca_auth->user->client_id,
                            'to_name' => $to_name,
                            'to_email' => $to_email,
                            'to_address' => $to_address,
                            'customer_id' => (!$customer_id) ? $query->customer_id : $customer_id,
                            'subtotal' => $subtotal,
                            'discount' => $discount,
                            'tax' => $tax,
                            'total' => $total,
                            'pay_total' => $pay_total,
                            'pay_date' => $pay_date ? $pay_date : null,
                            'status' => $status);
                $this->db->insert('client_invoices', $grand);
            }
            else
            {
                $grand = array(
                            'due_date' => $due_date,
                            'to_name' => $to_name,
                            'to_email' => $to_email,
                            'to_address' => $to_address,
                            'customer_id' => (!$customer_id) ? $query->customer_id : $customer_id,
                            'subtotal' => $subtotal,
                            'discount' => $discount,
                            'tax' => $tax,
                            'total' => $total,
                            'pay_total' => $pay_total,
                            'pay_date' => $pay_date ? $pay_date : null,
                            'status' => $status);
                
                $this->db->where('invoice_id', $invoice_id);
                if ($this->orca_auth->user->client_id)
                    $this->db->where('client_id', $this->orca_auth->user->client_id);
                $this->db->update('client_invoices', $grand);
            }
    
            $result['success'] = true;
            $result['invoice_id'] = $invoice_id;
        }
            
        echo json_encode($result);
    }
    
    public function delete()
	{
		$this->orca_auth->login_required();
		
		$data = $this->input->post('data');
		
		if ( $data )
		{
			$this->db->where('invoice_id', $data[0]);
			$this->db->delete('client_invoices_details');
			
			$this->db->where('invoice_id', $data[0]);
			$this->db->delete('client_invoices');
			echo json_encode(array('success' => true));
		}else{
			echo json_encode(array('success' => false));
		}
		
		
	}
    
    function all()
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
        
        $where = '1=1';
        if ( $this->orca_auth->user->client_id )
        {
            $where .= " AND client_id = ".$this->orca_auth->user->client_id . " ";
        }
        
        if ($q)
        {
            $where .= $this->_build_query($q);
        }
        
        $where .= " " . parse_filter2( $filter, $this->table_fields, 'client_invoices' );
        $totalCount = $this->db->query("SELECT COUNT(*) AS cnt FROM client_invoices WHERE $where")->row()->cnt;
        $result['totalCount'] = $totalCount;
        if ($totalCount > 0)
        {
            $where = '1=1';
            if ( $this->orca_auth->user->client_id )
            {
                $where .= " AND client_id = ".$this->orca_auth->user->client_id . " ";
            }

            if ($q)
            {
                $where .= $this->_build_query($q);
            }
            
            $where .= " ".parse_filter2( $filter, $this->table_fields, 'client_invoices' );
            $order = parse_sort2($sort, $this->table_fields);
            $query = $this->db->query("SELECT client_invoices.* FROM client_invoices WHERE $where $order LIMIT $start,$limit");
            
            if ($query->num_rows() > 0)
            {
                $result['rows'] = $query->result();
            }
        }
        
        echo json_encode($result);
    }
    
    private function _build_query( $query )
    {
        $q = intval($query);
        $query = $this->db->escape("%$query%");
        $where = "AND ( client_invoices.invoice_id = $q OR client_invoices.to_name LIKE $query OR client_invoices.to_address LIKE $query OR client_invoices.to_company LIKE $query )";
        return $where;
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
            $this->db->where('client_invoices.client_id', $this->orca_auth->user->client_id);
        }
        
        if ($query)
        {
            $query = strtoupper($query);
            $this->_build_query($query);
        }
        
        $result['totalCount'] = $this->db->count_all_results('client_invoices');
        if ($result['totalCount'] > 0)
        {
            $this->db->where('is_delete', 0);

            if ( $this->orca_auth->user->client_id )
            {
                $this->db->where('client_invoices.client_id', $this->orca_auth->user->client_id);
            }
            $this->_build_query($query);
            $query = $this->db->get( 'client_invoices', $limit, $start );
            if ($query->num_rows() > 0)
            {
                $result['rows'] = $query->result();
            }
        }
        
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($result);
    }
}

