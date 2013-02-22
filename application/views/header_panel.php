<!DOCTYPE html>
<html>
<head>
    <title><?php if (isset($page_title)) echo $page_title . " - "; ?><?php echo $this->config->item('site_name'); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" media="screen,mobile" href="<?php echo base_url('static/css/ext-all-access.css'); ?>" />
    <style type="text/css" media="screen">
        html,body,.x-panel-header-text-default,.x-body{font-family: arial !important;}
        a {color:#FFCC33;}
        a:hover {text-decoration:none; background:#FFCC33;color:#000; }
        h1,h2,h3,h4,h5{ font-family: "Trebuchet MS", "Times New Roman", serif; }
    </style>
    <script type="text/javascript" src="<?php echo base_url('static/bootstrap.js'); ?>"></script>
    <?php if ( isset($page_header) ) echo $page_header; ?>
</head>
<body>
