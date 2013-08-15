<?php

class Controller_Questions extends Controller_Rest {
	public $format = 'json';
	protected $limit = 20;
	


	// Parameters are set by the router.
	// Post parameters are used everywhere else
	// Get parameters are specified by query string

	// $source = 'course' or 'system'


	// Query string: limit=int, offset=int, include_tags=bool, search_tags=comma seperated list
	public function get_list($source, $source_id) {
		if(empty($source) || empty($source_id)) {
			return $this->response(array('error' => 'Unspecified source or source id'), 404);
		}

		$limit = Input::get('limit') ?: 20;
		$offset = Input::get('offset') ?: 0;
		$include_tags = Input::get('include_tags') ?: false;
		$search_tags = Input::get('search_tags') ?: false;

		if($source == 'course' || $source == 'system') {
			$questions = Model_Question::get_list_from_source($source, $source_id, $limit, $offset, $include_tags, $search_tags);
		} else {
			return $this->response(array('error' => 'Urecognized source'), 404);
		}
		
		return $this->response($questions);
	}


	// All actions below are done as transactions and accept arrays of ids/objects
	public function post_add_to_system($system_id) {
		$question['question_id'] = Input::post('question_id');
		$question['title'] = Input::post('title');
		$question['difficulty'] = Input::post('difficulty');
		$question['system_id'] = $system_id;
		
		$result = Model_Question::add_to_system($question);

		if($result) {
			return $this->response();
		} else {
			return $this->response(false, 400);
		}
	}

	public function post_delete($source, $source_id) {

	}


	// All actions below are done as transactions and reference a single object
	public function get_get($id) {
		$question = Model_Question::find($id);
		return $this->response($question->to_array());
	}
}