<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Default auth user
 *
 * @package    Kohana/Auth
 * @author     Kohana Team
 * @copyright  (c) 2007-2010 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Model_Auth_User extends ORM {

	// Relationships
	protected $_has_many = array(
		'user_tokens' => array('model' => 'user_token'),
		'roles'       => array('model' => 'role', 'through' => 'roles_users'),
	);

	// Validation rules
	protected $_rules = array(
		'username' => array(
			'not_empty'  => NULL,
			'min_length' => array(4),
			'max_length' => array(32),
			'regex'      => array('/^[-\pL\pN_.]++$/uD'),
		),
		'password' => array(
			'not_empty'  => NULL,
			'min_length' => array(5),
			'max_length' => array(42),
		),
		'password_confirm' => array(
			'matches'    => array('password'),
		),
		'email' => array(
			'not_empty'  => NULL,
			'min_length' => array(4),
			'max_length' => array(127),
			'email'      => NULL,
		),
	);

	// Validation callbacks
	protected $_callbacks = array(
		'username' => array('username_available'),
		'email' => array('email_available'),
	);

	// Field labels
	protected $_labels = array(
		'username'         => 'username',
		'email'            => 'email address',
		'password'         => 'password',
		'password_confirm' => 'password confirmation',
	);

	// Columns to ignore
	protected $_ignored_columns = array('password_confirm');

	/**
	 * Validates login information from an array, and optionally redirects
	 * after a successful login.
	 *
	 * @param   array    values to check
	 * @param   string   URI or URL to redirect to
	 * @return  boolean
	 */
	public function login(array & $array, $redirect = FALSE)
	{
		$fieldname = $this->unique_key($array['username']);
		$array = Validate::factory($array)
			->label('username', $this->_labels[$fieldname])
			->label('password', $this->_labels['password'])
			->filter(TRUE, 'trim')
			->rules('username', $this->_rules[$fieldname])
			->rules('password', $this->_rules['password']);

		// Get the remember login option
		$remember = isset($array['remember']);

		// Login starts out invalid
		$status = FALSE;

		if ($array->check())
		{
			// Attempt to load the user
			$this->where($fieldname, '=', $array['username'])->find();

			if ($this->loaded() AND Auth::instance()->login($this, $array['password'], $remember))
			{
				if (is_string($redirect))
				{
					// Redirect after a successful login
					Request::instance()->redirect($redirect);
				}

				// Login is successful
				$status = TRUE;
			}
			else
			{
				$array->error('username', 'invalid');
			}
		}

		return $status;
	}

	/**
	 * Validates an array for a matching password and password_confirm field,
	 * and optionally redirects after a successful save.
	 *
	 * @param   array    values to check
	 * @param   string   URI or URL to redirect to
	 * @return  boolean
	 */
	public function change_password(array & $array, $redirect = FALSE)
	{
		$array = Validate::factory($array)
			->label('password', $this->_labels['password'])
			->label('password_confirm', $this->_labels['password_confirm'])
			->filter(TRUE, 'trim')
			->rules('password', $this->_rules['password'])
			->rules('password_confirm', $this->_rules['password_confirm']);

		if ($status = $array->check())
		{
			// Change the password
			$this->password = $array['password'];

			if ($status = $this->save() AND is_string($redirect))
			{
				// Redirect to the success page
				Request::instance()->redirect($redirect);
			}
		}

		return $status;
	}

	/**
	 * Complete the login for a user by incrementing the logins and saving login timestamp
	 *
	 * @return void 
	 */
	public function complete_login()
	{
		if ( ! $this->_loaded)
		{
			// nothing to do
			return;
		}

		// Update the number of logins
		$this->logins = new Database_Expression('logins + 1');

		// Set the last login date
		$this->last_login = time();

		// Save the user
		$this->save();
	}

	/**
	 * Does the reverse of unique_key_exists() by triggering error if username exists.
	 * Validation callback.
	 *
	 * @param   Validate  Validate object
	 * @param   string    field name
	 * @return  void
	 */
	public function username_available(Validate $array, $field)
	{
		if ($this->unique_key_exists($array[$field], 'username'))
		{
			$array->error($field, 'username_available', array($array[$field]));
		}
	}

	/**
	 * Does the reverse of unique_key_exists() by triggering error if email exists.
	 * Validation callback.
	 *
	 * @param   Validate  Validate object
	 * @param   string    field name
	 * @return  void
	 */
	public function email_available(Validate $array, $field)
	{
		if ($this->unique_key_exists($array[$field], 'email'))
		{
			$array->error($field, 'email_available', array($array[$field]));
		}
	}

	/**
	 * Tests if a unique key value exists in the database.
	 *
	 * @param   mixed    the value to test
	 * @param   string   field name
	 * @return  boolean
	 */
	public function unique_key_exists($value, $field = NULL)
	{
		if ($field === NULL)
		{
			// Automatically determine field by looking at the value
			$field = $this->unique_key($value);
		}

		return (bool) DB::select(array('COUNT("*")', 'total_count'))
			->from($this->_table_name)
			->where($field, '=', $value)
			->where($this->_primary_key, '!=', $this->pk())
			->execute($this->_db)
			->get('total_count');
	}

	/**
	 * Allows a model use both email and username as unique identifiers for login
	 *
	 * @param   string  unique value
	 * @return  string  field name
	 */
	public function unique_key($value)
	{
		return Validate::email($value) ? 'email' : 'username';
	}

	/**
	 * Saves the current object. Will hash password if it was changed.
	 *
	 * @return  ORM
	 */
	public function save()
	{
		if (array_key_exists('password', $this->_changed))
		{
			$this->_object['password'] = Auth::instance()->hash_password($this->_object['password']);
		}

		return parent::save();
	}

} // End Auth User Model