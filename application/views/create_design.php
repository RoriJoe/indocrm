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
$page_title = 'Desain Template E-Mail';
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
                        
                        <form method="post" action="<?php echo site_url('design/create'); ?>">
                            <?php if ( isset($template_id) ) echo '<input type="hidden" name="template_id" value="'.$template_id.'" />'; ?>
                            
                            <div class="formfield rl">
                                <label for="id_name">Nama Template</label>
                                <input type="text" id="id_name" name="name" size="30" value="<?php if (isset($name)) echo htmlentities($name); ?>" />
                                <input id="uploadLink" type="button" class="btn rr" value="Upload Gambar" />
                            </div>
                            
                            <div class="tpltool">

                                <fieldset class="tplx">
                                    <legend>Template Pelanggan</legend>
                                    <a href="#" id="customerTag" class="post-tag">Nama</a>
                                    <a href="#" id="emailTag" class="post-tag">E-Mail</a>
                                    <a href="#" id="phoneTag" class="post-tag">Telepon</a>
                                    <a href="#" id="mobileTag" class="post-tag">Handphone</a>
                                </fieldset>

                                <fieldset class="tplx">
                                    <legend>Template Campaign</legend>
                                    <a href="#" id="campaignTitle" class="post-tag">Judul</a>
                                    <a href="#" id="campaignDescription" class="post-tag">Keterangan</a>
                                    <a href="#" id="sentDate" class="post-tag">Tanggal Kirim</a>
                                </fieldset>

                                <fieldset class="tplx">
                                    <legend>Template Usaha</legend>
                                    <a href="#" id="companyName" class="post-tag">Nama</a>
                                    <a href="#" id="companyEmail" class="post-tag">E-Mail</a>
                                    <a href="#" id="companyWeb" class="post-tag">Website</a>
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

                                    Ext.get("campaignTitle").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%CAMPAIGN_TITLE%}');
                                    });
                                    Ext.get("campaignDescription").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%CAMPAIGN_DESCRIPTION%}');
                                    });
                                    Ext.get("sentDate").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%SENT_DATE%}');
                                    });


                                    Ext.get("companyName").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%COMPANY_NAME%}');
                                    });
                                    Ext.get("companyEmail").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%COMPANY_EMAIL%}');
                                    });
                                    Ext.get("companyWeb").on('click',function() {
                                        tinyMCE.execCommand("mceInsertContent", false, '{%COMPANY_WEBSITE%}');
                                    });
                                });
                            </script>

                            <textarea id="txt" name="txt" cols="70" rows="25" style="width:100%;"><?php if (isset($template)) echo htmlentities($template); ?></textarea>
                            
                            <div id="buttonarea" style="text-align:right; margin:10px">
                                <input type="submit" class="btn" value="Save" />
                                <a href="<?php echo site_url('design/'); ?>">Cancel</a>
                            </div>

                        </form>
                    </div>

                    
                </div>

            <div class="cl"></div>

<?php $this->load->view('footer');
