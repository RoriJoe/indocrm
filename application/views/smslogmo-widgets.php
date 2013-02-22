<?php
if ($notvalid)
{
	?>
	<html>
		<head>
			<title>List SMS Masuk</title>
		</head>
	<body>
	<div>
		<strong>404 Not Found</strong>.<br/>
		<span style="color:red;font-weight:bold;">Maaf,</span> Anda tidak diperkenankan untuk membuka halaman ini
	</div>
	</body>
	</html>
	<?php
}else{
	
	$judul='SMS Masuk';
	$width=800;
	$sizefont=11;
	$colorsms='#000000';
	$colormobile='#FF0080';
	$jml = 0;

	$this->db->where('client_id', $client_id);
	$query = $this->db->get('log_mo_widgets_options');
	
	if ($query->num_rows()>0){
		$jml = $query->num_rows();
		$rs = $query->result_array();
		
		//echo print_r($rs);die;
		if (!empty($rs[0]['judul']))
			$judul = $rs[0]['judul'];
		if (!empty($rs[0]['height']))
			$height = $rs[0]['height'];
		if (!empty($rs[0]['sizefont']))
			$sizefont = $rs[0]['sizefont'];
		if (!empty($rs[0]['colorsms']))
			$colorsms = $rs[0]['colorsms'];
		if (!empty($rs[0]['colormobile']))
			$colormobile = $rs[0]['colormobile'];
	}
	
?>
<html>
	<head>
		<title>List SMS Masuk</title>
	<script src="<?php echo base_url()."static/jquery.js"; ?>"></script>
	<script src="<?php echo base_url()."static/datepicker/js/datepicker.js"; ?>"></script>
	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo base_url()."static/datepicker/css/datepicker.css"; ?>" />
	<style>
		body {font-family:arial,helvetica;}
		h2{text-align:center;}
		h3{text-align:center;}
		.cekall {
			font-weight:normal;
			font-size:11px;
			padding:4px;
			background-color:#17E0F0;
			color:#000000;
			text-decoration:none;
			}
		.cekall:hover{
			text-decoration:underline;
			font-weight:bold;
			}
		
		#header{position:relative;height:45px;}
		#logo {position:absolute;left:12px;}
		
		#wgcount {position:relative:height:40px;}
		#wgcount span{border:1pt solid #CCCCCC;}
		#wgcount a{color:#0080FF;background-color:#C0C0C0;padding:4px;border-radius:9px;}
		#wgcount a:hover{text-decoration:none;color:#FFF;}
		
		#widget-content{
			text-align:center;
			font-size:<?php echo $sizefont.'pt'; ?>;
			width:100%;
			border-radius:9px;
			background-color:#81BDEF;
		}
		.kebaca{
			background-color:#CCCCCC;border:1pt dotted #C9F8F8;padding:4px;
		}
		.kebaca:hover{
			background-color:#DAF5FA;
			}
		.belumkebaca{
			background-color:#FFFFFF;border:1pt dotted #C9F8F8;padding:4px;
			font-weight:bold;
		}
		.belumkebaca:hover{
			background-color:#DAF5FA;
			}
		.kepilih{
			background-color:#FFFF80;
		}
		#widget-content a { font-weight:bold;}
		#widget-content a:hover{ font-weight:normal;}
		#list-content {padding:0px;text-align:justify;}
		#widget-smskebaca { font-weight:normal;color:<?php echo $colorsms; ?>; }
		#widget-smsbelumkebaca { font-weight:bold;color:<?php echo $colorsms; ?>; }
		.cek {float:right;}
		
	</style>
	<script language="javascript">
		function pilihsemua(formku, elemenku, n){
			var theF = formku;
			for (i=0; i < theF.elements.length; i++){
				if (theF.elements[i].name==elemenku){
					theF.elements[i].checked = n;
				}
				
				if (theF.elements[i].name==elemenku && theF.elements[i].checked == true) {
					document.getElementById("li" + theF.elements[i].value).style.backgroundColor = '#FFFF80';
				}
				if (theF.elements[i].name==elemenku && theF.elements[i].checked == false) {
					if (document.getElementById("hidden" + theF.elements[i].value).value == 'belumkebaca'){
						document.getElementById("li" + theF.elements[i].value).style.backgroundColor = '#FFFFFF';
					}else{
						document.getElementById("li" + theF.elements[i].value).style.backgroundColor = '#C0C0C0';
					}
				}
				
				
			}
		}
		
		function pilihan(ini, nilai,kelas){
			var centang = document.form1;
			for(i=0; i < centang.elements.length; i++){
				if (centang.elements[i].checked  == true){
					document.getElementById("li"+ nilai).style.backgroundColor = '#FFFF80';
				}
			}
			
			if (ini.checked == false){
				if (kelas == 'kebaca'){
					document.getElementById("li"+ nilai).style.backgroundColor = '#CCCCCC';
				}else{
					document.getElementById("li"+ nilai).style.backgroundColor = '#FFFFFF';
				}
			}
		}
		
		function filtering(ini){
			var obj = document.form1;
			
			if (ini.checked == true){
				obj.jam.disabled = false;
				obj.menit.disabled = false;
				obj.jamto.disabled = false;
				obj.menitto.disabled = false;
			}
			
			if (ini.checked == false)
			{
				obj.jam.disabled = true;
				obj.menit.disabled = true;
				obj.jamto.disabled = true;
				obj.menitto.disabled = true;
			}
		}
		
		function filtertgl(ini){
			var obj = document.form1;
			
			if (ini.checked == true){
				obj.tanggal.disabled = false;
			}else{
				obj.tanggal.disabled = true;
			}
		}
	</script>
    <script type="text/javascript">

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-32128736-1']);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();

    </script>	
    </head>
