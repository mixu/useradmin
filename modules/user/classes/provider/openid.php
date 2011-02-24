<?php

/**
 * OpenID provider using LightOpenID
 */
class Provider_OpenID extends Provider {

  protected static $config = array(
      'google' => array( 'url' => 'https://www.google.com/accounts/o8/id' ),
      'yahoo' => array( 'url' => 'https://me.yahoo.com/' ),
     );

   private $provider = null;
   private $provider_name = '';
   private $uid = null;
   private $data = null;

   public function  __construct($provider_name) {
      include_once Kohana::find_file('vendor', 'lightopenid/openid');
		$this->provider = new LightOpenID;
      $this->provider_name = $provider_name;
   }

   /**
    * Get the URL to redirect to.
    * @return string
    */
   public function redirect_url($return_url) {
		$this->provider->identity = Provider_OpenID::$config[$this->provider_name]['url'];
		$this->provider->returnUrl = URL::site($return_url, true);
		$this->provider->required = array('namePerson', 'namePerson/first', 'namePerson/last', 'contact/email');
      return $this->provider->authUrl();
   }

   /**
    * Verify the login result and do whatever is needed to access the user data from this provider.
    * @return bool
    */
   public function verify() {
		if ($this->provider->validate()) {
         $this->uid = $this->provider->identity;
         $this->data = $this->provider->getAttributes();
			return true;
		}
		return false;
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
      if(isset($this->data['contact/email'])) {
         return $this->data['contact/email'];
      }
      return '';
   }

   /**
    * Get the full name (firstname surname) from the provider.
    * @return string
    */
   public function name() {
      // YAHOO supports this ax
      if(isset($this->data['namePerson'])) {
         return $this->data['namePerson'];
      }
      // GOOGLE uses these...
      if(isset($this->data['namePerson/first']) && isset($this->data['namePerson/last'])) {
         return $this->data['namePerson/first'].' '.$this->data['namePerson/last'];
      }
      return '';
   }
}