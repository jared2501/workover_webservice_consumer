'use strict';

var app = angular.module('workover');

app.service('Authuser', function ($http, $q, authUserConfig) {
	var User = authUserConfig;
	
	return User;
});
