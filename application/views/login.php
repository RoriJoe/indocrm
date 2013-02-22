<?php
$page_title = 'Login';
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
                        
                        $next = $this->input->get_post('next'); 
                        ?>
			
			<form id="loginform" method="post" action="<?php echo site_url('/auth/login?next='.rawurlencode($next)); ?>">
				<div class="formfield">
                                        <label for="id_username">Username</label><br />
					<input id="id_username" type="text" name="username" value="" size="30" placeholder="username" />
				</div>
				<div class="formfield">
                                        <label for="id_password">Password</label><br />
                                        <input id="id_password" type="password" name="password" value="" size="30" placeholder="password" />
				</div>
				<div class="remember">
					<label for="id_remember"><input type="checkbox" id="id_remember" name="remember" value="yes" />&nbsp;Ingat saya</label>
					&middot;
					<a href="<?php echo site_url('lupapassword/'); ?>">Lupa password</a>
				</div>
                                <div class="buttonarea">
                                    <input type="submit" value="Login" class="btn" />
                                </div>
                            
                                <input type="hidden" name="next" value="<?php echo htmlentities($next); ?>" />
			</form>
                    

                </div>


<?php $this->load->view('footer');
