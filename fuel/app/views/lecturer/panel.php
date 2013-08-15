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
				<div class="span3 leftmenu" ng-controller="MainMenuCtrl">
					<ul>
						<!--<li><a href="index.html"><span class="ico"><i class="icon-home"></i></span><span class="text">Home</span></a></li>-->
						<li ng-class="{active: currentLocation == 'courses'}"><a href="#/courses/list"><span class="ico"><i class="icon-folder-open"></i></span><span class="text">Courses</span></a></li>
						<li ng-class="{active: currentLocation == 'questions'}"><a href="#/questions/list"><span class="ico"><i class="icon-th-list"></i></span><span class="text">Questions</span></a></li>
						<li ng-class="{active: currentLocation == 'students'}"><a href="#/students/list"><span class="ico"><i class="icon-user"></i></span><span class="text">Students</span></a></li>
					</ul>
				</div>

				<div class="span9" id="content" ng-view></div>
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

		<!--<script type="text/javascript" src="components/select2-release-3.2/select2.js"></script>
		
		<script type="text/javascript" src="scripts/vendor/angular.js"></script>
		<script type="text/javascript" src="components/angular-ui/build/angular-ui.js"></script>-->

		<!-- build:js scripts/scripts.js -->
		<!--<script src="components/file-uploader-master/client/js/header.js"></script>
		<script src="components/file-uploader-master/client/js/util.js"></script>
		<script src="components/file-uploader-master/client/js/button.js"></script>
		<script src="components/file-uploader-master/client/js/handler.base.js"></script>
		<script src="components/file-uploader-master/client/js/handler.form.js"></script>
		<script src="components/file-uploader-master/client/js/handler.xhr.js"></script>
		<script src="components/file-uploader-master/client/js/uploader.basic.js"></script>
		<script src="components/file-uploader-master/client/js/uploader.js"></script>
		<script src="components/file-uploader-master/client/js/dnd.js"></script>
		<script src="components/file-uploader-master/client/js/jquery-plugin.js"></script>-->


	   <!-- <script src="scripts/app.js"></script>
		<script src="scripts/directives/directives.js"></script>

		<script src="scripts/services/courseSelector.js"></script>
		<script src="scripts/services/notifications.js"></script>
		<script src="scripts/services/authuser.js"></script>
		<script src="scripts/services/student.js"></script>
		<script src="scripts/services/course.js"></script>
		<script src="scripts/services/question.js"></script>
		<script src="scripts/services/tag.js"></script>

		<script src="scripts/controllers/mainMenu.js"></script>
		<script src="scripts/controllers/notifications.js"></script>

		<script src="scripts/controllers/students/list.js"></script>
		<script src="scripts/controllers/students/add.js"></script>
		<script src="scripts/controllers/courses/list.js"></script>
		<script src="scripts/controllers/courses/add.js"></script>
		<script src="scripts/controllers/questions/list.js"></script>-->
		<!-- endbuild -->

		<!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
		<script>
			var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
			(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
			g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
			s.parentNode.insertBefore(g,s)}(document,'script'));
		</script>
	</body>
</html>
