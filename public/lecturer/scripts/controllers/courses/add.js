'use strict';

var app = angular.module('workover');

app.controller('Courses/AddCtrl', function($scope, $location, Course, CourseSelector, Authuser, NotificationsService) {
	var loadQuestions = function() {
		Course.list().then(function(courses) {
			$scope.courses = courses;
		});
	}

	$scope.addExisting = function() {
		Course.add_to_user({
			'user_id': Authuser.id,
			'course_ids': [$scope.existingCourse]
		}).then(function(){
			CourseSelector.reloadCourses(); // weve made a modification to course so reload them
			$location.path('#/questions/list');
		});
	};

	var proccessing = false;
	$scope.addNew = function() {
		if(!proccessing) {
			proccessing = true;
			$scope.newCourse.users = [Authuser.id];

			NotificationsService.push({
				title: 'Loading...',
				message: '',
				type: 'warning'
			});
			
			Course.create({question: $scope.newCourse}).then(function(){
				proccessing = false;
				CourseSelector.reloadCourses(); // weve made a modification to course so reload them
				$location.path('#/questions/list');
			}, function(){
				proccessing = false;
			});
		}
	};

	$scope.courses = {};
	$scope.newCourse = {};
	loadQuestions();
});
