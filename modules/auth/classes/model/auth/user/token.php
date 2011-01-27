<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Default auth user toke
 *
 * @package    Kohana/Auth
 * @author     Kohana Team
 * @copyright  (c) 2007-2010 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Model_Auth_User_Token extends ORM {

	// Relationships
	protected $_belongs_to = array('user' => array());

	// Current timestamp
	protected $_now;

	/**
	 * Handles garbage collection and deleting of expired objects.
	 *
	 * @return  void
	 */
	public function __construct($id = NULL)
	{
		parent::__construct($id);

		// Set the now, we use this a lot
		$this->_now = time();

		if (mt_rand(1, 100) === 1)
		{
			// Do garbage collection
			$this->delete_expired();
		}

		if ($this->expires < $this->_now)
		{
			// This object has expired
			$this->delete();
		}
	}

	/**
	 * Overload saving to set the created time and to create a new token
	 * when the object is saved.
	 *
	 * @return  ORM
	 */
	public function save()
	{
		if ($this->loaded() === FALSE)
		{
			// Set the created time, token, and hash of the user agent
			$this->created = $this->_now;
			$this->user_agent = sha1(Request::$user_agent);
		}

		while (TRUE)
		{
			// Generate a new token
			$this->token = $this->create_token();

			try
			{
				return parent::save();
			}
			catch (Kohana_Database_Exception $e)
			{
				// Collision occurred, token is not unique
			}
		}
	}

	/**
	 * Deletes all expired tokens.
	 *
	 * @return  ORM
	 */
	public function delete_expired()
	{
		// Delete all expired tokens
		DB::delete($this->_table_name)
			->where('expires', '<', $this->_now)
			->execute($this->_db);

		return $this;
	}

	/**
	 * Generate a new unique token.
	 *
	 * @return  string
	 * @uses    Text::random
	 */
	protected function create_token()
	{
		// Create a random token
		return Text::random('alnum', 32);
	}

} // End Auth User Token Model