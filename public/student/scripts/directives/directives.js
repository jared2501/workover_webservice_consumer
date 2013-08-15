'use strict';

var app = angular.module('workover');

app.directive('checkboxModel', function($compile) {
	return {
		scope: {
			model: '=checkboxModel'
		},
		link: function($scope, element, attrs) {
			$scope.$on('checkboxModel.selectAll', function($event, value) {
				$scope.$apply(function(){
					$scope.model = value;
					element.prop('checked', value);
				});
			});
			
			$scope.$watch('model', function(newVal, oldVal) {
				if(!(typeof newVal === 'undefined' && typeof oldVal === 'undefined')) {
					$scope.$emit('checkboxModel.selected', newVal);
				}
			});

			element.bind('click', function($event){
				$scope.$apply(function(){
					$scope.model = $event.target.checked;
				});
			});
		}
	};
})
.directive('checkboxModelAll', function($compile) {
	return {
		scope: {
			objects: '=checkboxModelAll',
			disableActions: '=disableActions'
		},
		link: function($scope, element, attrs) {
			element.bind('click', function($event) {
				$scope.$parent.$broadcast('checkboxModel.selectAll', $event.target.checked);
			});


			var numSelected = 0;
			$scope.disableActions = true;

			$scope.$parent.$on('checkboxModel.selected', function($event, value) {
				value ? numSelected++ : numSelected-- ;

				if(numSelected >= $scope.objects.length) {
					element.prop('checked', true);
				} else {
					element.prop('checked', false);
				}

				if(numSelected > 0) {
					$scope.disableActions = false;
				} else {
					$scope.disableActions = true;
				}
			});
		}
	};
})
.directive('truncate', function($compile) {
	return {
		scope: {
			text: '@truncate'
		},
		replace: true,
		template: '<span>{{innerText}}<span ng-show="innerText.length > length-1">{{dotdotdot}} <a ng-click="changeMoreLess()">&#40;show {{moreLess}}&#41;</a></span></span>',
		link: function(scope, element, attrs) {
			var more = false;
			scope.length = attrs.truncateLength;

			if(typeof attrs.truncateLength === 'undefined') {
				scope.length = 100;
			}

			var truncate = function(val){
				if(more) {
					return val;
				} else {
					return val.substring(0, scope.length);
				}
			}
			
			scope.moreLess = 'more';
			scope.dotdotdot = '...';
			scope.changeMoreLess = function(){
				more = !more;
				if(more) {
					scope.moreLess = 'less';
					scope.dotdotdot = ''
				} else {
					scope.moreLess = 'more';
					scope.dotdotdot = '...';
				}

				scope.innerText = truncate(scope.text);
			}

			scope.$watch('text', function(newVal) {
				scope.innerText = truncate(newVal);
			});
		}
	};
})
.directive('placeholder', function($timeout){
	jQuery.support.placeholder = (function(){
		var i = document.createElement('input');
		return 'placeholder' in i;
	})();

	if (jQuery.support.placeholder) {
		return {};
	}
	return {
		link: function(scope, elm, attrs){
		if (attrs.type === 'password') {
			return;
		}
		$timeout(function(){
			elm.val(attrs.placeholder).focus(function(){
				if ($(this).val() == $(this).attr('placeholder')) {
					$(this).val('');
				}
			}).blur(function(){
				if ($(this).val() == '') {
					$(this).val($(this).attr('placeholder'));
				}
			});
		});
		}
	}
})
.directive('courseSelector', function($rootScope){
	return {
		replace: true,
		template: '<select ui-select2 ng-model="currentCourseId" class="select2-fullwidth"><option ng-repeat="course in courses" value="{{course.id}}">{{course.code}}</option></select>',
		scope: {},
		controller: function($scope, CourseSelector){
			// Keep the coures live
			$scope.$watch(function() { return CourseSelector.courses }, function(courses){
				$scope.courses = courses;
			});

			// Watch for other things changing the current course
			$scope.$watch(function() { return CourseSelector.currentCourse }, function(currentCourse){
				$scope.currentCourseId = currentCourse.id;
			});

			$scope.$watch('currentCourseId', function(currentCourseId){
				CourseSelector.updateCurrentCourse(currentCourseId);
			});
		},
		link: function($scope, element, attrs) {
			
		}
	};
})
.directive('addTag', function(Tag){
	return {
		replace: true,
		template: '<span><a ng-show="!showState">&#40;add&#41;</a> <input ng-show="showState" ng-model="newTagName" style="width: 80px;" placeholder="enter tag"></input></span>',
		scope: {
			addTag: '=',
			questionId: '=',
		},
		link: function($scope, elem, attrs) {
			$scope.showState = false;
			$scope.newTagName = '';

			var add = elem.children('a');
			var input = elem.children('input');

			var showInput = function() {
				$scope.$apply(function(){
					$scope.showState = true;
				});
				input.focus();
			};

			var addTag = function() {
				if($scope.newTagName != '' && $scope.addTag.indexOf($scope.newTagName) < 0) {
					Tag.create({
						question_id: $scope.questionId,
						tag_name: $scope.newTagName
					}).then(function(){
						$scope.addTag.push($scope.newTagName);
						$scope.newTagName = '';
						$scope.showState = false;
					});
				} else {
					$scope.$apply(function(){
						$scope.newTagName = '';
						$scope.showState = false;
					});
				}
			};

			add.bind('click', showInput);
			input.bind('blur', addTag);
			input.bind('keypress', function(e){
				if (e.which == 13) {
					addTag();
				}
			})
		}
	};
})
.directive('delTag', function(Tag){
	return {
		scope: {
			delTag: '=',
			questionId: '=',
			tagName: '='
		},
		link: function($scope, elem, attrs) {
			elem.bind('click', function(){
				Tag.remove({
					question_id: $scope.questionId,
					tag_name: $scope.tagName
				}).then(function(){
					var index = $scope.delTag.indexOf($scope.newTagName);
					$scope.delTag.splice(index, 1);
				});
			});
		}
	};
})
.directive('fineUploader', function(NotificationsService){
	return {
		scope: {
			success: '='
		},
		link: function($scope, elem, attrs) {
			var filetype = 'csv';

			if(!!attrs.filetype) {
				filetype = attrs.filetype;
			}

			elem.fineUploader({
				request: {
					endpoint: '/workover/users/1/upload/' + filetype
				},
				validation: {
					allowedExtensions: ['csv'],
					acceptFiles: 'csv'
				},
			})
			.on('submit', function(id, filename) {
				$scope.$apply(function(){
					NotificationsService.push({
						title: 'Uploading file...',
						type: 'info'
					});
				});
			})
			.on('complete', function(event, id, fileName, response) {
				if(response && !!response.success && !!response.file_output) {
					$scope.$apply(function(){
						$scope.success(response.file_output);
					});

					// Dirty hack for tabs
					$('#upload-students-done').tab('show');
				}
			})
			.on('error', function (id, fileName, reason) {
				$scope.$apply(function(){
					NotificationsService.push({
						title: 'Unable to succesfully upload file',
						type: 'error'
					});
				});
			});
		}
	};
})
.directive('iframeResize', function($timeout){
	return {
		link: function($scope, elem, attrs) {
			elem.load(function(){
				var that = this;
				
				var autoresize = function() {
					if(elem) {
						var doc = 'contentDocument' in that ? that.contentDocument : that.contentWindow.document;
						var newHeight = doc.body.offsetHeight;
						elem.height(newHeight);
						
						$timeout(autoresize, 250);
					}
				}

				autoresize();
			});
		}
	}
});