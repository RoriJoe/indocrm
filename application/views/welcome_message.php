<?php
    $page_title = 'Welcome';
    $page_header = '<script type="text/javascript">
    Ext.onReady(function() {
	Ext.get("loading").dom.style.display = "none";
    });
    </script>
    <script type="text/javascript" src="'.base_url().'static/jquery.js"></script>
    <script type="text/javascript" src="'.base_url().'static/jquery.cycle.all.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
    $(\'.slideshow\').cycle({
	fx: "wipe", // choose your transition type, ex: fade, scrollUp, shuffle, etc...
	speed: 2500,
	});
    });
    </script>
    ';
    $this->load->view('header', array('page_title' => $page_title, 'page_header' => $page_header));
?>

<div id="content">
    <div id="loading">loading...</div>
    
    <div id="promo">
        <div class="crmlogo">
            <img src="<?php echo base_url('static/crm_circle4.png'); ?>" />
        </div>
        
        <h1>E-Mail dan SMS broadcast dengan Mudah</h1>
        <p>IndoCRM membantu anda dalam mengirim E-Mail dan SMS broadcast ke pelanggan anda dengan sentuhan personal dan terarah</p>
        <div class="cl"></div>
        
        
        <div class="dialoginfo" id="mbuh">
            <ul>
                <li>Aplikasi broadcast SMS dan E-Mail terjadwal</li>
                <li>Desain-desain e-mail yang menarik</li>
            </ul> 
        </div>
        
    </div>
    <div class="cl"></div>
    <h4>Manual Penggunaan dalam bentuk PDF</h4>
    <p><a href="<?php base_url()?>manual/PDF/indocrm_manual.pdf" alt="manual indocrm odf "><b>Unduh disini</b></a></p>
    <br/>
    <div class="cl"></div>
    <h4>Manual Penggunaan dalam bentuk Video - Youtube</h4>
    <div style="height:320px;width:100%"> 
        <iframe width="100%" height="315" src="http://www.youtube.com/embed/Pz7oCRq0o-c" frameborder="0" allowfullscreen></iframe>
    </div>
    <div id="why">
        <h2>Kenapa IndoCRM lebih baik?</h2>
        
        
        <div class="slideshow">
            <div class="caption">
                <img src="<?php echo base_url('static/loyalitas.png'); ?>" />
                <h3>Membangun Loyalitas Pelanggan</h3>
                <p>Dengan pelayanan maksimal, loyalitas pelanggan akan terjaga</p>
            </div>
            <div class="caption">
                <img src="<?php echo base_url('static/terarah.png'); ?>" />
                <h3>Promosi Terarah</h3>
                <p>Promosi anda terarah langsung ke target market yang anda inginkan, dibanding dengan iklan dan brosur yang tidak jelas target marketnya</p>
            </div>
            <div class="caption">
                <img src="<?php echo base_url('static/personalized.png'); ?>" />
                <h3>Personalized</h3>
                <p>Lebih dapat diterima oleh pelanggan, karena pesan yang lebih personal ke pelanggan</p>
            </div>
        </div>
        <!--
            <ol class="horizontal-list">
            <li>
            <img src="<?php echo base_url('static/loyalitas.png'); ?>" />
            <h3>Membangun Loyalitas Pelanggan</h3>
            <p>Dengan pelayanan maksimal, loyalitas pelanggan akan terjaga</p>
            </li>
            <li>
            <img src="<?php echo base_url('static/terarah.png'); ?>" />
            <h3>Promosi Terarah</h3>
            <p>Promosi anda terarah langsung ke target market yang anda inginkan, dibanding dengan iklan dan brosur yang tidak jelas target marketnya</p>
            </li>
            <li>
            <img src="<?php echo base_url('static/personalized.png'); ?>" />
            <h3>Personalized</h3>
            <p>Lebih dapat diterima oleh pelanggan karena pesan yang lebih personal ke pelanggan</p>
            </li>
        </ol>-->
        <div class="cl"></div>
    </div>
</div>

<div id="sidebar">
    <?php if ( $this->orca_auth->is_logged_in() ): ?>
    <?php $this->load->view('dashboard_menu'); ?>
    <?php else: ?>
    <div class="sidebox">
        <h2>Login</h2>
        
        <form id="loginform" method="post" action="<?php echo site_url('/auth/login?next='.rawurlencode(site_url('dashboard/'))); ?>">
            <div class="formfield">
                <input id="id_username" type="text" name="username" value="" size="30" placeholder="username" />
            </div>
            <div class="formfield">
                <table>
                    <tr>
                        <td>
                            <input id="id_password" type="password" name="password" value="" size="20" placeholder="password" />
                        </td>
                        <td>
                            <input type="submit" value="Login" class="btn" />
                        </td>
                    </tr>
                </table>
            </div>
            <div class="remember">
                <label for="id_remember"><input type="checkbox" id="id_remember" name="remember" value="yes" />&nbsp;Ingat saya</label>
                &middot;
                <a href="<?php echo site_url('lupapassword/'); ?>">Lupa password</a>
            </div>
            
            <input type="hidden" name="next" value="<?php echo site_url('dashboard/'); ?>" />
        </form>
        
    </div>
    
    <div class="sidebox" id="registerbox">
        
        <h2>Registrasi</h2>
        
        <div class="dialoginfo">
            <p>GRATIS 50 E-Mail dan 50 SMS jika anda mendaftar sekarang!!</p>
        </div>
        
        <form method="post" action="<?php echo site_url('register/'); ?>">
            <div class="formfield">
                <input class="txt" id="id_username1" type="text" name="username" value="" size="30" placeholder="username" />
            </div>
            <div class="formfield">
                <input class="txt" id="id_password1" type="password" name="password" value="" size="30" placeholder="password" />
            </div>
            <div class="formfield">
                <input class="txt" id="id_password2" type="password" name="password2" value="" size="30" placeholder="konfirmasi password" />
            </div>
            <div class="formfield">
                <input class="txt" id="id_company" type="text" name="company" value="" size="30" placeholder="nama bisnis" />
            </div>
            <div class="formfield">
                <input class="txt" id="id_email" type="text" name="email" value="" size="30" placeholder="email" />
            </div>
            <div class="formfield" class="btnarea">
                <input type="submit" value="Daftar" class="btn signup-btn" />
                <input type="hidden" name="frontpage" value="1" />
            </div>
        </form>
        
    </div>
    
    
    <div class="sidebox" id="metabox">
        <div style="text-align:center;margin:auto;padding:10px;">
        <a href="ymsgr:SendIM?indocrm_simetri@yahoo.com" class="">
        <img border=0 src="http://opi.yahoo.com/online?u=indocrm_simetri@yahoo.com&m=g&t=2"/>
        </a>
        </div>
        <div style="text-align:center;margin:auto;padding:10px;">
        <a href="ymsgr:SendIM?indocrm_simetri01@yahoo.com" class="span2 center">
        <img border=0 src="http://opi.yahoo.com/online?u=indocrm_simetri01@yahoo.com&m=g&t=2"/>
        </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="cl"></div>


<?php $this->load->view('footer');
