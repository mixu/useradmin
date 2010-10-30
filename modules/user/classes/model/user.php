<?php

class Model_User extends Model_Auth_User {

   // be sure to include these if you add more relationships! - if not, you will get errors with auth.
   // they are defined in Model_Auth_User (in /modules/auth/classes/model/auth/user.php)
   protected $_table_name = 'users';
   protected $_has_many = array(
      // auth
      'roles' => array('through' => 'roles_users'), 
      'user_tokens' => array(),
      );
   protected $_has_one= array(
      );


   /* ORM supports:
    * _created_column['column'] - the column which is updated on create
    * _created_column['format'] - the column format which is passed to date
    * _updated_column['column'] - the column which is updated on modified
    * _updated_column['format'] - the column format which is passed to date
    *
    * Haven't seen this documented.
    */
// I have left these uncommented, since the default table SQL does not contain these columns. If you want these fields, add two DATETIME fields to the DB.
//   protected $_created_column = array('column' => 'created', 'format' => 'Y-m-d H:i:s');
//   protected $_updated_column = array('column' => 'modified', 'format' => 'Y-m-d H:i:s');


   protected $callback_data_id;

   /*
    * Note that most of these validation functions are just copied from Model_Auth_User (in /modules/auth/classes/model/auth/user.php)
    *
    * I have extended login() a bit, but to use the code you will need a couple of non-standard fields.
    *
    */
   /**
    * Validates a user when the record is first created.
    * 
    * @param $array An array of fields for the user record.
    * @return Validate Validation object, call check() on the return value to validate.           
    */
   public function validate_create($array) {
      // Initialise the validation library and setup some rules
      // See modules/auth/classes/model/auth/user.php for the definitions of $this->_rules.
      $validation = Validate::factory($array)
                  ->rules('password', $this->_rules['password'])
                  ->rules('username', $this->_rules['username'])
                  ->rules('email', $this->_rules['email'])
                  ->rules('password_confirm', $this->_rules['password_confirm'])
                  ->filter('username', 'trim')
                  ->filter('email', 'trim')
                  ->filter('password', 'trim')
                  ->filter('password_confirm', 'trim');

      // Executes username callbacks defined in parent

      // These callbacks are defined in modules/auth/classes/model/auth/user.php
      // As of 3.0.1.2, the only callback is "username_available"
      foreach($this->_callbacks['username'] as $callback){
         $validation->callback('username', array($this, $callback));
      }

      // Executes email callbacks defined in parent   
      // These callbacks are defined in modules/auth/classes/model/auth/user.php
      // As of 3.0.1.2, the only callback is "email_available"
      foreach($this->_callbacks['email'] as $callback){
         $validation->callback('email', array($this, $callback));
      }

      return $validation;
   }

   // See also: login() in modules/auth/classes/model/auth/user.php, which performs logins.

   /**
    * Validates a user when the record it is modified.
    * 
    * @param $array An array of fields for the user record.
    * @return Validate Validation object, call check() on the return value to validate.           
    */
   public function validate_edit($id, $array = array()) {

      $validation = Validate::factory($array)
                  ->rules('username', $this->_rules['username'])
                  ->rules('email', $this->_rules['email'])                  
                  ->filter('username', 'trim')
                  ->filter('email', 'trim')
                  ->filter('password', 'trim')
                  ->filter('password_confirm', 'trim');

      // if the password is set, then validate it - it is unset earlier in the controller if it is empty
      if(isset($array['password'])) {
         $validation->rules('password', $this->_rules['password'])
                    ->rules('password_confirm', $this->_rules['password_confirm']);
      } 

      // pass parameter via object
      $this->callback_data_id = $id;
      $validation->callback('username', array($this, 'username_is_unique'));
      $validation->callback('email', array($this, 'email_is_unique'));
      return $validation;
   }


   /**
    * Does the reverse of unique_key_exists() by triggering error if username exists
    * Validation Rule
    *
    * @param    Validate  $array   validate object
    * @param    string    $field   field name
    * @return   array
    */
   public function username_is_unique(Validate $array, $field) {
      $exists = (bool) DB::select(array('COUNT("*")', 'total_count'))
                  ->from($this->_table_name)
                  ->where('username',   '=',   $array[$field])
                  ->where('id',     '!=',   $this->callback_data_id)
                  ->execute($this->_db)
                  ->get('total_count');

      if ($exists) {
         $array->error($field, 'username_not_unique', array($array[$field]));
      }
   }

   /**
    * Does the reverse of unique_key_exists() by triggering error if email exists
    * Validation Rule
    *
    * @param    Validate  $array   validate object
    * @param    string    $field   field name
    * @return   array
    */
   public function email_is_unique(Validate $array, $field) {
      $exists = (bool) DB::select(array('COUNT("*")', 'total_count'))
                  ->from($this->_table_name)
                  ->where('email',   '=',   $array[$field])
                  ->where('id',     '!=',   $this->callback_data_id)
                  ->execute($this->_db)
                  ->get('total_count');

      if ($exists) {
         $array->error($field, 'email_not_unique', array($array[$field]));
      }
   }



   /**
    * Validates login information from an array, and optionally redirects
    * after a successful login.
    *
    * @param  array    values to check
    * @param  string   URI or URL to redirect to
    * @return boolean
    */
   public function login(array & $array, $redirect = FALSE) {

      $array = Validate::factory($array)
         ->filter(TRUE, 'trim')
         ->rules('username', $this->_rules['username'])
         ->rules('password', $this->_rules['password']);

      // Login starts out invalid
      $status = FALSE;

      if ($array->check()) {
         // Attempt to load the user
         $this->where('username', '=', $array['username'])->find();

/* Note: failed_login_count and last_failed_login do not exist in the default schema, so it is disabled here. failed_login_count is a int field, and last_failed_login is a datetime field.
 *
         // if there are too many recent failed logins, fail now
         if (($this->failed_login_count > 5) && (strtotime($this->last_failed_login) > strtotime("-5 minutes") )) {
            // do nothing, and fail (too many failed logins within 5 minutes).
            return FALSE;
         }
*/
         // if you want to allow 5 logins again after 5 minutes, then set the failed login count to zero here, if it is too high.

         if ($this->loaded() AND Auth::instance()->login($this, $array['password'])) {
            // Login is successful
            $status = TRUE;

            // set the number of failed logins to 0
//            $this->failed_login_count = 0;
            if(is_numeric($this->id) && ($this->id != 0)) {
               // only save if the user already exists
               $this->save();
            }
            
            if (is_string($redirect)) {
               // Redirect after a successful login
               Request::instance()->redirect($redirect);
            }
         } else {
/*
            // login failed: update failed login count

            $this->failed_login_count = $this->failed_login_count+1;
            $this->last_failed_login = date('Y-m-d H:i:s');

            if(is_numeric($this->id) && ($this->id != 0) ) {
               // only save if the user already exists
               $this->save();
            }
*/
            // set error status
            $array->error('username', 'invalid');
         }
      }
      return $status;
   }
}
