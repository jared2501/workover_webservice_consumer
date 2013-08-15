'use strict';

var app = angular.module('workover');

app.controller('Questions/ViewCtrl', function($scope, $routeParams, Question) {
	$scope.question = Question.get({question_id: $routeParams.questionId});
});
