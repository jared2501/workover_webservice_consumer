<?php

class Controller_Page extends Controller_Template {

	public $template = 'template';
	protected $auth_user = null;

	public function before() {
		$user_id = Session::get('auth_user.id');
		$this->auth_user = empty($user_id) ? null : Model_User::find($user_id);

		return parent::before();
	}


	public function get_login() {
		$data['flash']['errorText'] = Session::get_flash('errorText');

		$this->template->content = View::forge('account/login', $data);
	}

	public function post_login() {
		$username_or_email = Input::post('username_or_email');
		$password = Input::post('password');

		$this->auth_user = Model_User::query()
			->where_open()
			->where('username', '=', $username_or_email)
			->or_where('email', '=', $username_or_email)
			->where_close()
			->get_one();

		if(empty($this->auth_user) || !$this->auth_user->is_password($password)) {
			Session::set_flash('errorText', 'Unrecognized username or password.');
			Response::redirect(Router::get('login'));
		} elseif('lecturer' == $this->auth_user->get_role()) {
			Session::set('auth_user.id', $this->auth_user->id);
			Response::redirect(Router::get('lecturer'));
		} else {
			Session::set('auth_user.id', $this->auth_user->id);
			Response::redirect(Router::get('student'));
		}
	}


	public function action_logout() {
		Session::delete('auth_user');
		Response::redirect('/');
	}
}