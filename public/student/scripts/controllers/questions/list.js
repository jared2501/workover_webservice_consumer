'use strict';

var app = angular.module('workover');

app.controller('Questions/ListCtrl', function($scope, $routeParams, Question, Course) {
	$scope.questions = Question.list({
		'course_id': $routeParams.courseId,
		'include_tags': true
	});

	$scope.course = Course.get({
		'course_id': $routeParams.courseId
	});
});
