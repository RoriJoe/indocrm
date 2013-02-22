<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */
 
if ($this->orca_auth->user->group_id != 6){
?>
						<div class="sidebox">
							<h3>Quota</h3>
                            <style>
                                #quotainfo th { font-weight:bold; }
                                #quotainfo th,#quotainfo td { padding:5px; }
                                #quotainfo tr { border-bottom:1px solid #ccc; }
                                #quotainfo th { background-color:#ddd; }
                                #quotainfo td { background-color:#eee; }
                                #quotainfo table { margin:5px 0; }
                            </style>
							<div id="quotainfo">
							<?php
							
							if ($this->orca_auth->user->client_id)
							{
								if ( !isset($this->company) )
								{
									$this->company = $this->User_model->get_company($this->orca_auth->user->client_id);
								}
                                
                                echo "
                                <table style='width:100%;'>
                                <tr>
                                    <th>MEMBER STATUS</th>
                                    <td>".($this->company->is_active?"AKTIF":"PENDING")."</td>
                                </tr>
                                <tr>
                                    <th>E-Mail Terpakai</th>
                                    <td>{$this->company->mail_count} dari {$this->company->mail_quota}</td>
                                </tr>
                                <tr>
                                    <th>SMS Terpakai</th>
                                    <td>{$this->company->sms_count} dari {$this->company->sms_quota}</td>
                                </tr>
                                <tr>
                                    <th>SISA E-Mail Gratis</th>
                                    <td>{$this->company->mail_free}</td>
                                </tr>
                                <tr>
                                    <th>SISA SMS Gratis</th>
                                    <td>{$this->company->sms_free}</td>
                                </tr>
                                </table>
                                ";
							}
							 
							?>
							</div>
						</div>

						<div class="sidebox">
							<h3>Nomor Center Indocrm</h3>
							<h4>081330332222</h4>
							<br>
							<h3>Kirim SMS</h3>
							<div id="adhocsmsbox"></div>
							
							<script type="text/javascript">
								var sms_counter = 0;
								var INTERVAL = 0;
								
								if (typeof Ext != 'undefined') {
									function cl(evt, obj) {
										var count = obj.value.length;
										Ext.get('adhoclen').update('Karakter: <b>' + count + '</b>');
									}
									
									function start_timer() {
										if (sms_counter == 0) {
											clearInterval(INTERVAL);
											checksms();
										} else {
											sms_counter -= 1;
											Ext.get('smstimer').update(sms_counter + ' detik');
										}
									}
									
									function initsms() {
										Ext.get('adhocsms').on('change', cl);
										Ext.get('adhocsms').on('keyup', cl);
										Ext.get('dosendsms').on('click', function(evt) {
											
											evt.preventDefault();
											
											var msisdn = Ext.String.trim(Ext.get('id_msisdn').getValue());
											var sms = Ext.String.trim(Ext.get('adhocsms').getValue());
											
											if (!msisdn || !sms) {
												Ext.Msg.alert('error', 'Mohon isi SMS dan nomer handphone yg dituju');
												return false;
											}

											if (/[a-zA-Z0-9_]{160,}/.test(sms)) {
												Ext.Msg.alert('error', 'Mohon format SMS anda dengan ejaan yang benar');
												return false;
											}
											
											var con = new Ext.data.Connection();
											con.on( 'beforerequest', function(){ Ext.getBody().mask( 'Mengirim SMS...' ) } );
											con.on( 'requestcomplete', function(){ Ext.getBody().unmask() } );
											con.on( 'requestexception', function(){ Ext.getBody().unmask() } );
											con.request({
												url: '<?php echo site_url(); ?>/dashboard/sendsms',
												method: 'POST',
												params:{msisdn:msisdn,sms:sms},
												success:function(response){
													var data = Ext.JSON.decode(response.responseText);
													if (data.error) {
														Ext.get('adhocsmsbox').update('<p>'+data.error+'</p>');
													} else {
														sms_counter = data.counter;
														Ext.get('adhocsmsbox').update('<span id="smstimer">pesan terkirim...</span>');
														if (INTERVAL) clearInterval(INTERVAL);
														INTERVAL = setInterval(start_timer,1000);
													}
												}
											});
											
											return false;
										});
									}
									
									function checksms() {
										//Ext.Ajax.on('beforerequest', function(){Ext.getBody().mask('Menghapus...');});
										//Ext.Ajax.on('requestcomplete', function(){Ext.getBody().unmask();});
										//Ext.Ajax.on('requestexception', function(){Ext.getBody().unmask();});
										
										Ext.Ajax.request({
											url: '<?php echo site_url(); ?>/dashboard/checksms',
											method: 'POST',
											success:function(response){
												var data = Ext.JSON.decode(response.responseText);
												if (data.allow) {
													Ext.get('adhocsmsbox').update(data.html);
													initsms();
												} else if (data.error) {
													Ext.get('adhocsmsbox').update('<p>'+error+'</p>');
												} else if (data.counter) {
													sms_counter = 180-data.counter;
													Ext.get('adhocsmsbox').update('<span id="smstimer"></span>');
													if (INTERVAL) clearInterval(INTERVAL);
													INTERVAL = setInterval(start_timer,1000);
												}
											}
										});
									}
									
									Ext.onReady(function() {
										checksms();
									});
								}
							</script>

						</div>
						<?php }
				
				if ($this->orca_auth->user->group_id !=1){
					$this->db->where('client_id', $this->orca_auth->user->client_id);
					$this->db->select('client_type');
					$q = $this->db->get('clients');
					//echo $this->db->last_query();
					$restype = $q->result();
					if ($restype[0]->client_type >= 0){
					?>
				<div class="sidebox">
						<ul id="sidemenu">
							<?php $this->User_model->print_menu($this->orca_auth->user->id); ?>
						</ul>
					</div>
				<?php
					}
				} else {
					?>
					<div class="sidebox">
						<ul id="sidemenu">
							<?php $this->User_model->print_menu($this->orca_auth->user->id); ?>
						</ul>
					</div>
					<?php
				}
