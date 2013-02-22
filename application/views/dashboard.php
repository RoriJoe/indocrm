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
					
					$this->db->limit(1);
					$this->db->select('client_type');
					$this->db->where('client_id', $this->orca_auth->user->client_id);
					$q = $this->db->get('clients');
					$res = $q->result();
					
					if ($res[0]->client_type == -1): ?>
					<div class="dialog">
						<h2>SELAMAT DATANG DI INDOCRM</h2>
						<p>Anda sekarang ini berada di Paket <span style="color:red;font-weight:bold;">FREE</span>. Anda hanya punya kapasitas <span style="font-weight:bold;text-decoration:underline;">Quota 50 Free SMS</span>. Kami memberikan penawaran menarik bagi Anda untuk mengikuti Paket <strong>PERSONAL</strong>, <strong>PROFESSIONAL</strong> atau <strong>CORPORATE</strong>.</p>
						<p>Untuk Upgrade, silakan transfer uang sebesar Rp. 100.000,- untuk paket Personal, Rp. 250.000,- untuk paket Professional dan Rp. 500.000,- untuk paket CORPORATE ke BCA .an Joko Siswanto 448.028.3339 kemudian kirim email ke ferdhie @ simetri.web.id atau ke joko @ simetri.web.id (email tanpa spasi).</p>
					</div>
					<div class="msgbox info">
						<h3>Apa saja Keuntungannya?</h3>
						<ul>
							<li>Manajemen Kontak</li>
							<li>Manajemen Groups</li>
							<li>Mengirim broadcast Email</li>
							<li>Menambahkan user baru untuk Anda</li>
							<li>Dan masih banyak yang lainnya</li>
						</ul>
					</div>
					<?php
					else:

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
<?php endif; ?>
				</div>
				

		<div id="sidebar">
					<!-- menu admin -->
					<?php $this->load->view('dashboard_menu'); ?>
				</div>

				<div class="cl"></div>

<?php $this->load->view('footer');
