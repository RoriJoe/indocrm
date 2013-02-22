<?php
if ($result){
	$keyword = $result[0]->keyword;
	$description = $result[0]->description;
	$active = $result[0]->active;
}else{
	$keyword = '';
	$description = '';
	$active = '';
}

$page_title = 'INDOCRM KEYWORDS';
$this->load->view('header', array('page_title' => $page_title, 'page_header' => $page_header));
?>
		<div id="content">
			<h1><?php echo $page_title; ?></h1>
			<div class="dialog">
				Update Keyword IndoCRM
			 <?php
			if ($flash){ ?>
				<br><div style="color:red;font-weight:bold;"> <?php echo $flash; ?> </div>
				<?php 
			}
			?>
			</div>
			 <div style="width:100%; height:100%; margin-top:20px;">
			<?php
			echo form_open('keywords/save');
			echo form_hidden('keyword_id', $keyword_id);
			echo form_hidden('mode', $mode);
			?>
			<table class="list-table" cellpadding="4" cellspacing="0">
				<tr>
					<td><strong>Keyword</strong></td>
					<td><?php echo form_input('keyword',$keyword); ?></td>
				</tr>
				<tr>
					<td><strong>Deskripsi</strong></td>
					<td><?php echo form_textarea('description',$description,'rows="3" cols="40"'); ?></td>
				</tr>
				<tr>
					<td><strong>Status</strong></td>
					<td>
						<select name="active">
					<?php
					$arr = array( '1' => 'Aktif','0' => 'Tidak Aktif');
					foreach($arr as $key => $val){
						$selected = ($active == $key) ? "selected" : "";
						echo '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
					}
					?>
					</select>
					</td>
				</tr>
				<tr>
					<?php $strsave = ($mode == 'edit') ? 'Update' : 'Save'; ?>
					<td colspan="2"><?php echo form_submit('save', $strsave, 'class="btn small"'); ?></td>
				</tr>
			</table>
			
<?php	echo form_close();
?>

		</div>
	</div>
	<div id="sidebar">
		<!-- menu admin -->
		<?php $this->load->view('dashboard_menu'); ?>
	</div>

	<div class="cl"></div>
<?php
$this->load->view('footer');

