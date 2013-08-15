'use strict';

var app = angular.module('workover');

app.controller('Students/AddCtrl', function($scope, $location, CourseSelector, Student, NotificationsService) {
	$scope.students = [];
	$scope.numStudents = 10;
	$scope.doneUploading = false;

	$scope.fileUploaded = function(students) {
		$scope.students = students;
		$scope.numStudents = students.length;

		NotificationsService.push({
			title: 'Success!',
			message: 'Students Loaded.',
			type: 'success'
		});
	};

	$scope.updateNumStudents = function() {
		var length = $scope.students.length;

		if($scope.numStudents < length) {
			var howMany = length - $scope.numStudents;
			$scope.students.splice(-howMany, howMany);
		} else if($scope.numStudents > length) {
			var howMany = $scope.numStudents - length;
			for(var i = 0; i < howMany; i++) {
				$scope.students.push({});
			}
		}
	};

	$scope.addStudents = function() {
		NotificationsService.push({
			title: 'Adding students...',
			type: 'info'
		});

		Student.add({
			course_id: CourseSelector.currentCourse.id,
			students: $scope.students
		}).then(function(response) {
			var length = response.length;
			var atLeastOneFailed = false;

			if(response.length == $scope.students.length) {
				for(var i = 0; i < length; i++) {
					$scope.students[i].success = response[i].success;

					if(!response[i].success) {
						atLeastOneFailed = true;
					}
				}

				if(atLeastOneFailed) {
					NotificationsService.push({
						title: 'Warning!',
						message: 'At least one student has failed to be added to the specified course.'
					});
				} else {
					NotificationsService.push({
						title: 'Success!',
						message: 'All students have been added to the specified course.',
						type: 'success'
					});

					$scope.doneUploading = true;
				}
			} else {
				NotificationsService.push({
					title: 'Unexpected respone from server!',
					message: 'Reload page, and contact us if this problem persists.',
					type: 'error'
				});
			}
		})
	};

	$scope.goBack = function() {
		$location.url('/students/list');
	};

	$scope.resetStudents = function() {
		$scope.students = [];
		$scope.updateNumStudents();
		$scope.doneUploading = false;
	};

	$scope.updateNumStudents();
});
