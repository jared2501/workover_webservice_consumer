<?php

class Controller_Students extends Controller_Rest {
	public $format = 'json';
	protected $limit = 20;


	public function get_list($course_id) {
		$limit = Input::get('limit') ?: 20;
		$offset = Input::get('offset') ?: 0;

		$students = Model_User::get_student_list($course_id, $limit, $offset);

		return $this->response($students);
	}


	// All actions below are done as transactions and accept arrays of ids/objects
	// input params: code, description, users => [user_ids]
	public function post_add($course_id) {
		$course = Model_Course::find($course_id);

		if(empty($course)) {
			return $this->response(array('errors' => 'Could not the course specified.'), 404);
		}

		$output = Model_User::batch_create_students(Input::json(), $course);
		return $this->response($output);
	}

	// Find all first, and then delete them.
	// Handled by courses controller, updated_users
	// public function post_remove($course_id) {
		
	// }



	// All actions below are done as transactions and reference a single object
	// Query string: tags=bool
	// public function get_get($course_id) {
		
	// }


	// Only properties posted are updated (use this to update tags)
	// public function get_update($course_id) {
		
	// }


	public function after($response) {
		if(!empty($response)) {
			$response->set_header('Cache-Control', 'no-cache');
		}
		
		return parent::after($response);
	}
}