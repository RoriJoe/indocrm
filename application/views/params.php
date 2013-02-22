<?php
$page_title = 'PARAMETER KEYWORD '.strtoupper($keyword);
$this->load->view('header', array('page_title' => $page_title, 'page_header' => $page_header));
?>
		<div id="content">
			<h1><?php echo $page_title; ?></h1>
			<div class="dialog">
				Penambahan dan Modif Parameter untuk KEYWORD <strong><?php echo strtoupper($keyword); ?></strong>
				<br>
				Kembali ke <?php echo anchor('keywords', 'Halaman Keywords'); ?>
				<br>
				<?php
				if ($flash){ ?>
				<div style="color:red;font-weight:bold;"><?php echo $flash; ?> </div>
			<?php }
			?>
			 </div>
			 <div style="width:100%; height:100%; margin-top:20px;">
<?php
			echo form_open('keywords/save_param');
			echo form_hidden('mode', $mode);
			echo form_hidden('keyword_id', $keyword_id);
			echo form_hidden('param_id', $param_id);
			
			?>
			<table cellpadding="4" cellspacing="0" width="100%">
				<tr>
					<td><strong>Keyword</strong></td>
					<td><?php echo form_input('keyword',$keyword, 'readonly'); ?></td>
				</tr>
				<tr>
					<td><strong>Parameter</strong></td>
					<td><?php echo form_input('param',$param, (($mode == 'edit') ? 'readonly' :'')); ?></td>
				</tr>
				<tr>
					<td><strong>Reply</strong></td>
					<td><?php echo form_textarea('reply',$reply,'rows="3" cols="40"'); ?></td>
				</tr>
				<?php
//				if ($keywordclient == $this->orca_auth->user->client_id || $this->orca_auth->user->group_id == 1) { ?>
				<tr>
					<td colspan="2"><?php echo form_submit('save', 'Save', 'class="btn small"'); ?></td>
				</tr>
			<?php //} ?>
			</table>
			
<?php	echo form_close(); ?>
			</div>
			<div style="width:100%; height:100%; margin-top:20px;">
				<?php if (!$error) { ?>
				<table class="list-table">
					<tr>
						<th>Keyword</th>
						<th>Parameter</th>
						<th>Reply</th>
						<th>Action</th>
					</tr>
						<?php
					
					if ($replies){
						foreach($replies as $row){
							?>
							<tr>
								<td><?php echo $row->keyword; ?></td>
								<td><?php echo $row->param; ?></td>
								<td><?php echo $row->reply; ?></td>
								<td><?php echo anchor('keywords/params?keyword_id='.$row->keyword_id.'&param_id='.$row->param_id.'&mode=edit','Edit'); ?>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" onclick="x=confirm('Apakah yakin mau menghapus?');if(x==true){location.href='<?php echo site_url('keywords/delete_param?param_id='.$row->param_id.'&keyword_id='.$row->keyword_id); ?>';}else{return false;}return false;">Delete</a></td>
							</tr>
							<?php
						}
					}else{
						?>
						<tr>
							<td colspan='4'><div align='center'>No Data</div></td>
						</tr>
						<?php
					}
						?>
				</table>
			<?php } else{ ?>
				<div class="dialog">
						<p>Anda tidak punya hak akses untuk melihat keyword ini.</p>
				</div>
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
