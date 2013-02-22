<?php

@set_magic_quotes_runtime(0);
session_start();

?><!DOCTYPE html>
<head>
<title>Free WIFI</title>
<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
<style type="text/css">
body {margin: 0px; padding: 0px; font-family: "arial","helvetica", sans-serif; font-style: normal; background: #FFF; color: #333;}
#header  {background: #00ADEF; color: #FFF; margin: 0px 0px 5px 0px; padding: 2px;
    -webkit-box-shadow: 0 2px 10px rgba(0,0,0,.25);
    -moz-box-shadow:  0 2px 10px rgba(0,0,0,.25);
    box-shadow:  0 2px 10px rgba(0,0,0,.25);  
}
#header h1 { font-size:medium; font-weight:bold; padding:2px 10px; }
#content {padding: 0 3px;}
#footer  {clear: left; border-top: 1px solid #00ADEF; color: #FFF; background: #00ADEF; margin: 0; padding: 10px 5px; font-size: small;
    -webkit-box-shadow: 0 -2px 10px rgba(0,0,0,.25);
    -moz-box-shadow:  0 -2px 10px rgba(0,0,0,.25);
    box-shadow:  0 -2px 10px rgba(0,0,0,.25); 
}
#footer a,#footer a:visited { font-weight:bold; color:#fff; text-decoration:underline; }
#footer a:hover,#footer a:active { color:#00ADEF; background:#fff; }
h2 {font-size: medium; font-weight: bold; margin: 0px; padding: 5px 0px 5px 0px;}
ul {padding: 0; margin: 0px; list-style-type:none; list-style-position:inside;}
li {padding: 0; border-bottom:1px solid #ddd;}
li a {display:block;padding:2px;}
a {text-decoration: none;color:#017DC5;}
a b, a:hover {background:#00ADEF; padding:2px; color:#fff;}
a b { display:block; }
.cl {clear: both; height: 5px;}
.item {float:left; padding:5px; display:block; }
.tw, .fb { display:inline; }
#header img { width:100px; height:auto; }
.name { font-weight:bold;  }
.time { color:#999;  }
#content li { padding:10px 5px; }
#content li:hover  { background-color:#eee; 
    font-weight:bold;
    -webkit-box-shadow: inset 0 2px 10px rgba(0,0,0,.25);
    -moz-box-shadow: inset 0 2px 10px rgba(0,0,0,.25);
    box-shadow: inset 0 2px 10px rgba(0,0,0,.25);  
}
input,select,textarea { padding:5px; -moz-border-radius:5px; -webkit-border-radius:5px; border-radius:5px; border:1px solid #ccc; width:95%; }
a.feat,a.feat:hover,a.feat:active { font-weight:bold; background:transparent; color:#017DC5;}
#billboard {padding:5px;}
.sender { color:#FF00CC; font-weight:bold; }
.msg {font-weight:normal !important;}
.timestamp { font-weight:normal !important; font-size:small; color:#999; }
.nav { padding:5px; text-align:center; }
p {margin:0;padding:0;margin-bottom:.5em;}
</style>
</head>
<body>
<div id="header">
    <h1>Free WIFI</h1>
</div>
<div id="content">

<?php if (isset($_REQUEST['next'])): ?>

    <p>Iklan lagi...., ga sabar? Klik <a href="<?php echo $_REQUEST['next']; ?>">DISINI</a></a>

    <meta name="refresh" content="5; URL=<?php echo $_REQUEST['next']; ?>" />
    <script type="text/javascript">
    window.onload=function() {
        setTimeout(function(){ location.href="<?php echo $_REQUEST['next']; ?>"; },5000);
    }
    </script>

<?php elseif ( isset($_REQUEST['auth']) ): ?>

    <p>Ini halaman iklan, silahkan tunggu sebentar aja untuk login, ga sabar? Klik <a href="javascript:document.sendin.submit()">DISINI</a></a>
    
	<form name="sendin" action="<?php echo $_REQUEST['login']; ?>" method="post">
		<input type="hidden" name="username" value="admin" />
		<input type="hidden" name="password" value="admin" />
		<input type="hidden" name="dst" value="<?php echo $_REQUEST['dst']; ?>" />
	</form>

	<script type="text/javascript">
    window.onload=function() {
        setTimeout(function(){ document.sendin.submit(); },5000);
    }
	</script>


<?php else:

    //http://www.indocrm.com/freewifi.php?login=$(link-login-only)&dst=$(link-orig-esc)&username=T-$(mac-esc)
    $login = $_REQUEST['login'];
    $dst = $_REQUEST['dst'];
    $username = $_REQUEST['username'];
    
    $link = "$login?username=$username&dst=$dst";

?>
    <p>Ini halaman iklan, silahkan tunggu sebentar aja untuk login, ga sabar? Klik <a href="<?php echo $link; ?>">DISINI</a></a>
    
    <meta name="refresh" content="5; URL=<?php echo $link; ?>" />
    <script type="text/javascript">
    window.onload=function() {
        setTimeout(function(){ location.href="<?php echo $link; ?>"; },5000);
    }
    </script>

<?php endif; ?>

</div>
<div id="footer">&copy; 2012 FreeWIFI. Powered by <a href="http://www.simetri.web.id/">SIMETRI</a></div>
</body>
</html>