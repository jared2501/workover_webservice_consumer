<?php

class Controller_Courses extends Controller_Rest {
	public $format = 'json';
	protected $limit = 20;

	// Query string: user_id=int
	public function get_list() {
		$limit = Input::get('limit') ?: 20;
		$offset = Input::get('offset') ?: 0;
		$user_id = Input::get('user_id') ?: null;

		$courses = Model_Course::get_list($limit, $offset, $user_id);

		return $this->response($courses);
	}


	// All actions below are done as transactions and accept arrays of ids/objects
	// input params: code, description, users => [user_ids]
	public function post_create() {
		try {
			$course = Model_Course::make(Input::json());
			$course->save();
		} catch (Exception $e) {
			return $this->response(array('errors' => $e->getMessage()), 400);
		}
	}

	// Find all first, and then delete them.
	public function post_delete() {
		try {
			Model_Course::remove(Input::json());
		} catch (Exception $e) {
			return $this->response(array('errors' => $e->getMessage()), 400);
		}
	}



	// All actions below are done as transactions and reference a single object
	// Query string: tags=bool
	public function get_get($course_id) {
		$course = Model_Course::find($course_id);

		if(empty($course)) {
			return $this->response(array('errors' => 'Could not find the course specified.'), 404);
		}

		return $this->response($course->to_array());
	}


	// Only properties posted are updated (use this to update tags)
	public function get_update($course_id) {
		$course = Model_Course::find($course_id);

		if(empty($course)) {
			return $this->response(array('errors' => 'Could not find the course specified.'), 404);
		}

		try {
			$course->ammend(Input::get());
			$course->save();
		} catch (Exception $e) {
			return $this->response(array('errors' => $e->getMessage()), 400);
		}
	}


	public function post_update_users($remove_or_add, $course_id) {
		$course = Model_Course::find($course_id);
		$user_ids = Input::json();
		
		if(empty($course)) {
			return $this->response(array('errors' => 'Could not find the course specified.'), 404);
		} elseif(empty($user_ids) || !is_array($user_ids)) {
			return $this->response(array('errors' => 'Please specify user_ids as an non-empty array'), 400);
		}

		try {
			if($remove_or_add == 'remove') {
				$course->remove_associations($user_ids, 'users');
				$course->save();
			} elseif($remove_or_add == 'add') {
				$course->add_associations($user_ids, 'users');
				$course->save();
			} else {
				throw new Exception("Remove or add should be 'remove' or 'add'");
			}
		} catch (Exception $e) {
			return $this->response(array('errors' => $e->getMessage()), 400);
		}
	}


	public function post_update_questions($remove_or_add, $course_id) {
		$course = Model_Course::find($course_id);
		$question_ids = Input::json();

		if(empty($course)) {
			return $this->response(array('errors' => 'Could not find the course specified.'), 404);
		} elseif(empty($question_ids) || !is_array($question_ids)) {
			return $this->response(array('errors' => 'Please specify question_ids as an non-empty array'), 400);
		}

		try {
			if($remove_or_add == 'remove') {
				$course->remove_associations($question_ids, 'questions');
				$course->save();
			} elseif($remove_or_add == 'add') {
				$course->add_associations($question_ids, 'questions');
				$course->save();
			} else {
				throw new Exception("Remove or add should be 'remove' or 'add'");
			}
		} catch (Exception $e) {
			return $this->response(array('errors' => $e->getMessage()), 400);
		}
	}


	public function after($response) {
		if(!empty($response)) {
			$response->set_header('Cache-Control', 'no-cache');
		}
		
		return parent::after($response);
	}
}