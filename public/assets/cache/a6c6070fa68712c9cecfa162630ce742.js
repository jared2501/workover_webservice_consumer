'use strict';var workoverFrontendApp=angular.module('workover',['ui','NotifcationsService']).config(['$routeProvider',function($routeProvider){$routeProvider.when('/courses/list',{templateUrl:'views/courses/list.html',controller:'Courses/ListCtrl'}).when('/courses/add',{templateUrl:'views/courses/add.html',controller:'Courses/AddCtrl'}).when('/questions/list',{templateUrl:'views/questions/list.html',controller:'Questions/ListCtrl'}).when('/students/list',{templateUrl:'views/students/list.html',controller:'Students/ListCtrl'}).when('/students/add',{templateUrl:'views/students/add.html',controller:'Students/AddCtrl'}).otherwise({redirectTo:'/courses/list'});}]);
'use strict';var app=angular.module('workover');app.directive('checkboxModel',function($compile){return{scope:{model:'=checkboxModel'},link:function($scope,element,attrs){$scope.$on('checkboxModel.selectAll',function($event,value){$scope.$apply(function(){$scope.model=value;element.prop('checked',value);});});$scope.$watch('model',function(newVal,oldVal){if(!(typeof newVal==='undefined'&&typeof oldVal==='undefined')){$scope.$emit('checkboxModel.selected',newVal);}});element.bind('click',function($event){$scope.$apply(function(){$scope.model=$event.target.checked;});});}};}).directive('checkboxModelAll',function($compile){return{scope:{objects:'=checkboxModelAll',disableActions:'=disableActions'},link:function($scope,element,attrs){element.bind('click',function($event){$scope.$parent.$broadcast('checkboxModel.selectAll',$event.target.checked);});var numSelected=0;$scope.disableActions=true;$scope.$parent.$on('checkboxModel.selected',function($event,value){value?numSelected++:numSelected--;if(numSelected>=$scope.objects.length){element.prop('checked',true);}else{element.prop('checked',false);}
if(numSelected>0){$scope.disableActions=false;}else{$scope.disableActions=true;}});}};}).directive('truncate',function($compile){return{scope:{text:'@truncate'},replace:true,template:'<span>{{innerText}}<span ng-show="innerText.length > length-1">{{dotdotdot}} <a ng-click="changeMoreLess()">&#40;show {{moreLess}}&#41;</a></span></span>',link:function(scope,element,attrs){var more=false;scope.length=attrs.truncateLength;if(typeof attrs.truncateLength==='undefined'){scope.length=100;}
var truncate=function(val){if(more){return val;}else{return val.substring(0,scope.length);}}
scope.moreLess='more';scope.dotdotdot='...';scope.changeMoreLess=function(){more=!more;if(more){scope.moreLess='less';scope.dotdotdot=''}else{scope.moreLess='more';scope.dotdotdot='...';}
scope.innerText=truncate(scope.text);}
scope.$watch('text',function(newVal){scope.innerText=truncate(newVal);});}};}).directive('placeholder',function($timeout){if(!$.browser.msie||$.browser.version>=10){return{};}
return{link:function(scope,elm,attrs){if(attrs.type==='password'){return;}
$timeout(function(){elm.val(attrs.placeholder).focus(function(){if($(this).val()==$(this).attr('placeholder')){$(this).val('');}}).blur(function(){if($(this).val()==''){$(this).val($(this).attr('placeholder'));}});});}}}).directive('courseSelector',function($rootScope){return{replace:true,template:'<select ui-select2 ng-model="currentCourseId" class="select2-fullwidth"><option ng-repeat="course in courses" value="{{course.id}}">{{course.code}}</option></select>',scope:{},controller:function($scope,CourseSelector){$scope.$watch(function(){return CourseSelector.courses},function(courses){$scope.courses=courses;});$scope.$watch(function(){return CourseSelector.currentCourse},function(currentCourse){$scope.currentCourseId=currentCourse.id;});$scope.$watch('currentCourseId',function(currentCourseId){CourseSelector.updateCurrentCourse(currentCourseId);});},link:function($scope,element,attrs){}};}).directive('addTag',function(Tag){return{replace:true,template:'<span><a ng-show="!showState">&#40;add&#41;</a> <input ng-show="showState" ng-model="newTagName" style="width: 80px;" placeholder="enter tag"></input></span>',scope:{addTag:'=',questionId:'=',},link:function($scope,elem,attrs){$scope.showState=false;$scope.newTagName='';var add=elem.children('a');var input=elem.children('input');var showInput=function(){$scope.$apply(function(){$scope.showState=true;});input.focus();};var addTag=function(){if($scope.newTagName!=''&&$scope.addTag.indexOf($scope.newTagName)<0){Tag.create({question_id:$scope.questionId,tag_name:$scope.newTagName}).then(function(){$scope.addTag.push($scope.newTagName);$scope.newTagName='';$scope.showState=false;});}else{$scope.$apply(function(){$scope.newTagName='';$scope.showState=false;});}};add.bind('click',showInput);input.bind('blur',addTag);input.bind('keypress',function(e){if(e.which==13){addTag();}})}};}).directive('delTag',function(Tag){return{scope:{delTag:'=',questionId:'=',tagName:'='},link:function($scope,elem,attrs){elem.bind('click',function(){Tag.remove({question_id:$scope.questionId,tag_name:$scope.tagName}).then(function(){var index=$scope.delTag.indexOf($scope.newTagName);$scope.delTag.splice(index,1);});});}};}).directive('fineUploader',function(NotificationsService){return{scope:{success:'='},link:function($scope,elem,attrs){var filetype='csv';if(!!attrs.filetype){filetype=attrs.filetype;}
elem.fineUploader({request:{endpoint:'/workover/users/1/upload/'+filetype},validation:{allowedExtensions:['csv'],acceptFiles:'csv'},}).on('submit',function(id,filename){$scope.$apply(function(){NotificationsService.push({title:'Uploading file...',type:'info'});});}).on('complete',function(event,id,fileName,response){if(response&&!!response.success&&!!response.file_output){$scope.$apply(function(){$scope.success(response.file_output);});$('#upload-students-done').tab('show');}}).on('error',function(id,fileName,reason){$scope.$apply(function(){NotificationsService.push({title:'Unable to succesfully upload file',type:'error'});});});}};});
'use strict';var app=angular.module('workover');app.service('CourseSelector',function($http,$q,$timeout,Course,Authuser){var self=this;self.courses=[];self.currentCourse={};var loadCourses=function(){var deferred=$q.defer();Course.list({'user_id':Authuser.id}).then(function(courses){self.courses=courses;deferred.resolve(courses);});return deferred.promise;}
var deferred=$q.defer();loadCourses().then(function(){deferred.resolve();});self.afterLoad=deferred.promise;$timeout(function(){self.afterLoad.then(function(){$timeout(function(){if(!self.currentCourse.id){self.resetCurrentCourse();}});});});this.reloadCourses=function(){var deferred=$q.defer();loadCourses().then(function(){deferred.resolve();});self.afterLoad=deferred.promise;}
this.updateCurrentCourse=function(newCurrentCourseId){var deferred=$q.defer();self.afterLoad.then(function(){var length=self.courses.length;for(var i=0;i<length;i++){if(self.courses[i].id==newCurrentCourseId){self.currentCourse=self.courses[i];deferred.resolve(self.currentCourse);}}
deferred.reject();});return deferred.promise;};this.resetCurrentCourse=function(reloadCourses){var deferred=$q.defer();if(reloadCourses){self.loadCourses().then(function(){if(self.courses.length<1){deferred.reject();}else{self.currentCourse=self.courses[0];deferred.resolve(self.currentCourse);}});}else{if(self.courses.length<1){deferred.reject();}else{self.currentCourse=self.courses[0];deferred.resolve(self.currentCourse);}}
return deferred.promise;}});
var app=angular.module('NotifcationsService',[],function($httpProvider){$httpProvider.responseInterceptors.push(function($q,NotificationsService){return function(promise){var deferred=$q.defer();promise.then(function(response){deferred.resolve(response);},function(response){console.log(response);NotificationsService.push({title:'Error '+response.status+'!',message:response.data.errors,type:'error'});deferred.reject(response);});return deferred.promise;}});});app.service('NotificationsService',function($timeout){this.notifications=[];var notifications=this.notifications;var primaryNotificationExists=false;var defaultTime=10;var nextNotificationId=1;this.push=function(notification){var that=this;notification.remove=function(){if(!angular.isUndefined(this.timeOut)){$timeout.cancel(this.timeOut);}
notifications.splice(notifications.indexOf(this),1);if(this.isPrimary){primaryNotificationExists=false;}}
if(!angular.isFunction(notification.click)){notification.click=notification.remove;}
if(angular.isUndefined(notification.isPrimary)){notification.isPrimary=true;}
if(notification.persistent!=true){notification.timeOut=$timeout(function(){notification.remove();},defaultTime*1000);}
if(notification.isPrimary==true&&primaryNotificationExists){notifications[0].remove();notifications.unshift(notification);primaryNotificationExists=true;}else if(notification.isPrimary==true&&!primaryNotificationExists){notifications.unshift(notification);primaryNotificationExists=true;}else{notifications.unshift(notification);}
return notification;};});
'use strict';var app=angular.module('workover');app.service('Authuser',function($http,$q){var User={id:1};return User;});
'use strict';var app=angular.module('workover');app.service('Student',function($http,$q,NotificationsService){this.list=function(params){var deferred=$q.defer();var queryString='';$http.get('/workover/courses/'+params.course_id+'/students').success(function(data){deferred.resolve(data);}).error(function(data,status,headers,config){deferred.reject({'status':status,'data':data});});return deferred.promise;};this.add=function(params){var deferred=$q.defer();$http.post('/workover/courses/'+params.course_id+'/students/add',params.students).success(function(data){deferred.resolve(data);}).error(function(data,status,headers,config){deferred.reject({'status':status,'data':data});});return deferred.promise;};this.remove=function(params){var deferred=$q.defer();$http.post('/workover/courses/'+params.course_id+'/students/remove',params.user_ids).success(function(data){NotificationsService.push({title:'Success!',message:'The selected students have been succesfully removed.',type:'success'});deferred.resolve(data);}).error(function(data,status,headers,config){deferred.reject({'status':status,'data':data});});return deferred.promise;};this.reset_password=function(params){var deferred=$q.defer();$http.post('/workover/users/reset_password',{user_ids:params.user_ids,password:params.password}).success(function(data){NotificationsService.push({title:'Success!',message:'The selected students have been succesfully added.',type:'success'});deferred.resolve(data);}).error(function(data,status,headers,config){deferred.reject({'status':status,'data':data});});return deferred.promise;};});
'use strict';var app=angular.module('workover');app.service('Course',function($http,$q,NotificationsService){this.list=function(params){var deferred=$q.defer();var queryString='';if(!angular.isUndefined(params)){if(!angular.isUndefined(params.user_id)){queryString+='user_id='+params.user_id;}}
$http.get('/workover/courses?'+queryString).success(function(data){deferred.resolve(data);}).error(function(data,status,headers,config){deferred.reject({'status':status,'data':data});});return deferred.promise;};this.create=function(params){var deferred=$q.defer();$http.post('/workover/courses/create',params.question).success(function(data){NotificationsService.push({title:'Success!',message:'The course has been successfully created.',type:'success'});deferred.resolve(data);}).error(function(data,status,headers,config){deferred.reject({'status':status,'data':data});});return deferred.promise;};this.remove_from_user=function(params){var deferred=$q.defer();$http.post('/workover/users/'+params.user_id+'/courses/delete',params.course_ids).success(function(data){NotificationsService.push({title:'Success!',message:'The course(s) has been removed from your courses.',type:'success'});deferred.resolve(data);}).error(function(data,status,headers,config){deferred.reject({'status':status,'data':data});});return deferred.promise;};this.add_to_user=function(params){var deferred=$q.defer();$http.post('/workover/users/'+params.user_id+'/courses/add',params.course_ids).success(function(data){NotificationsService.push({title:'Success!',message:'The course(s) has been added to your courses.',type:'success'});deferred.resolve(data);}).error(function(data,status,headers,config){deferred.reject({'status':status,'data':data});});return deferred.promise;}});
'use strict';var app=angular.module('workover');app.service('Question',function($http,$q,NotificationsService){this.list=function(params){var deferred=$q.defer();var queryString='';if(params){if(!angular.isUndefined(params.include_tags)){queryString=queryString+'include_tags='+params.include_tags;}}
$http.get('/workover/courses/'+params.course_id+'/questions?'+queryString).success(function(data){deferred.resolve(data);}).error(function(data,status,headers,config){deferred.reject({'status':status,'data':data});});return deferred.promise;};this.remove_from_course=function(params){var deferred=$q.defer();$http.post('courses/(:num)/questions/remove',{user_ids:params.user_ids,password:params.password}).success(function(data){NotificationsService.push({title:'Success!',message:'The selected students have been succesfully added.',type:'success'});deferred.resolve(data);}).error(function(data,status,headers,config){deferred.reject({'status':status,'data':data});});return deferred.promise;};});
'use strict';var app=angular.module('workover');app.service('Tag',function($http,$q,NotificationsService){var Tag={create:function(params){var deferred=$q.defer();$http.post('/workover/questions/'+params.question_id+'/tags/add/'+params.tag_name).success(function(data){NotificationsService.push({title:'Tag Added!',type:'success'});deferred.resolve(data);}).error(function(data,status,headers,config){deferred.reject({'status':status,'data':data});});return deferred.promise;},remove:function(params){var deferred=$q.defer();$http.post('/workover/questions/'+params.question_id+'/tags/remove/'+params.tag_name).success(function(data){NotificationsService.push({title:'Tag Removed!',type:'success'});deferred.resolve(data);}).error(function(data,status,headers,config){deferred.reject({'status':status,'data':data});});return deferred.promise;}};return Tag;});
'use strict';var app=angular.module('workover');app.controller('MainMenuCtrl',function($scope,$location){$scope.updateMenu=function(location){try{var reg=new RegExp("^/(.+)/");var result=reg.exec(location);if(!angular.isUndefined(result)){if(!angular.isUndefined(result.length)&&result.length>1){$scope.currentLocation=result[1];}}}catch(err){if(!angular.isUndefined(console)&&!angular.isUndefined(console.log)){console.log(err);}}}
$scope.$watch(function(){return $location.path();},function(path){$scope.updateMenu(path);});});
'use strict';var app=angular.module('workover');app.controller('NotificationsCtrl',function($scope,NotificationsService){$scope.$watch(function(){return NotificationsService.notifications;},function(notifications){$scope.notifications=notifications;});});
'use strict';var app=angular.module('workover');app.controller('Students/ListCtrl',function($scope,$location,CourseSelector,Student){$scope.students=null;$scope.course=null;var loadStudents=function(courseId){$scope.students=null;Student.list({'course_id':courseId}).then(function(students){$scope.students=students;});}
$scope.$watch(function(){return CourseSelector.currentCourse},function(currentCourse){if(currentCourse.id){$scope.course=currentCourse;loadStudents(currentCourse.id);}});var getSelectedStudents=function(){var ids=[];var length=$scope.students.length;for(var i=0;i<length;i++){if($scope.students[i].selected){ids.push($scope.students[i].id);}}
return ids;}
$scope.remove=function(){var ids=getSelectedStudents();Student.remove({'course_id':CourseSelector.currentCourse.id,'user_ids':ids}).then(function(){loadStudents(CourseSelector.currentCourse.id);});};$scope.resetPassword={error:false,newPassword:'',confirmNewPassword:'',reset:function(){if($scope.resetPassword.newPassword==$scope.resetPassword.confirmNewPassword&&$scope.resetPassword.newPassword.length>2){var ids=getSelectedStudents();Student.reset_password({user_ids:ids,password:$scope.resetPassword.newPassword}).then(function(){loadStudents(CourseSelector.currentCourse.id);$scope.resetPassword.newPassword='';$scope.resetPassword.confirmNewPassword='';$scope.resetPassword.error=false;$('#reset-password').modal('hide');});}else{$scope.resetPassword.error=true;}}};});
'use strict';var app=angular.module('workover');app.controller('Students/AddCtrl',function($scope,$location,CourseSelector,Student,NotificationsService){$scope.students=[];$scope.numStudents=10;$scope.doneUploading=false;$scope.fileUploaded=function(students){$scope.students=students;$scope.numStudents=students.length;NotificationsService.push({title:'Success!',message:'Students Loaded.',type:'success'});};$scope.updateNumStudents=function(){var length=$scope.students.length;if($scope.numStudents<length){var howMany=length-$scope.numStudents;$scope.students.splice(-howMany,howMany);}else if($scope.numStudents>length){var howMany=$scope.numStudents-length;for(var i=0;i<howMany;i++){$scope.students.push({});}}};$scope.addStudents=function(){NotificationsService.push({title:'Adding students...',type:'info'});Student.add({course_id:CourseSelector.currentCourse.id,students:$scope.students}).then(function(response){var length=response.length;var atLeastOneFailed=false;if(response.length==$scope.students.length){for(var i=0;i<length;i++){$scope.students[i].success=response[i].success;if(!response[i].success){atLeastOneFailed=true;}}
if(atLeastOneFailed){NotificationsService.push({title:'Warning!',message:'At least one student has failed to be added to the specified course.'});}else{NotificationsService.push({title:'Success!',message:'All students have been added to the specified course.',type:'success'});$scope.doneUploading=true;}}else{NotificationsService.push({title:'Unexpected respone from server!',message:'Reload page, and contact us if this problem persists.',type:'error'});}})};$scope.goBack=function(){$location.url('/students/list');};$scope.resetStudents=function(){$scope.students=[];$scope.updateNumStudents();$scope.doneUploading=false;};$scope.updateNumStudents();});
'use strict';var app=angular.module('workover');app.controller('Courses/ListCtrl',function($scope,$timeout,Course,CourseSelector,Authuser){$scope.courses=null;var loadCourses=function(){$scope.courses=null;CourseSelector.afterLoad.then(function(){$scope.courses=CourseSelector.courses;});}
$scope.remove=function(){var ids=[];var length=$scope.courses.length;for(var i=0;i<length;i++){if($scope.courses[i].selected){ids.push($scope.courses[i].id);}}
Course.remove_from_user({'user_id':Authuser.id,'course_ids':ids}).then(function(){CourseSelector.reloadCourses();loadCourses();});};loadCourses();});
'use strict';var app=angular.module('workover');app.controller('Courses/AddCtrl',function($scope,$location,Course,CourseSelector,Authuser,NotificationsService){var loadQuestions=function(){Course.list().then(function(courses){$scope.courses=courses;});}
$scope.addExisting=function(){Course.add_to_user({'user_id':Authuser.id,'course_ids':[$scope.existingCourse]}).then(function(){CourseSelector.reloadCourses();$location.path('#/questions/list');});};var proccessing=false;$scope.addNew=function(){if(!proccessing){proccessing=true;$scope.newCourse.users=[Authuser.id];NotificationsService.push({title:'Loading...',message:'',type:'warning'});Course.create({question:$scope.newCourse}).then(function(){proccessing=false;CourseSelector.reloadCourses();$location.path('#/questions/list');},function(){proccessing=false;});}};$scope.courses={};$scope.newCourse={};loadQuestions();});
'use strict';var app=angular.module('workover');app.controller('Questions/ListCtrl',function($scope,CourseSelector,Question){$scope.questions=null;$scope.course={};var loadQuestions=function(courseId){$scope.courses=null;Question.list({'course_id':courseId,'include_tags':true}).then(function(questions){$scope.questions=questions;});}
$scope.$watch(function(){return CourseSelector.currentCourse},function(currentCourse){if(currentCourse.id){$scope.course=currentCourse;loadQuestions(currentCourse.id);}});});