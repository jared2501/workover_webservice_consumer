'use strict';

var workoverFrontendApp = angular.module('workover', ['ui', 'NotifcationsService'])
.config(['$provide', '$routeProvider', function($provide, $routeProvider) {
	// gloablAuthUser should be defined by fuelphp, accessible at top of page.
	$provide.constant('authUserConfig', globalAuthUser);

	$routeProvider
		.when('/courses/list', {
			templateUrl: 'views/courses/list.html',
			controller: 'Courses/ListCtrl'
		})
		.when('/questions/:courseId/list', {
			templateUrl: 'views/questions/list.html',
			controller: 'Questions/ListCtrl'
		})
		.when('/questions/:courseId/:questionId', {
			templateUrl: 'views/questions/view.html',
			controller: 'Questions/ViewCtrl'
		})
		.otherwise({
			redirectTo: '/courses/list'
		});
}]);
