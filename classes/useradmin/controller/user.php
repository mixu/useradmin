<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * User controller: user administration, also user accounts/profiles.
 *
 * @author Mikito Takada
 * @package default
 * @version 1.0
 */
class Useradmin_Controller_User extends Controller_App {

	/**
	 * @var string Filename of the template file.
	 */
	public $template = 'template/useradmin';

	/**
	 * Controls access for the whole controller, if not set to FALSE we will only allow user roles specified.
	 *
	 * See Controller_App for how this implemented.
	 *
	 * Can be set to a string or an array, for example array('login', 'admin') or 'login'
	 */
	public $auth_required = FALSE;

	/** Controls access for separate actions
	 *
	 * See Controller_App for how this implemented.
	 *
	 * Examples:
	 * 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
	 * 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
	 */
	public $secure_actions = array(
		// user actions
		'index' => 'login', 
		'profile' => 'login', 
		'profile_edit' => 'login', 
		'unregister' => 'login', 
		'change_password' => 'login'
	); // the others are public (forgot, login, register, reset, noaccess)
	// logout is also public to avoid confusion (e.g. easier to specify and test post-logout page)

    public function before(){
        $baseUrl = Url::base(true);
        if(substr($this->request->referrer(),0,strlen($baseUrl)) == $baseUrl){
            $urlPath = ltrim(parse_url($this->request->referrer(),PHP_URL_PATH),'/');
            $processedRef = Request::process_uri($urlPath);
            $referrerController = Arr::path(
                $processedRef,
                'params.controller',
                false
            );
            if($referrerController && $referrerController != 'user' && !Session::instance()->get('noReturn',false)){
                Session::instance()->set('returnUrl',$this->request->referrer());
            }

        }

        parent::before();
        
    }

	// USER SELF-MANAGEMENT
	/**
	 * View: Redirect admins to admin index, users to user profile.
	 */
	public function action_index()
	{
		// if the user has the admin role, redirect to admin_user controller
		if (Auth::instance()->logged_in('admin'))
		{
			$this->request->redirect('admin_user/index');
		}
		else
		{
			$this->request->redirect('user/profile');
		}
	}

	/**
	 * View: Access not allowed.
	 */
	public function action_noaccess()
	{
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('Access not allowed');
		$view = $this->template->content = View::factory('user/noaccess');
	}

	/**
	 * View: User account information
	 */
	public function action_profile()
	{
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('User profile');
		if (Auth::instance()->logged_in() == false)
		{
			// No user is currently logged in
			$this->request->redirect('user/login');
		}
		$view = $this->template->content = View::factory('user/profile');
		// retrieve the current user and set the view variable accordingly
		$view->set('user', Auth::instance()->get_user());
	}

	/**
	 * View: Profile editor
	 */
	public function action_profile_edit()
	{
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('Edit user profile');
		$user = Auth::instance()->get_user();
		$id = $user->id;
		// load the content from view
		$view = View::factory('user/profile_edit');
		// save the data
		if (! empty($_POST) && is_numeric($id))
		{
			if (empty($_POST['password']) || empty($_POST['password_confirm']))
			{
				// force unsetting the password! Otherwise Kohana3 will automatically hash the empty string - preventing logins
				unset($_POST['password'], $_POST['password_confirm']);
			}
			try
			{
				$user->update_user($_POST, 
				array(
					'username', 
					'password', 
					'email'
				));
				// message: save success
				Message::add('success', __('Values saved.'));
				// redirect and exit
				$this->request->redirect('user/profile');
				return;
			}
			catch (ORM_Validation_Exception $e)
			{
				// Get errors for display in view
				// Note how the first param is the path to the message file (e.g. /messages/register.php)
				Message::add('error', __('Error: Values could not be saved.'));
				$errors = $e->errors('register');
				$errors = array_merge($errors, ( isset($errors['_external']) ? $errors['_external'] : array() ));
				$view->set('errors', $errors);
				// Pass on the old form values
				$user->password = '';
				$view->set('data', $user->as_array());
			}
		}
		else
		{
			// load the information for viewing
			$view->set('data', $user->as_array());
		}
		// retrieve roles into array
		$roles = array();
		foreach ($user->roles->find_all() as $role)
		{
			$roles[$role->name] = $role->description;
		}
		$view->set('user_roles', $roles);
		$view->set('id', $id);
		$this->template->content = $view;
	}

