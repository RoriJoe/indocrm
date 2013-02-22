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
    <script type="text/javascript" src="'.base_url('static/models.js').'"></script>
    <script type="text/javascript" src="'.base_url('static/campaign.js').'"></script>
    <style type="text/css">
    
    .x-action-col-cell img {height: 16px;width: 16px;cursor: pointer;}
    .x-action-col-cell img.buy-col {background-image: url('.base_url('static/fam/accept.png').');}
    .x-action-col-cell img.alert-col {background-image: url('.base_url('static/fam/error.png').');}
    .x-ie6 .x-action-col-cell img.buy-col {background-image: url('.base_url('static/fam/accept.gif').');}
    .x-ie6.x-action-col-cell img.alert-col {background-image: url('.base_url('static/fam/error.gif').');}
    .x-ie6 .x-action-col-cell img {position:relative;top:-1px;}
        
    </style>
';


$page_title = 'History Campaign';
$this->load->view('header', array('page_title' => $page_title, 'page_header' => $page_header));
?>

                <div id="content">
                    <h1><?php echo $page_title; ?></h1>
                    
                    <?php
                        $msg = flashmsg_get();
                        if ($msg)
                        {
                            echo '<div class="msgbox warning">'.htmlentities($msg).'</div>';
                        }
                    ?>
                    
                    <div class="dialog">
                        <p>Untuk merencanakan promosi, broadcast e-mail, silahkan buat campaign baru.</p>
                        <p>Setelah anda buat, silahkan set ke mode publish agar bisa dikirim oleh sistem kami</p>
                        
                        <div class="actionarea">
                            <ul>
                                <li><a href="<?php echo site_url('campaign/create'); ?>">Buat Campaign Baru</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div id="campaign-grid" style="width:100%; height:100%; margin-top:20px;"></div>
                </div>
		<div id="sidebar">
                    <!-- menu admin -->
                    <?php $this->load->view('dashboard_menu'); ?>
                </div>

                <div class="cl"></div>

<?php $this->load->view('footer');
