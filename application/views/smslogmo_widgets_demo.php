<html>
<body>
<?php
	$judul='SMS Masuk';
	$width=250;
	$heigth=250;
	$sizefont=11;
	$colorsms='#000000';
	$colormobile='#FF0080';
	
	$this->db->where('client_id', $client_id);
	$query = $this->db->get('log_mo_widgets_options');
	
	if ($query->num_rows()>0){
		$rs = $query->result_array();
		
		//echo print_r($rs);die;
		if (!empty($rs[0]['judul']))
			$judul = $rs[0]['judul'];
		if (!empty($rs[0]['width']))
			$width = $rs[0]['width'];
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
	<style>
	h3{text-align:center;}
	#widget-content{
		font-size:<?php echo $sizefont.'pt'; ?>;
		font-family:arial,helvetica;
		width:100%;
		border-radius:9px;
		background-color:#81BDEF;
		}
	#widget-content p { text-align:center;}
	#widget-content a { font-weight:bold;}
	#widget-content a:hover{ font-weight:normal;}
	#widget-content ul {padding:0px;}
	#widget-content li {background-color:#FFFFFF;border:1pt dotted #CCCCCC;padding:4px;}
	#widget-content li:hover {background-color:#DAF8F8;}
	</style>
<div id="widget-content">
<?php
	echo '<h3>'.$judul.'</h3>';
	echo '<ul style="text-align:justify;">';
	
	echo '<li type="none"><strong>Pengirim:</strong> (<span style="font-weight:bold;color:'.$colormobile.';">089809111xxx</span>)<br />';
	echo '<strong>Pesan:</strong> <span style="color:'.$colorsms.'">Lorem ipsum dollor et moye</span><br /><strong>Tgl: </strong>'.date('Y-m-d').'<br /><strong>Jam: </strong> '.date('H:i').'</li>';
	echo '<li type="none"><strong>Pengirim:</strong> (<span style="font-weight:bold;color:'.$colormobile.';">089809112xxx</span>)<br />';
	echo '<strong>Pesan:</strong> <span style="color:'.$colorsms.'">Lorem ipsum dollor et moye</span><br /><strong>Tgl: </strong>'.date('Y-m-d').'<br /><strong>Jam: </strong> '.date('H:i').'</li>';
	echo '<li type="none"><strong>Pengirim:</strong> (<span style="font-weight:bold;color:'.$colormobile.';">089809991xxx</span>)<br />';
	echo '<strong>Pesan:</strong> <span style="color:'.$colorsms.'">Lorem ipsum dollor et moye</span><br /><strong>Tgl: </strong>'.date('Y-m-d').'<br /><strong>Jam: </strong> '.date('H:i').'</li>';
	
	echo '</ul>';
	?>
	<div id="page">
	<p><a href="#">1</a> &nbsp; &nbsp; <a href="#">2</a></p>
	<?php
	echo '<br />';
?>
</div>
</div>
</body>
</html>
