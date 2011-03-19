<?php defined('SYSPATH') or die('No direct script access.');

return array(
    /**
     * The number of failed logins allowed can be specified here:
     * If the user mistypes their password X times, then they will not be permitted to log in during the jail time.
     * This helps prevent brute-force attacks.
     */
   'auth' => array(
      /**
       * Define the maximum failed attempts to login
       * set 0 to disable the login jail
       */
      'max_failed_logins' => 5,
      /**
       * Define the time that user who archive the max_failed_logins will need to 
       * wait before his next attempt
       */
      'login_jail_time' => "5 minutes",
    ),
    /**
     * 3rd party providers supported/allowed.
     */
    'providers' => array(
       /**
        * Toggle Facebook support: if set, then users can log in using Facebook.
        *
        * Setup:
        * - You need the extra table from schema.sql for storing 3rd party identifiers
        * - You must register your app with FB and add the information in /config/facebook.php
        * - You must have the Facebook SDK at /vendors/facebook/src/facebook.php (bundled in the default repo)
        *
        */
       'facebook' => true,
       /**
        * Toggle Twitter support: if set, users can log in using Twitter
        *
        * Setup:
        * - You need the extra table from schema.sql for storing 3rd party identifiers
        * - You must register your app with Twitter and add the information in /config/oauth.php (Kohana-Oauth's config)
        * - You must enable the Kohana Core oauth module
        */
       'twitter' => true,
       /**
        * Toggle Google support: if set, users can log in using their Google account.
        *
        * Setup:
        * - You need the extra table from schema.sql for storing 3rd party identifiers
        * - You must have LightOpenID in /vendors/lightopenid/openid.php (bundled in the repo)
        */
       'google' => true,
       /**
        * Toggle Yahoo support: if set, users can log in using their Yahoo account.
        *
        * Setup:
        * - You need the extra table from schema.sql for storing 3rd party identifiers
        * - You must have LightOpenID in /vendors/lightopenid/openid.php (bundled in the repo)
        */
       'yahoo' => true,
    ),
    /**
     * Toggle email support: if set, then users (except admins) can reset user accounts via email.
     * They will be sent an email with a reset token, which they enter, then their password will be reset to a new random password.
     *
     * Setup:
     * - You must have the Kohana-email module enabled (bundled in default repo)
     */
    'email' => true,
    /* change this to the email address you want the password reset emails to come from. */
    'email_address' => 'no-response@example.com',
    /**
     * Toggle reCaptcha support: if set, then during registration the user is shown
     * a reCaptcha which they must answer correctly (unless they are using one of the 3rd party accounts).
     *
     * Setup
     * - You must have the reCaptcha library (e.g. http://recaptcha.net) in your vendors directory. (bundled in the default repo)
     * - You must set the private and public key in /config/recaptcha.php from https://www.google.com/recaptcha/admin/create
     */
    'captcha' => false,
    );
