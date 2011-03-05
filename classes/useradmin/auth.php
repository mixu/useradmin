<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * User authorization library. Handles user login and logout, as well as secure
 * password hashing.
 *
 * @package    Useradmin/Auth
 * @author     Gabriel R. Giannattasio
 */
abstract class Useradmin_Auth extends Kohana_Auth {
	
	/**
	 * Singleton pattern
	 *
	 * @return Auth
	 */
	public static function instance()
	{
		
		if ( ! isset(Auth::$_instance))
		{
			// Load the configuration for this type
			$config = Kohana::config('auth');

			if ( ! $type = $config->get('driver'))
			{
				$type = 'file';
			}

			// Set the session class name
			$class = 'Auth_'.ucfirst($type);
			
			$config->set("useradmin", Kohana::config('useradmin.auth') );

			// Create a new session instance
			Auth::$_instance = new $class($config);
		}

		return Auth::$_instance;
	}

	/**
	 * Attempt to log in a user by using an ORM object and plain-text password.
	 *
	 * @param   string   username to log in
	 * @param   string   password to check against
	 * @param   boolean  enable autologin
	 * @return  boolean
	 */
	public function login($username, $password, $remember = FALSE) {

		$status = parent::login($username, $password, $remember);
		
		return $status;
	}

}