<body>
<?php

$do = (isset($_REQUEST['do']) ? $_REQUEST['do'] : "");
$isread = (isset($_REQUEST['isread']) ? $_REQUEST['isread'] : "");
$selaction = (isset($_REQUEST['selaction']) ? $_REQUEST['selaction'] : "");
$dofilter = (isset($_REQUEST['dofilter']) ? $_REQUEST['dofilter'] : "");
$dofiltertgl = (isset($_REQUEST['dofiltertgl']) ? $_REQUEST['dofiltertgl'] : "");
$tanggal = (isset($_REQUEST['tanggal']) ? $_REQUEST['tanggal'] : date("Y-m-d"));
$filter = (isset($_REQUEST['filter']) ? $_REQUEST['filter'] : "");
$jamsebelum = date("H", mktime(date("H")-1,0,0,0,0,0));
$jam = (isset($_REQUEST['jam']) ? $_REQUEST['jam'] : $jamsebelum);
$menit = (isset($_REQUEST['menit']) ? $_REQUEST['menit'] : date("i"));
$jamto = (isset($_REQUEST['jamto']) ? $_REQUEST['jamto'] : date("H"));
$menitto = (isset($_REQUEST['menitto']) ? $_REQUEST['menitto'] : date("i"));


if ($do){
	if ($selaction == '1'){
		foreach($isread as $val){
			$sql = "UPDATE log_mo SET is_read = '1' WHERE id = '$val'";
			$this->db->query($sql);
		}
		//redirect("/smslogmo/wg/".$client_id."/".$key,'refresh');
		//redirect($key,'refresh');
		header("Location: ".current_url());
	}
	
	if ($selaction == '2'){
		foreach($isread as $val){
			$sql = "UPDATE log_mo SET is_read = '2' WHERE id = '$val'";
			$this->db->query($sql);
		}
		//redirect("/smslogmo/wg/".$client_id."/".$key,'refresh');
		//redirect($key,'refresh');
		header("Location: ".current_url());
	}
	
	if ('0' == $selaction){
		foreach($isread as $val){
			$sql = "UPDATE log_mo SET is_read = '0' WHERE id = '$val'";
			
			$this->db->query($sql);
			//echo $this->db->last_query()."<br/>";
		}
		//redirect("/smslogmo/wg/".$client_id."/".$key,'refresh');
		//redirect($key,'refresh');
		header("Location: ".current_url());
	}
	
}

