<?php
$page_title = 'Bagaimana Mencari CampaignID';
$this->load->view('header', array('page_title' => $page_title));
?>

		<div class="card">
			<h1><?php echo $page_title; ?></h1>
                        
                        <div class="left75" id="abusenote">
                            
                            <h2>Campaign ID</h2>
                            <p>Untuk melihat Campaign ID Unik IndoCRM, klik "View Full Headers" pada aplikasi email Anda. Karena setiap aplikasi email berbeda, Anda mungkin harus merujuk ke file bantuan Anda. Biasanya, jika Anda melakukan pencarian untuk "header email lengkap" atau "header lengkap," Anda akan menemukan instruksi yang Anda butuhkan.</p>

                            <p>CampaignID berisi informasi yang memungkinkan sistem kami untuk segera menghapus Anda dari daftar pengirim, tapi kami tidak memberitahu pengirim bahwa Anda melakukan pelaporan. Hal ini juga mengirimkan peringatan ke meja penyalahgunaan kita sehingga kami dapat menyelidiki pengirim.</p>

                            <p>Gambar di bawah menunjukkan di mana ID Kampanye terletak ketika header lengkap kampanye yang ditampilkan:</p>
                            
                            <p><img style="margin:10px 0; padding:10px 0; border:1px solid #555; width:500px;" src="<?php echo base_url('static/abuseinfo.gif'); ?>" /></p>

                            <p>Tujuannya CampaignID ada dua. Kami ingin membuatnya jelas bagi penerima bahwa kampanye berasal dari IndoCRM, dan menyediakan sarana untuk melaporkan penyalahgunaan langsung ke pusat pelaporan kami. Campaign ID (CID) memberitahu kita yang kampanye yang menyebabkan masalah dan memungkinkan kita untuk menyelidiki pengguna untuk pelanggaran <a href="<?php echo site_url('tos'); ?>">ketentuan pemakaian layanan</a>.</p>

                        </div>
                        
                        <div class="cl"></div>
                </div>


<?php $this->load->view('footer');
