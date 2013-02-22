<?php
$page_title = 'Registrasi';
$this->load->view('header', array('page_title' => $page_title));
?>

		<div class="card" id="registerpage">

                        <h1><?php echo $page_title; ?></h1>
                        
                        <div id="registerform2" class="left75">
                                <?php

                                $frontpage = $this->input->post('frontpage');
                                $msg = flashmsg_get();
                                if ($msg)
                                {
                                    echo '<div class="msgbox warning">'.htmlentities($msg).'</div>';
                                }

                                ?>

                                <div class="msgbox info">
                                    <?php if ($frontpage): ?>
                                        <p>Sedikit lagi, mohon isi alamat lengkap, dan nomer telepon anda dan pastikan anda telah setuju dengan Perjanjian Penggunaan Aplikasi kami</p>
                                    <?php else: ?>
                                        <p>Silahkan mengisi form dibawah untuk mendaftar.</p>
                                    <?php endif; ?>
                                </div>

                                <form method="post" action="<?php echo site_url('register/'); ?>">
                                        <div class="formfield">
                                                <label for="id_username1">Username</label>
                                                <input id="id_username1" type="text" name="username" value="<?php echo set_value('username'); ?>" size="30" placeholder="username" />
                                                <?php if (form_error('username')) echo form_error('username'); ?>
                                        </div>
                                        <div class="formfield">
                                                <label for="id_password1">Password</label>
                                                <input id="id_password1" type="password" name="password" value="<?php echo set_value('password'); ?>" size="30" placeholder="password" />
                                                <?php if (form_error('password')) echo form_error('password'); ?>
                                        </div>
                                        <div class="formfield">
                                                <label for="id_password2">Konfirmasi password</label>
                                                <input id="id_password2" type="password" name="password2" value="<?php echo set_value('password2'); ?>" size="30" placeholder="konfirmasi password" />
                                        </div>
                                        <div class="formfield">
                                                <label for="id_company">Nama perusahaan</label>
                                                <input id="id_company" type="text" name="company" value="<?php echo set_value('company'); ?>" size="30" placeholder="nama bisnis" />
                                                <?php if (form_error('company')) echo form_error('company'); ?>
                                        </div>
                                        <div class="formfield">
                                                <label for="id_email">Alamat e-mail</label>
                                                <input id="id_email" type="text" name="email" value="<?php echo set_value('email'); ?>" size="30" placeholder="email" />
                                                <?php if (form_error('email')) echo form_error('email'); ?>
                                        </div>
                                    
                                        <div class="formfield">
                                                <label for="id_address">Alamat</label>
                                                <input id="id_address" type="text" name="address" value="<?php echo set_value('address'); ?>" size="50" placeholder="alamat anda" />
                                                <?php if (!$frontpage & form_error('address')) echo form_error('address'); ?>
                                        </div>
                                    
                                        <div class="formfield">
                                                <label for="id_country">Negara</label>
                                                <select id="id_country" name="country">
                                                    <option value="">Pilih negara</option>
                                                    <?php $query = $this->db->order_by('Country')->get("countries"); foreach($query->result() as $row) {
                                                        echo '<option value="'.h($row->Code).'"'.set_select('country', $row->Code).'>'.$row->Country.'</option>';
                                                    } ?>
                                                </select>
                                                <?php if (!$frontpage & form_error('country')) echo form_error('country'); ?>
                                        </div>
                                    
                                        <div class="formfield">
                                                <label for="id_state">Propinsi</label>
                                                <select id="id_state" name="state">
                                                    <option value="">Pilih propinsi</option>
                                                </select>
                                                <?php if (!$frontpage & form_error('state')) echo form_error('state'); ?>
                                        </div>
                                    
                                        <div class="formfield">
                                                <label for="id_kota">Kota</label>
                                                <select id="id_city" name="city">
                                                    <option value="">Pilih kota</option>
                                                </select>
                                                <?php if (!$frontpage & form_error('city')) echo form_error('city'); ?>
                                        </div>
                                    
                                        <div class="formfield">
                                                <label for="id_zip_code">Kode Pos</label>
                                                <input id="id_zip_code" type="text" name="zip_code" value="<?php echo set_value('zip_code'); ?>" size="6" placeholder="zip code" />
                                        </div>
                                    
                                        <div class="formfield">
                                                <label for="id_phone">Telepon</label>
                                                <input id="id_phone" type="text" name="phone" value="<?php echo set_value('phone'); ?>" size="10" placeholder="telepon anda" />
                                                <?php if (!$frontpage & form_error('phone')) echo form_error('phone'); ?>
                                        </div>
                                        
                                        <div class="formfield">
											<div class="msgbox info">
											<?php
											$q = $this->db->get('paket');
											foreach($q->result() as $row){
												?>
												<p>Paket <strong><?php echo strtoupper($row->nama_paket); ?></strong>:</p>
												<p><?php echo $row->description; ?></p>
												<?php
											}
											?>
											</div>
											<label for="client_type">Paket</label>
											<select name="client_type">
												<option value="-1">Free</option>
												<option value="0">Personal</option>
												<option value="1">Professional</option>
												<option value="2">Corporate</option>
											</select>
                                        </div>
                                    
                                        <div class="formfield">
                                                <?php echo $recaptcha; ?>
                                        </div>
                                        <div class="formfield" class="buttonarea">
                                                <input type="submit" value="Daftar" class="btn signup-btn" />
                                        </div>
                                </form>
                            
                        </div>
                        
                        <div class="cl"></div>
                </div>

                <script type="text/javascript">
                    var timer1 = null;
                    BASE_URL = '<?php echo site_url(); ?>';
                    
                    Ext.onReady(function() {
                        
                        Ext.get('id_country').on('change', function(evt, o) {
                            if (!o.value) return;
                            if (window.console)
                                console.log(['country changed', o.value]);
                            
                            if (timer1) {
                                clearTimeout(timer1);
                                var state = Ext.get('id_state').dom;
                                state.disabled=false;
                            }
                            
                            timer1 = setTimeout(function() {
                                var city = Ext.get('id_city').dom;
                                city.options.length=1;
                                city.options[0] = new Option( "Pilih kota", "" );
                                    
                                var state = Ext.get('id_state').dom;
                                state.disabled=true;

                                Ext.Ajax.request({
                                    url: BASE_URL + '/countries/states',
                                    method: 'POST',
                                    params: {'country': o.value},
                                    success:function(xhr){
                                        var result = Ext.JSON.decode(xhr.responseText);
                                        if (result.success) {
                                            var rows = result.rows;
                                            state.options.length = 1;
                                            state.options[0] = new Option( "Pilih propinsi", "" );
                                            for(var i=0,len=rows.length; i<len; i++) {
                                                var row = rows[i];
                                                state.options[i+1]=new Option( row.State, row.State );
                                            }
                                        }
                                        state.disabled=false;
                                        timer1 = null;
                                    },
                                    failure: function(response, opts) {
                                        state.disabled=false;
                                        timer1 = null;
                                    }
                                });
                            }, 50);
                        });
                        
                        Ext.get('id_state').on('change', function(evt, o) {
                            if (!o.value) return;
                            
                            if (window.console)
                                console.log(['state changed', o.value]);
                            
                            if (timer1) {
                                clearTimeout(timer1);
                                var city = Ext.get('id_city').dom;
                                city.disabled=false;
                            }
                            
                            timer1 = setTimeout(function() {
                                var city = Ext.get('id_city').dom;
                                var country = Ext.get('id_country').dom;
                                city.disabled=true;

                                Ext.Ajax.request({
                                    url: BASE_URL + '/countries/cities',
                                    method: 'POST',
                                    params: {'state': o.value, 'country':country.value},
                                    success:function(xhr){
                                        var result = Ext.JSON.decode(xhr.responseText);
                                        if (result.success) {
                                            var rows = result.rows;
                                            city.options.length = 1;
                                            city.options[0] = new Option( "Pilih kota", "" );
                                            for(var i=0,len=rows.length; i<len; i++) {
                                                var row = rows[i];
                                                city.options[i+1]=new Option( row.City, row.City );
                                            }
                                        }
                                        city.disabled=false;
                                        timer1 = null;
                                    },
                                    failure: function(response, opts) {
                                        city.disabled=false;
                                        timer1 = null;
                                    }
                                });
                            }, 50);
                        });
                        
                    });
                </script>

<?php $this->load->view('footer');