$query = $this->db->query("SELECT mobile FROM clients WHERE client_id = '$client_id'");
$resgw = $query->result_array();

$mob_gw = $resgw[0]['mobile'];

$str = "";
if (!empty($mob_gw)){
	$str = " dari $mob_gw";
}

if ($dofilter){
	$h = $jam;
	$m = $menit;
	$ht = $jamto;
	$mt = $menitto;
	
	$hdisabled = '';
	$mdisabled = '';
	$htdisabled = '"';
	$mtdisabled = '';
	
	$arrSelected = array();
	
	foreach ($isread as $value){
		$arrSelected[] = $value;
	}
}else{
	$h = '-';
	$m = '-';
	$ht = '-';
	$mt = '-';
	
	$hdisabled = 'disabled="true"';
	$mdisabled = 'disabled="true"';
	$htdisabled = 'disabled="true"';
	$mtdisabled = 'disabled="true"';
}

?>
<div id="header">
<span id="logo"><a href="http://www.indocrm.com/"><img src="http://www.indocrm.com/static/logo.gif" /></a></span><h2 ><?php echo $judul.$str; ?></h2>
</div>
<div id="wgcount"></div>
<div id="widget-content">
	<form method="post" name="form1" action="<?php echo base_url()."index.php/smslogmo/wg/".$client_id."/".$key."/".$h."/".$m."/".$ht."/".$mt; ?>">
	<p>
	<a href="#" class="cekall" onclick="pilihsemua(document.form1, 'isread[]',1);return false;">Check All</a> | 
	<a href="#" class="cekall" onclick="pilihsemua(document.form1, 'isread[]',0);return false;">UnCheck All</a> | 
	<strong>With Selected Action : </strong>
	<select name="selaction">
		<option value="1">Mark as read</option>
		<option value="0">Mark as unread</option>
		<option value="2">Delete</option>
	</select>
	&nbsp;&nbsp;<input type="submit" value="Apply" name="do"  style="padding:4px;font-size:12px;font-family:arial;border:1pt solid #000000;" />
	<br/>
	<input type="checkbox" id="dofiltertgl" name="dofiltertgl" value="dofiltertgl" <?php echo ($dofiltertgl) ? "checked" : ""; ?> onclick="filtertgl(this);" />&nbsp;
	Dengan Filter Tanggal&nbsp;
	<?php
	if ($dofiltertgl){
		$tgl = $tanggal;
		$tgldisabled = "";
		$arrSelected = array();
	
		foreach ($isread as $value){
			$arrSelected[] = $value;
		}
	}else{
		$tgl = "";
		$tgldisabled = "disabled='true'";
	}
	?>
	<input type="text" id="inputDate" name="tanggal" value="<?=$tanggal?>" maxlength="10" size="10" <?=$tgldisabled?> />
	<br/>
	<input type="checkbox" id="dofilter" name="dofilter" value="dofilter" <?php echo ($dofilter) ? "checked" : ""; ?> onClick="filtering(this);" />&nbsp;
	Dengan Filter Jam&nbsp;
	<input type="text" id="jam" name="jam" value="<?=str_pad($jam,2,'0',STR_PAD_LEFT)?>" size="2" maxlength="2" <?=$hdisabled?> />&nbsp;
	<strong>:</strong>&nbsp;<input type="text" id="menit" name="menit" value="<?=str_pad($menit,2,'0',STR_PAD_LEFT)?>" size="2" maxlength="2" <?=$mdisabled?> /> 
	<strong>Sampai</strong>&nbsp;<input type="text" id="jamto" name="jamto" value="<?=str_pad($jamto,2,'0',STR_PAD_LEFT)?>" size="2" maxlength="2" <?=$htdisabled?> />&nbsp;
	<strong>:</strong>&nbsp;<input type="text" id="menitto" name="menitto" value="<?=str_pad($menitto,2,'0',STR_PAD_LEFT)?>" size="2" maxlength="2" <?=$mtdisabled?> /> <br />
	<input type="submit" name="filter" value="Tampilkan" style="padding:4px;font-size:12px;font-family:arial;border:1pt solid #000000;" />
	<input type="hidden" id="hiddenjam" name="hiddenjam" value="<?php echo (!isset($_REQUEST['jam'])? '-' : $jam); ?>">
	<input type="hidden" id="hiddenmenit" name="hiddenmenit" value="<?php echo (!isset($_REQUEST['menit'])? '-' : $menit); ?>">
	<input type="hidden" id="hiddenjamto" name="hiddenjamto" value="<?php echo (!isset($_REQUEST['jamto'])? '-' : $jamto); ?>">
	<input type="hidden" id="hiddenmenitto" name="hiddenmenitto" value="<?php echo (!isset($_REQUEST['menitto'])? '-' : $menitto); ?>">
	</p>
