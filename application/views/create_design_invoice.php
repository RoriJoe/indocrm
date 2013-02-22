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
    ROOT_URL = '.json_encode(base_url()).';
</script>
<script type="text/javascript" src="'.base_url('static/models.js').'"></script>
<script type="text/javascript" src="'.base_url('static/upload_asset.js').'"></script>
<script type="text/javascript" src="'.base_url('static/tiny_mce/tiny_mce.js').'"></script>
<script type="text/javascript" src="'.base_url('static/tinymceinit.js').'"></script>
';
$page_title = 'Desain Template Invoice';
$this->load->view('header', array('page_title' => $page_title, 'page_header' => $page_header));
?>

                <div id="design-content" class="card">
                    <h1><?php echo $page_title; ?></h1>
                    
                    <?php
                        $msg = flashmsg_get();
                        if ($msg)
                        {
                            echo '<div class="msgbox warning">'.htmlentities($msg).'</div>';
                        }
                    ?>
                    
                    <div id="design-area" style="width:95%; margin:5px;">
                        
                        <form method="post" action="<?php echo site_url('invoices/create'); ?>">
                            <?php if ( isset($template_id) ) echo '<input type="hidden" name="template_id" value="'.$template_id.'" />'; ?>
                            
                            <div class="formfield rl">
                                <label for="id_name">Nama Template</label>
                                <input type="text" id="id_name" name="name" size="30" value="<?php if (isset($name)) echo htmlentities($name); ?>" />
                                <input id="uploadLink" type="button" class="btn rr" value="Upload Gambar" />
                            </div>
                            
                            <div class="tpltool">
                                <fieldset class="tplf">
                                    <legend>Template Pelanggan</legend>
                                    <a href="#" id="customerTag" class="post-tag">Nama</a>
                                    <a href="#" id="emailTag" class="post-tag">E-Mail</a>
                                    <a href="#" id="phoneTag" class="post-tag">Telepon</a>
                                    <a href="#" id="mobileTag" class="post-tag">Handphone</a>
                                    <a href="#" id="addressTag" class="post-tag">Alamat</a>
                                </fieldset>

                                <fieldset class="tplf">
                                    <legend>Template Invoice</legend>
                                    <a href="#" id="invNoTag" class="post-tag">Kode Invoice</a>
                                    <a href="#" id="invDateTag" class="post-tag">Tanggal</a>
                                    <a href="#" id="invDueTag" class="post-tag">Due</a>
                                    <a href="#" id="invTotalTag" class="post-tag">Total</a>
                                    <a href="#" id="invTaxTag" class="post-tag">Tax</a>
                                    <a href="#" id="invDiscTag" class="post-tag">Discount</a>
                                    <a href="#" id="invGrandTotalTag" class="post-tag">Grand Total</a>
                                </fieldset>

                                <fieldset class="tplf">
                                    <legend>Detil Invoice</legend>
                                    <a href="#" id="invDetailStart" class="post-tag">Detail Start</a>
                                    <a href="#" id="invDetailHeader" class="post-tag">Detail Header</a>
                                    <a href="#" id="invDetailEnd" class="post-tag">Detail End</a>
                                    <a href="#" id="invDetailNo" class="post-tag">Nomer Item</a>
                                    <a href="#" id="invDetailCode" class="post-tag">Kode Item</a>
                                    <a href="#" id="invDetailDesc" class="post-tag">Nama Item</a>
                                    <a href="#" id="invDetailPrice" class="post-tag">Harga</a>
                                    <a href="#" id="invDetailQty" class="post-tag">Jumlah</a>
                                    <a href="#" id="invDetailSubtotal" class="post-tag">Subtotal</a>
                                    <a href="#" id="invDetailDisc" class="post-tag">Discount</a>
                                    <a href="#" id="invDetailTax" class="post-tag">Pajak</a>
                                    <a href="#" id="invDetailTotal" class="post-tag">Total</a>
                                </fieldset>
                                
                                <fieldset class="tplf last">
                                    <legend>Template Perusahaan</legend>
                                    <a href="#" id="companyName" class="post-tag">Nama</a>
                                    <a href="#" id="companyEmail" class="post-tag">E-Mail</a>
                                    <a href="#" id="companyWeb" class="post-tag">Website</a>
                                    <a href="#" id="companyAddress" class="post-tag">Alamat</a>
                                </fieldset>
                                
                                <div class="cl"></div>
                            </div>
                            
                            <script type="text/javascript">
                                Ext.onReady(function() {
                                    Ext.get("customerTag").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%CUSTOMER_NAME%}');
                                    });
                                    Ext.get("emailTag").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%CUSTOMER_EMAIL%}');
                                    });
                                    Ext.get("phoneTag").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%CUSTOMER_PHONE%}');
                                    });
                                    Ext.get("mobileTag").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%CUSTOMER_MOBILE%}');
                                    });
                                    Ext.get("addressTag").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%CUSTOMER_ADDRESS%}');
                                    });
                                    
                                    //--invoice
                                    Ext.get("invNoTag").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%INVOICE_ID%}');
                                    });
                                    Ext.get("invDateTag").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%INVOICE_DATE%}');
                                    });
                                    Ext.get("invDueTag").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%INVOICE_DUE%}');
                                    });
                                    Ext.get("invTotalTag").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%INVOICE_TOTAL%}');
                                    });
                                    Ext.get("invTaxTag").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%INVOICE_TAX%}');
                                    });
                                    Ext.get("invDiscTag").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%INVOICE_DISCOUNT%}');
                                    });
                                    Ext.get("invGrandTotalTag").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%INVOICE_GRAND_TOTAL%}');
                                    });

                                    //--detail
                                    Ext.get("invDetailStart").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%DETAIL_START%}');
                                    });
                                    Ext.get("invDetailHeader").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%DETAIL_HEADER_SEPARATOR%}');
                                    });
                                    Ext.get("invDetailEnd").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%DETAIL_END%}');
                                    });
                                    Ext.get("invDetailNo").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%ITEM_NO%}');
                                    });
                                    Ext.get("invDetailCode").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%ITEM_CODE%}');
                                    });
                                    Ext.get("invDetailDesc").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%ITEM_DESC%}');
                                    });
                                    Ext.get("invDetailPrice").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%ITEM_PRICE%}');
                                    });
                                    Ext.get("invDetailQty").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%ITEM_QTY%}');
                                    });
                                    Ext.get("invDetailDisc").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%ITEM_DISCOUNT%}');
                                    });
                                    Ext.get("invDetailTax").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%ITEM_TAX%}');
                                    });
                                    Ext.get("invDetailSubtotal").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%ITEM_SUBTOTAL%}');
                                    });
                                    Ext.get("invDetailTotal").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%ITEM_TOTAL%}');
                                    });
                                    
                                    //
                                    
                                    Ext.get("companyName").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%COMPANY_NAME%}');
                                    });
                                    Ext.get("companyEmail").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%COMPANY_EMAIL%}');
                                    });
                                    Ext.get("companyWeb").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%COMPANY_WEBSITE%}');
                                    });
                                    Ext.get("companyAddress").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%COMPANY_ADDRESS%}');
                                    });
                                });
                            </script>
                             
                            <textarea id="txt" name="txt" cols="70" rows="30" style="width:100%;"><?php if (isset($template)) echo htmlentities($template); ?></textarea>
                            
                            <div id="buttonarea" style="text-align:right; margin:10px">
                                <input type="submit" class="btn" value="Save" />
                                <a href="<?php echo site_url('invoices/design'); ?>">Cancel</a>
                            </div>

                        </form>
                    </div>

                    
                </div>

            <div class="cl"></div>

<?php $this->load->view('footer');
