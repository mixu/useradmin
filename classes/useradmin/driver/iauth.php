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
	 * Register a single user
	 * Method to register new user by Useradmin Auth module, when you set the
	 * fields, be sure they must respect the driver rules
	 * 
	 * @param array $fields An array witch contains the fields to be populate
	 * @returnboolean Operation final status
	 */
	 public function register($fields);
	 
	/**
	 * Unegister multiple users
	 * Method to unregister existing user by Useradmin Auth module, when you set the
	 * Model_User reference for removing a user.
	 * 
	 * @param array(Model_User) $users An array witch contains Model_User's
	 * @return void
	 */
	 public function unregister($users);
}