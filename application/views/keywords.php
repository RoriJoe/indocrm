<?php

$page_title = 'INDOCRM KEYWORDS';
$this->load->view('header', array('page_title' => $page_title, 'page_header' => $page_header));
?>
		<div id="content">
			<h1><?php echo $page_title; ?></h1>
			<div class="dialog">
				List Keyword IndoCRM
			 <?php
			 
			if (!empty($flash)){ ?>
				<br><div style="color:red;font-weight:bold;"> <?php echo $flash; ?> </div>
				<?php 
			}
			?>
			</div>
			<div style="width:100%; height:100%; margin-top:20px;">
			<form method="post" action="">
			<strong>Pencarian Keyword</strong>&nbsp;&nbsp;<input type="text" name="search" value='<?php echo $search; ?>'>&nbsp;<?php echo form_submit('cari', 'Cari', 'class="btn small"'); ?> <button name="tambah" class="btn small" onclick="javascript:location.href='<?php echo site_url('keywords/save'); ?>';return false;">Tambah Keyword</button>
			</form>
				<table class="list-table">
					<tr>
						<th>Keywords</th>
						<th>Params</th>
						<th>Description</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				<?php
			if ($keywords){
				
				foreach($keywords as $row){
					?>
					<tr>
						<td><?php echo $row->keyword; ?></td>
						<td><?php 
						if ($row->client_id == $this->orca_auth->user->client_id || $this->orca_auth->user->id == 1){
							echo anchor('keywords/params/?keyword_id='.$row->keyword_id, 'Parameters'); 
						}else{
							echo "Authorized User Only";
						}
						?>
						</td>
						<td><?php echo $row->description; ?></td>
						<td><?php echo ($row->active == 1) ? 'Aktif' : 'Tidak Aktif'; ?></td>
						<td align="center"><?php 
						if ($row->client_id == $this->orca_auth->user->client_id || $this->orca_auth->user->id == 1){
							echo anchor('keywords/save?keyword_id='.$row->keyword_id.'&mode=edit', 'Edit'); ?>&nbsp;|&nbsp;<a href="<?php echo site_url('keywords/delete_keyword?keyword_id='.$row->keyword_id); ?>" onclick="return confirm('Apakah Anda yakin mau menghapus?');return false();">Delete</a>
						<?php }else{
							echo "Authorized User Only";
						}
						?>
						</td>
					</tr>
					<?php
				}
			}else{
				echo "<tr>
					<td colspan='4'><div align='center'>No Data</div></td>
				</tr>";
			}
				?>
				</table>
				<?php echo $paging; ?>
			</div>
		</div>
		<div id="sidebar">
			<!-- menu admin -->
			<?php $this->load->view('dashboard_menu'); ?>
		</div>

		<div class="cl"></div>
<?php
$this->load->view('footer');
