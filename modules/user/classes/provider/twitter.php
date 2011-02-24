<?php
class Provider_Twitter extends Provider_OAuth {
   /**
    * Data storage
    * @var int
    */
   private $uid = null;
   private $data = null;

   public function  __construct() {
      parent::__construct('twitter');
   }

   /**
    * Verify the login result and do whatever is needed to access the user data from this provider.
    * @return bool
    */
   public function verify() {
      // create token
      $request_token = OAuth_Token::factory('request', array(
         'token' => Session::instance()->get('oauth_token'),
         'secret' => Session::instance()->get('oauth_token_secret'),
      ));

		// Store the verifier in the token
		$request_token->verifier($_REQUEST['oauth_verifier']);

		// Exchange the request token for an access token
		$access_token = $this->provider->access_token($this->consumer, $request_token);

		if ($access_token AND $access_token->name === 'access') {
         // @link  http://dev.twitter.com/doc/get/account/verify_credentials
         $request = OAuth_Request::factory('resource', 'GET', 'http://api.twitter.com/1/account/verify_credentials.json', array(
            'oauth_consumer_key' => $this->consumer->key,
            'oauth_token'        => $access_token->token,
         ));

         // Sign the request using only the consumer, no token is available yet
         $request->sign(new OAuth_Signature_HMAC_SHA1(), $this->consumer, $access_token);
         // decode and store data
         $data = json_decode($request->execute(), true);
         $this->uid = $data['id'];
         $this->data = $data;
         return true;
		} else {
			return false;
		}
   }

   /**
    * Attempt to get the provider user ID.
    * @return mixed
    */
   public function user_id() {
      return $this->uid;
   }

   /**
    * Attempt to get the email from the provider (e.g. for finding an existing account to associate with).
    * @return string
    */
   public function email() {
      if(isset($this->data['email'])) {
         return $this->data['email'];
      }
      return '';
   }

   /**
    * Get the full name (firstname surname) from the provider.
    * @return string
    */
   public function name() {
      if(isset($this->data['name'])) {
         return $this->data['name'];
      } else if(isset($this->data['screen_name'])) {
         return $this->data['screen_name'];
      }
      return '';
   }
}
