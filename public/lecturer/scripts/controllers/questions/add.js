'use strict';

var app = angular.module('workover');

app.controller('Questions/AddCtrl', function($scope, $routeParams, $location, CourseSelector, Course, Question) {
	$scope.questions = null;
	$scope.course = {};
	$scope.system = {create_url: 'http://localhost/workover_ws/question/create'};

	Question.list({
		'system_id': $routeParams.systemId,
		'include_tags': true
	}).then(function(questions){
		$scope.questions = questions;
	});

	$scope.$watch(function() { return CourseSelector.currentCourse }, function(currentCourse){
		if(currentCourse.id) {
			$scope.course = currentCourse;
		}
	});

	$scope.add = function() {
		var ids = getSelectedQuestions();

		Course.add_questions({
			'course_id': $scope.course.id,
			'question_ids': ids
		}).then(function(questions) {
			$location.path('/questions/list');
		});
	}

	var getSelectedQuestions = function() {
		var ids = [];
		var length = $scope.questions.length;

		for(var i = 0; i < length; i++) {
			if($scope.questions[i].selected) {
				ids.push($scope.questions[i].id);
			}
		}

		return ids;
	}
});
