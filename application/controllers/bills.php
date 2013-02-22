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
 * Description of myinvoice
 *
 * @author ferdhie
 */
class Bills extends CI_Controller 
{
    var $table_fields = array (
    0 => 'invoice_id',
    1 => 'create_date',
    2 => 'due_date',
    3 => 'client_id',
    4 => 'to_name',
    5 => 'to_company',
    6 => 'to_address',
    7 => 'pay_from',
    8 => 'pay_type',
    9 => 'pay_detail',
    10 => 'subtotal',
    11 => 'discount',
    12 => 'discount_percent',
    13 => 'tax',
    14 => 'tax_percent',
    15 => 'total',
    16 => 'pay_total',
    17 => 'pay_date',
    18 => 'status',
    );
    
    function index()
    {
        $this->orca_auth->login_required();
        $this->load->view('bills');
    }
    
    function approve()
    {
        $this->orca_auth->login_required();
        
        if ($this->orca_auth->user->client_id)
        {
            show_error ("Forbidden!", 403);
            return;
        }
        
        $id = $this->input->get_post('id');
        $query = $this->db->get_where('invoices', array('invoice_id'=>$id), 1);
        $invoice = null;
        if ($query->num_rows())
        {
            $invoice = $query->row();
        }
        
        if (!$invoice)
        {
            show_404('Invoice not found!');
        }
        
        $this->db->query("UPDATE invoices SET status = 2 WHERE invoice_id = {$invoice->invoice_id}");
        $this->db->query("UPDATE clients SET active_date = CURDATE(), invoice_status = 0, is_active = 1, sms_count = 0, mail_count = 0 WHERE client_id = {$invoice->client_id}");
        
        if ($invoice->status != 2)
        {
            $admin_email = $this->config->item('admin_email');
            $site_name = $this->config->item('site_name');
            
            $qry = $this->db->get_where('clients',array('client_id' => $invoice->client_id), 1);
            if ($qry->num_rows())
            {
                $client = $qry->row();
                $subject = "[IndoCRM.com] Persetujuan Pembayaran Tagihan #$invoice_id";
                $body = "Pembayaran anda telah kami terima dan quota anda telah diupdate. Detail sbb:\n".
                    "Invoice Number: #{$invoice->invoice_id}\n".
                    "Transfer Date: {$invoice->pay_date}\n".
                    "Amount: {$invoice->pay_total}\n".
                    "BCA: {$invoice->pay_from}\n".
                    "Client: #{$invoice->client_id}\n\n".
                    "Terima Kasih\n".
                    "\n\n--\nPesan ini telah diproduksi dan didistribusikan oleh PT Sinar Media Tiga, Raya Sulfat 96c, Malang, Indonesia 65123 - 0341-406633";

                @mail($client->email, $subject, $body, "From: $admin_email");
            }
        }
        
        echo json_encode(array('success'=>true));
    }
    
    function decline()
    {
        $this->orca_auth->login_required();
        
        if ($this->orca_auth->user->client_id)
        {
            show_error ("Forbidden!", 403);
            return;
        }
        
        $id = $this->input->get_post('id');
        $message = $this->input->get_post('message');
        $query = $this->db->get_where('invoices', array('invoice_id'=>$id), 1);
        $invoice = null;
        if ($query->num_rows())
        {
            $invoice = $query->row();
        }
        
        if (!$invoice)
        {
            show_404('Invoice not found!');
        }
        
        $this->db->query("UPDATE invoices SET status = 0 WHERE invoice_id = {$invoice->invoice_id}");
        $this->db->query("UPDATE clients SET is_active = 0 WHERE client_id = {$invoice->client_id}");
        
        echo json_encode(array('success'=>true));
    }
    
