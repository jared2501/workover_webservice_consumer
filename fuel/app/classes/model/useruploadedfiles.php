<?php

require_once(APPPATH.'vendor'.DS.'parsecsv-0.4.3-beta'.DS.'parsecsv.lib.php');

class Model_UserUploadedFiles extends Orm\Model {
    protected static $_table_name = 'users_uploadedfiles';

    protected static $_primary_key = array('id');

    protected static $_properties = array('id', 'user_id', 'filename', 'filetype', 'created_at', 'updated_at');

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

    public function parse($filetype) {
        if($filetype == 'csv') {
            $csv = new parseCSV();

            $csv->fields = array('username', 'password', 'email', 'first_name', 'last_name');
            $csv->auto(APPPATH.'uploads/'.$this->filetype.DS.$this->filename);

            return $csv->data;
        }
    }
}