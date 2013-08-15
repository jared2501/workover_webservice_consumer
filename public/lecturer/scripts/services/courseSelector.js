'use strict';

var app = angular.module('workover');

app.service('CourseSelector', function ($http, $q, $timeout, Course, Authuser) {
	var self = this;
	self.courses = [];
	self.currentCourse = {};

	// Load the courses list
	var loadCourses = function() {
		var deferred = $q.defer();

		Course.list({
			'user_id': Authuser.id
		}).then(function(courses) {
			self.courses = courses;
			deferred.resolve(courses);
		});

		return deferred.promise;
	}

	var deferred = $q.defer();
	loadCourses().then(function(){
		deferred.resolve();
	});
	self.afterLoad = deferred.promise;


	// Execute everything, and then if we still havent assigned a current course, reset it.
	// ie give time to controllers to set the current course before its reset
	// Probably dont need two timeouts?
	$timeout(function(){
		self.afterLoad.then(function(){
			$timeout(function(){
				if(!self.currentCourse.id) {
					self.resetCurrentCourse();
				}
			});
		});
	});
	

	// Called to reload courses in case external services make modifications
	this.reloadCourses = function() {
		var deferred = $q.defer();
		loadCourses().then(function(){
			deferred.resolve();
		});
		self.afterLoad = deferred.promise;
	}

	this.updateCurrentCourse = function(newCurrentCourseId) {
		var deferred = $q.defer();

		// Wait until weve loaded the course list at least once (useful on init)
		self.afterLoad.then(function(){
			var length = self.courses.length;
			for(var i = 0; i < length; i++) {
				if(self.courses[i].id == newCurrentCourseId) {
					self.currentCourse = self.courses[i];
					deferred.resolve(self.currentCourse);
				}
			}

			deferred.reject();
		});

		return deferred.promise;
	};

	this.resetCurrentCourse = function(reloadCourses) {
		var deferred = $q.defer();

		// Find the first course in the list of courses
		if(reloadCourses) {
			self.loadCourses().then(function(){
				if(self.courses.length < 1) {
					deferred.reject();
				} else {
					self.currentCourse = self.courses[0];
					deferred.resolve(self.currentCourse);
				}
			});
		} else {
			if(self.courses.length < 1) {
				deferred.reject();
			} else {
				self.currentCourse = self.courses[0];
				deferred.resolve(self.currentCourse);
			}
		}

		return deferred.promise;
	}
});
