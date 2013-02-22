<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */
error_reporting(E_ALL);

$counter = 0;

$sql = "SELECT counter_limit FROM clients WHERE client_id = '".$this->orca_auth->user->client_id."'";
$q = $this->db->query($sql);
$res = $q->result();
$counter = $res[0]->counter_limit;

$page_header = '
	<script type="text/javascript">
		STATIC_URL = '.json_encode(base_url('/static')).';
		BASE_URL = '.json_encode(site_url()).';
	</script>
	<script type="text/javascript" src="'.base_url('static/models.js').'"></script>
	<script type="text/javascript" src="'.base_url('static/campaign_sms.js').'"></script>
	<style type="text/css">
	
	.x-action-col-cell img {height: 16px;width: 16px;cursor: pointer;}
	.x-action-col-cell img.buy-col {background-image: url('.base_url('static/fam/accept.png').');}
	.x-action-col-cell img.alert-col {background-image: url('.base_url('static/fam/error.png').');}
	.x-ie6 .x-action-col-cell img.buy-col {background-image: url('.base_url('static/fam/accept.gif').');}
	.x-ie6.x-action-col-cell img.alert-col {background-image: url('.base_url('static/fam/error.gif').');}
	.x-ie6 .x-action-col-cell img {position:relative;top:-1px;}
		
	</style>
';

$page_title = 'History Broadcast SMS';
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
                        <p>Untuk merencanakan promosi, broadcast sms, silahkan Klik tombol Tambah atau Link di bawah ini.</p>
                        <?php if (in_array($this->orca_auth->user->client_id, array(11,45,46,47,70))): ?>
                        
                         <p style="color:red;font-weight:bold;">PERHATIAN!</p> Anda Hanya diperkenankan mengirimkan <strong style="color:red;">Broadcast SMS 500 SMS per hari</strong>. Apabila Anda melakukan SMS melebihi quota harian, maka SMS tersebut tidak akan dikirimkan.</p><p>Quota harian Anda sekarang <strong><?php echo (500-$counter); ?></strong> SMS.</p>
                         <?php endif; ?>
                        
                        <div class="actionarea">
                            <ul>
                                <li><a href="<?php echo site_url('campaign/create_broadcast'); ?>">Buat Broadcast SMS</a></li>
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
