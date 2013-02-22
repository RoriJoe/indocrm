<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

$company = false;

if ($this->orca_auth->user->client_id) {
	$r = $this->User_model->get_company($this->orca_auth->user->client_id);
	if ($r) $company = $r->name;
} 

if (!$company) $company = 'Admin';


$page_title = 'Welcome ' . $company;
$this->load->view('header', array('page_title' => $page_title));
?>

		<div id="content">
					<h1><?php echo $page_title; ?></h1>
					
					<?php
					
					$this->db->where('client_id', $this->orca_auth->user->client_id)->where('is_delete', 0);
					$customer_count = $this->db->count_all_results("customers");
					
					$query = $this->db->get_where('mailconfig', array('client_id'=> $this->orca_auth->user->client_id), 1);
					$has_config = $query->num_rows() ? true : false;
					
					if ($customer_count == 0): ?>
					
					<div class="dialog">
						<h2>Pertama Kali</h2>
						<p>Selamat datang di Simetri-CRM, untuk memulai, mari menambahkan data pelanggan anda.</p>
						<div class="actionarea">
							<ul>
					
								<?php if (!$has_config): ?>
								<li>
									<div class="msgbox info">
										<p>Anda belum melakukan setting konfigurasi e-mail untuk melakukan pengiriman, mohon lakukan segera</p>
										<p><a href="<?php echo site_url('mailconf'); ?>">Konfigurasi E-Mail</a></p>
									</div>
								</li>
								<?php endif; ?>
								
								<li><a href="<?php echo site_url('customers/upload/'); ?>">Upload informasi Kontak</a></li>
								
								<li><a href="<?php echo site_url('customers/'); ?>">Tambah data Kontak</a></li>
							</ul>
						</div>
					</div>

					<?php else: ?>

						<?php if (!$has_config): ?>
							<div class="msgbox info">
								<p>Anda belum melakukan setting konfigurasi e-mail untuk melakukan pengiriman, mohon lakukan segera</p>
								<p><a href="<?php echo site_url('mailconf'); ?>">Konfigurasi E-Mail</a></p>
							</div>
						<?php endif; ?>
					
					<?php endif; ?>
					
					<h3>Highlight aktivitas anda</h3>
					
					<ul>
					<?php
					
						$qrt = $this->db->query("SELECT COUNT(*) AS cnt FROM customers WHERE customers.client_id = ? AND customers.is_delete = 0", array($this->orca_auth->user->client_id));
						$unproc_cnt = 0;
						if ($qrt->num_rows())
						{
							$unproc = $qrt->row();
							$unproc_cnt = $unproc->cnt;
						}

						echo '<li><div class="msgbox info">Anda mempunyai total <a href="'.site_url('customers').'">'.$customer_count.' kontak</a></div></li>';
					
						$query = $this->db->query("SELECT is_sent, COUNT(*) AS cnt FROM campaign WHERE client_id = {$this->orca_auth->user->client_id} GROUP BY is_sent");
						if ($query->num_rows())
						{
							$sent = 0; 
							$unsent = 0;
							foreach($query->result() as $row)
							{
								if ($row->is_sent)
								{
									$sent = $row->cnt;
								}
								else
								{
									$unsent = $row->cnt;
								}
							}
							
							if ($sent || $unsent)
							{
								echo '<li><div class="msgbox warning">Ada <a href="'.site_url('campaign').'">'.$unsent.' campaigns</a> yang belum terproses dan <a href="'.site_url('campaign').'">'.$sent.' campaigns</a> yang sudah terproses</div></li>';
							}
						}
						
						$cnt = $this->db->query("SELECT COUNT(*) AS cnt FROM client_invoices WHERE status = 0 AND client_id = ?", array($this->orca_auth->user->client_id))->row();
						$outstanding = $cnt->cnt;
						if ($outstanding > 0)
							echo '<li><div class="msgbox warning">Ada <a href="'.site_url('invoices').'">'.$outstanding.' invoice</a> yang belum terbayar</div></li>';
					
					?>
					</ul>

				</div>

		<div id="sidebar">
					<!-- menu admin -->
					<?php $this->load->view('dashboard_menu'); ?>
				</div>

				<div class="cl"></div>

<?php $this->load->view('footer');
