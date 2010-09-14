<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Auth_Role extends ORM {

	protected $_has_many = array('users' => array('through' => 'roles_users'));

	protected $_rules = array
	(
		'name'		=> array
		(
			'not_empty'	=> NULL,
			'min_length'	=> array(4),
			'max_length'	=> array(32),
		),
		'description'	=> array
		(
			'max_length'	=> array(255),
		),
	);

} // End Auth Role Model