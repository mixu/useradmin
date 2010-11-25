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

	/**
   * Validates a user when the record is modified.
    *
   * Different rules are needed (e.g. the email and username do not need to be new, just unique to this user).
    *
   * Unobtrusive: we setup the _validate value (see ORM) with custom values, then just run check().
   * Should not require further changes to surrounding code.
    * 
    * @param $array An array of fields for the user record.
    * @return Validate Validation object, call check() on the return value to validate.           
    */
   public function check_edit() {
      $values = $this->as_array();
      // since removing validation rules is tricky (this is needed to ignore the password),
      // we will just create our own alternate _validate object and store it in the model.
      $this->_validate = Validate::factory($values)
                  ->label('username', $this->_labels['username'])
                  ->label('email', $this->_labels['email'])
                  ->rules('username', $this->_rules['username'])
                  ->rules('email', $this->_rules['email'])
                  ->filter('username', 'trim')
                  ->filter('email', 'trim')
                  ->filter('password', 'trim')
                  ->filter('password_confirm', 'trim');
      // if the password is set, then validate it 
      // Note: the password field is always set if the model was loaded from DB (since there is a DB value for it)
      // So we will check for the password_confirm field instead.
      if(isset($values['password_confirm']) && (trim($values['password_confirm']) != '')) {
         $this->_validate
                  ->label('password', $this->_labels['password'])
                  ->label('password_confirm', $this->_labels['password_confirm'])
                  ->rules('password', $this->_rules['password'])
                    ->rules('password_confirm', $this->_rules['password_confirm']);
      } 

      // Since new versions of Kohana automatically exclude the current user from the uniqueness checks,
      // we no longer need to define our own callbacks. 
		foreach ($this->_callbacks as $field => $callbacks) {
			foreach ($callbacks as $callback) {
				if (is_string($callback) AND method_exists($this, $callback)) {
					// Callback method exists in current ORM model
					$this->_validate->callback($field, array($this, $callback));
				} else {
					// Try global function
					$this->_validate->callback($field, $callback);
   }
      }
   }

      return $this->_validate->check();
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
   
   /**
    * Generates a password of given length using mt_rand.
    * @param int $length
    * @return string
    */
   function generate_password($length = 8) {
      // start with a blank password
      $password = "";
      // define possible characters (does not include l, number relatively likely)
      $possible = "123456789abcdefghjkmnpqrstuvwxyz123456789";
      $i = 0;
      // add random characters to $password until $length is reached
      while ($i < $length) {
         // pick a random character from the possible ones
         $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);

         $password .= $char;
         $i++;

      }
      return $password;
   }

   /**
    * Check whether a username exists.
    * @param string $username
    * @return boolean
    */
   public function check_username($username) {
       $exists = (bool) DB::select(array('COUNT("*")', 'total_count'))
                     ->from($this->_table_name)
                     ->where('username',   '=',   $username)
                     ->execute($this->_db)
                     ->get('total_count');

      if ($exists) {
         return false;
      }
      return true;
   }

}