	/**
	 * Register a new user.
	 */
	public function action_register()
	{
		// Load reCaptcha if needed
		if (Kohana::$config->load('useradmin')->captcha)
		{
			include Kohana::find_file('vendor', 'recaptcha/recaptchalib');
			$recaptcha_config = Kohana::$config->load('recaptcha');
			$recaptcha_error = null;
		}
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('User registration');
		// If user already signed-in
		if (Auth::instance()->logged_in() != false)
		{
			// redirect to the user account
			$this->request->redirect('user/profile');
		}
		// Load the view
		$view = View::factory('user/register');
		// If there is a post and $_POST is not empty
		if ($_POST)
		{
			// optional checks (e.g. reCaptcha or some other additional check)
			$optional_checks = true;
			// if configured to use captcha, check the reCaptcha result
			if (Kohana::$config->load('useradmin')->captcha)
			{
				$recaptcha_resp = recaptcha_check_answer(
					$recaptcha_config['privatekey'], 
					$_SERVER['REMOTE_ADDR'], 
					$_POST['recaptcha_challenge_field'], 
					$_POST['recaptcha_response_field']
				);
				if (! $recaptcha_resp->is_valid)
				{
					$optional_checks = false;
					$recaptcha_error = $recaptcha_resp->error;
					Message::add('error', __('The captcha text is incorrect, please try again.'));
				}
			}
			try
			{
				if (! $optional_checks)
				{
					throw new ORM_Validation_Exception("Invalid option checks");
				}
				Auth::instance()->register($_POST, TRUE);
				// sign the user in
				Auth::instance()->login($_POST['username'], $_POST['password']);
				// redirect to the user account
				$this->request->redirect(Session::instance()->get_once('returnUrl','user/profile'));
			}
			catch (ORM_Validation_Exception $e)
			{
				// Get errors for display in view
				// Note how the first param is the path to the message file (e.g. /messages/register.php)
				$errors = $e->errors('register');
				// Move external errors to main array, for post helper compatibility
				$errors = array_merge($errors, ( isset($errors['_external']) ? $errors['_external'] : array() ));
				$view->set('errors', $errors);
				// Pass on the old form values
				$_POST['password'] = $_POST['password_confirm'] = '';
				$view->set('defaults', $_POST);
			}
		}
		if (Kohana::$config->load('useradmin')->captcha)
		{
			$view->set('captcha_enabled', true);
			$view->set('recaptcha_html', recaptcha_get_html($recaptcha_config['publickey'], $recaptcha_error));
		}
		$this->template->content = $view;
	}

	/**
	 * Close the current user's account.
	 */
	public function action_unregister()
	{
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('Close user account');
		if (Auth::instance()->logged_in() == false)
		{
			// No user is currently logged in
			$this->request->redirect('user/login');
		}
		// get the user id
		$id = Auth::instance()->get_user()->id;
		$user = ORM::factory('user', $id);
		// KO3 ORM is lazy loading, which means we have to access a single field to actually have something happen.
		if ($user->id != $id)
		{
			// If the user is not the current user, redirect
			$this->request->redirect('user/profile');
		}
		// check for confirmation
		if (is_numeric($id) && isset($_POST['confirmation']) && $_POST['confirmation'] == 'Y')
		{
			if (Auth::instance()->logged_in())
			{
				// Log the user out, their account will no longer exist
				Auth::instance()->logout();
			}
			// Delete the user
			$user->delete($id);
			// Delete any associated identities
			DB::delete('user_identity')->where('user_id', '=', $id)
			                           ->execute();
			// message: save success
			Message::add('success', __('User deleted.'));
			$this->request->redirect(Session::instance()->get_once('returnUrl','user/profile'));
		}
		// display confirmation
		$this->template->content = View::factory('user/unregister')
			->set('id', $id)
			->set('data', array('username' => Auth::instance()->get_user()->username));
	}

