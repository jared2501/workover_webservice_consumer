<?php

class Controller_Student extends Controller {
	protected $auth_user = null;

	public function before() {
		$user_id = Session::get('auth_user.id');
		$this->auth_user = empty($user_id) ? null : Model_User::find($user_id);

		if(empty($this->auth_user)) {
			Response::redirect('/');
		}

		return parent::before();
	}

	public function action_index() {
		\Casset::add_group('js', 'angular_lecturer_app', array(
			'select2::select2.js',

			'angular::angular.js',

			'angular-ui::build/angular-ui.js',

			'file-uploader::header.js',
			'file-uploader::util.js',
			'file-uploader::button.js',
			'file-uploader::handler.base.js',
			'file-uploader::handler.form.js',
			'file-uploader::handler.xhr.js',
			'file-uploader::uploader.basic.js',
			'file-uploader::uploader.js',
			'file-uploader::dnd.js',
			'file-uploader::jquery-plugin.js',


			'angular_student_app::app.js',

			'angular_student_app::directives/directives.js',

			'angular_student_app::services/authuser.js',
			'angular_student_app::services/course.js',
			'angular_student_app::services/question.js',
			'angular_student_app::services/notifications.js',
			
			'angular_student_app::controllers/courses/list.js',
			'angular_student_app::controllers/questions/list.js',
			'angular_student_app::controllers/questions/view.js',

			'angular_student_app::controllers/notifications.js',
		), array(
			'min' => false,
			'deps' => array('jquery')
		));

		return Response::forge(View::forge('student/panel', array('user' => $this->auth_user)));
	}
}