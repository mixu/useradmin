<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * ORM Auth driver extended for Useradmin module support.
 *
 * @package    Useradmin/Auth
 * @author     Gabriel R. Giannattasio
 */
class Useradmin_Auth_ORM extends Kohana_Auth_ORM implements Useradmin_Driver_iAuth {
	
	/**
	 * Extends the Kohana Auth ORM driver to give useradmin module extras
	 * @see Kohana_Auth_ORM::_login()
	 */
	protected function _login($user, $password, $remember) 
	{
		if ( ! is_object($user))
		{
			$username = $user;

			// Load the user
			$user = ORM::factory('user');
			$user->where($user->unique_key($username), '=', $username)->find();
		}
		
		// if there are too many recent failed logins, fail now
		if (($this->_config["useradmin"]["max_failed_logins"] > 0) && ($user->failed_login_count > $this->_config["useradmin"]["max_failed_logins"] ) && (strtotime($user->last_failed_login) > strtotime("-".$this->_config["useradmin"]["login_jail_time"] ) )) 
		{
			// do nothing, and fail (too many failed logins within {login_jail_time} minutes).
			return FALSE;
		}
		// Loads default driver before extend the results
		$status = parent::_login($user, $password, $remember);

		if($status) 
		{
			// Successful login
			// Reset the login failed count
			$user->failed_login_count = 0;
			$user->save();
		} else {
			// Failed login
			$user->failed_login_count = $user->failed_login_count+1;
			$user->last_failed_login = date("Y-m-d H:i:s");
			// Verify if the user id if valid before save it
			if(is_numeric( $user->id ) && $user->id != 0)
			{
				$user->save();
			}
		}
		
		return $status;
	}
	
	/**
	 * Register a new user
	 * 
	 * @param array $fields contain $_POST array
	 */
	public function register($fields) {
		// TODO register new user by ORM Driver
	}
}