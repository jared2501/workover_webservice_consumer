<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="utf-8"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
		<title></title>
		<meta name="description" content=""/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<?php echo Casset::render_css() ?>

		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>
	<body ng-app="workover">

		<!--[if lt IE 7]>
			<p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
		<![endif]-->
		
		<!-- Add your site or application content here -->
		<div class="container-fluid nopadding">
			<div class="row-fluid">
				<div class="span12">
					<div id="header">
						<a href="#/">
							<?php echo Casset::img('logo.png'); ?>
						</a>
						<div class="hright">
							<!--<div id="search" class="column">
								<div class="search">
									<form method="post" action="#">
										<input type="text" placeholder="Search here" value="Search here" name="keyword" id="keyword"> 
									</form>
								</div>
							</div>
							<!--<div id="userinfo" class="column">
								<a class="userinfo dropown-toggle" data-toggle="dropdown" href="#userinfo">
									<img alt="" src="img/avatar.jpg" />
									<span><strong>Admin</strong></span>
								</a>
								<ul class="dropdown-menu">
									<li><a href="#">Edit Profile</a></li>
									<li><a href="#">Edit Preferences</a></li>
									<li><a href="#">Private Messages</a></li>
									<li class="divider"></li>
									<li><a href="login.html">Logout</a></li>
								</ul>
							</div>-->
						</div>
					</div>
				</div>
			</div>
		</div>


		<div class="container-fluid" id="container">
			<?php echo $content; ?>
		</div>

		<?php
			echo Casset::render_js();
			echo Casset::render_js_inline();
		?>

		<!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
		<script>
			var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
			(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
			g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
			s.parentNode.insertBefore(g,s)}(document,'script'));
		</script>
	</body>
</html>
