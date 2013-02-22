<?php
$page_title = 'Mobile Pengirim Terbanyak';
$this->load->view('header', array('page_title' => $page_title, 'page_header' => $page_header));
?>
	<div id="content">
		<h1><?php echo $page_title; ?></h1>
		<div class="dialog">
			<p>Jumlah Mobile Terbanyak Polling <?php echo $judul_polling; ?></p>
			<p style="color:red;font-weight:bold;"><?php echo $err; ?></p>
		</div>
		<?php
			if ($result){ ?>
			<table class="list-table" border="1">
				<tr>
					<th>Mobile</th>
					<th>Jumlah</th>
				</tr>
			<?php
			foreach($result as $row){ ?>
				<tr>
					<td><?php echo $row->msisdn; ?></td>
					<td><?php echo $row->cnt; ?></td>
				</tr>
		<?php }
			?>
			</table>
		<?php echo $page;
			} else { ?>
			 <p>Belum ada data</p>
			<?php } ?>
		<?php echo anchor('polling', 'back to Polling'); ?>
	</div>
	<div id="sidebar">
		<!-- menu admin -->
		<?php $this->load->view('dashboard_menu'); ?>
	</div>

	<div class="cl"></div>
<?php $this->load->view('footer');
