<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * User authorization library. Handles user login and logout, as well as secure
 * password hashing.
 *
 * @package    Useradmin/Auth
 * @author     Gabriel R. Giannattasio
 */
class Auth extends Kohana_Auth {
	
	/* Just implements, will be ignored, and use the ORM Driver
	 * @see Kohana_Auth::_login()
	 */
	protected function _login($username, $password, $remember) { }

	/* Just implements, will be ignored, and use the ORM Driver
	 * @see Kohana_Auth::password()
	 */
	public function password($username) { }

	/* Just implements, will be ignored, and use the ORM Driver
	 * @see Kohana_Auth::check_password()
	 */
	public function check_password($password) { }

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