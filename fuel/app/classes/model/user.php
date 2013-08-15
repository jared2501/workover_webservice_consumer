<?php

class Model_User extends Orm\Model {
	protected static $_table_name = 'users';
	
	protected static $_properties = array(
		'id',
		'username' => array(
			'data_type' => 'varchar',
			'validation' => array('required', 'min_length' => array(3), 'max_length' => array(50)),
        ),
		'email' => array(
			'data_type' => 'varchar',
			'validation' => array('required', 'valid_email'),
        ),
		'password' => array(
			'data_type' => 'varchar',
			'validation' => array('required', 'min_length' => array(3), 'hash_password'),
		),
		'role' => array(
			'default' => self::student,
		),
		'activated' => array(
			'default' => false,
		),
		'name_first' => array(
			'validation' => array('required', 'min_length' => array(3), 'max_length' => array(255)),
		),
		'name_last' => array(
			'validation' => array('required', 'min_length' => array(3), 'max_length' => array(255)),
		),
		'created_at',
		'updated_at'
	);
	
	protected static $_to_array_exclude = array('created_at', 'updated_at', 'role', 'password', 'courses', 'uploaded_files');

	public static function _validation_hash_password($password, $options = null) {
		return self::hash_password($password);
	}
	
	protected static $_has_many = array(
		'uploaded_files' => array(
			'key_from' => 'id',
			'model_to' => 'Model_UserUploadedFiles',
			'key_to' => 'user_id',
			'cascade_save' => true,
			'cascade_delete' => false,
		)
	);

