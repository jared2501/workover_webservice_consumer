'use strict';

var app = angular.module('workover');

app.controller('Courses/ListCtrl', function($scope, $timeout, Course, CourseSelector, Authuser) {
	$scope.courses = null;
	
	var loadCourses = function() {
		$scope.courses = null;

		CourseSelector.afterLoad.then(function(){
			$scope.courses = CourseSelector.courses;
		});
	}
	
	$scope.remove = function() {
		var ids = [];
		var length = $scope.courses.length;

		for(var i = 0; i < length; i++) {
			if($scope.courses[i].selected) {
				ids.push($scope.courses[i].id);
			}
		}

		Course.remove_from_user({
			'user_id': Authuser.id,
			'course_ids': ids
		}).then(function(){
			CourseSelector.reloadCourses(); // weve made a modification to course so reload them
			loadCourses();
		});
	};


	loadCourses();
});
