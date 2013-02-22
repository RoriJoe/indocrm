<?php

define('BASEPATH', dirname(__FILE__));

include "application/config/database.php";

mysql_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password']);
mysql_select_db($db['default']['database']);

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

echo <<< EOT

<html>
<head>
<title>Device Activation</title>
</head>
<body>
<h3>Device Activation</h3>
<h1>$msg</h1>
<form method="post">
<table border="0" method="post">
<tr>
<td>Nomor Device</td><td>:</td><td><input type="text" name="nomor" value="" /></td>
</tr>
<tr>
<td>Nomor GSM</td><td>:</td><td><input type="text" name="gsm" value="" /></td>
</tr>
<tr>
<td></td><td></td><td><input type="submit" value="Activate" /></td>
</tr>
</table>
</form>
</body>
</html>
EOT;
