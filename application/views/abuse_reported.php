<?php
$page_title = 'Laporan Penyalahgunaan Diterima';
$this->load->view('header', array('page_title' => $page_title));
?>

		<div class="card">
                    
			<h1><?php echo $page_title; ?></h1>
                        
                        <div class="left75" id="abusenote">

                            <?php

                            $msg = flashmsg_get();
                            if ($msg)
                            {
                                echo '<div class="msgbox warning">'.htmlentities($msg).'</div>';
                            }

                            ?>
                            
                            <p><a href="<?php echo site_url(); ?>">Silahkan lanjutkan ke halaman depan</a></p>
                            
                        </div>
                        
                        <div class="cl"></div>
                </div>


<?php $this->load->view('footer');
