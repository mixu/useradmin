<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Test the user module extension for auth singleton
 *
 * @group modules
 * @group modules.useradmin
 * @group modules.useradmin.auth
 * 
 * @package    Useradmin
 * @category   Tests
 * @author     Gabriel R. Giannattasio
 * @author     gabrielgiannattasio <gabriel@l6.com.br>
 * @copyright  (c) 2011-2011 Fleep.me
 */
class UserTests extends Unittest_Model_TestCase {
	
	private function add_valid_users()
	{
		$users_provider = $this->providerValidUsers();
		$users = array();
		$i = 0;
		foreach ($users_provider as $user)
		{
			try
			{
				$users[$i] = Model::factory("user")
					->create_user($user[0], array(
						"username", 
						"email", 
						"password", 
						));
				
				$login_role = new Model_Role(array('name' =>'login'));
            	$users[$i]->add('roles', $login_role);
			}
			catch (Kohana_Exception $e)
			{
				$this->fail("Can't create user: ".Kohana_Debug::dump($e));
			}
			$i++;
		}
		return $users;
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
	 * Valid user updates Provider
	 */
	public function providerValidUserUpdates()
	{
		return array(
			// #0: Update email
			array(
				// login
				array(
					"username" => "validuser",
					"password" => "123456",
				), 
				// fields
				array(
					"username" => "validuser",
					"email" => "valid_changed@user.com",
				),
			),
			// #1: Update username
			array(
				// login
				array(
					"username" => "validuser",
					"password" => "123456",
				), 
				// fields
				array(
					"username" => "validuser_changed",
					"email" => "valid_changed@user.com",
				),
			),
			// #2: Update password
			array(
				// login
				array(
					"username" => "validuser",
					"password" => "123456",
				), 
				// fields
				array(
					"password" => "123456789",
					"password_confirm" => "123456789",
				),
			),
			// #3: Update nothing
			array(
				// login
				array(
					"username" => "validuser",
					"password" => "123456",
				), 
				// fields
				array(
				),
			),
			// Loop
		);
	}
	
	/**
	 * Invalid user updates Provider
	 */
	public function providerInvalidUserUpdates()
	{
		return array(
			// #0: Short password
			array(
				// login
				array(
					"username" => "MyOwnUser",
					"password" => "ab38cab38c",
				), 
				// fields
				array(
					"password" => "12345",
					"password_confirm" => "12345",
				),
			),
			// #1: Wrong password confirmation
			array(
				// login
				array(
					"username" => "MyOwnUser",
					"password" => "ab38cab38c",
				), 
				// fields
				array(
					"password" => "123456",
					"password_confirm" => "654321",
				),
			),
			// #2: Used username
			array(
				// login
				array(
					"username" => "MyOwnUser",
					"password" => "ab38cab38c",
				), 
				// fields
				array(
					"username" => "A",
				),
			),
			// #3: Used email
			array(
				// login
				array(
					"username" => "MyOwnUser",
					"password" => "ab38cab38c",
				), 
				// fields
				array(
					"email" => "a@User.com",
				),
			),
			// #4: Username with spaces
			array(
				// login
				array(
					"username" => "MyOwnUser",
					"password" => "ab38cab38c",
				), 
				// fields
				array(
					"username" => "name with spaces",
				),
			),
			// #5: Username with invalid char
			array(
				// login
				array(
					"username" => "MyOwnUser",
					"password" => "ab38cab38c",
				), 
				// fields
				array(
					"username" => "name with spaces",
				),
			),
			// #6: Blank username
			array(
				// login
				array(
					"username" => "MyOwnUser",
					"password" => "ab38cab38c",
				), 
				// fields
				array(
					"username" => "",
				),
			),
			// #7: Too long username
			array(
				// login
				array(
					"username" => "MyOwnUser",
					"password" => "ab38cab38c",
				), 
				// fields
				array(
					"username" => "anamewithtoomanycharacterscantwork",
				),
			),
			// #8: Invalid e-mail
			array(
				// login
				array(
					"username" => "MyOwnUser",
					"password" => "ab38cab38c",
				), 
				// fields
				array(
					"email" => "anamewithtoo@manychar@aar.sck",
				),
			),
			// #9: Used username with space
			array(
				// login
				array(
					"username" => "MyOwnUser",
					"password" => "ab38cab38c",
				), 
				// fields
				array(
					"username" => "A ",
				),
			),
			// Loop
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
				"password_confirm" => "123456789",
				"email" => "validu@sername5@User.com",
			)),
			// Already registred user with diferente case
//			array(array(
//				"username" => "A",
//				"password" => "123456789",
//				"password_confirm" => "123456789",
//				"email" => "a_new@User.com",
//			)),
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
		$status = TRUE;
		try 
		{
			Auth::instance()->register($fields);
		} catch (Exception $e) {
			$status = FALSE;
		}
		$this->assertTrue( $status, 'Must be a valid user.');
	}
	
