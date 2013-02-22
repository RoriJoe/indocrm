<!DOCTYPE html>
<html>
<head>
    <title><?php 
        $the_title = "Invoice #{$invoice->invoice_id}";
        echo $the_title . ' - ' . htmlentities($this->config->item('site_name')); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" media="print,screen,mobile" href="<?php echo base_url('static/simetri.css'); ?>" />
    <style type="text/css" media="print">
        .hideprint{display:none}
    </style>
    <style type="text/css" media="all">
        html,body{background:#fff;margin:0 !important;padding:0 !important; height:100% !important;}
        #bill {background:none}
        #invoicefoot{background:none}
        #invoicefoot div {width:100%}
        p{line-height:1.2; margin-bottom: 1em;}
    </style>
</head>
<body>

<?php echo $invoice_html; ?>

<script type="text/javascript">
    function printPage() { print(); }
</script>    

</body>
<html>