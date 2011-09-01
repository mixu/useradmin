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
			$config = Kohana::$config->load('auth');

			if ( ! $type = $config->get('driver'))
			{
				$type = 'file';
			}

			// Set the session class name
			$class = 'Auth_'.ucfirst($type);
			
			$config->set("useradmin", Kohana::$config->load('useradmin.auth') );

			// Create a new session instance
			Auth::$_instance = new $class($config);
		}

		return Auth::$_instance;
	}

}