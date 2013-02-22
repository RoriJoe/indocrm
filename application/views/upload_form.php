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
    <script type="text/javascript" src="'.base_url('static/upload_customers.js').'"></script>
';
$page_title = 'Upload Data Pelanggan';
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
                        <p>Anda bisa upload data pelanggan dengan beberapa format yaitu:</p>

                        <ul>
                            <li>
                                <h3>Excel File (XLS)</h3>
                                <p>Aplikasi ini mendukung format XLS (Excel 97/2000/XP), baris pertama merupakan nama kolom, dan selanjutnya adalah kolom</p>
                                <p>Aplikasi ini tidak mendukung banyak sheet, jadi hanya sheet pertama yang akan diproses</p>
                                <p><a href="<?php echo base_url('contoh1.xls'); ?>"><b>Contoh File Excel</b></a></p>
                            </li>
                            <li>
                                <h3>CSV File (CSV)</h3>
                                <p>Jika anda menggunakan excel baru, silahkan pilih File, Save As kemudian pilih CSV, baris pertama adalah header dari kolom</p>
                                <p><a href="<?php echo base_url('contoh1.csv'); ?>"><b>Contoh File CSV</b></a></p>
                            </li>
                        </ul>
                        
                    </div>
                    
                    <div id="upload-form" style="width:100%; height:100%; padding:10px;"></div>

                </div>
		<div id="sidebar">
                    <!-- menu admin -->
                    <?php $this->load->view('dashboard_menu'); ?>
                </div>

                <div class="cl"></div>

<?php $this->load->view('footer');
