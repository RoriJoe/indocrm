<?php
$page_title = 'Reset Password';
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
                            <p>Halo <?php echo $user->username; ?>, silahkan masukkan password baru anda dibawah, beserta konfirmasikan password anda</p>
                        </div>
			
			<form id="loginform" method="post" action="<?php echo site_url('lupapassword/resetpass'); ?>">
                                <div class="formfield">
                                        <label for="id_email">Password baru</label><br />
                                        <input id="id_password1" type="password" name="password" value="" size="30" placeholder="password baru" />
                                </div>
                                <div class="formfield">
                                        <label for="id_email">Ulangi password baru</label><br />
                                        <input id="id_password2" type="password" name="password2" value="" size="30" placeholder="konfirmasi password" />
                                </div>
                                <div class="buttonarea">
                                    <input type="submit" value="Reset Password Saya" class="btn btn.large" />
                                    <input type="hidden" name="x" value="<?php echo $confirm_hash; ?>" />
                                </div>
			</form>
                    

                </div>


<?php $this->load->view('footer');
