<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Default auth role
 *
 * @package    Kohana/Auth
 * @author     Kohana Team
 * @copyright  (c) 2007-2010 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class Model_Auth_Role extends ORM {

	// Relationships
	protected $_has_many = array('users' => array('through' => 'roles_users'));

	// Validation rules
	protected $_rules = array(
		'name' => array(
			'not_empty'  => NULL,
			'min_length' => array(4),
			'max_length' => array(32),
		),
		'description' => array(
			'max_length' => array(255),
		),
	);

} // End Auth Role Model