	/**
	 * test auth register invalid users
	 * @author Gabriel Giannattasio
	 * @test
	 * @dataProvider providerInvalidUsers
	 * @depends test_if_auth_exists
	 */
	public function test_auth_register_invalid_users( $fields )
	{
		try 
		{
			$status = Auth::instance()->register($fields);
			$this->fail("Register a invalid user: ".Kohana_Debug::dump($fields));
		} 
		catch (ORM_Validation_Exception $e) 
		{
			$this->assertInstanceOf("ORM_Validation_Exception",$e);
			return;
		}
		catch (Exception $e)
		{
			$this->fail("Unspected exception: ".$e->getMessage());
		}
	}
	
	/**
	 * test auth register invalid fields
	 * @author Gabriel Giannattasio
	 * @test
	 * @depends test_if_auth_exists
	 */
	public function test_auth_register_invalid_fields()
	{
		$registerFail = FALSE;
		try {
			Auth::instance()->register(NULL);
		} catch (Exception $e) {
			$registerFail = TRUE;
		} 
		$this->assertTrue($registerFail);
	}
	
	/**
	 * test auth valid logins and logout
	 * @author Gabriel Giannattasio
	 * @test
	 * @dataProvider providerValidUsers
	 */
	public function test_auth_valid_logins_and_logout( $fields )
	{
		// Setup valid users
		$this->add_valid_users();
		
		$this->assertTrue( Auth::instance()->logout(TRUE, TRUE), "Force logout all" );
		$this->assertTrue( Auth::instance()->login( $fields['username'], $fields['password']) , "Check login using username");
		$this->assertTrue( Auth::instance()->login( $fields['email'], $fields['password']) , "Check login using email");
		// Get the user model
		$user = Auth::instance()->get_user();
		$this->assertInstanceOf("Model_User", $user, "Checking the Model_User instance");
		$this->assertSame($fields['username'], $user->username, "Check the logedin username");
		$this->assertTrue( Auth::instance()->logout() );
	}
	
	/**
	 * test auth valid logins and update_user
	 * @author Gabriel Giannattasio
	 * @test
	 * @dataProvider providerValidUserUpdates
	 */
	public function test_auth_valid_logins_and_update_user( $login, $fields )
	{
		// Setup valid users
		$this->add_valid_users();
		
		$this->assertTrue( Auth::instance()->logout(TRUE, TRUE), "Force logout all" );
		$this->assertTrue( Auth::instance()->login( $login['username'], $login['password']), "Do the login" );
		$user = Auth::instance()->get_user();
		try 
		{
			$user->update_user( $fields, array(
				'username',
				'password',
				'email',
			) );
			$status = TRUE;
		} 
		catch (ORM_Validation_Exception $e) 
		{
			$status = FALSE;
		}
		
		$this->assertTrue( $status, "Do the user update");
	}
	
