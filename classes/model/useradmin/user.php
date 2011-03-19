<?php

class Model_Useradmin_User extends Model_Auth_User {

   protected $_table_name = 'users';
   
   /**
    * A user has many tokens and roles
    *
    * @var array Relationhips
    */
   protected $_has_many = array(
      // auth
      'roles' => array('through' => 'roles_users'),
      'user_tokens' => array(),
      // for facebook / twitter / google / yahoo identities
      'user_identity' => array(),
      );
   protected $_has_one= array(
      );
   protected $_created_column = array('column' => 'created', 'format' => 'Y-m-d H:i:s');
   protected $_updated_column = array('column' => 'modified', 'format' => 'Y-m-d H:i:s');

   /**
    * Rules for the user model. Because the password is _always_ a hash
    * when it's set,you need to run an additional not_empty rule in your controller
    * to make sure you didn't hash an empty string. The password rules
    * should be enforced outside the model or with a model helper method.
    *
    * @return array Rules
    * @see Model_Auth_User::rules
    */
   public function rules()
   {
      $parent = parent::rules();
      // fixes the min_length username value
      $parent['username'][1] = array('min_length', array(':value', 1));
      return $parent;
   }

   // TODO overload filters() and add username/created_on/updated_on coluns filters

   /**
    * Password validation for plain passwords.
    *
    * @param array $values
    * @return Validation
    * @see Model_Auth_User::get_password_validation
    */
   public static function get_password_validation($values) {
      return Validation::factory($values)
         ->rule('password', 'min_length', array(':value', 6))
         ->rule('password_confirm', 'matches', array(':validation', ':field', 'password'));
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
    * Transcribe name to ASCII
    * @param string $string
    * @return string
    */
   function transcribe($string) {
    $string = strtr($string,
       "\xA1\xAA\xBA\xBF\xC0\xC1\xC2\xC3\xC5\xC7\xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1\xD2\xD3\xD4\xD5\xD8\xD9\xDA\xDB\xDD\xE0\xE1\xE2\xE3\xE5\xE7\xE8\xE9\xEA\xEB\xEC\xED\xEE\xEF\xF0\xF1\xF2\xF3\xF4\xF5\xF8\xF9\xFA\xFB\xFD\xFF\xC4\xD6\xE4\xF6",
        "_ao_AAAAACEEEEIIIIDNOOOOOUUUYaaaaaceeeeiiiidnooooouuuyyAOao");
    $string = strtr($string, array("\xC6"=>"AE", "\xDC"=>"Ue", "\xDE"=>"TH", "\xDF"=>"ss",  "\xE6"=>"ae", "\xFC"=>"ue", "\xFE"=>"th"));
    $string = preg_replace("/([^a-z0-9\.]+)/", "", strtolower($string));
    return($string);
   }

   /**
    * Given a string, this function will try to find an unused username by appending a number.
    * Ex. username2, username3, username4 ...
    *
    * @param string $base
    */
   function generate_username($base = '') {
      $base = $this->transcribe($base);
      $username = $base;
      $i = 2;
      while($this->username_exist($username)) {
         $username = $base.$i;
         $i++;
      }
      return $username;
   }

   /**
    * Check whether a username exists.
    * @param string $username
    * @return boolean
    */
   public function username_exist($username) 
   {
      return ( (bool) $this->unique_key_exists( $username, 'username') ) ;
   }

}
