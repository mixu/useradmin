<?php
class Model_User_Identity extends ORM {

   protected $_table_name = 'user_identity';
   protected $_belongs_to = array(
           'user' => array(),
      );

   /**
    * Rules for the user identity.
    * @return array Rules
    */
   public function rules()
   {
      return array(
         'user_id' => array(
            array('not_empty'),
            array('numeric'),
         ),
         'provider' => array(
            array('not_empty'),
            array('max_length', array(':value', 255)),
         ),
         'identity' => array(
            array('not_empty'),
            array('max_length', array(':value', 255)),
            array(array($this, 'unique_identity'), array(':validation', ':field')),
         ),
      );
   }

   /**
    * Triggers error if identity exists.
    * Validation callback.
    *
    * @param   Validation  Validation object
    * @param   string    field name
    * @return  void
    */
   public function unique_identity (Validation $validation, $field)
   {
      $identity_exists = (bool) DB::select(array('COUNT("*")', 'total_count'))
         ->from($this->_table_name)
         ->where('identity', '=', $validation['identity'])
         ->and_where('provider', '=', $validation['provider'])
         ->execute($this->_db)
         ->get('total_count');
      if ($identity_exists) {
         $validation->error($field, 'identity_available', array($validation[$field]));
      }
   }
}