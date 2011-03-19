<?php defined('SYSPATH') or die('No direct script access.');

// see /system/mesages/validation.php for the defaults for each rule. These can be overridden on a per-field basis.
return array(
      'username' => array(
         'not_empty' => 'Username must not be empty.',
         'invalid' => 'Password or username is incorrect.',
       ),
      'password' => array(
         'not_empty'      => 'Password must not be empty.',
         'invalid' => 'Password or username is incorrect.',
      ),
);

