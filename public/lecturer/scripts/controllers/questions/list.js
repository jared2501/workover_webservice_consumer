'use strict';

var app = angular.module('workover');

app.controller('Questions/ListCtrl', function($scope, CourseSelector, Question, Course) {
	$scope.questions = null;
	$scope.course = {};

	var loadQuestions = function(courseId) {
		$scope.courses = null;

		Question.list({
			'course_id': courseId,
			'include_tags': true
		}).then(function(questions) {
			$scope.questions = questions;
		});
	}

	$scope.$watch(function() { return CourseSelector.currentCourse }, function(currentCourse){
		if(currentCourse.id) {
			$scope.course = currentCourse;
			loadQuestions(currentCourse.id);
		}
	});

	$scope.remove = function() {
		var ids = getSelectedQuestions();

		Course.remove_questions({
			'course_id': $scope.course.id,
			'question_ids': ids
		}).then(function(questions) {
			loadQuestions($scope.course.id);
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
