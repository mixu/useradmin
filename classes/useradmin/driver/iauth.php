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
	 */
	 public function register($fields);
}