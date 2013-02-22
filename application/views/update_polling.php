<?php
$page_header = '<script type="text/javascript" src="'.base_url('static/jquery.js').'"></script>
<script type="text/javascript" src="'.base_url('static/datepicker/js/datepicker.js').'"></script>
<link rel="stylesheet" media="screen" type="text/css" href="'.base_url("static/datepicker/css/datepicker.css").'" />
';//pake jquery huehuehue

$page_title = 'MAJEMEN POLLING';
$this->load->view('header', array('page_title' => $page_title, 'page_header' => $page_header));

$judul = '';
$keyword = '';
$pilihan1 = '';
$pilihan2 = '';
$pilihan3 = '';
$pilihan4 = '';
$pilihan5 = '';
$start_date = date('Y-m-d');
$end_date = date('Y-m-d');

if ($mode == 'edit'){
	if (!empty($polling_id)){
		$this->db->where('polling_id', $polling_id);
		$q = $this->db->get('polling');
		if ($q->num_rows()>0){
			$result = $q->result();
			$judul = $result[0]->judul_polling;
			$keyword = $result[0]->keyword;
			$pilihan1 = $result[0]->pilihan1;
			$pilihan2 = $result[0]->pilihan2;
			$pilihan3 = $result[0]->pilihan3;
			$pilihan4 = $result[0]->pilihan4;
			$pilihan5 = $result[0]->pilihan5;
			$start_date = $result[0]->start_date;
			$end_date = $result[0]->end_date;
		}
	}else{
		$mode='add';
	}
}

?>
	<div id="content">
			<h1><?php echo $page_title; ?></h1>
			<div class="dialog">
				<p>Update Polling</p>
				<?php 
				if($err){ ?>
					<div style="color:red;font-weight:bold;"><?php echo $err; ?></div>
			<?php	}
				?>
			</div>
			<div style="width:100%; height:100%; margin-top:20px;">
			<?php
			echo form_open('polling/save');
			echo form_hidden('mode', $mode);
			echo form_hidden('polling_id', $polling_id);
			?>
				<table class="list-table" border="0" cellspacing="10" cellpadding="4">
					<tr>
						<td><strong>Judul Polling</strong></td>
						<td><?php echo form_input('judul_polling', $judul, 'size="60" maxlength="100"'); ?></td>
					</tr>
					<tr>
						<td><strong>Keyword untuk Polling</strong></td>
						<td>
						<?php
						
							$this->db->where('client_id', $this->orca_auth->user->client_id);
							$this->db->where('active',1);
							$q = $this->db->get('keywords');
							if ($q->num_rows()>0){
								?>
								<select name="keyword">
									<option value=''>Pilih Keyword</option>
								<?php
								foreach($q->result() as $row){
									$selected = ($keyword == $row->keyword) ? 'selected' : '';
									?>
									<option value='<?php echo $row->keyword; ?>' <?php echo $selected; ?>><?php echo $row->keyword; ?></option>
									<?php
								} ?>
								</select>
								<?php
							}
							echo anchor('keywords/save', 'Tambah Keyword Baru');
						?>
							
						</td>
					</tr>
					<tr>
						<td><strong>A. </strong></td>
						<td><?php echo form_input('pilihan1', $pilihan1, 'size="40" maxlength="160"'); ?><br/> *) Minimal pilihan adalah 2. Kosongi pilihan yang tidak perlu di isi</td>
					</tr>
					<tr>
						<td><strong>B.</strong></td>
						<td><?php echo form_input('pilihan2', $pilihan2, 'size="40" maxlength="160"'); ?></td>
					</tr>
					<tr>
						<td><strong>C.</strong></td>
						<td><?php echo form_input('pilihan3', $pilihan3, 'size="40" maxlength="160"'); ?></td>
					</tr>
					<tr>
						<td><strong>D.</strong></td>
						<td><?php echo form_input('pilihan4', $pilihan4, 'size="40" maxlength="160"'); ?></td>
					</tr>
					<tr>
						<td><strong>E.</strong></td>
						<td><?php echo form_input('pilihan5', $pilihan5, 'size="40"'); ?></td>
					</tr>
					<tr>
						<td><strong>Start Date</strong></td>
						<td><?php echo form_input('start_date', $start_date, 'id="start_date" size="10"'); ?></td>
					</tr>
					<tr>
						<td><strong>End Date</strong></td>
						<td><?php echo form_input('end_date', $end_date, 'id="end_date" size="10"'); ?></td>
					</tr>
					<tr>
						<td colspan="2"><?php echo form_submit('save', 'Save', 'class="btn small"'); ?> &nbsp;&nbsp; <?php echo anchor('polling', 'back to polling'); ?></td>
					</tr>
				</table>
			<?php
			echo form_close();
			?>
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