	/**
	 * View: Login form.
	 */
	public function action_login()
	{
		// ajax login
		if ($this->request->is_ajax() && isset($_REQUEST['username'], $_REQUEST['password']))
		{
			$this->auto_render = false;
			$this->request->headers('Content-Type', 'application/json');
			if (Auth::instance()->logged_in() != 0)
			{
				$this->response->status(200);
				$this->template->content = $this->request->body('{ "success": "true" }');
				return;
			}
			else {
				if (Auth::instance()->login($_REQUEST['username'],$_REQUEST['password'],
                                            Arr::get($_REQUEST,'remember',false)!=false)
                ){
					$this->response->status(200);
					$this->template->content = $this->request->body('{ "success": "true" }');
					return;
				}
            }
			$this->response->status(500);
			$this->template->content = $this->request->body('{ "success": "false" }');
			return;
		}
		else
		{
			// set the template title (see Controller_App for implementation)
			$this->template->title = __('Login');
			// If user already signed-in
			if (Auth::instance()->logged_in() != 0)
			{
				// redirect to the user account
				$this->request->redirect(Session::instance()->get_once('returnUrl','user/profile'));
			}
			$view = View::factory('user/login');
			// If there is a post and $_POST is not empty
			if ($_REQUEST && isset($_REQUEST['username'], $_REQUEST['password']))
			{
				// Check Auth if the post data validates using the rules setup in the user model
				if (Auth::instance()->login($_REQUEST['username'], $_REQUEST['password'],
                                            Arr::get($_REQUEST,'remember',false)!=false)
                ){
					// redirect to the user account
					$this->request->redirect(Session::instance()->get_once('returnUrl','user/profile'));
					return;
				}
				else
				{
					$view->set('username', $_REQUEST['username']);
					// Get errors for display in view
					$validation = Validation::factory($_REQUEST)
						->rule('username', 'not_empty')
						->rule('password', 'not_empty');
					if ($validation->check())
					{
						$validation->error('password', 'invalid');
					}
					$view->set('errors', $validation->errors('login'));
				}
			}
			// allow setting the username as a get param
			if (isset($_GET['username']))
			{
				$view->set('username', htmlspecialchars($_GET['username']));
			}
			$providers = Kohana::$config->load('useradmin.providers');
			$view->set('facebook_enabled', 
			isset($providers['facebook']) ? $providers['facebook'] : false);
			$this->template->content = $view;
		}
	}

	/**
	 * Log the user out.
	 */
	public function action_logout()
	{
		// Sign out the user
		Auth::instance()->logout();
		// redirect to the user account and then the signin page if logout worked as expected
		$this->request->redirect(Session::instance()->get_once('returnUrl','user/profile'));
	}

	/**
	 * A basic implementation of the "Forgot password" functionality
	 */
	public function action_forgot()
	{
		// Password reset must be enabled in config/useradmin.php
		if (! Kohana::$config->load('useradmin')->email)
		{
			Message::add('error', 'Password reset via email is not enabled. Please contact the site administrator to reset your password.');
			$this->request->redirect('user/register');
		}
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('Forgot password');
		if (isset($_POST['reset_email']))
		{
			$user = ORM::factory('user')->where('email', '=', $_POST['reset_email'])->find();
			// admin passwords cannot be reset by email
			if (is_numeric($user->id) && ( $user->username != 'admin' ))
			{
				// send an email with the account reset token
				$user->reset_token = $user->generate_password(32);
				$user->save();
				$message = "You have requested a password reset. You can reset password to your account by visiting the page at:\n\n" .
				           ":reset_token_link\n\n" .
				           "If the above link is not clickable, please visit the following page:\n" .
				           ":reset_link\n\n" .
				           "and copy/paste the following Reset Token: :reset_token\nYour user account name is: :username\n";
				$mailer = Email::connect();
				// Create complex Swift_Message object stored in $message
				// MUST PASS ALL PARAMS AS REFS
				$subject = __('Account password reset');
				$to = $_POST['reset_email'];
				$from = Kohana::$config->load('useradmin')->email_address;
				$body = __($message, array(
					':reset_token_link' => URL::site('user/reset?reset_token='.$user->reset_token.'&reset_email='.$_POST['reset_email'], TRUE), 
					':reset_link' => URL::site('user/reset', TRUE), 
					':reset_token' => $user->reset_token, 
					':username' => $user->username
				));
				// FIXME: Test if Swift_Message has been found.
				$message_swift = Swift_Message::newInstance($subject, $body)->setFrom($from)->setTo($to);
				if ($mailer->send($message_swift))
				{
					Message::add('success', __('Password reset email sent.'));
					$this->request->redirect('user/login');
				}
				else
				{
					Message::add('failure', __('Could not send email.'));
				}
			}
			else 
				if ($user->username == 'admin')
				{
					Message::add('error', __('Admin account password cannot be reset via email.'));
				}
				else
				{
					Message::add('error', __('User account could not be found.'));
				}
		}
		$this->template->content = View::factory('user/reset/forgot');
	}

