<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

$page_header = '';
$page_title = 'Desain Template E-Mail';
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
                        <p>Berikut adalah desain template untuk broadcast e-mail. <a href="<?php echo site_url('design/create'); ?>">Buat template baru</a></p>
                    </div>
                    
                    <?php if ( $my_templates ): ?>
                    <div id="mydesign" class="itemlist">
                        <h2>Desain Anda</h2>
                        
                        <?php foreach($my_templates as $template): ?>
                        <div class="template-item">
                            <a href="<?php echo site_url('design/create?id='.urlencode($template->template_id)); ?>">
                            <?php if ( $template->thumbnail ): ?>
                            <img src="<?php echo base_url('u/'.$template->thumbnail); ?>" width="100" />
                            <?php else: ?>
                            <img src="<?php echo base_url('static/no_image.gif'); ?>" width="100" />
                            <?php endif; ?>
                            <br /><?php echo htmlentities($template->name); ?></a>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="cl"></div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ( $templates ): ?>
                    <div id="alldesign" class="itemlist">
                        <h2>Desain Kami</h2>
                        
                        <?php foreach($templates as $template): ?>
                        <div class="template-item">
                            <a href="<?php echo site_url('design/create?id='.urlencode($template->template_id)); ?>">
                            <?php if ( $template->thumbnail ): ?>
                            <img src="<?php echo base_url('u/'.$template->thumbnail); ?>" width="100" />
                            <?php else: ?>
                            <img src="<?php echo base_url('static/no_image.gif'); ?>" width="100" />
                            <?php endif; ?>
                            <br /><?php echo htmlentities($template->name); ?></a>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="cl"></div>
                    </div>
                    <?php endif; ?>

                </div>
		<div id="sidebar">
                    <!-- menu admin -->
                    <?php $this->load->view('dashboard_menu'); ?>
                </div>

                <div class="cl"></div>

<?php $this->load->view('footer');