	protected static $_many_many = array(
		'courses' => array(
			'key_from' => 'id',
			'key_through_from' => 'user_id', // column 1 from the table in between, should match a posts.id
			'table_through' => 'courses_users', // both models plural without prefix in alphabetical order
			'key_through_to' => 'course_id', // column 2 from the table in between, should match a users.id
			'model_to' => 'Model_Course',
			'key_to' => 'id',
			'cascade_save' => true,
			'cascade_delete' => false,
		)
	);
	
	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_UpdatedAt' => array(
			'events' => array('before_save'),
			'mysql_timestamp' => false,
		),
		'Orm\Observer_Validation' => array(
			'events' => array('before_save')
		)
    );
	
	
	
	const student = 0;
	const lecturer = 1;
	const admin = 2;
	const password_salt = "dsgdsfkmn32lk4n32l4kn323l213l21k54213n";
	
	public static $hasher = null;

	/**
	 * Hashes the password using the hasher instance
	 *
	 * @return	string
	 */
	public static function hash_password($password) {
		return base64_encode(self::hasher()->pbkdf2($password, self::password_salt, 10000, 32));
	}
	private static function hasher() {
		is_null(self::$hasher) and self::$hasher = new \PHPSecLib\Crypt_Hash();
		return self::$hasher;
	}
	
	

	public static function get_student_list($course_id, $limit, $offset) {
		$where = array(array('role', '<', self::lecturer));
		$related = array();
		
		if(!empty($course_id)) {
			$related[] = 'courses';
			$where[] = array('courses.id', '=', $course_id);
		}
		
		$users = self::find('all', array(
			'related' => $related,
			'where' => $where
		)) ?: array();
		
		$out = array();
		foreach($users as $u) {
			$out[] = $u->to_array();
		}
		
		return $out;
	}

	// Array of results
	// Message, success, student (set if success true)
	public static function batch_create_students($student_data, $course) {
		$out = array();
		
		foreach($student_data as $key=>$s) {
			$result = array();
			
			if(!isset($s['username']) || !isset($s['email']) || !isset($s['password']) || !isset($s['first_name']) || !isset($s['last_name'])) {
				$result['message'] = 'Missing data, please specify students in the format given.';
				$result['success'] = false;
			} else {
				$student = self::find('first', array('where' => array(
					array('username', '=', $s['username']),
					'or' => array(
						array('email', '=', $s['email']),
					),
				)));
				
				// Create the student if its not found
				if(empty($student)) {
					$student = self::forge(array(
						'username' => $s['username'],
						'email' => $s['email'],
						'password' => $s['password'],
						'role' => self::student,
						'activated' => false,
						'name_first' => $s['first_name'],
						'name_last' => $s['last_name']
					));
				}

				$student->courses[] = $course;

				try {
					$student->save();
					$result['message'] = 'Student added.';
					$result['success'] = true;
					$result['student'] = $student->to_array();
				} catch (Orm\ValidationFailed $e) {
					$result['message'] = $e->getMessage();
					$result['success'] = false;
					$result['student'] = $student->to_array();
				}
			}
			
			// Build output
			$out[$key] = $result;
		}
		
		return $out;
	}


	public static function batch_reset_passwords($user_ids, $password) {
		foreach($user_ids as $uid) {
			$user = self::find($uid);

			if(!empty($user)) {
				$user->password = $password;
				$user->activated = false;
				$user->save();
			}
		}
	}


	public function is_password($password) {
		return $this->password == self::hash_password($password);
	}

	// Throws RecordNotFound
	public function add_associations($assoc_ids, $assoc_name) {
		$assoc_class = self::$_many_many[$assoc_name]['model_to'];

		foreach($assoc_ids as $id) {
			$assoc = $assoc_class::find($id);

			if(empty($assoc)) {
				throw new Orm\RecordNotFound('An association id specified was not found or has already been deleted.');
			}

			$this->{$assoc_name}[] = $assoc;
		}

		return $this;
	}

	// Throws RecordNotFound
	public function remove_associations($assoc_ids, $assoc_name) {
		foreach($assoc_ids as $id) {
			foreach($this->{$assoc_name} as $index => $assoc) {
				if($assoc->id == $id) {
					unset($this->{$assoc_name}[$index]);
				}
			}
		}

		return $this;
	}


	public function save_file($filename, $filetype) {
		$new_file = Model_UserUploadedFiles::forge(array(
			'filename' => $filename,
			'filetype' => $filetype
		));
		
		$this->uploaded_files[] = $new_file;

		if($this->save()) {
			return $new_file;
		} else {
			return false;
		}
	}
	
	
	




























	/* HORENDOUSLY INEFFIECENT!!!!!!!!!!!!!!!!!!!!!!!!!!!1 */
	public static function add_students($student_data, $course_id = null) {		
		if(!empty($course_id)) {
			$course = Model_Course::find($course_id);
			if(empty($course)) {
				return false;
			}
		}
		
		$out = array();
		
		foreach($student_data as $key=>$s) {
			$result = false;
			
			$student = self::find('first', array('where' => array(
				array('username', '=', $s['username']),
				'or' => array(
					array('email', '=', $s['email']),
				),
			)));
			
			if(!isset($s['username']) || !isset($s['email']) || !isset($s['password']) || !isset($s['first_name']) || !isset($s['last_name'])) {
				$result['message'] = 'bad data';
				$result['created'] = false;
				continue;
			}
			
			// Create the student if its not found
			if(empty($student)) {
				$student = self::forge(array(
					'username' => $s['username'],
					'email' => $s['email'],
					'password' => $s['password'],
					'role' => self::student,
					'activated' => false,
					'name_first' => $s['first_name'],
					'name_last' => $s['last_name']
				));
				
				try {
					$student->save();
					$result['message'] = 'created';
					$result['added'] = true;
				} catch (Orm\ValidationFailed $e) {
					$result['message'] = 'bad data';
					$result['added'] = false;
					$errors = $e->getMessage();
				}
			} else {
				$result['message'] = 'added';
				$result['added'] = true;
			}
			
			// Add the course to the student and save
			if(!empty($course_id)) {
				$student->courses[] = $course;
			}
			
			try {
				$student->save();
				$result['added'] = true;
			} catch (Orm\ValidationFailed $e) {
				$result['message'] = 'bad data';
				$result['added'] = false;
				$errors = $e->getMessage();
			}
			
			// Build output
			$out[$key] = $result;
			if(!empty($errors)) $out[$key]['errors'] = $errors;
		}
		
		return $out;
	}
	
	
	
	
	
	public function activate() {
		$this->activated = true;
		$this->save();
	}
	
	public function update_password($password) {
		$this->password = $password;
		$this->save();
	}
	
	public function add_course($course) {
		$this->courses[] = $course;
		$this->save();
	}
	
	public function get_role() {
		switch ($this->role) {
			case self::student:
				return 'student';
				break;
			case self::lecturer:
				return 'lecturer';
				break;
			case self::admin:
				return 'admin';
				break;
		}
	}
	
	public function edit($data) {		
		foreach($data as $key=>$value) {
			$this->{$key} = $value;
		}
		
		return $this->save();
	}
	
	
	
	public function order_upto($course_id) {
		$user_order_upto = 0;
		
		$query = DB::query(
			'SELECT * FROM (SELECT questions.*, (SELECT COUNT(*) FROM submissions WHERE submissions.question_id = questions.question_id and submissions.system_id = questions.system_id and submissions.completed = true and submissions.user_id = :user_id) > 0 AS completed FROM questions) q
			JOIN courses_questions ON q.question_id = courses_questions.question_id AND q.system_id = courses_questions.system_id
			WHERE q.completed < 1 AND courses_questions.course_id = :course_id ORDER BY courses_questions.order LIMIT 1'
		);
		$result = $query->bind(':user_id', $this->id)->bind(':course_id', $course_id)->execute();
		
		if(count($result) > 0) {
			$user_order_upto = $result[0]['order'];
		}
		
		return $user_order_upto;
	}
}