	/**
	 * A basic version of "reset password" functionality.
	 */
	function action_reset()
	{
		// Password reset must be enabled in config/useradmin.php
		if (! Kohana::$config->load('useradmin')->email)
		{
			Message::add('error', 'Password reset via email is not enabled. Please contact the site administrator to reset your password.');
			$this->request->redirect('user/register');
		}
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('Reset password');
		if (isset($_REQUEST['reset_token']) && isset($_REQUEST['reset_email']))
		{
			// make sure that the reset_token has exactly 32 characters (not doing that would allow resets with token length 0)
			if (( strlen($_REQUEST['reset_token']) == 32 ) && ( strlen(trim($_REQUEST['reset_email'])) > 1 ))
			{
				$user = ORM::factory('user')
					->where('email', '=', $_REQUEST['reset_email'])
					->and_where('reset_token', '=', $_REQUEST['reset_token'])
					->find();
				// The admin password cannot be reset by email
				if ($user->has('roles',ORM::factory('role',array('name'=>'admin'))))
				{
					Message::add('failure', __('The admin password cannot be reset by email.'));
				}
				else 
					if (is_numeric($user->id) && ( $user->reset_token == $_REQUEST['reset_token'] ))
					{
						$password = $user->generate_password();
						$user->password = $password;
						// This field does not exist in the default config:
						//               $user->failed_login_count = 0;
						$user->save();
						Message::add('success', __('Password reset.'));
						Message::add('success', '<p>' 
						                      . __('Your password has been reset to: ":password".', array(':password' => $password)) 
						                      . '</p><p>' 
						                      . __('Please log in below.') 
						                      . '</p>'
						);
						$this->request->redirect('user/login?username=' . $user->username);
					}
			}
		}
		$this->template->content = View::factory('user/reset/reset');
	}

	/**
	 * Allow the user to change their password.
	 */
	function action_change_password()
	{
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('Change password');
		$user = Auth::instance()->get_user();
		$id = $user->id;
		// load the content from view
		$view = View::factory('user/change_password');
		// save the data
		if (! empty($_POST) && is_numeric($id))
		{
			// editing requires that the username and email do not exist (EXCEPT for this ID)
			// If the post data validates using the rules setup in the user model
			$param_by_ref = array(
				'password' => $_POST['password'], 
				'password_confirm' => $_POST['password_confirm']
			);
			$validate = $user->change_password($param_by_ref, FALSE);
			if ($validate)
			{
				// message: save success
				Message::add('success', __('Values saved.'));
				// redirect and exit
				$this->request->redirect('user/index'); //index will redir ya whereever you need
				return;
			}
			else
			{
				// UNFORTUNATELY, it is NOT possible to get errors for display in view
				// since they will never be returned by change_password()
				Message::add('error', __('Password could not be changed, please make sure that the passwords match.'));
				// Pass on the old form values
				$_POST['password'] = $_POST['password_confirm'] = '';
				$view->set('defaults', $_POST);
			}
		}
		else
		{
			// load the information for viewing
			$view->set('data', $user->as_array());
		}
		$this->template->content = $view;
	}

	/**
	 * Redirect to the provider's auth URL
	 * @param string $provider
	 */
	function action_provider ($provider_name = null)
	{
		if (Auth::instance()->logged_in())
		{
			Message::add('success', 'Already logged in.');
			// redirect to the user account
			$this->request->redirect('user/profile');
		}
		$provider = Provider::factory($provider_name);
		if ($this->request->query('code') && $this->request->query('state'))
		{
			$this->action_provider_return($provider_name);
			return;
		}
		if (is_object($provider))
		{
			$this->request->redirect(
			$provider->redirect_url('/user/provider_return/' . $provider_name));
			return;
		}
		Message::add('error', 'Provider is not enabled; please select another provider or log in normally.');
		$this->request->redirect('user/login');
		return;
	}

