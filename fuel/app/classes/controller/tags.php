<?php

class Controller_Tags extends Controller_Rest {
	public $format = 'json';
	
	// Get list designed to jQuery UI autocomplete list interface, returning a JSON array of tags to display given a partial string
	public function get_list() {
		$tags = Model_Tag::get_list(Input::get('term'));
		
		$this->response($tags);
	}
	
	// Adds a tag to a question. If the tag doesnt already exist, then it is created.
	public function post_add($question_id, $tag_name) {	
		if(empty($question_id) || !Model_Question::find($question_id)) {
			return $this->response(array('error' => 'Specified question_id not found'), 404);
		}

		try {
			$tag = Model_Tag::get_or_add($tag_name);
			$tag->add_to_question($question_id);
		} catch(Exception $e) {
			return $this->response(array('error' => $e->getMessage()), 400);
		}
	}
	
	// Removes a tag from a question.
	public function post_remove($question_id, $tag_name) {
		if(empty($question_id) || !Model_Question::find($question_id)) {
			return $this->response(array('error' => 'Specified question_id not found'), 404);
		}

		$tag = Model_Tag::get_by_name($tag_name);
		
		if(empty($tag)) {
			return $this->response(array('error' => 'The tag specified does not exist'), 404);
		}
		
		$tag->remove_from_question($question_id);
	}
}