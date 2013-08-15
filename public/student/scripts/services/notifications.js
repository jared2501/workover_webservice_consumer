//~ Notifcations service

var app = angular.module('NotifcationsService', [], function($httpProvider) {
	$httpProvider.responseInterceptors.push(function($q, NotificationsService) {
		return function ( promise ) {
			var deferred = $q.defer();

			promise.then(function(response) {
				deferred.resolve(response);
			}, function(response) {
				console.log(response);
				NotificationsService.push({
					title: 'Error ' + response.status + '!',
					message: response.data.errors,
					type: 'error'
				});
				deferred.reject(response);
			});

			return deferred.promise;
		}
	});
});

app.service('NotificationsService', function($timeout){
	this.notifications = [];
	var notifications = this.notifications;

	var primaryNotificationExists = false;
	var defaultTime = 10;
	var nextNotificationId = 1;

	this.push = function(notification){
		var that = this;
		notification.remove = function() {
			if(!angular.isUndefined(this.timeOut)) {
				$timeout.cancel(this.timeOut); // Cancel the timeout created below (incase its deleted before it times out)
			}
			notifications.splice(notifications.indexOf(this), 1);
			if(this.isPrimary){
				primaryNotificationExists = false;
			}
		}
		if(!angular.isFunction(notification.click)){
			notification.click = notification.remove;
		}
		if(angular.isUndefined(notification.isPrimary)){
			notification.isPrimary = true; // Default is notifications are primary
		}

		if(notification.persistent != true) {
			notification.timeOut = $timeout( function(){
				notification.remove();
			}, defaultTime*1000);
		}

		// If notification is primary, and theres already one, replace it. If its primary and theres none, unshift the stack.
		// Otherwise its not primary, therefore push it.
		if(notification.isPrimary == true && primaryNotificationExists) {
			notifications[0].remove();
			notifications.unshift(notification);
			primaryNotificationExists = true;
		} else if(notification.isPrimary == true && !primaryNotificationExists) {
			notifications.unshift(notification);
			primaryNotificationExists = true;
		} else {
			notifications.unshift(notification);
		}

		return notification;
	};
});