	function action_associate($provider_name = null)
	{
	if ($this->request->query('code') && $this->request->query('state'))
	{
		$this->action_associate_return($provider_name);
		return;
	}
		if (Auth::instance()->logged_in())
		{
			if (isset($_POST['confirmation']) && $_POST['confirmation'] == 'Y')
			{
				$provider = Provider::factory($provider_name);
				if (is_object($provider))
				{
					$this->request->redirect($provider->redirect_url('/user/associate_return/' . $provider_name));
					return;
				}
				else
				{
					Message::add('error', 'Provider is not enabled; please select another provider or log in normally.');
					$this->request->redirect('user/login');
					return;
				}
			}
			else 
				if (isset($_POST['confirmation']))
				{
					Message::add('error', 'Please click Yes to confirm associating the account.');
					$this->request->redirect('user/profile');
					return;
				}
		}
		else
		{
			Message::add('error', 'You are not logged in.');
			$this->request->redirect('user/login');
			return;
		}
		$this->template->content = View::factory('user/associate')->set('provider_name', $provider_name);
	}

	/**
	 * Associate a logged in user with an account.
	 *
	 * Note that you should not trust the OAuth/OpenID provider-supplied email
	 * addresses. Yes, for Facebook, Twitter, Google and Yahoo the user is actually
	 * required to ensure that the email is in fact one that they control.
	 *
	 * However, with generic OpenID (and non-trusted OAuth providers) one can setup a
	 * rogue provider that claims the user owns a particular email address without
	 * actually owning it. So if you trust the email information, then you open yourself to
	 * a vulnerability since someone might setup a provider that claims to own your
	 * admin account email address and if you don't require the user to log in to
	 * associate their account they gain access to any account.
	 *
	 * TL;DR - the only information you can trust is that the identity string is
	 * associated with that user on that openID provider, you need the user to also
	 * prove that they want to trust that identity provider on your application.
	 *
	 */
	function action_associate_return($provider_name = null)
	{
		if (Auth::instance()->logged_in())
		{
			$provider = Provider::factory($provider_name);
			// verify the request
			if (is_object($provider) && $provider->verify())
			{
				$user = Auth::instance()->get_user();
				if ($user->loaded() && is_numeric($user->id))
				{
					if (Auth::instance()->logged_in() && Auth::instance()->get_user()->id == $user->id)
					{
						// found: "merge" with the existing user
						$user_identity = ORM::factory('user_identity');
						$user_identity->user_id = $user->id;
						$user_identity->provider = $provider_name;
						$user_identity->identity = $provider->user_id();
						if ($user_identity->check())
						{
							Message::add('success', __('Your user account has been associated with this provider.'));
							$user_identity->save();
							// redirect to the user account
							$this->request->redirect('user/profile');
							return;
						}
						else
						{
							Message::add('error', 'We were unable to associate this account with the provider. Please make sure that there are no other accounts using this provider identity, as each 3rd party provider identity can only be associated with one user account.');
							$this->request->redirect('user/login');
							return;
						}
					}
				}
			}
		}
		Message::add('error', 'There was an error associating your account with this provider.');
		$this->request->redirect('user/login');
		return;
	}

