<?php
$page_title = 'Registrasi Sukses';
$this->load->view('header', array('page_title' => $page_title));
?>

		<div class="card" id="loginpage">

                        <h1><?php echo $page_title; ?></h1>
                        
                        <div class="msgbox info">
                            <p>Terima kasih telah mendaftar di SimetriCRM</p>
                            <p>E-mail konfirmasi telah dikirimkan ke account e-mail anda. Silahkan cek inbox anda untuk instruksi selanjutnya</p>
                        </div>
			
                </div>


<?php $this->load->view('footer');
