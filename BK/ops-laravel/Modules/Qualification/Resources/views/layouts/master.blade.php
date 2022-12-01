<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Module Qualification</title>
        <link href="{{ URL::asset('public/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('public/css/normalize.min.css')}}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('public/css/style.css')}}" rel="stylesheet" type="text/css">
        <link href="{{ URL::asset('public/css/media.css')}}" rel="stylesheet" type="text/css">
        <script src="{{ URL::asset('public/js/jquery.min.js') }}"> </script>
        <script src="{{ URL::asset('public/js/bootstrap.min.js') }}"> </script>
    </head>
    <style type="text/css">
    	@font-face {
		    font-family: 'QanelasMedium';
		    src: url('../public/fonts/qualification/QanelasMedium.eot');
		    src: url('../public/fonts/qualification/QanelasMedium.eot') format('embedded-opentype'),
		         url('../public/fonts/qualification/QanelasMedium.woff2') format('woff2'),
		         url('../public/fonts/qualification/QanelasMedium.woff') format('woff'),
		         url('../public/fonts/qualification/QanelasMedium.ttf') format('truetype'),
		         url('../public/fonts/qualification/QanelasMedium.svg#QanelasMedium') format('svg');
		}
		@font-face {
		    font-family: 'QanelasExtraBold';
		    src: url('../public/fonts/qualification/QanelasExtraBold.eot');
		    src: url('../public/fonts/qualification/QanelasExtraBold.eot') format('embedded-opentype'),
		         url('../public/fonts/qualification/QanelasExtraBold.woff2') format('woff2'),
		         url('../public/fonts/qualification/QanelasExtraBold.woff') format('woff'),
		         url('../public/fonts/qualification/QanelasExtraBold.ttf') format('truetype'),
		         url('../public/fonts/qualification/QanelasExtraBold.svg#QanelasExtraBold') format('svg');
		}
    	/* Header */
		#header{
			padding: 35px 0px;
		}
		#logo {
		    position: absolute;
		    max-width: 100%;
		}
		.menu-block-outer nav {
		    display: inline-block;
		    vertical-align: bottom;
		    margin-bottom: 0;
		    min-height: auto;
		    float: right;
		}
		.navbar-collapse.collapse{
			padding-left: 0;
			padding-right: 0;
		}
		.navbar-nav>li>a:hover,
		.navbar-nav>li>a:focus {
			color: #2aa464;
			background: transparent;
		}
		.navbar-nav>li>a {
			font-size: 14px;
			color: #07378a;
			font-family: 'QanelasMedium';
		}
		.navbar-nav>li.active>a{
			color: #2aa464;
			font-family: 'QanelasExtraBold';
		}
		.header-right-menu ul li:not(:last-child) {
		    margin-bottom: 14px;
		    display: inline-block;
		    width: 100%;
		}
		.nav>li>a {
		    position: relative;
		    display: block;
		    padding: 3px 10px;
		}
		.navbar-nav>li:last-child a {
		    background: #07378a;
		    color: #fff;
		    text-transform: uppercase;
		    font-family: 'QanelasExtraBold';
		    font-size: 13px;
		    letter-spacing: 1px;
		}
		.header-right-menu ul li a{
			background-color: #2aa464;
			color: #ffffff;
			font-size: 16px;
			line-height: 16px;
		    padding: 5px 10px;
		}
		.header-right-menu ul li:last-child a{
			background-color: #07378b;
		}
		.double-border-line{
			width: 100%;
			border-top: 14px solid #07378b;
			border-bottom: 14px solid #4472c0;
		}
		.navbar-toggle {
		    background-color: #fdd530;
		}
		.navbar-toggle .icon-bar {
		    background-color: #07378b;
		}
		.menu-block-outer nav.navbar {
		    margin-top: 32px;
		}
		.menu-block-outer nav ul {
		    margin-bottom: 0;
		}
		/* Header */

		#banner{
			background: url('../public/qualification/banner-pattern.png') no-repeat center bottom;
			background-size: 100% auto;
			padding: 60px 0px;
			min-height: 300px;
		}
		.parent-card-block {
		    position: relative;
		    display: block;
		    max-width: 340px;
		    margin: 0 auto;
		}
		.card-block {
		    position: absolute;
		    width: 100%;
		    height: 100%;
		    left: 0;
		    right: 0;
		    top: 0;
		    margin: auto;
		}
		.card-upper-block{
			height: 45%;
			padding: 15px 0px 5px;
		}
		.card-lower-block{
			height: 55%;
		}
		.card-company-block{
			width: 70%;
		    float: left;
		}
		.card-upper-logo {
		    width: 27%;
		    float: left;
		    position: relative;
		    min-height: 30px;
		    padding: 0 3px;
		}
		.card-upper-logo:after {
		    content: '';
		    position: absolute;
		    top: 10px;
		    bottom: 0;
		    background: url(../public/qualification/arrowright.jpg);
		    width: 15px;
		    height: 16px;
		    right: -28px;
		    /*transform: rotate(180deg);*/
		}	
		
		.card-upper-logo img{
			max-width: 100%;
			max-height: 64px;
		}
		.card-bottom-logo {
		    float: left;
		    width: 30%;
		    padding: 10px 0px;
		}
		.domians-name{
			float: left;
		    width: 70%;
		    display: table;
		    height: 100%;
		}
		.card-company-block span{
			display: block;
			/*min-height: 20px;*/
			position: relative;
			color: #22498e;
			text-align: left;
			padding-left: 25px; 
			font-family: 'Open Sans', sans-serif;
		    font-weight: 600;
		    font-size: 14px;
			line-height: 15px;
			word-break: break-word;
		}
		.card-company-block span:before{
			content: '';
			position: absolute;
			top: 0;
			bottom: 0;
			background: url(../public/qualification/arrow.jpg);
			width: 15px;
			height: 15px;
			left: -16px;
		}
		.card-company-block span.arrow-disable:before,
		.card-upper-logo.arrow-disable:after{
			display: none;
		}
		.domians-name ul{
			display: table-cell;
		    vertical-align: middle;
		    margin: 0;
		    color: #fff;
		    padding: 0;
    		list-style: none;
		}
		.domians-name ul li {
		    font-size: 9px;
		    text-align: right;
		    padding: 2px 0px;
		    padding-right: 26px;
		    position: relative;
		}
		.domians-name ul li:after {
		    content: '';
		    position: absolute;
		    width: 15px;
		    height: 5px;
		    background: gold;
		    top: 6px;
		    right: 0;
		}
		.registration-step {
		    min-height: 550px !important;
			border-bottom: 14px solid #07378b;
			position: relative;
		}
		#loader{
			position: absolute;
			width: 100%;
			height: 100%;
			left: 0;
			top: 0;
			background-color: rgba(255, 255, 255, 0.8);
			z-index: 9;
		}
		#loader svg{
			width: 60px;
			position: absolute;
			left: 0;
			right: 0;
			top: 0;
			bottom: 0;
			margin: auto;
		}
		#loader svg path{
			fill: #07378b;
		}
    </style>
    <body>
		<header id="header">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-2 col-md-2">
						<div id="logo">
							<figure>
								<img src={{url('public/img/cartptp.png')}} class="img-responsive" />
							</figure>
						</div>
					</div>
					<div class="col-xs-12 col-sm-10 col-md-10 menu-section">
						<div class="menu-block-outer pull-right">
							<!-- <nav class="header-right-menu">
								<ul>
							        <li><a href="#">Contactez-nous</a></li>
							        <li><a href="#">Mon espace</a></li>
							    </ul>
							</nav> -->
							<nav class="navbar">
								<!-- Brand and toggle get grouped for better mobile display -->
								<div class="navbar-header">
								  	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
									    <span class="sr-only">Toggle navigation</span>
									    <span class="icon-bar"></span>
									    <span class="icon-bar"></span>
									    <span class="icon-bar"></span>
								  	</button>
								</div>

								<!-- Collect the nav links, forms, and other content for toggling -->
								<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
									<ul class="nav navbar-nav">
								        <li><a href="#">Les avantages de la carte</a></li>
								        <li><a href="#">Entreprises</a></li>
								        <li><a href="#">Maîtres d’ouvrages</a></li>
								        <li><a href="#"><span>Maîtres d’oeuvre</span> </a></li>
								        <li class="active"><a href="#">Demandez votre carte</a></li>
								        <li><a href="#">Contact</a></li>
								        <li><a href="#">Mon espace</a></li>
							      	</ul>
							    </div>
							</nav>
						</div>
					</div>
				</div>
			</div>
		</header>
        @yield('content')
        
        
    </body>
</html>
