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
		<!--<link href="components/file-uploader-master/client/fineuploader.css" rel="stylesheet">
		<link rel="stylesheet" href="components/select2-release-3.2/select2.css"/>-->
		<link rel="stylesheet" href="styles/main.css"/>

		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<script>
			var globalAuthUser = <?php echo json_encode($user->to_array()); ?>;
		</script>
	</head>
	<body ng-app="workover">

		<!--[if lt IE 7]>
			<p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
		<![endif]-->
		
		
			<script src="scripts/vendor/es5-shim.min.js"></script>
			<script src="scripts/vendor/json3.min.js"></script>
		

		<!-- Add your site or application content here -->
		<div class="container-fluid nopadding">
			<div class="row-fluid">
				<div class="span12">
					<div id="header">
						<a href="#/">
							<img src="img/logo.png" alt="" />
						</a>
						<div class="hright">
							<div id="userinfo" class="column">
								<a href="<?php echo Router::get('logout'); ?>" class="btn"><i class="icon-off"></i> Log Out</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>


		<div class="container-fluid" id="container">
			<div class="row-fluid">
				<div class="span12" id="content" ng-view>

				</div>
			</div>
		</div>


		<div class="notifications" ng-controller="NotificationsCtrl">
			<div ng-repeat="notification in notifications"
				class="alert-message notification warning {{notification.type}}"
				ng-click="notification.click()">
					<strong>{{notification.title}}</strong>
					<span ng-bind-html-unsafe="notification.message"></span>
					<span class="close-text" ng-click="notification.remove()">&#40;close&#41;</span>
			</div>
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
