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
    </style>
</head>
<body>
<div id="bill">
    
    <div id="invoiceHeading" style="margin-bottom:15px; padding-top:50px;">
    <h1>Invoice #<?php echo $invoice->invoice_id; ?></h1>
    </div>
    
    <table style="width:100%;margin:0 auto 10px;">
        <tr>
            <td style="width:50%; vertical-align:top;">
                <p class="date">
                    Date: <?php echo date('j/M/Y', strtotime($invoice->create_date)); ?><br />
                    Due: <?php echo date('j/M/Y', strtotime($invoice->due_date)); ?><br />
                </p>
            </td>
            <td style="width:50%; vertical-align:top;">
                <?php if ($invoice->status == 3): ?>
                <h2>Lunas</h2>
                <?php else: ?>
                <p class="hideprint"><a id="confirmButton" href="<?php echo site_url('bills/confirm?id='.$invoice->invoice_id); ?>" target="top" onclick="if (typeof parent != 'undefined') parent.location.href='<?php echo site_url('bills/confirm?id='.$invoice->invoice_id); ?>'; else location.href='<?php echo site_url('bills/confirm?id='.$invoice->invoice_id); ?>'; return false;"><b>Konfirmasi Pembayaran</b></a></p>
                <?php endif; ?>
            </td>
        </tr>
    </table>
    
    <table style="width:90%;margin:0 auto 10px;">
        <tr>
            <td style="width:50%; vertical-align:top;">
                <h3>To: <?php echo $invoice->to_name; ?></h3>
                <p>
                    <b><?php echo $invoice->to_company; ?></b><br />
                    <?php echo nl2br($invoice->to_address); ?></b>
                </p>
            </td>
            <td style="width:50%; vertical-align:top;">
                <h3>Pay To: PT Sinar Media Tiga</h3>
                <p>
                    JL Raya Sulfat 96C, Malang<br />
                    Jawa Timur<br />
                    Indonesia, 65123<br />
                    0341-406633<br />
                </p>
            </td>
        </tr>
    </table>
    
    <table id="invoicedetail">
        <thead>
        <tr>
            <th>Description</th>
            <th class="amt">Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $tax = 0;
        foreach($details as $det): ?>
        <tr>
            <td style="padding:5px;"><?php echo h($det->description); ?><?php if ($det->tax) echo '*'; ?></td>
            <td style="padding:5px;text-align:right;"><?php echo h(number_format($det->total, 2, '.', ',')); ?></td>
        </tr>
        <?php 
        $tax+= $det->tax;
        endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <th class="d">TAX</th>
            <th class="amt"><?php echo h(number_format($det->tax, 2, '.', ',')); ?></th>
        </tr>
        <tr>
            <th class="d">TOTAL</th>
            <th class="amt"><?php echo h(number_format($det->total, 2, '.', ',')); ?></th>
        </tr>
        </tfoot>
    </table>
    
    <div id="invoicefoot">
        <div>
            <p>
                <b>Terima kasih</b><br /><br />
                Pembayaran via transfer Rekening<br />
                <b>BCA</b> an <b>Joko Siswanto</b><br />
                <b>448.028.3339</b>
            </p>
        </div>
        
        <div class="cl"></div>
    </div>
    
</div>    
    
    <script type="text/javascript">
    function printPage() { print(); }
    </script>    

</body>
<html>