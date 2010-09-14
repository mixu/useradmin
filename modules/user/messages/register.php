<?php defined('SYSPATH') or die('No direct script access.');

// see /system/mesages/validation.php for the defaults for each rule. These can be overridden on a per-field basis.
return array(
      'username' => array(
         'username_available' => 'This username is already registered, please choose another one.',
       ),
      'email' => array(
         'email_available' => 'This email address is already in use.',
       ),
      'password' => array(
         'matches'      => 'The password and password confirmation are different.',
      ),
      'password_confirm' => array(
         'matches'      => 'The password and password confirmation are different.',
      ),
);

