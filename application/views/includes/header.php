<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>:: Admin Ventas ::</title>
		<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico" />
		<link rel="stylesheet"    href="<?php echo base_url(); ?>assets/css/style.default.css" type="text/css" />
		<link rel="stylesheet"    href="<?php echo base_url(); ?>assets/css/responsive-tables.css">
        <link rel="stylesheet"    href="<?php echo base_url(); ?>assets/css/jquery-impromptu.css" type="text/css" />
		
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-1.9.1.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-migrate-1.1.1.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-ui-1.9.2.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/modernizr.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.cookie.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.uniform.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/flot/jquery.flot.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/flot/jquery.flot.resize.min.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/responsive-tables.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-impromptu.js"></script>
        <!--Archivos del sistema -->
        <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/system/global_system.js"></script>
		<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/custom.js"></script>
		<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>assets/js/excanvas.min.js"></script><![endif]-->
	</head>
	<body>
		<div class="mainwrapper">
			<div class="header">
		        <div class="logo">
		            <a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>assets/images/logo.png" alt="" /></a>
		        </div>
		        <!--headmenu-->
		        <div class="headerinner">
		            <ul class="headmenu">
		                <li class="right">
		                    <div class="userloggedinfo">
		                        <img src="<?php echo base_url().'assets/'.$this->session->userdata('avatar') ?>" style="max-width:80px;max-height:90px;" alt="" />
		                        <div class="userinfo">
		                            <h5><?php echo $this->session->userdata('name') ?><small>- <?php echo $this->session->userdata('mail') ?></small></h5>
		                            <h5>{<?php echo $this->session->userdata('nivel') ?>}</h5>
		                            <ul>
		                                <li><a href="<?php echo base_url() ?>">Edita tu Perfil</a></li>
		                                <li><a href="<?php echo base_url() ?>">Configuraciones</a></li>
		                                <li><a href="<?php echo base_url() ?>logout">Cerrar Sesión</a></li>
		                            </ul>
		                        </div>
		                    </div>
		                </li>
		            </ul>
		        </div>
		        <!--headmenu-->
		    </div>
		    <!-- leftpanel -->
		    <div class="leftpanel">
		        <div class="leftmenu">        
		            <ul class="nav nav-tabs nav-stacked">
		            	<li class="nav-header">Navigation</li>
		            	<li class="dropdown"><a href=""><span class="iconfa-pencil"></span> Forms</a>
		                	<ul>
		                    	<li><a href="forms.html">Form Styles</a></li>
		                        <li><a href="wizards.html">Wizard Form</a></li>
		                        <li><a href="wysiwyg.html">WYSIWYG</a></li>
		                    </ul>
		                </li>
		            </ul>
		        </div>
		    </div>
		    <!-- leftpanel -->
	  		
	  		<!--rightpanel-->
	  		<div class="rightpanel">
	  			<ul class="breadcrumbs">
		            <li><a href="<?php echo base_url();?>welcome"><i class="iconfa-home"></i></a> <span class="separator"></span></li>
		            <li>Dashboard</li>
		            <li class="right" id="skin-colors">
	                    <a href="" data-toggle="dropdown" class="dropdown-toggle"><i class="icon-tint"></i> Color Skins</a>
	                    <ul class="dropdown-menu pull-right skin-color">
	                        <li><a href="default">Default</a></li>
	                        <li><a href="navyblue">Navy Blue</a></li>
	                        <li><a href="palegreen">Pale Green</a></li>
	                        <li><a href="red">Red</a></li>
	                        <li><a href="green">Green</a></li>
	                        <li><a href="brown">Brown</a></li>
	                    </ul>
		            </li>
		        </ul>
		        <!--pageheader-->
		        <div class="pageheader">
		            <div class="pageicon"><span class="iconfa-laptop"></span></div>
		            <div class="pagetitle">
		                <h5>All Features Summary</h5>
		                <h1>Dashboard</h1>
	            	</div>
	        	</div>
	        	<!--pageheader-->
	  			
	  			<!--maincontent-->
		  		<div class="maincontent">
		            <div class="maincontentinner">
		            	<!--row-fluid-->
		                <div class="row-fluid">
		                	
		                