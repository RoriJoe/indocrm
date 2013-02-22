<?php

@set_magic_quotes_runtime(0);
session_start();
define('BASEPATH', dirname(__FILE__));
include BASEPATH."/application/libraries/paging.php";
include BASEPATH."/application/helpers/my_helper.php";

define('CRYPT_KEY', hash("SHA256", 'ini password untuk enkripsi cookie ya, rahasia', true));
define('COOKIE_TIMEOUT', 86400*30); //1year

function connectdb()
{
    global $conn;
    
    if (!isset($conn))
    {
        include "application/config/database.php";
        $conn = mysql_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password']);
        mysql_select_db($db['default']['database'], $conn);
    }
    
    return $conn;
}

function encrypt( $text )
{    
    # Add PKCS7 padding.
    $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    if (($pad = $block - (strlen($text) % $block)) < $block) 
    {
        $text .= str_repeat(chr($pad), $pad);
    }
    
    $size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($size, MCRYPT_DEV_URANDOM);
    
    return base64_encode($iv.mcrypt_encrypt(MCRYPT_RIJNDAEL_256, CRYPT_KEY, $text, MCRYPT_MODE_CBC,$iv));
}

function decrypt( $text  )
{
    $text = base64_decode($text);
    
    $size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    $iv = substr($text, 0, $size);
    $str = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, CRYPT_KEY, substr($text, $size), MCRYPT_MODE_CBC, $iv);
    
    $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
    $pad = ord($str[($len = strlen($str)) - 1]);
    if ($pad && $pad < $block && preg_match('/' . chr($pad) . '{' . $pad . '}$/', $str)) 
    {
        return substr($str, 0, strlen($str) - $pad);
    }
    
    return $str;
}

function apps_get_login()
{
    $data = isset($_COOKIE['d']) ? $_COOKIE['d'] : '';
    $c = isset($_COOKIE['c']) ? $_COOKIE['c'] : '';
    if ($data)
    {
        $data = decrypt($data);
        setcookie('d', encrypt($data), time()+COOKIE_TIMEOUT, '/' );
        return $data;
    }

    return false;
}

function apps_logout()
{
    setcookie('d', '', time()+COOKIE_TIMEOUT, '/' );
}

function apps_login()
{
    $msisdn = isset($_REQUEST['msisdn']) ? $_REQUEST['msisdn'] : '';
    $msisdn = preg_replace('~[^0-9]~', '', $msisdn);
    if ($msisdn)
    {
        $data = encrypt($msisdn);
        setcookie('d', $data, time()+COOKIE_TIMEOUT, '/' );
        return true;
    }
    return false;
}

function smilify($subject)
{
    $smilies = array(
		':mrgreen:' => 'icon_mrgreen.gif',
		':neutral:' => 'icon_neutral.gif',
		':twisted:' => 'icon_twisted.gif',
		  ':arrow:' => 'icon_arrow.gif',
		  ':shock:' => 'icon_eek.gif',
		  ':smile:' => 'icon_smile.gif',
		    ':???:' => 'icon_confused.gif',
		   ':cool:' => 'icon_cool.gif',
		   ':evil:' => 'icon_evil.gif',
		   ':grin:' => 'icon_biggrin.gif',
		   ':idea:' => 'icon_idea.gif',
		   ':oops:' => 'icon_redface.gif',
		   ':razz:' => 'icon_razz.gif',
		   ':roll:' => 'icon_rolleyes.gif',
		   ':wink:' => 'icon_wink.gif',
		    ':cry:' => 'icon_cry.gif',
		    ':eek:' => 'icon_surprised.gif',
		    ':lol:' => 'icon_lol.gif',
		    ':mad:' => 'icon_mad.gif',
		    ':sad:' => 'icon_sad.gif',
		      '8-)' => 'icon_cool.gif',
		      '8-O' => 'icon_eek.gif',
		      ':-(' => 'icon_sad.gif',
		      ':-)' => 'icon_smile.gif',
		      ':-?' => 'icon_confused.gif',
		      ':-D' => 'icon_biggrin.gif',
		      ':-P' => 'icon_razz.gif',
		      ':-o' => 'icon_surprised.gif',
		      ':-x' => 'icon_mad.gif',
		      ':-|' => 'icon_neutral.gif',
		      ';-)' => 'icon_wink.gif',
		       '8)' => 'icon_cool.gif',
		       '8O' => 'icon_eek.gif',
		       ':(' => 'icon_sad.gif',
		       ':)' => 'icon_smile.gif',
		       ':?' => 'icon_confused.gif',
		       ':D' => 'icon_biggrin.gif',
		       ':P' => 'icon_razz.gif',
		       ':o' => 'icon_surprised.gif',
		       ':x' => 'icon_mad.gif',
		       ':|' => 'icon_neutral.gif',
		       ';)' => 'icon_wink.gif',
		      ':!:' => 'icon_exclaim.gif',
		      ':?:' => 'icon_question.gif',
		);

    $replace = array();
    foreach ($smilies as $smiley => $imgName)
    {
        array_push($replace, '<img src="static/smilies/'.$imgName.'" alt="'.$smiley.'" />');
    }
    $subject = str_replace(array_keys($smilies), $replace, $subject);
    return $subject;
}

$_SESSION['msg'] = '';

if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
    $method = isset($_POST['m']) ? $_POST['m'] : '';
    if ($method == 'login' && apps_login())
    {
        header("Location: $_SERVER[PHP_SELF]?loggedin=true");
        exit;
    }
    else
    {
        $_SESSION['msg'] = 'Maaf, format nomer handphone anda tidak valid';
    }
}

$msisdn = apps_get_login();
if ($msisdn)
{
    //user login
    if ( isset($_GET['logout']) )
    {
        apps_logout();
        unset($msisdn);
        header("Location: $_SERVER[PHP_SELF]?loggedout=true");
        exit;
    }
}

$radio = isset($_GET['r']) ? $_GET['r'] : '';
$radiolist = array('pro1', 'pro2', 'pro3', 'pro4');
if (!in_array($radio, $radiolist)) $radio='';

$feat = isset($_GET['f']) ? $_GET['f'] : '';
$featlist = array('request', 'wall');
if (!in_array($feat, $featlist)) $feat='';
        
?><!DOCTYPE html>
<head>
<title>RRI Community</title>
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
    <?php if ( in_array('radio', $radiolist) ): ?>
    <img src="radio/<?php echo $radio; ?>.png" />
    <?php endif; ?>
    <h1>RRI Malang Community</h1>
    
    <?php if ($msisdn): ?>
    <div style="position:absolute; top:5px; right:5px;"><?php echo htmlspecialchars($msisdn); ?></div>
    <?php endif; ?>
</div>
<div id="billboard">
    <p>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>">Home</a>
        <?php if ($radio) echo ' &raquo; <a href="'.$_SERVER['PHP_SELF'].'?r='.$radio.'">'.strtoupper($radio).'</a>'; ?>
        <?php if ($feat) echo ' &raquo; <a href="'.$_SERVER['PHP_SELF'].'?r='.$radio.'&f='.$feat.'">'.strtoupper($feat).'</a>'; ?>
    </p>
</div>
<div id="content">
<?php 

if (!$msisdn)
{
    include BASEPATH . '/radio/loginform.php';
}
else
{
    if (!$radio)
    {
        include BASEPATH . '/radio/selectradio.php';
    }
    else
    {
        $feat = 'request';
        include BASEPATH . '/radio/request.php';
    }
}


?>

</div>
<div id="footer">&copy; 2012 RRI Malang. Powered by <a href="http://www.simetri.web.id/">SIMETRI</a></div>
</body>
</html>