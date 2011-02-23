<?php
class Model_User_Identity extends ORM {

   protected $_table_name = 'user_identity';
   protected $_belongs_to = array(
           'user' => array(),
      );

	protected $_rules = array(
         'user_id' => array(
            'not_empty' => NULL,
            'numeric' => NULL,
         ),
         'provider' => array(
            'not_empty' => NULL,
   			'max_length' => array(255),
         ),
         'identity' => array(
            'not_empty' => NULL,
   			'max_length' => array(255),
         ),
      );

	protected $_callbacks = array(
		'identity' => array('unique_identity'),
	);

	/**
	 * Triggers error if identity exists.
	 * Validation callback.
	 *
	 * @param   Validate  Validate object
	 * @param   string    field name
	 * @return  void
	 */
	public static function unique_identity(Validate $array, $field) {
		$identity_exists = (bool) DB::select(array(DB::expr('COUNT("*")'), 'total_count'))
			->from('user_identity')
			->where('identity', '=', $array['identity'])
			->and_where('provider', '=', $array['provider'])
			->execute()
			->get('total_count');
      if($identity_exists) {
			$array->error($field, 'identity_available', array($array[$field]));
      }
	}
}