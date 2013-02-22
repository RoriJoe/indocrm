<?php
$page_title = 'Reset Password';
$this->load->view('header', array('page_title' => $page_title));
?>

		<div class="card" id="loginpage">

                        <h1><?php echo $page_title; ?></h1>
                        
                        <div class="msgbox info">
                            <p>Password anda telah diganti. Silahkan kunjungi <a href="<?php echo site_url('auth/login'); ?>">Halaman Login</a> untuk login ke account anda.</p>
                        </div>
			
                </div>


<?php $this->load->view('footer');
