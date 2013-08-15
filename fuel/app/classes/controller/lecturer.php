<?php

class Controller_Lecturer extends Controller {
	protected $auth_user = null;

	public function before() {
		$user_id = Session::get('auth_user.id');
		$this->auth_user = empty($user_id) ? null : Model_User::find($user_id);

		if(empty($this->auth_user)) {
			Response::redirect('/');
		} elseif('student' === $this->auth_user->get_role()) {
			Response::redirect(Router::get('student'));
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


			'angular_lecturer_app::app.js',

			'angular_lecturer_app::directives/directives.js',

			'angular_lecturer_app::services/courseSelector.js',
			'angular_lecturer_app::services/notifications.js',
			'angular_lecturer_app::services/authuser.js',
			'angular_lecturer_app::services/student.js',
			'angular_lecturer_app::services/course.js',
			'angular_lecturer_app::services/question.js',
			'angular_lecturer_app::services/tag.js',

			'angular_lecturer_app::controllers/mainMenu.js',
			'angular_lecturer_app::controllers/notifications.js',
			
			'angular_lecturer_app::controllers/students/list.js',
			'angular_lecturer_app::controllers/students/add.js',
			'angular_lecturer_app::controllers/courses/list.js',
			'angular_lecturer_app::controllers/courses/add.js',
			'angular_lecturer_app::controllers/questions/list.js',
			'angular_lecturer_app::controllers/questions/view.js',
			'angular_lecturer_app::controllers/questions/add.js',
		), array(
			'min' => false,
			'deps' => array('jquery')
		));

		\Casset::add_group('css', 'angular_lecturer_app', array(
			'file-uploader::fineuploader.css',
			'select2::select2.css',
		));

		return Response::forge(View::forge('lecturer/panel', array('user' => $this->auth_user)));
	}
}