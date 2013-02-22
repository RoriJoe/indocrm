<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

$page_header = '
<script type="text/javascript">
	STATIC_URL = '.json_encode(base_url('/static')).';
	BASE_URL = '.json_encode(site_url()).';
	ROOT_URL = '.json_encode(base_url()).';
</script>
<script type="text/javascript" src="'.base_url('static/models.js').'"></script>
';
$page_title = 'Desain Template Teks';
$this->load->view('header', array('page_title' => $page_title, 'page_header' => $page_header));
?>

				<script type="text/javascript">
					var cmpg = <?php echo json_encode($campaign) ?> ;
				</script>
				<div id="design-content" class="card">
					<h1><?php echo $page_title; ?></h1>
					
					<?php
						$msg = flashmsg_get();
						if ($msg)
						{
							echo '<div class="msgbox warning">'.htmlentities($msg).'</div>';
						}
					?>
					
					<?php if ( $campaign->campaign_type == 'sms' ):?>
					<p>Desain template SMS. Panjang maksimal adalah: <?php echo (160 * 4)-20; ?> karakter</p>
					<?php else: ?>
					<p>Desain template teks diperlukan agar e-mail anda tidak terkena spam</p>
					<?php endif; ?>
					
					<div id="design-area" style="width:95%; margin:5px;">
						
						<div class="tpltool">

							<fieldset class="tplx">
								<legend>Template Pelanggan</legend>
								<a href="#" id="customerTag" class="post-tag">Nama</a>
								<a href="#" id="emailTag" class="post-tag">E-Mail</a>
								<a href="#" id="phoneTag" class="post-tag">Telepon</a>
								<a href="#" id="mobileTag" class="post-tag">Handphone</a>
							</fieldset>

							<fieldset class="tplx">
								<legend>Template Campaign</legend>
								<a href="#" id="campaignTitle" class="post-tag">Judul</a>
								<a href="#" id="campaignDescription" class="post-tag">Keterangan</a>
								<a href="#" id="sentDate" class="post-tag">Tanggal Kirim</a>
							</fieldset>

							<fieldset class="tplx">
								<legend>Template Usaha</legend>
								<a href="#" id="companyName" class="post-tag">Nama Perusahaan</a>
								<a href="#" id="companyEmail" class="post-tag">E-Mail Perusahaan</a>
								<a href="#" id="companyWeb" class="post-tag">Website Perusahaan</a>
							</fieldset>

							<div class="cl"></div>
						</div>
						
						<script type="text/javascript">
							function execCommand( a,b,tag ) {
								var txt = document.getElementById('txt');
								if (document.selection) {
									txt.focus();
									var sel = document.selection.createRange();
									sel.text = tag;
									txt.focus();
								}
								else if (txt.selectionStart || txt.selectionStart == "0") {
									// MOZILLA/NETSCAPE support
									var top = txt.scrollTop;
									var startPos = txt.selectionStart;
									var endPos = txt.selectionEnd;
									var text = txt.value;
									txt.value = text.substring(0, startPos) + tag + text.substring(endPos, text.length);
									txt.selectionStart = startPos;
									txt.selectionEnd = txt.selectionStart+tag.length;
									txt.scrollTop = top;
								} else {
									// giveup
									txt.value += tag;
								}
							}
							
							Ext.onReady(function() {
								Ext.get("customerTag").on('click',function() {
									execCommand("mceInsertContent", false, '{%CUSTOMER_NAME%}');
								});
								Ext.get("emailTag").on('click',function() {
									execCommand("mceInsertContent", false, '{%CUSTOMER_EMAIL%}');
								});
								Ext.get("phoneTag").on('click',function() {
									execCommand("mceInsertContent", false, '{%CUSTOMER_PHONE%}');
								});
								Ext.get("mobileTag").on('click',function() {
									execCommand("mceInsertContent", false, '{%CUSTOMER_MOBILE%}');
								});
								
								Ext.get("campaignTitle").on('click',function() {
									execCommand("mceInsertContent", false, '{%CAMPAIGN_TITLE%}');
								});
								Ext.get("campaignDescription").on('click',function() {
									execCommand("mceInsertContent", false, '{%CAMPAIGN_DESCRIPTION%}');
								});
								Ext.get("sentDate").on('click',function() {
									execCommand("mceInsertContent", false, '{%SENT_DATE%}');
								});
								
								
								Ext.get("companyName").on('click',function() {
									execCommand("mceInsertContent", false, '{%COMPANY_NAME%}');
								});
								Ext.get("companyEmail").on('click',function() {
									execCommand("mceInsertContent", false, '{%COMPANY_EMAIL%}');
								});
								Ext.get("companyWeb").on('click',function() {
									execCommand("mceInsertContent", false, '{%COMPANY_WEBSITE%}');
								});
								
								function calculateLength(evt, obj) {
									var count = obj.value.length;
									
									if (cmpg.campaign_type == 'sms') {
										var max = (160 * 4) - 20;
										if (cmpg.client_type == 0){
											max = 160 - 20;
										}
										if ( count > max ) {
											obj.value = obj.value.substring(0, max);
											count = max;
										}
									}
									
									Ext.get('textLength').update('Panjang karakter: <b>' + count + '</b> / ' + max);
								}
								
								Ext.get('txt').on('change', calculateLength);
								Ext.get('txt').on('keyup', calculateLength);
							});
						</script>
						
						
						<form method="post" action="<?php echo site_url('campaign/plaintext?id='.$campaign->campaign_id); ?>">
							<div class="formfield">
								
								<div class="counterText" style="positon:relative; width:100%;">
									<label for="txt">Template Teks</label>
									<span id="textLength" style="display:block; float:right; font-size:20px; color:#666;"></span>
									<textarea id="txt" name="txt" cols="70" rows="20" style="width:100%;"><?php echo htmlentities($campaign->plaintemplate); ?></textarea><br />
								</div>
							</div>
							
							<div id="buttonarea" style="margin:10px">
								<div style="float:left;">
									<input type="submit" class="btn" name="test" value="Test Kirim" id="buttonKirim" />
								</div>
								<div style="float:right;">
									<input type="submit" class="btn" value="Simpan" id="buttonSimpan" />
									<input type="submit" class="btn" name="selesai" value="Simpan dan Lanjut"  id="buttonNext" />
								</div>
								<div style="clear:both;"></div>
							</div>
						</form>
					</div>

					
				</div>

			<div class="cl"></div>
			
			<script type="text/javascript">
				Ext.onReady(function() {
					Ext.get('buttonKirim').on('click', function() {
						Ext.getBody().mask("Mengirim...");
					});
					Ext.get('buttonSimpan').on('click', function() {
						Ext.getBody().mask("Menyimpan...");
					});
					Ext.get('buttonNext').on('click', function() {
						Ext.getBody().mask("Menyimpan...");
					});
				});
			</script>

<?php $this->load->view('footer');
