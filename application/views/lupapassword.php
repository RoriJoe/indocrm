<?php
$page_title = 'Lupa Password';
$this->load->view('header', array('page_title' => $page_title));
?>

		<div class="card" id="loginpage">

                        <h1><?php echo $page_title; ?></h1>
                        
                        <?php
                        
                        $msg = flashmsg_get();
                        if ($msg)
                        {
                            echo '<div class="msgbox warning">'.htmlentities($msg).'</div>';
                        }

                        ?>
                        
                        <div class="msgbox info">
                            <p>Masukkan e-mail anda, password akan dikirimkan melalui e-mail anda.</p>
                        </div>
			
			<form id="loginform" method="post" action="<?php echo site_url('lupapassword'); ?>">
				<div class="formfield">
                                        <label for="id_email">E-Mail</label><br />
					<input id="id_username" type="text" name="email" value="" size="35" placeholder="email anda" />
				</div>
				<div class="formfield">
                                    <?php echo $recaptcha; ?>
				</div>
                                <div class="buttonarea">
                                    <input type="submit" value="E-mailkan Password Saya" class="btn btn.large" />
                                </div>
			</form>
                    

                </div>


<?php $this->load->view('footer');
