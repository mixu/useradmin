<?php

return array(
    /*
     * Toggle Facebook support: if set, then users can log in using Facebook.
     *
     * Setup:
     * - You need to run the following query to add the facebook_user_id column:
     * ALTER TABLE `users` ADD `facebook_user_id` BIGINT( 20 ) NULL AFTER `password`
     * - You must register your app and add the information in /config/facebook.php
     * - You must have the Facebook SDK at /vendors/facebook/src/facebook.php (bundled in the default repo)
     *
     */
    'facebook' => true,
    /*
     * Toggle email support: if set, then users (except admins) can reset user accounts via email.
     * They will be sent an email with a reset token, which they enter, then their password will be reset to a new random password.
     *
     * Setup:
     * - You need to have the Kohana-email module enabled (bundled in default repo)
     */
    'email' => true,
    // change this to the email address you want the password resets to come from.
    'email_address' => 'test@example.com'
    );