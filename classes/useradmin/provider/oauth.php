<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Oauth 1.0 using Kohana's bundled OAuth module.
 *
 * Kohana's bundled OAuth module supports Twitter (and Google) as providers.
 * 
 */
abstract class Useradmin_Provider_OAuth extends Provider {

	/**
	 * Privately used for OAuth requests
	 * @var OAuth_Provider
	 */
	protected $provider;

	protected $provider_name;

	/**
	 * Privately used for OAuth requests
	 * @var OAuth_Consumer
	 */
	protected $consumer;

	public function __construct($provider)
	{
		$this->provider_name = $provider;
		// Load the configuration for this provider
		$config = Kohana::$config->load('oauth.' . $this->provider_name);
		// Create an consumer from the config
		$this->consumer = OAuth_Consumer::factory($config);
		// Load the provider
		$this->provider = OAuth_Provider::factory($this->provider_name);
	}

	/**
	 * Get the URL to redirect to.
	 * @return string
	 */
	public function redirect_url($return_url)
	{
		// Add the callback URL to the consumer
		$this->consumer->callback(URL::site($return_url, true));
		// Get a request token for the consumer
		$request_token = $this->provider->request_token($this->consumer);
		Session::instance()->set('oauth_token', $request_token->token);
		Session::instance()->set('oauth_token_secret', $request_token->secret);
		// Redirect to the twitter login page
		return $this->provider->authorize_url($request_token);
	}
}