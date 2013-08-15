'use strict';

var app = angular.module('workover');

app.service('Course', function ($http, $q, NotificationsService) {
	this.get = function (params) {
		var deferred = $q.defer();

		$http.get('/workover/courses/'+params.course_id).success(function(data) {
			deferred.resolve(data);
		}).error(function(data, status, headers, config) {
			deferred.reject({'status': status, 'data': data});
		});
		
		return deferred.promise;
	};

	this.list = function (params) {
		var deferred = $q.defer();
		var queryString = '';

		if(!angular.isUndefined(params)) {
			if(!angular.isUndefined(params.user_id)) {
				queryString += 'user_id=' + params.user_id;
			}
		}

		$http.get('/workover/courses?'+queryString).success(function(data) {
			deferred.resolve(data);
		}).error(function(data, status, headers, config) {
			deferred.reject({'status': status, 'data': data});
		});
		
		return deferred.promise;
	};

	this.create = function(params) {
		var deferred = $q.defer();

		$http.post('/workover/courses/create', params.question).success(function(data) {
			
			NotificationsService.push({
				title: 'Success!',
				message: 'The course has been successfully created.',
				type: 'success'
			});

			deferred.resolve(data);
		}).error(function(data, status, headers, config) {
			deferred.reject({'status': status, 'data': data});
		});
		
		return deferred.promise;
	};

	this.remove_questions = function(params) {
		var deferred = $q.defer();

		$http.post('/workover/courses/'+params.course_id+'/questions/remove', params.question_ids).success(function(data) {

			NotificationsService.push({
				title: 'Success!',
				message: 'The questions(s) has been removed from this course.',
				type: 'success'
			});

			deferred.resolve(data);
		}).error(function(data, status, headers, config) {
			deferred.reject({'status': status, 'data': data});
		});
		
		return deferred.promise;
	}

	this.add_questions = function(params) {
		var deferred = $q.defer();

		$http.post('/workover/courses/'+params.course_id+'/questions/add', params.question_ids).success(function(data) {

			NotificationsService.push({
				title: 'Success!',
				message: 'The questions(s) has been added from this course.',
				type: 'success'
			});

			deferred.resolve(data);
		}).error(function(data, status, headers, config) {
			deferred.reject({'status': status, 'data': data});
		});
		
		return deferred.promise;
	}

	this.remove_from_user = function(params) {
		var deferred = $q.defer();

		$http.post('/workover/users/'+params.user_id+'/courses/delete', params.course_ids).success(function(data) {

			NotificationsService.push({
				title: 'Success!',
				message: 'The course(s) has been removed from your courses.',
				type: 'success'
			});

			deferred.resolve(data);
		}).error(function(data, status, headers, config) {
			deferred.reject({'status': status, 'data': data});
		});
		
		return deferred.promise;
	};

	this.add_to_user = function(params) {
		var deferred = $q.defer();

		$http.post('/workover/users/'+params.user_id+'/courses/add', params.course_ids).success(function(data) {

			NotificationsService.push({
				title: 'Success!',
				message: 'The course(s) has been added to your courses.',
				type: 'success'
			});

			deferred.resolve(data);
		}).error(function(data, status, headers, config) {
			deferred.reject({'status': status, 'data': data});
		});
		
		return deferred.promise;
	}
});
