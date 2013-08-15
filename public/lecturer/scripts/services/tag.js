'use strict';

var app = angular.module('workover');

app.service('Tag', function ($http, $q, NotificationsService) {
	var Tag = {
		create: function(params) {
			var deferred = $q.defer();

			$http.post('/workover/questions/'+params.question_id+'/tags/add/'+params.tag_name).success(function(data) {
				
				NotificationsService.push({
					title: 'Tag Added!',
					type: 'success'
				});

				deferred.resolve(data);
			}).error(function(data, status, headers, config) {
				deferred.reject({'status': status, 'data': data});
			});
			
			return deferred.promise;
		},
		remove: function(params) {
			var deferred = $q.defer();
			
			$http.post('/workover/questions/'+params.question_id+'/tags/remove/'+params.tag_name).success(function(data) {
				
				NotificationsService.push({
					title: 'Tag Removed!',
					type: 'success'
				});

				deferred.resolve(data);
			}).error(function(data, status, headers, config) {
				deferred.reject({'status': status, 'data': data});
			});
			
			return deferred.promise;
		}
	};

	return Tag;
});
