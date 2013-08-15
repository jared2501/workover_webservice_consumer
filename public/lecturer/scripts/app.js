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
		.when('/courses/add', {
			templateUrl: 'views/courses/add.html',
			controller: 'Courses/AddCtrl'
		})
		.when('/questions/list', {
			templateUrl: 'views/questions/list.html',
			controller: 'Questions/ListCtrl'
		})
		.when('/questions/add', {
			redirectTo: '/questions/add/1'
		})
		.when('/questions/add/:systemId', {
			templateUrl: 'views/questions/add.html',
			controller: 'Questions/AddCtrl'
		})
		.when('/questions/:questionId', {
			templateUrl: 'views/questions/view.html',
			controller: 'Questions/ViewCtrl'
		})
		.when('/students/list', {
			templateUrl: 'views/students/list.html',
			controller: 'Students/ListCtrl'
		})
		.when('/students/add', {
			templateUrl: 'views/students/add.html',
			controller: 'Students/AddCtrl'
		})
		.otherwise({
			redirectTo: '/courses/list'
		});
}]);
