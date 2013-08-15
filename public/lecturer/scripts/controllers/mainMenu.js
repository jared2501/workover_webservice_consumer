'use strict';

var app = angular.module('workover');

app.controller('MainMenuCtrl', function($scope, $location) {
	$scope.updateMenu = function (location) {
		try {
			var reg = new RegExp("^/(.+)/");
			var result = reg.exec(location);
			
			if(!angular.isUndefined(result)) {
				if(!angular.isUndefined(result.length) && result.length > 1) {
					$scope.currentLocation = result[1];
				}
			}
		} catch(err) {
			if(!angular.isUndefined(console) && !angular.isUndefined(console.log)) {
				console.log(err);
			}
		}
	}

	$scope.$watch( function() { return $location.path(); }, function ( path ) {
		$scope.updateMenu(path);
	});
});