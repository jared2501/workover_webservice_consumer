'use strict';

var app = angular.module('workover');

app.controller('Students/ListCtrl', function($scope, $location, CourseSelector, Student) {
	$scope.students = null;
	$scope.course = null;
	
	var loadStudents = function(courseId) {
		$scope.students = null;

		Student.list({
			'course_id': courseId
		}).then(function(students) {
			$scope.students = students;
		});
	}

	$scope.$watch(function() { return CourseSelector.currentCourse }, function(currentCourse){
		if(currentCourse.id) {
			$scope.course = currentCourse;
			loadStudents(currentCourse.id);
		}
	});

	var getSelectedStudents = function() {
		var ids = [];
		var length = $scope.students.length;

		for(var i = 0; i < length; i++) {
			if($scope.students[i].selected) {
				ids.push($scope.students[i].id);
			}
		}

		return ids;
	}

	$scope.remove = function() {
		var ids = getSelectedStudents();

		Student.remove({
			'course_id': CourseSelector.currentCourse.id,
			'user_ids': ids
		}).then(function(){
			loadStudents(CourseSelector.currentCourse.id);
		});
	};


	$scope.resetPassword = {
		error: false,
		newPassword: '',
		confirmNewPassword: '',
		reset: function() {
			if($scope.resetPassword.newPassword == $scope.resetPassword.confirmNewPassword && $scope.resetPassword.newPassword.length > 2) {
				var ids = getSelectedStudents();

				Student.reset_password({
					user_ids: ids,
					password: $scope.resetPassword.newPassword
				}).then(function(){
					loadStudents(CourseSelector.currentCourse.id);
					$scope.resetPassword.newPassword = '';
					$scope.resetPassword.confirmNewPassword = '';
					$scope.resetPassword.error = false;

					// Bad dom manipulation, get rid of this later.
					$('#reset-password').modal('hide');
				});
			} else {
				$scope.resetPassword.error = true;
			}
		}
	};
});