    function confirm()
    {
        $this->orca_auth->login_required();
        $id = $this->input->get_post('id');
        $query = $this->db->get_where('invoices', array('invoice_id'=>$id), 1);
        $invoice = null;
        if ($query->num_rows())
        {
            $invoice = $query->row();
        }
        
        $error = '';
        $success  = false;
        $pay_from = $this->input->post('pay_from');
        $pay_type = $this->input->post('pay_type');
        $pay_total = intval($this->input->post('pay_total'));
        $pay_date = $this->input->post('pay_date');

        if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
        {
            do
            {
                if (!$invoice)
                {
                    $error = "Invoice tidak ada";
                    break;
                }
                
                if (!$pay_from)
                {
                    $error = "Nomer rekening pembayar tidak diisi dengan benar";
                    break;
                }
                
                if (!$pay_total)
                {
                    $error = "Jumlah total pembayaran tidak boleh nol";
                    break;
                }
                
                if (!$pay_total < $invoice->total)
                {
                    $error = "Jumlah total pembayaran kurang dari total pembayaran.";
                    break;
                }
                
                if (!$pay_date)
                {
                    $error = "Masukkan tanggal anda melakukan pembayaran";
                    break;
                }
                
                $this->db->where('invoice_id', $id);
                $this->db->where('client_id', $this->orca_auth->user->client_id);
                $this->db->where('status', 0);
                $this->db->update('invoices', array(
                    'pay_from' => $pay_from, 
                    'pay_type' => 'BCA', 
                    'pay_total' => $pay_total, 
                    'pay_date' => $pay_date,
                    ));
                
                $success = true;
                
                $admin_email = $this->config->item('admin_email');
                $site_name = $this->config->item('site_name');
                
                $subject = "[IndoCRM.com] Konfirmasi Pembayaran Tagihan #$invoice_id";

                $body = "Konfirmasi Transfer\n".
                    "Invoice Number: #{$invoice_id}\n".
                    "Transfer Date: {$pay_date}\n".
                    "Amount: {$pay_total}\n".
                    "BCA: {$pay_from}\n".
                    "ID Client: #{$invoice->client_id}\n".
                    "\n\n--\nPesan ini telah diproduksi dan didistribusikan oleh PT Sinar Media Tiga, Raya Sulfat 96c, Malang, Indonesia 65123 - 0341-406633";
                
                @mail($admin_email, $subject, $body, "From: $admin_email");
            }
            while(0);
        }
        
        $this->load->view('confirm_payment',array('id' => $id,'success'=>$success,'error'=>$error, 'pay_date' => $pay_date, 'pay_total' => $pay_total, 'pay_from' => $pay_from));
    }
    
    function detail()
    {
        $this->orca_auth->login_required();
        $id = $this->input->get_post('id');
        if ($id)
        {
            $query = $this->db->get_where('invoices', array('invoice_id'=>$id), 1);
            if ($query->num_rows())
            {
                $invoice = $query->row();
                $query = $this->db->get_where('invoice_detail', array('invoice_id'=>$id));
                $details = $query->result();
                $this->load->view('invoice_detail',array('invoice'=>$invoice, 'details' =>$details));
                return;
            }
        }
        show_404();
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
        
        $where .= " " . parse_filter2( $filter, $this->table_fields, 'invoices' );
        $totalCount = $this->db->query("SELECT COUNT(*) AS cnt FROM invoices WHERE $where")->row()->cnt;
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
            
            $where .= " ".parse_filter2( $filter, $this->table_fields, 'invoices' );
            $order = parse_sort2($sort, $this->table_fields);
            $query = $this->db->query("SELECT invoices.* FROM invoices WHERE $where $order LIMIT $start,$limit");
            
            if ($query->num_rows() > 0)
            {
                $result['rows'] = $query->result();
            }
        }
        
        echo json_encode($result);
    }
    
    private function _build_query( $query )
    {
        $query = $this->db->escape("%$query%");
        $where = "AND ( invoices.to_name LIKE $query OR invoices.to_address LIKE $query OR invoices.to_company LIKE $query )";
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
            $this->db->where('client_id', $this->orca_auth->user->client_id);
        }
        
        if ($query)
        {
            $query = strtoupper($query);
            $this->_build_query($query);
        }
        
        $result['totalCount'] = $this->db->count_all_results('invoices');
        if ($result['totalCount'] > 0)
        {
            $this->db->where('is_delete', 0);

            if ( $this->orca_auth->user->client_id )
            {
                $this->db->where('customers.client_id', $this->orca_auth->user->client_id);
            }
            $this->_build_query($query);
            $query = $this->db->get( 'invoices', $limit, $start );
            if ($query->num_rows() > 0)
            {
                $result['rows'] = $query->result();
            }
        }
        
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($result);
    }
}

