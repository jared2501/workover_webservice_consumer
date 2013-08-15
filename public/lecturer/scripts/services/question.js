'use strict';

var app = angular.module('workover');

app.service('Question', function ($http, $q, NotificationsService) {
	this.get = function (params) {
		var deferred = $q.defer();

		$http.get('/workover/questions/'+params.question_id).success(function(data) {
			deferred.resolve(data);
		}).error(function(data, status, headers, config) {
			deferred.reject({'status': status, 'data': data});
		});
		
		return deferred.promise;
	};

	this.list = function (params) {
		var deferred = $q.defer();
		var queryString = '';


		if(params) {
			if(!angular.isUndefined(params.include_tags)) {
				queryString = queryString + 'include_tags=' + params.include_tags;
			}
		}

		if(params.course_id) {
			$http.get('/workover/courses/'+params.course_id+'/questions?'+queryString).success(function(data) {
				deferred.resolve(data);
			}).error(function(data, status, headers, config) {
				deferred.reject({'status': status, 'data': data});
			});
		} else {
			$http.get('/workover/systems/'+params.system_id+'/questions?'+queryString).success(function(data) {
				deferred.resolve(data);
			}).error(function(data, status, headers, config) {
				deferred.reject({'status': status, 'data': data});
			});
		}
		
		return deferred.promise;
	};
});