	/**
	 * Allow the user to login and register using a 3rd party provider.
	 */
	function action_provider_return($provider_name = null)
	{
		$provider = Provider::factory($provider_name);
		if (! is_object($provider))
		{
			Message::add('error', 'Provider is not enabled; please select another provider or log in normally.');
			$this->request->redirect('user/login');
			return;
		}
		// verify the request
		if ($provider->verify())
		{
			// check for previously connected user
			$uid = $provider->user_id();
			$user_identity = ORM::factory('user_identity')
				->where('provider', '=', $provider_name)
				->and_where('identity', '=', $uid)
				->find();
			if ($user_identity->loaded())
			{
				$user = $user_identity->user;
				if ($user->loaded() && $user->id == $user_identity->user_id && is_numeric($user->id))
				{
					// found, log user in
					Auth::instance()->force_login($user);
					// redirect to the user account
					$this->request->redirect('user/profile');
					return;
				}
			}
			// create new account
			if (! Auth::instance()->logged_in())
			{
				// Instantiate a new user
				$user = ORM::factory('user');
				// fill in values
				// generate long random password (maximum that passes validation is 42 characters)
				$password = $user->generate_password(42);
				$values = array(
					// get a unused username like firstname.surname or firstname.surname2 ...
					'username' => $user->generate_username(
						str_replace(' ', '.', $provider->name())
					), 
					'password' => $password, 
					'password_confirm' => $password
				);
				if (Valid::email($provider->email(), TRUE))
				{
					$values['email'] = $provider->email();
				}
				try
				{
					// If the post data validates using the rules setup in the user model
					$user->create_user($values, array(
						'username', 
						'password', 
						'email'
					));
					// Add the login role to the user (add a row to the db)
					$login_role = new Model_Role(array(
						'name' => 'login'
					));
					$user->add('roles', $login_role);
					// create user identity after we have the user id
					$user_identity = ORM::factory('user_identity');
					$user_identity->user_id  = $user->id;
					$user_identity->provider = $provider_name;
					$user_identity->identity = $provider->user_id();
					$user_identity->save();
					// sign the user in
					Auth::instance()->login($values['username'], $password);
					// redirect to the user account
					$this->request->redirect('user/profile');
				}
				catch (ORM_Validation_Exception $e)
				{
					if ($provider_name == 'twitter')
					{
						Message::add('error', 'The Twitter API does not support retrieving your email address; you will have to enter it manually.');
					}
					else
					{
						Message::add('error', 'We have successfully retrieved some of the data from your other account, but we were unable to get all the required fields. Please complete form below to register an account.');
					}
					// in case the data for some reason fails, the user will still see something sensible:
					// the normal registration form.
					$view = View::factory('user/register');
					$errors = $e->errors('register');
					// Move external errors to main array, for post helper compatibility
					$errors = array_merge($errors, ( isset($errors['_external']) ? $errors['_external'] : array() ));
					$view->set('errors', $errors);
					// Pass on the old form values
					$values['password'] = $values['password_confirm'] = '';
					$view->set('defaults', $values);
					if (Kohana::$config->load('useradmin')->captcha)
					{
						// FIXME: Is this the best place to include and use recaptcha?
						include Kohana::find_file('vendor', 'recaptcha/recaptchalib');
						$recaptcha_config = Kohana::$config->load('recaptcha');
						$recaptcha_error = null;
						$view->set('captcha_enabled', true);
						$view->set('recaptcha_html', recaptcha_get_html($recaptcha_config['publickey'], $recaptcha_error));
					}
					$this->template->content = $view;
				}
			}
			else
			{
				Message::add('error', 'You are logged in, but the email received from the provider does not match the email associated with your account.');
				$this->request->redirect('user/profile');
			}
		}
		else
		{
			Message::add('error', 'Retrieving information from the provider failed. Please register below.');
			$this->request->redirect('user/register');
		}
	}

	/**
	 * Media routing code. Allows lazy users to load images via Kohana. See also: init.php.
	 * I recommend just serving the files via apache, e.g. copy the public directory to your webroot.
	 */
	public function action_media()
	{
		// prevent auto render
		$this->auto_render = FALSE;
		// Generate and check the ETag for this file
		//		$this->request->check_cache(sha1($this->request->uri));
		// Get the file path from the request
		$file = Request::current()->param('file');
		$dir = Request::current()->param('dir');
		// Find the file extension
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		// Remove the extension from the filename
		$file = substr($file, 0, - ( strlen($ext) + 1 ));
		$file = Kohana::find_file('public', $dir . '/' . $file, $ext);
		if ($file)
		{
			// Send the file content as the response
			$this->response->body(file_get_contents($file));
		}
		else
		{
			// Return a 404 status
			$this->response->status(404);
		}
		// Set the proper headers to allow caching
		$this->response->headers('Content-Type', File::mime_by_ext($ext));
		$this->response->headers('Content-Length', (string) filesize($file));
		$this->response->headers('Last-Modified', date('r', filemtime($file)));
	}
}
