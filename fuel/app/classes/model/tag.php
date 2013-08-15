<?php

class Model_Tag extends Orm\Model {
	protected static $_table_name = 'tags';
	
	protected static $_properties = array(
		'id',
		'name' => array(
			'validation' => array(
				'required', 'min_length' => array(3), 'max_length' => array(255)
			),
		),
		'created_at',
		'updated_at');
	
	protected static $_many_many = array(
		'questions' => array(
			'key_from' => 'id',
			'key_through_from' => 'tag_id',
			'table_through' => 'questions_tags',
			'key_through_to' => array('question_id'),
			'model_to' => 'Model_Question',
			'key_to' => array('id'),
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

	
	

	// Gets an array of the tag names that are like the $term given.
	public static function get_list($term) {
		$where = array();
		if(!empty($term)) {
			$where[] = array('name', 'LIKE', '%'.$term.'%');
		}
		
		$ts = self::find('all', array(
			'where' => $where
		));
		
		$tags = array();
		
		foreach($ts as $t) {
			$tags[] = $t->name;
		}
		
		return $tags;
	}
	
	// Finds a particular tag by name
	public static function get_by_name($name) {
		return self::find('first', array(
			'where' => array(
				array('name', '=', $name)
			)
		));
	}

	// Adds a tag with no associations
	public static function add($tag_name) {
		$new_tag = Model_Tag::forge(array(
			'name' => $tag_name
		));

		if($new_tag->save()) {
			return $new_tag;
		}
	}
	
	public static function get_or_add($tag_name){
		$tag = self::get_by_name($tag_name);

		if(empty($tag)) {
			$tag = self::add($tag_name);
		}

		return $tag;
	}
	
	
	/**
	 * Removes the tag from a question, and if it doesnt have any other relationships, deletes it.
	 *
	 * @param	int		$system_id
	 * @param	int		$question_id
	 * 
	 * @return	boolean
	 */
	public function remove_from_question($question_id) {		
		$query = DB::delete('questions_tags')
			->where(array(
				array('question_id', '=', $question_id),
				array('tag_id', '=', $this->id),
			));
		
		if($query->execute() < 1)
			return false;
		
		// Check if the tag is assigned to any other questions
		$result = DB::select('*')->from('questions_tags')->where(array(
			array('tag_id', '=', $this->id)
		))->limit(1)->execute();
		
		// if it isnt, then delete the tag
		if(count($result) < 1) {
			return $this->delete();
		}
		
		return true;
	}
	
	/**
	 * Adds the tag to the question
	 *
	 * @param	int		$system_id
	 * @param	int		$question_id
	 * 
	 * @return	boolean
	 */
	public function add_to_question($question_id) {	
		// Finding existing relationship
		$result = DB::select('*')->from('questions_tags')->where(array(
			array('question_id', '=', $question_id),
			array('tag_id', '=', $this->id)
		))->execute()->current();
		
		// If the relation ship doesnt exist..
		if(count($result) < 1) {
			$query = DB::insert('questions_tags');
			$query->columns(array(
				'question_id',
				'tag_id')
			)->values(array(
				$question_id,
				$this->id
			));
			
			$query->execute();
			
			return true;
		}
		
		return true;
	}


	// Custom to array since we dont want the ids, names, etc. Just an array of tags
	public function to_array($custom = false, $recurse = false) {
		return $this->name;
	}
}