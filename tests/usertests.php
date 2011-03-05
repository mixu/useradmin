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
	/**
	 * Populate Schema
	 * Exec the file from tests/data if founded
	 * @param string $schema SQL Schema file, without extension.
	 */
	public function runSchema($schema)
	{
		$testDb = Kohana::config('database.test');

		// Try define the path for the schema if connection config exists
		if( ( ! is_null($testDb) ) && ($path = Kohana::find_file('tests', 'data/'.$schema, 'sql')) )
		{
			$command = "-u{$testDb['connection']['username']} -p{$testDb['connection']['password']} {$testDb['connection']['database']}";
			return (exec("mysql $command < $path "))?TRUE:FALSE;
		}
		return FALSE;
	}
	
	/**
	 * test if auth is a class
	 * @author Gabriel Giannattasio
	 * @test
	 */
	function test_if_auth_is_a_class()
	{
		$this->assertTrue( class_exists("Auth"), 'Auth class not found' );
	} // test if auth is a class
	
	/**
	 * test auth register user
	 * @author Gabriel Giannattasio
	 * @test
	 */
	function test_auth_register_user()
	{
		// Start the test from a new and clean Schema
		$this->runSchema("clean-setup");
		
		
		
		$this->assertTrue( class_exists("Auth"), 'Auth class not found' );
	} // test auth register user
	
}