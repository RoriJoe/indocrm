<?php

$page_header = '<script type="text/javascript" src="'.base_url('static/jquery.js').'"></script>
<script type="text/javascript" src="'.base_url('static/datepicker/js/datepicker.js').'"></script>
<link rel="stylesheet" media="screen" type="text/css" href="'.base_url("static/datepicker/css/datepicker.css").'" />
';//pake jquery huehuehue

$page_title = 'POLLING ';
$this->load->view('header', array('page_title' => $page_title, 'page_header' => $page_header));
?>
	<div id="content">
		<h1><?php echo $page_title; ?></h1>
		<div class="dialog">
			<p>Daftar Polling yang telah dibuat</p>
			<p style="color:red;font-weight:bold;"><?php echo $err; ?></p>
		</div>
		<div style="width:100%; height:100%; margin-top:20px;">
		<?php
			echo form_open('polling');
		?>
			<table>
				<tr>
					<td>Cari Polling: 
					<select name='kolom'>
						<option value='judul'>Judul</option>
						<option value='start_date'>Tgl Mulai (YYYY-MM-DD)</option>
						<option value='end_date'>Tgl Akhir (YYYY-MM-DD)</option>
					</select>
					&nbsp;<input type='text' name='query'>&nbsp;<input type="submit" name="cari" value="Go" />
					<button class="btn small" onclick="location.href='<?php echo site_url("polling/save?mode=add"); ?>';return false;">Tambah Baru</button>
					</td>
				</tr>
			</table>
		<?php
			echo form_close();
		?>
			<table class="list-table">
				<tr>
					<th>Judul Polling</th>
					<th>Tgl Mulai</th>
					<th>Tgl Akhir</th>
					<th>Action</th>
				</tr>
				<?php 
				if ($result){
					foreach($result as $row){
					?>
					<tr>
						<td><?php echo $row->judul_polling; ?></td>
						<td><?php echo $row->start_date; ?></td>
						<td><?php echo $row->end_date; ?></td>
						<td><a href="<?php echo site_url("polling/save?mode=edit&polling_id=".$row->polling_id); ?>">Edit</a>
						&nbsp;&nbsp;|&nbsp;&nbsp;
						<a href="#" onclick="x=confirm('Apakah yakin mau menghapus?');if(x==true){location.href='<?php echo site_url('polling/delete?polling_id='.$row->polling_id); ?>';}else{return false;}return false;">Delete</a>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo anchor('polling/result?polling_id='.$row->polling_id,'Grafik'); ?>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo anchor('polling/mobile_count?polling_id='.$row->polling_id,'Mobile summary'); ?></td>
					</tr>
					<?php
					}
				}else{
					?>
					<tr>
						<td colspan="4" align="center">No Data</td>
					</tr>
					<?php
				}
				?>
			</table>
		</div>
	</div>
	<div id="sidebar">
		<!-- menu admin -->
		<?php $this->load->view('dashboard_menu'); ?>
	</div>

	<div class="cl"></div>
<?php 
echo "<script type='text/javascript'>
	$('#start_date').DatePicker({
			format:'Y-m-d',
			date: $('#start_date').val(),
			current: $('#start_date').val(),
			onChange: function(formated, dates){
				$('#start_date').val(formated);
				$('#start_date').DatePickerHide();
			}
		});
	$('#end_date').DatePicker({
			format:'Y-m-d',
			date: $('#end_date').val(),
			current: $('#end_date').val(),
			onChange: function(formated, dates){
				$('#end_date').val(formated);
				$('#end_date').DatePickerHide();
			}
		});
	</script>";

$this->load->view('footer');
