<?php
$page_title = 'Formulir Berhenti Menerima E-Mail';
$this->load->view('header', array('page_title' => $page_title));
?>

		<div class="card">
                    
			<h1><?php echo $page_title; ?></h1>
                        
                        <div class="left75">
                            
                            <h2>Berhenti Menerima E-Mail</h2>
                            
                            <?php

                            $msg = flashmsg_get();
                            if ($msg)
                            {
                                echo '<div class="msgbox warning">'.htmlentities($msg).'</div>';
                            }

                            ?>
                            
                            <?php if ($form): ?>
                            
                                <div id="abuseform">

                                <p>Gunakan formulir dibawah untuk berhenti menerima e-mail dari Pelanggan IndoCRM</p>

                                <form method="get" action="<?php echo site_url('unsubscribe'); ?>">
                                        <div class="formfield">
                                                <label for="id_email">E-Mail Anda</label><br />
                                                <input class="txt" id="id_name" type="text" name="email" value="<?php echo isset($email) ? h($email) : ""; ?>" size="30" placeholder="E-Mail anda" />
                                        </div>
                                        <div class="buttonarea">
                                            <input type="submit" value="Berhenti" class="btn" />
                                        </div>
                                        <input type="hidden" name="id" value="<?php echo isset($mail_id) ? h($mail_id) : ''; ?>" /> 
                                        <input type="hidden" name="c" value="<?php echo isset($client_id) ? h($client_id) : ''; ?>" /> 
                                </form>

                                </div>
                            
                            <?php endif; ?>
                            
                        </div>

                        <div class="cl"></div>
                </div>


<?php $this->load->view('footer');
