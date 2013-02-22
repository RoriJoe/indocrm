<!DOCTYPE html>
<html>
<head>
	<title><?php 
		$the_title = '';
		if (isset($page_title) && $page_title) 
			$the_title = "$page_title";
		echo $the_title . ' - ' . htmlentities($this->config->item('site_name')); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" media="screen,mobile" href="<?php echo base_url('static/css/ext-all.css'); ?>" />
	<link rel="stylesheet" type="text/css" media="screen,mobile" href="<?php echo base_url('static/simetri.css'); ?>?reload=true" />
	<script type="text/javascript" src="<?php echo base_url('static/placeholder.js'); ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('static/bootstrap.js'); ?>"></script>
	<script type="text/javascript">
		function ext_ajax(params, mask) {
			var con = new Ext.data.Connection();
			if (mask) {
				con.on( 'beforerequest', function(){ Ext.getBody().mask( mask ) } );
				con.on( 'requestcomplete', function(){ Ext.getBody().unmask() } );
				con.on( 'requestexception', function(){ Ext.getBody().unmask() } );
			}
			con.request(params);
		}
	</script>
	<?php if ( isset($page_header) && $page_header ) echo $page_header; ?>
	<script type="text/javascript">
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-32128736-1']);
	  _gaq.push(['_trackPageview']);
	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	</script>
</head>
<body>
<div id="fauxHeaderContainer">
	<div id="fauxHeader">
	<div id="header">
			<h1 id="logo"><a href="<?php echo site_url(''); ?>" title="Simetri CRM">Simetri CRM</a></h1>
			
			<?php if (!$this->orca_auth->is_logged_in()): ?>
			<ul id="mainmenu">
				<li><a href="<?php echo $this->orca_auth->is_logged_in() ? site_url('dashboard/') : site_url(''); ?>" title="Home">Home</a></li>
					<li><a href="<?php echo site_url('register/'); ?>" title="Daftar sebagai member">Daftar</a></li>
					<li><a href="<?php echo site_url('dashboard/'); ?>" title="Login">Login</a></li>
			</ul>
			<?php endif; ?>
			
			<div class="cl"></div>
			<?php if ( $this->orca_auth->is_logged_in() ): ?>
			<div id="account-link">
				Hello <b><?php echo $this->orca_auth->user->name; ?></b> |
				<a id="profileLink" href="<?php echo site_url('dashboard/') ;?>">Dashboard</a> | 
				<a id="profileLink" href="<?php echo site_url('profile/') ;?>">Profil Saya</a> |
				<a id="profileLink" href="<?php echo site_url('docs/faqs') ;?>"> FAQ</a> |
				<a href="<?php echo site_url('auth/logout') ;?>">Logout</a>
			</div>
			<?php endif; ?>
	</div>
	</div>
</div>
<div id="container">
	<?php 
	
	if ( $this->orca_auth->is_logged_in() && $this->orca_auth->user->client_id )
	{
		if ( !isset($this->company) )
		{
			$this->company = $this->User_model->get_company($this->orca_auth->user->client_id);
		}
		
		if ($this->company && $this->uri->segment(1) && $this->uri->segment(1) != 'welcome')
		{
			if ($this->company->image)
			{
				echo '<div class="company-logo">
						<img src="'.base_url('u/'.$this->company->image).'" alt="'.$this->company->name.'" width="64" />
						<span class="company-name">'.$this->company->name.'</span>
						<div class="cl"></div>
					</div>';
			}
		}
	}
	
	?>
	
	<div id="columnwrap">
		

		