<?php
	echo '<ul id="list-content">';
	
	foreach($result as $row){
		$mobile = $row['msisdn'];
		
		if (in_array($row['id'], $arrSelected)){
			$checked = "checked='checked'";
			$class="kepilih";
			if ($row['is_read'] == '1') { 
				$classx="kebaca";
			}else{
				$classx="belumkebaca";
			}
		}else{
			$checked = "";
			if ($row['is_read'] == '1') { 
				$class="kebaca";
				$classx="kebaca";
			} else { 
				$class="belumkebaca"; 
				$classx="belumkebaca"; 
			}
		}
		
		$ln = strlen($mobile);
		$mobile = substr($mobile,0,$ln-3)."XXX";
		
		echo '<li type="none" id="li'.$row['id'].'" class="'.$class.'"><strong>Pengirim:</strong> (<span style="font-weight:bold;color:'.$colormobile.';">'.$mobile.'</span>)<br />';
		echo '<strong>Pesan : </strong><span id="widget-sms'.$class.'">'.$row['sms'].'</span><br /><strong>Tgl: </strong>'.$row['date'].'&nbsp;&nbsp;&nbsp;';
		echo '<strong>Jam: </strong> '.$row['time'].'<input type="checkbox" class="cek" id="isread[]" name="isread[]" value="'.$row['id'].'" onLoad="pilihan(this, \''.$row['id'].'\',\''.$classx.'\');" onClick="pilihan(this, \''.$row['id'].'\',\''.$classx.'\');" '.$checked.'> ';
		echo '<input type="hidden" id="hidden'.$row['id'].'" value="'.$class.'"></li>';
	}
	
	echo '</ul>';
	?>
	<div id="page">
	<?php 
	if (!empty($page))
		echo $page; 
	else
		echo '<p>&nbsp;</p>';
	?>
	</div>
	<p><strong>Powered by</strong> &copy; <a href="http://www.simetri.web.id/" style="text-decoration:none;color:#FFFFFF;">Simetri</a> <?php echo date('Y'); ?></p>
	<br />
</div>
</form>
<script language="javascript">
	var auto_refresh = setInterval(
		function ()
		{
			$('#wgcount').load('<?php echo base_url().'index.php/smslogmo/wg_load_cnt/'.$client_id.'/'.$key.'/'.$total.'/'.$h.'/'.$m.'/'.$ht.'/'.$mt.'/'.$tgl; ?>' ).fadeIn("slow");
		},30000);
		
		$('#inputDate').DatePicker({
			format:'Y-m-d',
			date: $('#inputDate').val(),
			current: $('#inputDate').val(),
			onChange: function(formated, dates){
				$('#inputDate').val(formated);
				$('#inputDate').DatePickerHide();
			}
		});
		
</script>
</body>
</html>

<?php
}
?>
