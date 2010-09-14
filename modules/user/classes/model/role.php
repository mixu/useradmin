<?php

/**
 *
 * @author Mikito Takada
 * @package default
 * @version 1.0
 */

class Model_Role extends ORM {

   protected $_table_name = 'roles';
   protected $_has_many = array('users' => array('through' => 'roles_users'), );


}