<?php

require_once APPPATH.'vendor/qqFileUploader.php';

class Controller_Users extends Controller_Rest {
	public $format = 'json';
	protected $limit = 20;

	// Query string: user_id=int
	public function get_list() {
		
	}


	// All actions below are done as transactions and accept arrays of ids/objects
	public function post_create() {
		
	}

	// Find all first, and then delete them.
	public function post_delete() {
	
	}



	// All actions below are done as transactions and reference a single object
	public function get_get($course_id) {
	
	}


	// Only properties posted are updated (use this to update tags)
	public function post_update($course_id) {
		
	}


	public function post_reset_password() {
		$user_ids = Input::json('user_ids');
		$password = Input::json('password');

		try {
			Model_User::batch_reset_passwords($user_ids, $password);
		} catch (Exception $e) {
			return $this->response(array('errors' => $e->getMessage()), 400);
		}
	}


	public function post_update_courses($remove_or_add, $user_id) {
		$user = Model_User::find($user_id);
		$user_ids = Input::json();
		
		if(empty($user)) {
			return $this->response(array('errors' => 'Could not find the user specified.'), 404);
		} elseif(empty($user_ids) || !is_array($user_ids)) {
			return $this->response(array('errors' => 'Please specify user_ids as an non-empty array'), 400);
		}

		try {
			if($remove_or_add == 'remove') {
				$user->remove_associations($user_ids, 'courses');
				$user->save();
			} elseif($remove_or_add == 'add') {
				$user->add_associations($user_ids, 'courses');
				$user->save();
			} else {
				throw new Exception("Remove or add should be 'remove' or 'add'");
			}
		} catch (Exception $e) {
			return $this->response(array('errors' => $e->getMessage()), 400);
		}
	}
	

	public function post_upload($filetype, $user_id) {
		$user = Model_User::find($user_id);
		
		if(empty($user)) {
			return $this->response(array('errors' => 'Could not find the user specified.'), 404);
		}

		$uploader = new qqFileUploader();
		$uploader->allowedExtensions = array($filetype);
		$uploader->sizeLimit = 2 * 1024 * 1024;
		$uploader->inputName = 'qqfile';
		$uploader->chunksFolder = APPPATH.'chunks';
		$result = $uploader->handleUpload(APPPATH.'uploads/'.$filetype, md5(mt_rand()).'_'.$uploader->getName());

		if($uploader->getUploadName()) {
			$file = $user->save_file($uploader->getUploadName(), $filetype);

			if(!empty($file)){
				$result['file_output'] = $file->parse($filetype);
			}
		} 
		
		if($result) {
			return $this->response($result, 200);
		} else {
			return $this->response(array('errors' => 'Internal server error when attempting to save files.'), 500);
		}

		return $this->response($result);
	}
}