<?php
$page_title = 'Lupa Password';
$this->load->view('header', array('page_title' => $page_title));
?>

		<div class="card" id="loginpage">

                        <h1><?php echo $page_title; ?></h1>
                        
                        <div class="msgbox info">
                            <p>E-mail konfirmasi reset password telah dikirimkan ke account e-mail anda. Silahkan cek inbox anda untuk instruksi selanjutnya</p>
                        </div>
			
                </div>


<?php $this->load->view('footer');
