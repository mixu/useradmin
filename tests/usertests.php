<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Test the user module extension for auth singleton
 *
 * @group modules
 * @group modules.useradmin
 * 
 * @package    Useradmin
 * @category   Tests
 * @author     Gabriel R. Giannattasio
 * @author     gabrielgiannattasio <gabriel@l6.com.br>
 * @copyright  (c) 2011-2011 Fleep.me
 */
class UserTest extends Unittest_TestCase 
{
	static public $dbConnection = "test";
	
	/**
	 * Populate Schema
	 * Exec the file from tests/data if founded
	 * @param string $schema SQL Schema file, without extension.
	 */
	static public function runSchema($schema)
	{
		$testDb = Kohana::config('database.default');

		// Try define the path for the schema if connection config exists
		if( ( ! is_null($testDb) ) && ($path = Kohana::find_file('tests', 'data/'.$schema, 'sql')) )
		{
			$command = "-u{$testDb['connection']['username']} -p{$testDb['connection']['password']} {$testDb['connection']['database']}";
			// Set the default database to test connection
			return (exec("mysql $command < $path "))?TRUE:FALSE;
		}
		return FALSE;
	}
	
	/**
	 * Configure database for test
	 */
	static public function useTestDB() {
		return Kohana::config('database')->set( "default", Kohana::config('database.'.UserTest::$dbConnection) );
	}
	
	/**
	 * Valid users Provider
	 */
	function providerValidUsers() {
		return array(
			// Basic username
			array(array(
				"username" => "validuser",
				"password" => "12345678",
				"password_confirm" => "12345678",
				"email" => "valid@user.com",
			)),
			// Caps username
			array(array(
				"username" => "MyOwnUser",
				"password" => "ab38cab38c",
				"password_confirm" => "ab38cab38c",
				"email" => "MyOwn@User.com",
			)),
			// Underline username
			array(array(
				"username" => "separate_user",
				"password" => "verylongpasswordwith@chars!dix97",
				"password_confirm" => "verylongpasswordwith@chars!dix97",
				"email" => "separate@User.com",
			)),
			// Single char username
			array(array(
				"username" => "a",
				"password" => "verylongpasswordwith@chars!dix97",
				"password_confirm" => "verylongpasswordwith@chars!dix97",
				"email" => "a@User.com",
			)),
		);
	}
	
	/**
	 * Invalid users Provider
	 */
	function providerInvalidUsers() {
		return array(
			// Userame with spaces
			array(array(
				"username" => "name with spaces",
				"password" => "123456789",
				"password_confirm" => "123456789",
				"email" => "email with spaces@user.com",
			)),
			// Userame with a invalid char
			array(array(
				"username" => "namewith@char",
				"password" => "ab38cab38c",
				"password_confirm" => "ab38cab38c",
				"email" => "namewith@char@User.com",
			)),
			// Blank username
			array(array(
				"username" => "",
				"password" => "verylongpasswordwith@chars!dix97",
				"password_confirm" => "verylongpasswordwith@chars!dix97",
				"email" => "@User.com",
			)),
			// Too long username
			array(array(
				"username" => "anamewithtoomanycharacterscantwork",
				"password" => "verylongpasswordwith@chars!dix97",
				"password_confirm" => "verylongpasswordwith@chars!dix97",
				"email" => "anamewithtoomanycharacterscantwork@User.com",
			)),
			// No password
			array(array(
				"username" => "validusername2",
				"password" => "",
				"password_confirm" => "",
				"email" => "validusername2@User.com",
			)),
			// No confirm password
			array(array(
				"username" => "validusername3",
				"password" => "123456789",
				"password_confirm" => "",
				"email" => "validusername3@User.com",
			)),
			// Diferent confirm password
			array(array(
				"username" => "validusername4",
				"password" => "123456789",
				"password_confirm" => "987654321",
				"email" => "validusername4@User.com",
			)),
			// Invalid e-mail
			array(array(
				"username" => "validusername5",
				"password" => "123456789",
				"password_confirm" => "987654321",
				"email" => "validu@sername5@User.com",
			)),
		);
	}
	
	function setUp() 
	{	
		parent::setUp();
		UserTest::useTestDB();
	}
	
	static function setUpBeforeClass()
	{
		UserTest::useTestDB();
		UserTest::runSchema("clean-setup" );
	}
	
	/**
	 * test if auth exists
	 * @author Gabriel Giannattasio
	 * @test
	 */
	function test_if_auth_exists()
	{
		$this->assertTrue( class_exists("Auth"), 'Auth class not found' );
	} // test if auth is a class
	
	/**
	 * test auth register user
	 * @author Gabriel Giannattasio
	 * @test
	 * @dataProvider providerValidUsers
	 * @depends test_if_auth_exists
	 */
	function test_auth_register_valid_users( $fields )
	{
		// Start the tests
		$this->assertTrue( Auth::instance()->register($fields), 'Must be a valid user.');
	} // test auth register user
	
	/**
	 * test for unique fields
	 * @author Gabriel Giannattasio
	 * @test
	 * @dataProvider providerValidUsers
	 * @depends test_auth_register_valid_users
	 */
	function test_for_unitque_fileds( $fields ) {
		$user = ORM::factory("user");
		$this->assertTrue( $user->username_exist( $fields['username'] ) );
	}
	
	/**
	 * test auth register invalid users
	 * @author Gabriel Giannattasio
	 * @test
	 * @dataProvider providerInvalidUsers
	 * @depends test_if_auth_exists
	 */
	function test_auth_register_invalid_users( $fields )
	{
		// Start the tests
		$this->assertFalse( Auth::instance()->register($fields), 'Must be a invalid user.');
	} // test auth register user
	
	/**
	 * test auth register invalid fields
	 * @author Gabriel Giannattasio
	 * @test
	 * @depends test_if_auth_exists
	 */
	function test_auth_register_invalid_fields()
	{
		// Start the tests
		$this->assertFalse( Auth::instance()->register(NULL), 'Must be a invalid field user.');
	} // test auth register user
}