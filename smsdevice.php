<?php

define('BASEPATH', dirname(__FILE__));

include "application/config/database.php";

mysql_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password']);
mysql_select_db($db['default']['database']);

/*
SELECT concat( 'INSERT INTO smsqueue (`id` ,`tanggal` ,`number` ,`message` ,`status`) VALUES (NULL, CURRENT_TIMESTAMP, \'', msisdn, '\', \'&&', deviceid, ',Z31,60,1\', \'0\'), (NULL, CURRENT_TIMESTAMP, \'', msisdn, '\', \'&&', deviceid, ',Z32,30,18,6000,1\', \'0\')' )
FROM `devices`
WHERE idkategori =11
*/

if (isset($_POST['nomor']))
{
	extract($_POST);
	if ($nomor && $gsm)
	{
		mysql_query("INSERT INTO  `k9071857_indocrm`.`smsqueue` (`id` ,`tanggal` ,`number` ,`message` ,`status`)
		VALUES (NULL, CURRENT_TIMESTAMP, '$gsm', '&&$nomor,Z39,1,46.137.240.165,9999,1',  '0')");
		mysql_query("INSERT INTO  `k9071857_indocrm`.`smsqueue` (`id` ,`tanggal` ,`number` ,`message` ,`status`)
		VALUES (NULL, CURRENT_TIMESTAMP, '$gsm', '&&$nomor,Z21,0',  '0')");
		mysql_query("INSERT INTO  `k9071857_indocrm`.`smsqueue` (`id` ,`tanggal` ,`number` ,`message` ,`status`)
		VALUES (NULL, CURRENT_TIMESTAMP, '$gsm', '&&$nomor,Z10,internet,,',  '0')");
		mysql_query("INSERT INTO  `k9071857_indocrm`.`smsqueue` (`id` ,`tanggal` ,`number` ,`message` ,`status`)
		VALUES (NULL, CURRENT_TIMESTAMP, '$gsm', '&&$nomor,Y02',  '0')");
		$msg = "Activation sent";
	}
	else
	{
		$msg = "Data not completed";		
	}
}

$cats = '';
$catlist = "http://gpsmobile.polresmakota.com/rpc.php?act=listkategori";
$text = file_get_contents($catlist);
$json = json_decode($text);

$str=print_r($json, 1);

echo <<< EOT

<html>
<head>
<title>Device Manager</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>

<script type="text/javascript">

jQuery(document).ready(function(){
	$.getJSONP("",{"act":"listkategori"},function(callback) {		
		alert(callback);
	});	
	
});

function clickRadio(num)
{
	if (num=="rad1")
	{
		$("#perdevice").show();
		$("#perkategori").hide();
	}
	else
	if (num=="rad2")
	{
		$("#perdevice").hide();
		$("#perkategori").show();
	}
	else
	{
		$("#perdevice").hide();
		$("#perkategori").hide();		
	}
}
</script>

</head>
<body>
<h2>Device Controller</h2>
<h1>$msg</h1>
<form method="post">
<h3>SMS to Send</h3>
<input type="text" name="msg" size="50" /> (tanpa IWA)
<h3>Select Target</h3>
<input checked type="radio" name="pilih" onchange="clickRadio(this.value)" value="rad1" id="rad1"/> <label for="rad1">Per Device</label>
<table border="0" method="post" id="perdevice">
<tr>
<td>Nomor Device</td><td>:</td><td><input type="text" name="nomor" value="" /></td>
</tr>
<tr>
<td>Nomor GSM</td><td>:</td><td><input type="text" name="gsm" value="" /></td>
</tr>
</table>
<br/>
<input type="radio" name="pilih" onchange="clickRadio(this.value)" value="rad2" id="rad2"/> <label for="rad2">Per Kategori</label>
<table border="0" method="post" id="perkategori" style="display:none">
<tr>
<td>Kategori</td><td>:</td><td><select name="nomor"></select></td>
</tr>
</table>
<br/>
<input type="radio" name="pilih" onchange="clickRadio(this.value)" value="rad3" id="rad3"/> <label for="rad3">Semua Device</label>
<br/><br/>
<input type="submit" value="Send SMS" />
</form>
<pre>$json</pre>
</body>
</html>
EOT;
