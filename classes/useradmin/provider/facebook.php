<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Oauth 2.0 using Facebook's own API class.
 * If Oauth 2.0 becomes more common, a base class could be created to abstract away from Facebook.
 */
class Useradmin_Provider_Facebook extends Provider {

	private $facebook = null;

	private $me = null;

	private $uid = null;

	public function __construct()
	{
		include_once Kohana::find_file('vendor', 'facebook/src/facebook');
		// Create our Facebook SDK instance.
		$this->facebook = new Facebook(array(
			'appId'  => Kohana::$config->load('facebook')->app_id, 
			'secret' => Kohana::$config->load('facebook')->secret, 
			'cookie' => true // enable optional cookie support
		));
	}

	/**
	 * Get the URL to redirect to.
	 * @return string
	 */
	public function redirect_url($return_url)
	{
		return $this->facebook->getLoginUrl(array(
			'next'       => URL::site($return_url, true), 
			'cancel_url' => URL::site($return_url, true), 
			'req_perms'  => 'email'
		));
	}

	/**
	 * Verify the login result and do whatever is needed to access the user data from this provider.
	 * @return bool
	 */
	public function verify()
	{
		if ($this->facebook->getSession())
		{
			try
			{
				$this->uid = $this->facebook->getUser();
				// read user info as array from Graph API
				$this->me = $this->facebook->api('/me');
			}
			catch (FacebookApiException $e)
			{
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * Attempt to get the provider user ID.
	 * @return mixed
	 */
	public function user_id()
	{
		return $this->uid;
	}

	/**
	 * Attempt to get the email from the provider (e.g. for finding an existing account to associate with).
	 * @return string
	 */
	public function email()
	{
		if (isset($this->me['email']))
		{
			return $this->me['email'];
		}
		return '';
	}

	/**
	 * Get the full name (firstname surname) from the provider.
	 * @return string
	 */
	public function name()
	{
		if (isset($this->me['first_name']))
		{
			return $this->me['first_name'] . ' ' . $this->me['last_name'];
		}
		return '';
	}
}