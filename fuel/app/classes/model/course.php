<?php

class Model_Course extends Orm\Model_Soft {
	protected static $_table_name = 'courses';

	protected static $_soft_delete = array(
		'deleted_field' => 'deleted_at',
		'mysql_timestamp' => false,
	);

	protected static $_properties = array(
		'id',
		'code' => array(
			'data_type' => 'varchar',
			'validation' => array(
				'required', 'min_length' => array(3), 'max_length' => array(255)
			),
        ),
		'description' => array(
			'data_type' => 'text',
			'validation' => array('required'),
        ),
		'created_at',
		'updated_at',
		'deleted_at'
	);

	protected static $_to_array_exclude = array('created_at', 'updated_at', 'deleted_at', 'questions', 'users');
	
	protected static $_many_many = array(
		'questions' => array(
			'key_from' => 'id',
			'key_through_from' => 'course_id', // column 1 from the table in between, should match a posts.id
			'table_through' => 'courses_questions', // both models plural without prefix in alphabetical order
			'key_through_to' => 'question_id', // column 2 from the table in between, should match a users.id
			'model_to' => 'Model_Question',
			'key_to' => 'id',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		'users' => array(
			'key_from' => 'id',
			'key_through_from' => 'course_id', // column 1 from the table in between, should match a posts.id
			'table_through' => 'courses_users', // both models plural without prefix in alphabetical order
			'key_through_to' => 'user_id', // column 2 from the table in between, should match a users.id
			'model_to' => 'Model_User',
			'key_to' => 'id',
			'cascade_save' => true,
			'cascade_delete' => false,
		)
	);
	
	protected static $_created_at = 'created_at';
	protected static $_updated_at = 'modified_at';
	
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


	public static function get_list($limit, $offset, $user_id = null) {
		$courses = self::query();

		if(!empty($user_id)) {
			$courses
				->related(array('users'))
				->where(array(array('users.id', '=', $user_id)));
		}
		
		$courses = $courses->get();
		
		$out = array();
		foreach($courses as $c) {
			$out[] = $c->to_array();
		}
		
		return $out;
	}

	// Throws RecordNotFound through add_users
	public static function make($data) {
		$user_ids = isset($data['users']) ? $data['users'] : array();
		unset($data['users']);

		$new = self::forge($data);

		$new->add_associations($user_ids, 'users');

		return $new;
	}


	public function ammend($data) {
		foreach($data as $index => $prop) {
			if(in_array($prop, self::$_to_array_exclude)) {
				unset($data[$index]);
			}
		}

		$this->set($data);

		return $this;
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
}