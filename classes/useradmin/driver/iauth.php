<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * User authorization library. Handles user login and logout, as well as secure
 * password hashing.
 *
 * @package    Useradmin/Auth
 * @author     Gabriel R. Giannattasio
 */
interface Useradmin_Driver_iAuth {
	/**
	 * Register
	 * Method to register new user by Useradmin Auth module, when you set the
	 * fields, be sure they must respect the driver rules
	 * 
	 * @param array $fields An array witch contains the fields to be populate
	 * @returnboolean Operation final status
	 */
	 public function register($fields);
	 
	/**
	 * Unegister
	 * Method to unregister existing user by Useradmin Auth module, when you set the
	 * Model_User reference for removing a user.
	 * 
	 * @param mixed $users An array witch contains the Model_User or a array of Model_User
	 * @return boolean Operation final status
	 */
	 public function unregister($users);
}