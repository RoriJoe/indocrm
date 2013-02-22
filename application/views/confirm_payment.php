<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

$page_header = '
    <script type="text/javascript">
        STATIC_URL = '.json_encode(base_url('/static')).';
        BASE_URL = '.json_encode(site_url()).';
    </script>
';


$page_title = 'Konfirmasi Pembayaran Invoice #'.$id;
$this->load->view('header', array('page_title' => $page_title, 'page_header' => $page_header));
?>

                <div class="card" id="confirmwindow">
                    <h1><?php echo $page_title; ?></h1>
                    
                    <?php
                        $msg = flashmsg_get();
                        if (!$msg && $error) $msg = $error;
                        if ($msg)
                        {
                            echo '<div class="msgbox warning">'.htmlentities($msg).'</div>';
                        }
                    ?>
                    
                    <?php if ($success): ?>
                        <p>Konfirmasi sudah kami terima. Akan kami cocokan dengan data rekening kami. Setelah tercocokkan, kami akan update via e-mail.
                        Silahkan lanjutkan ke halaman <a href="<?php echo site_url('dashboard');?>">dashboard</a>.</p>
                    <?php else: ?>
                        <div class="dialog">
                            <p>Silahkan konfirmasikan pembayaran anda</p>
                        </div>

                        <div id="confirm-grid" style="margin:auto; width:75%; height:100%; margin-top:20px;">

                            <form method="post" action="<?php echo site_url('bills/confirm'); ?>" id="paymentform">
                                    <div class="formfield">
                                            <label for="id_invoice_id">Invoice #</label>
                                            <input id="id_invoice_id" type="text" name="id" value="<?php echo intval($id); ?>" size="30" placeholder="nomer invoice" />
                                    </div>
                                    <div class="formfield">
                                            <label for="id_pay_from">Nomer Rekening</label>
                                            <input id="id_pay_from" type="text" name="pay_from" value="<?php echo h($pay_from); ?>" size="30" placeholder="nomer rekening" />
                                    </div>
                                    <div class="formfield">
                                            <div id="tot">
                                            <label for="id_pay_total">Jumlah Transfer</label>
                                            <input id="id_pay_total" type="text" name="pay_total" value="<?php echo h($pay_total); ?>" size="30" placeholder="total transfer" />
                                            </div>
                                    </div>
                                    <div class="formfield">
                                            <div id="tgl">
                                            <label for="id_pay_date">Tanggal Transfer (tahun-bulan-tanggal)</label>
                                            <input id="id_pay_date" type="text" name="pay_date" value="<?php echo h($pay_date); ?>" size="30" placeholder="tanggal transfer" />
                                            </div>
                                    </div>

                                    <div class="formfield" class="buttonarea">
                                            <input type="submit" value="Submit Konfirmasi" class="btn signup-btn" />
                                    </div>
                            </form>

                        </div>
                        
                        <script type="text/javascript">
                            Ext.onReady(function() {
                                Ext.get('tgl').update('');
                                Ext.get('tot').update('');
                                Ext.create('Ext.form.field.Date', {
                                    renderTo: Ext.get('tgl'),
                                    fieldLabel: 'Tanggal Transfer',
                                    maxValue: new Date(),
                                    name: 'pay_date',
                                    format: 'Y-m-d',
                                    labelWidth:160,
                                    value: '<?php echo $pay_date; ?>'
                                });
                                Ext.create('Ext.form.field.Number', {
                                    renderTo: Ext.get('tot'),
                                    fieldLabel: 'Jumlah Transfer (Rp)',
                                    minValue: 1,
                                    name: 'pay_total',
                                    format: '0',
                                    labelWidth:160,
                                    value: '<?php echo $pay_total; ?>'
                                });
                            });
                        </script>
                    <?php endif; ?>

                </div>

                <div class="cl"></div>

<?php $this->load->view('footer');
