'use strict';

var app = angular.module('workover');

app.controller('NotificationsCtrl', function($scope, NotificationsService) {
	$scope.$watch( function () { return NotificationsService.notifications; }, function ( notifications ) {
		$scope.notifications = notifications;
	});
});