	/**
	 * test auth valid logins and invalid update_user
	 * @author Gabriel Giannattasio
	 * @test
	 * @dataProvider providerInvalidUserUpdates
	 */
	public function test_auth_valid_logins_and_invalid_update_user( $login, $fields )
	{
		// Setup valid users
		$this->add_valid_users();
		
		$this->assertTrue( Auth::instance()->logout(TRUE, TRUE), "Force logout all" );
		$this->assertTrue( Auth::instance()->login( $login['username'], $login['password']), "Do the login" );
		$user = Auth::instance()->get_user();
		try 
		{
			$user->update_user( $fields, array(
				'username',
				'password',
				'email',
			) );
			$status = TRUE;
		} 
		catch (ORM_Validation_Exception $e) 
		{
			$status = FALSE;
		}
		
		$this->assertFalse( $status, "Do the user update");
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
		$this->assertTrue( Auth::instance()->logout(TRUE, TRUE), "Force logout all" );
		$this->assertFalse( Auth::instance()->login( $fields['username'], $fields['password']), "Check login using username" );
		$this->assertFalse( Auth::instance()->login( $fields['email'], $fields['password']), "Check login using email" );
		$this->assertFalse( Auth::instance()->get_user(), "get_user must fail.");
		$this->assertTrue( Auth::instance()->logout() );
	}
	
	/**
	 * test auth login jail
	 * @author Gabriel Giannattasio
	 * @test
	 * @depends test_auth_register_valid_users
	 */
	public function test_auth_login_jail()
	{
		$this->assertTrue( Auth::instance()->logout(TRUE, TRUE), "Force logout all" );
		$validUser = $this->providerValidUsers();
		// Must fail 5 times before jail the user for 5 minutes
		$this->assertFalse( Auth::instance()->login( $validUser[2][0]['username'], "*********"), "Check login using wrong password #1" );
		$this->assertFalse( Auth::instance()->login( $validUser[2][0]['username'], "*********"), "Check login using wrong password #2" );
		$this->assertFalse( Auth::instance()->login( $validUser[2][0]['username'], "*********"), "Check login using wrong password #3" );
		$this->assertFalse( Auth::instance()->login( $validUser[2][0]['username'], "*********"), "Check login using wrong password #4" );
		$this->assertFalse( Auth::instance()->login( $validUser[2][0]['username'], "*********"), "Check login using wrong password #5" );
		// Try to login with the correct password must fail
		$this->assertFalse( Auth::instance()->login( $validUser[2][0]['username'], $validUser[2][0]['password']), "Check login using correct password" );
	}
	
	/**
	 * test auth delete loged user
	 * @author Gabriel Giannattasio
	 * @test
	 */
	public function test_auth_delete_loged_user()
	{
		// Setup valid users
		$this->add_valid_users();
		
		$validUser = $this->providerValidUserUpdates();
		$this->assertTrue( Auth::instance()->login( $validUser[3][0]['username'], $validUser[3][0]['password']), "Do the login" );
		$this->assertInstanceOf("Model_User", $user = Auth::instance()->get_user(), "Get Model_User instance form Auth");
		$this->assertNull( Auth::instance()->unregister($user), "Delete the logged user");
		$this->assertNull( Auth::instance()->logged_in(), "The user was deleted, why this isn't Null?");
	}
	
	/**
	 * test auth delete multiple users
	 * @author Gabriel Giannattasio
	 * @test
	 */
	public function test_auth_delete_multiple_users()
	{
		// Setup valid users
		$this->add_valid_users();
		
		$validUsers = $this->providerValidUsers();
		array_walk ($validUsers, function(&$user)
		{
			$username = $user[0]['username'];
			$user = new Model_User();
			$user->where("username","=", $username)
				 ->find();
		});
		$this->assertNull( Auth::instance()->unregister($validUsers), "Delete the users in array");
		foreach ($validUsers as $user)
		{
			$this->assertFalse( $user->loaded(), "Ok, so you think the user was deleted? think again!");
		}
	}
}
