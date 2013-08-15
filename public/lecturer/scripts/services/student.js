'use strict';

var app = angular.module('workover');

app.service('Student', function ($http, $q, NotificationsService) {
	this.list = function (params) {
		var deferred = $q.defer();
		var queryString = '';

		$http.get('/workover/courses/'+params.course_id+'/students').success(function(data) {
			deferred.resolve(data);
		}).error(function(data, status, headers, config) {
			deferred.reject({'status': status, 'data': data});
		});
		
		return deferred.promise;
	};

	this.add = function (params) {
		var deferred = $q.defer();

		$http.post('/workover/courses/'+params.course_id+'/students/add', params.students).success(function(data) {
			deferred.resolve(data);
		}).error(function(data, status, headers, config) {
			deferred.reject({'status': status, 'data': data});
		});
		
		return deferred.promise;
	};

	this.remove = function (params) {
		var deferred = $q.defer();

		$http.post('/workover/courses/'+params.course_id+'/students/remove', params.user_ids).success(function(data) {
			NotificationsService.push({
				title: 'Success!',
				message: 'The selected students have been succesfully removed.',
				type: 'success'
			});

			deferred.resolve(data);
		}).error(function(data, status, headers, config) {
			deferred.reject({'status': status, 'data': data});
		});
		
		return deferred.promise;
	};

	this.reset_password = function (params) {
		var deferred = $q.defer();

		$http.post('/workover/users/reset_password', {
			user_ids: params.user_ids,
			password: params.password
		}).success(function(data) {
			NotificationsService.push({
				title: 'Success!',
				message: 'The selected students have been succesfully added.',
				type: 'success'
			});

			deferred.resolve(data);
		}).error(function(data, status, headers, config) {
			deferred.reject({'status': status, 'data': data});
		});
		
		return deferred.promise;
	};
});
