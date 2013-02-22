<?php
$page_title = 'INDOCRM API';
$this->load->view('header', array('page_title' => $page_title, 'page_header' => $page_header));
?>
		<div id="content">
			<h1><?php echo $page_title; ?></h1>
			<div class="dialog">
				<p>INDOCRM mempunyai API yang bisa digunakan untuk cross platform.</p>
				<?php if ($privatekey){ ?>
				<p>API KEY Anda: <strong><?php echo $privatekey; ?></strong>
				<p>Klik di <?php echo anchor(base_url().'IndoCRM.API.v.1.02.zip', 'di sini'); ?> untuk mendownload IndoCRM API beserta Help dan dokumentasinya.</p>
				<p>Client ID Anda: <?php echo $this->orca_auth->user->client_id; ?></p>
				<p>User ID Anda: <?php echo $this->orca_auth->user->id; ?></p>
				<p>Batas maksimal Anda menggunakan API per hari adalah 12.000</p>
				<p>API yang telah Anda gunakan hari ini <?php echo $sisa; ?>
				<?php }else{ ?>
					<p>Anda belum mempunyai key API. Untuk mendapatkannya silakan registrasi dulu dengan klik tautan di bawah ini</p>
				<?php echo anchor('api/register?new=true', 'Daftar'); ?>
				<?php } ?>
			 </div>
		</div>
		<div id="sidebar">
			<!-- menu admin -->
			<?php $this->load->view('dashboard_menu'); ?>
		</div>

		<div class="cl"></div>
<?php
$this->load->view('footer');
