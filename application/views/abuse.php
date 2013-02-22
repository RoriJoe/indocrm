<?php
$page_title = 'Laporan Penyalahgunaan';
$this->load->view('header', array('page_title' => $page_title));
?>

		<div class="card">
                    
			<h1><?php echo $page_title; ?></h1>
                        
                        <div class="left50" id="abusenote">
                            
                            <h2>Laporan Penyalahgunaan IndoCRM</h2>
                            
                            <blockquote>
                            <p>Dengan ratusan ribu pengguna IndoCRM mengirimkan kampanye untuk puluhan juta penerima, kita terikat untuk menyediakan pelaporan penyalahgunaan setiap saat. Kami menanggapi laporan penyalahgunaan secara serius, dan kami berusaha keras untuk mencegah penyalahgunaan dalam sistem kami.</p>
                            </blockquote>

                            <h2>Bagaimana mencegah dan memerangi penyalahgunaan IndoCRM</h2>
                            <ol>
                                <li><p>Kami memiliki tim untuk memonitor, menyetujui, menolak akun IndoCRM baru dan mendeteksi penyalahgunaan. Staf kami menggunakan berbagai kriteria untuk mengevaluasi akun, mulai dari yang umum seperti catatan WHOIS dan IP, sampai kepada beberapa pola perilaku yang tidak terlalu jelas.</p></li>
                                <li><p>Kami mengharuskan semua pengguna untuk menyetujui ketentuan berlangganan sebelum mereka dapat membuat account IndoCRM, dan sekali lagi sebelum mereka dapat mengimpor daftar pelanggan yang ada ke dalam rekening mereka.</p></li>
                                <li><p>Kami mengembangkan aplikasi khusus untuk mendeteksi penyalahgunaan, membuat sistem kita bersih dengan memprediksi perilaku buruk dalam kampanye bahkan sebelum keluar pintu.</p></li>
                                <li><p>Pelanggan IndoCRM harus mendaftarkan alamat jelas dan pengingat izin ("Anda menerima email ini karena Anda mendaftar di ...") untuk setiap informasi yang dikirim melalui IndoCRM. Kami memasukkan informasi itu ke dalam kampanye mereka.</p></li>
                                <li><p>Kami akan secara otomatis menyisipkan link unsubscribe satu-klik di setiap kampanye yang dikirim dari sistem kami.</p></li>
                            </ol>


                            <h2>Bagaimana kita mendidik pelanggan</h2>
                            <ol>
                                <li><p>Kami menawarkan panduan praktis kami untuk membantu pengguna mempelajari cara mereka menggunakan IndoCRM.</p></li>
                                <li><p>Sepanjang antarmuka pengguna IndoCRM itu (saat mengirim kampanye, menyiapkan daftar, dan merancang template), kami menyediakan pelanggan dengan konstan, link bermanfaat dan informasi latar belakang yang membantu mereka memahami etika dan hukum spam.</p></li>
                            </ol>

                            <h2>Bagaimana kita menangani masalah</h2>
                            <ol>
                                <li><p>Kami menanamkan setiap kampanye email yang dikirimkan dari server kami dengan ID Kampanye sehingga penerima dapat dengan mudah melaporkan penyalahgunaan ke IndoCRM. Ketika kami menerima keluhan melalui form penyalahgunaan kami, kami segera diselidiki. Jika kampanye atau akun pengguna yang mencurigakan muncul dengan cara apapun, kami akan menangguhkan account selama penyelidikan.</p></li>
                                <li><p>Kami terdaftar dengan ISP terkemuka dan anti-spam berwenang untuk menerima pemberitahuan loop umpan balik otomatis bila ada penerima pengguna kami melaporkan penyalahgunaan. Bila kita mampu mengurai pemberitahuan tersebut, kami menghapus penerima dari daftar pengguna kami. Jika laporan melebihi batas tertentu, kita mengirim peringatan kepada pengguna kami. Jika peringatan melebihi ambang batas yang wajar, kami menangguhkan account pengguna dan menyelidiki. ESPs paling dan ISP akan menceritakan sebuah ambang batas yang wajar untuk pengaduan penyalahgunaan adalah 0,1 persen (1 dari seribu orang melaporkan promosi Anda sebagai sampah). Karena tipis volume email yang dikirim dari IP kita, dan karena kebanyakan IP kami dibagi di beberapa pengguna, ambang kita sebenarnya jauh lebih ketat.</p></li>
                            </ol>
                        </div>
                        
                        <div class="right50">
                            
                            <div id="abuseform">
                            
                            <h2>Formulir Pelaporan Penyalahgunaan</h2>
                            
                            <p>Gunakan formulir dibawah untuk melaporkan Campaign (menggunakan ID Campaign Anda temukan di header email) ke Team penanggulangan penyalahgunaan IndoCRM</p>
                            <p>Jika Anda ingin respon dari kami, atau jika Anda ingin kami menghapus Anda dari daftar pengguna IndoCRM, silakan sertakan alamat email Anda atau informasi kontak bersama dengan laporan Anda. Termasuk alamat email Anda adalah opsional, tapi sangat berguna dalam membantu kita menyelidiki pengirim untuk penyalahgunaan.</p>
                            
                            <?php

                            $msg = flashmsg_get();
                            if ($msg)
                            {
                                echo '<div class="msgbox warning">'.htmlentities($msg).'</div>';
                            }

                            ?>

                            <form method="post" action="<?php echo site_url('abuse/report'); ?>">
                                    <div class="formfield">
                                            <label for="id_id">Campaign ID</label><br />
                                            <input class="txt" id="id_id" type="text" name="id" value="<?php echo $mail_id ? h($mail_id) : ""; ?>" size="30" placeholder="Campaign ID" /><br />
                                            <p>
                                                <a href="<?php echo site_url('abuse/campaignid'); ?>">Bagaimana untuk melihat Campaign ID</a>
                                            </p>
                                    </div>
                                    <div class="formfield">
                                            <label for="id_name">Nama Anda</label><br />
                                            <input class="txt" id="id_name" type="text" name="name" value="<?php echo isset($name) ? h($name) : ""; ?>" size="30" placeholder="Nama Anda" />
                                    </div>
                                    <div class="formfield">
                                            <label for="id_email">E-Mail Anda</label><br />
                                            <input class="txt" id="id_name" type="text" name="email" value="<?php echo isset($email) ? h($email) : ""; ?>" size="30" placeholder="E-Mail anda" />
                                    </div>
                                    <div class="formfield">
                                            <label for="id_reason">Alasan anda melaporkan</label><br />
                                            <textarea class="txt" cols="30" rows="5" id="id_reason" name="reason" placeholder="Alasan anda melaporkan e-mail"><?php echo isset($reason) ? h($reason) : ""; ?></textarea>
                                    </div>

                                    <div class="buttonarea">
                                        <input type="submit" value="Laporkan!" class="btn" />
                                    </div>
                            </form>
                            
                            </div>
                            
                            
                        </div>
                        
                        <div class="cl"></div>
                </div>


<?php $this->load->view('footer');
