<?php defined('SYSPATH') or die('No direct script access.');

return array(
    /**
     * Toggle Facebook support: if set, then users can log in using Facebook.
     *
     * Setup:
     * - You need to run the following query to add the facebook_user_id column:
     * ALTER TABLE `users` ADD `facebook_user_id` BIGINT( 20 ) NULL AFTER `password`
     * - You must register your app and add the information in /config/facebook.php
     * - You must have the Facebook SDK at /vendors/facebook/src/facebook.php (bundled in the default repo)
     *
     */
    'facebook' => false,
    /**
     * Toggle email support: if set, then users (except admins) can reset user accounts via email.
     * They will be sent an email with a reset token, which they enter, then their password will be reset to a new random password.
     *
     * Setup:
     * - You must have the Kohana-email module enabled (bundled in default repo)
     */
    'email' => true,
    /* change this to the email address you want the password reset emails to come from. */
    'email_address' => 'test@example.com',
    /**
     * Toggle reCaptcha support: if set, then during registration the user is shown
     * a reCaptcha which they must answer correctly.
     *
     * Setup
     * - You must have the reCaptcha library (e.g. http://recaptcha.net) in your vendors directory. (bundled in the default repo)
     * - You must set the private and public key in /config/recaptcha.php from https://www.google.com/recaptcha/admin/create
     */
    'captcha' => false,
    );