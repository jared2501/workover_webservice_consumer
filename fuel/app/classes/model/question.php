<?php

class Model_Question extends Orm\Model {
	protected static $_table_name = 'questions';
	
	protected static $_properties =  array('id', 'system_id', 'question_id', 'title' => array('default' => ''), 'difficulty' => array('default' => ''), 'created_at', 'updated_at');
	
	protected static $_to_array_exclude = array('created_at', 'updated_at', 'courses');

	protected static $_many_many = array(
		'tags' => array(
			'key_from' => 'id',
			'key_through_from' => 'question_id', // column 1 from the table in between, should match a posts.id
			'table_through' => 'questions_tags', // both models plural without prefix in alphabetical order
			'key_through_to' => 'tag_id', // column 2 from the table in between, should match a users.id
			'model_to' => 'Model_Tag',
			'key_to' => 'id',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
		'courses' => array(
			'key_from' => 'id',
			'key_through_from' => 'question_id', // column 1 from the table in between, should match a posts.id
			'table_through' => 'courses_questions', // both models plural without prefix in alphabetical order
			'key_through_to' => 'course_id', // column 2 from the table in between, should match a users.id
			'model_to' => 'Model_Course',
			'key_to' => 'id',
			'cascade_save' => true,
			'cascade_delete' => false,
		),
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
    );


	public static function get_list_from_source($source, $source_id, $limit, $offset, $include_tags = false, $search_tags = false) {
		if($source === 'course') {
			$related = array('courses');
			$where = array(array('courses.id', '=', $source_id));
		} elseif($source === 'system') {
			$related = array();
			$where = array(array('system_id', '=', $source_id));
		} else {
			return array();
		}

		

		if(!empty($search_tags) and is_array($search_tags)) {
			$related[] = 'tags';
			$tag_array = array_map(function($tag) { return trim($tag); }, explode(',', $tags));
			$where[] = array('tags.name', 'in', $tag_array);
		} elseif(in_array($include_tags, array(true, 'true', 1, 0, '1', '0'), true)) {
			$related[] = 'tags';
		}

		$questions = self::query()
			->where($where)
			// ->limit(7)
			// ->offset(0)
			->related($related)
			->get();

		// Get rid of indexes...
		$questions = array_values($questions);

		$out = array();

		foreach($questions as $q) {
			$out[] = $q->to_array();
		}

		return $out;
	}

	public static function add_to_system($question) {
		if(empty($question['system_id']) || empty($question['question_id']))
			return false;
		
		$q = self::find('first', array(
			'where' => array(
				array('system_id','=',$question['system_id']),
				array('question_id','=',$question['question_id']),
			)
		));
		
		if(!empty($q)) {
			$q->delete();
		}
		
		$q = self::forge($question);
		
		try {
			return $q->save();
		} catch(Exception $e) { return false; }
	}



	// Custom to array since we dont want an array of strings for tags
	public function to_array($custom = false, $recurse = false) {
		$question = parent::to_array($custom, $recurse);
		if(isset($question['tags']) and is_array($question['tags'])) {
			$question['tags'] = array_values($question['tags']);
		}

		$question['urls'] = array(
			'show' => array(
				'without_answers' => "http://localhost/workover_ws/question/show?question_id={$this->question_id}",
				'answers' => "http://localhost/workover_ws/question/show?question_id={$this->question_id}&show_answers=true"
			)
		);

		return $question;
	}
















	protected static $qids_in_course = array();

	public static function count_from_course($course_id) {
		$result = DB::select(DB::expr('COUNT(*) as count'))->from('courses_questions')->where(array(array('course_id', '=', $course_id)))->execute()->current();
		return $result['count'];
	}
	
	/* ---------------------------------------------------------------------------------------------------------------------------- */
	/* ---------------------------------------------------------------------------------------------------------------------------- */
	public static function remove_from_course($questions, $courses) {
		$query = DB::delete('courses_questions');
		foreach($questions as $q) {
			foreach($courses as $cid) {
				$query->or_where_open();
				$query->and_where(array(
					array('system_id', '=', $q['system_id'],),
					array('question_id', '=', $q['question_id']),
					array('course_id', '=', $cid),
				));
				$query->or_where_close();
			}
		}
		$result = $query->execute();
		$query->reset();
		return true;
	}
	
	public static function add_to_course($questions, $courses) {
		if(self::remove_from_course($questions, $courses)) {
			$query = DB::insert('courses_questions');
			$query->columns(array(
				'system_id',
				'question_id',
				'course_id',
				'order'
			));
			foreach($questions as $q) {
				foreach($courses as $cid) {
					$query->values(array(
						$q['system_id'],
						$q['question_id'],
						$cid,
						0
					));
				}
			}
			$query->execute();
		}
		return true;
	}
	/* ---------------------------------------------------------------------------------------------------------------------------- */
	/* ---------------------------------------------------------------------------------------------------------------------------- */
	
	public function get_user_completed($user_id) {
		$result = DB::select('id')
			->from('submissions')
			->where(array(
				array('system_id', '=', $this->system_id),
				array('question_id', '=', $this->question_id),
				array('user_id', '=', $user_id),
				array('completed', '=', true),
			))
			->limit(1)
			->cached(3600, "db.question.user_completed_for_sid-{$this->system_id}_qid-{$this->question_id}_uid-{$user_id}")
			->execute()
			->current();
		
		return count($result) > 0;
	}
	
	
	public function in_course($course_id) {
		$courses_join_table = self::$_many_many['courses']['table_through'];
		
		if(!isset(self::$qids_in_course[$course_id])) {
			self::$qids_in_course[$course_id] = DB::select('*')
				->from($courses_join_table)
				->where(array(
					array('course_id', '=', $course_id)
				))
				// ->order_by('system_id')
				// ->order_by('question_id')
				->execute();
		}
		
		// order by and then binary search?
		foreach(self::$qids_in_course[$course_id] as $q) {
			if($this->system_id == $q['system_id'] and $this->question_id == $q['question_id']) {
				return true;
			}
		}
		
		return false;
	}
	
	
	public function get_order($course_id) {
		$courses_join_table = self::$_many_many['courses']['table_through'];
		
		$result = DB::select('order')->from($courses_join_table)->where(array(
			array('system_id', '=', $this->system_id),
			array('question_id', '=', $this->question_id),
			array('course_id', '=', $course_id),
		))->cached(3600, "db.question.order_for_sid-{$this->system_id}_qid-{$this->question_id}_cid-{$course_id}")->execute()->current();
		
		if(isset($result['order'])) {
			return $result['order'];
		} else {
			return null;
		}
	}
	
	public function update_order($course_id, $order) {
		$courses_join_table = self::$_many_many['courses']['table_through'];
		
		$result =  DB::update($courses_join_table)->where(array(
			array('system_id', '=', $this->system_id),
			array('question_id', '=', $this->question_id),
			array('course_id', '=', $course_id),
		))->set(array(
			'order' => $order
		))->execute();
		
		Cache::delete("db.question.order_for_sid-{$this->system_id}_qid-{$this->question_id}_cid-{$course_id}");
		
		return true;
	}
}