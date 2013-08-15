'use strict';

var app = angular.module('workover');

app.controller('Courses/ListCtrl', function($scope, $timeout, Course, Authuser) {
	$scope.courses = Course.list({user_id: Authuser.id});
});
