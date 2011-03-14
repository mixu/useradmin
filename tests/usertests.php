<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Test the user module extension for auth singleton
 *
 * @group modules
 * @group modules.useradmin
 * @group modules.useradmin.auth
 * @group modules.useradmin.auth_class_only
 * 
 * @package    Useradmin
 * @category   Tests
 * @author     Gabriel R. Giannattasio
 * @author     gabrielgiannattasio <gabriel@l6.com.br>
 * @copyright  (c) 2011-2011 Fleep.me
 */
class UserTests extends Unittest_TestCase {

	const dbConnection = "unittest";
	
	/**
	 * Configure database for test
	 */
	static public function useTestDB() 
	{
		Kohana::config('database')->set( "default", Kohana::config('database.'.UserTests::dbConnection) );
	}
	
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
	
	public function setUp() 
	{	
		parent::setUp();
		UserTests::useTestDB();
	}
	
	static public function setUpBeforeClass()
	{
		UserTests::useTestDB();
		UserTests::runSchema( "clean-setup" );
	}
	
	/**
	 * Valid users Provider
	 */
	public function providerValidUsers() 
	{
		return array(
			// Basic username
			array(array(
				"username" => "validuser",
				"password" => "123456",
				"password_confirm" => "123456",
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
	public function providerInvalidUsers() 
	{
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
			// Too short Password
			array(array(
				"username" => "validusername6",
				"password" => "12345",
				"password_confirm" => "12345",
				"email" => "validusername6@User.com",
			)),
			// Invalid e-mail
			array(array(
				"username" => "validusername5",
				"password" => "123456789",
				"password_confirm" => "987654321",
				"email" => "validu@sername5@User.com",
			)),
			// Already registred user with diferente case
			array(array(
				"username" => "A",
				"password" => "123456789",
				"password_confirm" => "123456789",
				"email" => "a_new@User.com",
			)),
		);
	}
	
	/**
	 * test if auth exists
	 * @author Gabriel Giannattasio
	 * @test
	 */
	public function test_if_auth_exists()
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
	public function test_auth_register_valid_users( $fields )
	{
		UserTests::useTestDB();
		$this->assertTrue( Auth::instance()->register($fields), 'Must be a valid user.');
	} // test auth register user
	
	/**
	 * test auth register invalid users
	 * @author Gabriel Giannattasio
	 * @test
	 * @dataProvider providerInvalidUsers
	 * @depends test_if_auth_exists
	 */
	public function test_auth_register_invalid_users( $fields )
	{
		// Start the tests
		$this->assertFalse( Auth::instance()->register($fields), 'Must be a invalid user.');
	} // test auth register user
	
	/**
	 * test auth register invalid fields
	 * @author Gabriel Giannattasio
	 * @test
	 * @depends test_if_auth_exists
	 * @expectedException ErrorException
	 */
	public function test_auth_register_invalid_fields()
	{
		$this->assertFalse( Auth::instance()->register(NULL), 'Must be a invalid field user.');
	}
	
	/**
	 * test auth valid logins and logout
	 * @author Gabriel Giannattasio
	 * @test
	 * @dataProvider providerValidUsers
	 * @depends test_auth_register_valid_users
	 */
	public function test_auth_valid_logins_and_logout( $fields )
	{
		$this->assertTrue( Auth::instance()->login( $fields['username'], $fields['password']) );
		$this->assertTrue( Auth::instance()->logout() );
	}
	
	/**
	 * test auth invalid logins and logout
	 * @author Gabriel Giannattasio
	 * @test
	 * @dataProvider providerInvalidUsers
	 * @depends test_auth_register_invalid_users
	 */
	public function test_auth_invalid_logins_and_logout( $fields )
	{
		$this->assertFalse( Auth::instance()->login( $fields['username'], $fields['password']) );
		$this->assertTrue( Auth::instance()->logout() );
	}
}