<?php
return array(
	'_root_'  => 'page/login',

	'_404_'   => function() {
		return new Response('resource not found', 404);
	},

	'login' => array('page/login', 'name' => 'login'),
	'logout' => array('page/logout', 'name' => 'logout'),
	'lecturer/index' => array('lecturer/index', 'name' => 'lecturer'),
	'student/index' => array('student/index', 'name' => 'student'),

	// Courses
	'courses/(:num)' => 'courses/get/$1',
	'courses' => 'courses/list',
	'courses/(:num)/users/delete' => 'courses/update_users/remove/$1',

	'courses/(:num)/questions' => 'questions/list/course/$1',
	'courses/(:num)/questions/remove' => 'courses/update_questions/remove/$1',
	'courses/(:num)/questions/add' => 'courses/update_questions/add/$1',

	'courses/(:num)/students' => 'students/list/$1',
	'courses/(:num)/students/add' => 'students/add/$1',
	'courses/(:num)/students/remove' => 'courses/update_users/remove/$1',

	// Systems
	'systems/(:num)/questions' => 'questions/list/system/$1',

	// Questions
	'questions/(:num)' => 'questions/get/$1',
	'questions/(:num)/tags/add/(:segment)' => 'tags/add/$1/$2',
	'questions/(:num)/tags/remove/(:segment)' => 'tags/remove/$1/$2',

	// Users
	'users/(:num)/courses/delete' => 'users/update_courses/remove/$1',
	'users/(:num)/courses/add' => 'users/update_courses/add/$1',
	'users/(:num)/upload/csv' => 'users/upload/csv/$1',
	'users/reset_password' => 'users/reset_password',

	// Systems
	'systems/(:num)/questions/add' => 'questions/add_to_system/$1'
);