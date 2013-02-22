<?php 
$page_title = 'SMS Masuk Penyiar Profile Setting';
$this->load->view('header', array('page_title' => $page_title));
?>

<script language="javascript" src="<?php echo base_url()."static/jscolor/jscolor.js" ?>"></script>

<?php
$save = (isset($_REQUEST['save']) ? $_REQUEST['save'] : '');
$blacklistnumber = (isset($_REQUEST['blacklistnumber']) ? $_REQUEST['blacklistnumber'] : '');
$blacklistwords = (isset($_REQUEST['blacklistwords']) ? $_REQUEST['blacklistwords'] : '');
$judul = (isset($_REQUEST['judul']) ? $_REQUEST['judul'] : 'SMS Masuk');
$width = (isset($_REQUEST['width']) ? $_REQUEST['width'] : '250');
$height = (isset($_REQUEST['height']) ? $_REQUEST['height'] : '250');
$sizefont = (isset($_REQUEST['sizefont']) ? $_REQUEST['sizefont'] : '11');
$colorsms = (isset($_REQUEST['colorsms']) ? $_REQUEST['colorsms'] : '#000000');
$colormobile = (isset($_REQUEST['colormobile']) ? $_REQUEST['colormobile'] : '#FF0080');
$view = (isset($_REQUEST['view']) ? $_REQUEST['view'] : '10');

$client_id = $this->orca_auth->user->client_id;

$sql = "SELECT confirm_hash FROM users WHERE client_id = '$client_id' LIMIT 1";
$query = $this->db->query($sql);
$res = $query->result_array();
$keyhash = $res[0]['confirm_hash'];

$error = "";

if ($save){
	
	//echo "HERE";
	
	$arrMobile = array();
	$arrWords = array();
	
	if (!empty($blacklistnumber)){
		$tmp = explode(",", $blacklistnumber);
		foreach($tmp as $mobile){
			if (preg_match("/^[0-9]+[0-9]+[0-9]$/i", trim($mobile))){
				$arrMobile[]= trim($mobile);
			}else{
				$error = "Format mobile tidak benar";
			}
		}
	}
	
	if (!$error){
		
		$arrayInsert = array ( 
			'blacklistnumber' => $blacklistnumber, 
			'blacklistwords' => $blacklistwords,
			'sizefont' => $sizefont,
			'colorsms' => $colorsms,
			'colormobile' => $colormobile,
			'view' => $view,
			'client_id' => $client_id
		);
			
		$arrayUpdate = array ( 
			'blacklistnumber' => $blacklistnumber, 
			'blacklistwords' => $blacklistwords,
			'sizefont' => $sizefont,
			'colorsms' => $colorsms,
			'colormobile' => $colormobile,
			'view' => $view
			);
		
		$query = $this->db->query("SELECT * FROM log_mo_widgets_options WHERE client_id = '$client_id'");
		if ($query->num_rows()>0){
			
			$this->db->where('client_id', $client_id);
			$this->db->update('log_mo_widgets_options', $arrayUpdate);
			//echo $this->db->last_query();
			//die;
		}else{
			$this->db->insert('log_mo_widgets_options', $arrayInsert);
		}
		
		redirect('smslogmo/widget_sms_logmo_edit/?success=1', 'refresh');
	}
}


$blacklistnumber = "";
$blacklistWords = "";

$this->db->where('client_id', $client_id);
$query = $this->db->get('log_mo_widgets_options');
//echo $this->db->last_query();


if ($query->num_rows()>0){
	foreach($query->result_array() as $row){
		$blacklistnumber = $row['blacklistnumber'];
		$blacklistwords = $row['blacklistwords'];
		$judul = (empty($row['judul']) ? $judul : $row['judul']);
		$sizefont = (empty($row['sizefont']) ? $sizefont : $row['sizefont']);
		$colorsms = (empty($row['colorsms']) ? $colorsms : $row['colorsms']);
		$colormobile = (empty($row['colormobile']) ? $colormobile : $row['colormobile']);
		$view = (empty($row['view']) ? $view : $row['view']);
	}
}

//echo __LINE__.": this";

if (isset($_REQUEST['success']) && $_REQUEST['success'])
{
	$error =  "Data sukses tersimpan";
}

if (!isset($_REQUEST['success']) || !$_REQUEST['success'])
{
	$style = "color:red;font-weight:bold;";
}else{
	$style = "";
}

?>
<div id='content'>
	<h1><?php echo $page_title; ?></h1>
	<div class="dialog">
		<p>Setting ini akan muncul di halaman <?php echo anchor('dashboard/wgpenyiar', 'SMS Masuk Penyiar'); ?></p>
	</div>
	<?php if (!empty($error))
	{ ?>
	<div style="<?php echo $style; ?>background-color:#96ED94;border:1pt solid #88DEE6;border-radius:9px;height:40px;text-align:center;">
	<br><?php echo $error; ?>
	</div>
	<?php 
	}
	?>
	<form method="post" action="<?php echo base_url().'index.php/smslogmo/widget_sms_logmo_edit/'; ?>">
	<p>
	<strong>Judul Widget</strong>
	<input type='text' name="judul" value="<?php echo $judul; ?>" size="45" />
	</p>
	<p>
	<strong>Blacklist Number</strong>[separated by comas (,)]<br />
	<textarea name='blacklistnumber' rows="5" cols="55" class="txt"><?php echo $blacklistnumber; ?></textarea>
	</p>
	<p>
	<strong>Blacklist Word</strong>[separated by comas (,)]<br />
	<textarea name='blacklistwords' rows="5" cols="55" class="txt"><?php echo $blacklistwords; ?></textarea>
	</p>
	<p>
		<strong>Limit SMS Show</strong><br />
		<select name="view">
		<?php 
		$arr = array(10,15,20,25);
		foreach($arr as $x){
			$selected = ($x == $view) ? 'selected' : '';
			echo '<option value="'.$x.'" '.$selected.'>'.$x.'</option>';
		}
		?>
		</select>
	</p>
	<p>
		<strong>Ukuran font</strong>&nbsp;:&nbsp;<input type='text' name='sizefont' size="3" value='<?php echo $sizefont; ?>' />
	</p>
	<p>
		<strong>Warna tulisan mobile</strong>&nbsp;:&nbsp;<input type='text' class="color {hash:true,pickerClosable:true}" name='colormobile' size="10" value='<?php echo $colormobile; ?>' />
	</p>
	<p>
		<strong>Warna tulisan sms</strong>&nbsp;:&nbsp;<input type='text' class="color {hash:true,pickerClosable:true}" name='colorsms' size="10" value='<?php echo $colorsms; ?>' />
	</p>
	<p><input type="submit" name="save" value="Simpan" class="btn" /> | <?php echo anchor('dashboard', 'Back to dashboard'); ?></p>
	</form>
</div>
<div id="sidebar">
		<!-- menu admin -->
		<?php $this->load->view('dashboard_menu'); ?>
	</div>

	<div class="cl"></div>
<?php
$this->load->view('footer');

