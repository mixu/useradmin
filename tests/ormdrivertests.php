<?php defined('SYSPATH') OR die('No direct access allowed.');
require_once Kohana::find_file('tests','usertests');
/**
 * Test the user module extension for auth singleton
 *
 * @group modules
 * @group modules.useradmin
 * @group modules.useradmin.auth
 * @group modules.useradmin.auth.drivers.orm
 * 
 * @package    Useradmin
 * @category   Tests
 * @author     Gabriel R. Giannattasio
 * @author     gabrielgiannattasio <gabriel@l6.com.br>
 * @copyright  (c) 2011-2011 Fleep.me
 */
class ORMDriverTests extends UserTests {
	
	/**
	 * test if auth orm driver exists
	 * @author Gabriel Giannattasio
	 * @test
	 */
	public function test_if_auth_orm_driver_exists()
	{
		$this->assertTrue( class_exists("Model_User"), 'Auth class not found' );
	} // test if auth is a class
	
	/**
	 * test auth register user
	 * @author Gabriel Giannattasio
	 * @test
	 * @dataProvider providerValidUsers
	 */
	public function test_auth_register_valid_users( $fields ) {
		parent::test_auth_register_valid_users( $fields );
	}
	
	/**
	 * test for unique fields
	 * @author Gabriel Giannattasio
	 * @test
	 * @dataProvider providerValidUsers
	 * @depends test_auth_register_valid_users
	 * @depends test_if_auth_orm_driver_exists
	 */
	public function test_for_unique_fileds( $fields ) {
		$user = ORM::factory("user");
		$this->assertTrue( $user->username_exist( $fields['username'] ) );
		// Test for diferent username case
		$this->assertTrue( $user->username_exist( strtoupper( $fields['username'] ) ) );
	}
}