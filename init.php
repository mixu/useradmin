<?php defined('SYSPATH') or die('No direct access allowed.');

Route::set('user/provider', 'user/provider(/<provider>)', array('provider' => '.+'))
	->defaults(array(
		'controller' => 'user',
		'action'     => 'provider',
		'provider'       => NULL,
	));

Route::set('user/provider_return', 'user/provider_return(/<provider>)', array('provider' => '.+'))
	->defaults(array(
		'controller' => 'user',
		'action'     => 'provider_return',
		'provider'       => NULL,
	));

// Static file serving (CSS, JS, images)
Route::set('css', '<dir>(/<file>)', array('file' => '.+', 'dir' => '(css|img)'))
   ->defaults(array(
		'controller' => 'user',
		'action'     => 'media',
		'file'       => NULL,
		'dir'       => NULL,
	));

/**
 * Set the default database to test database when testing env is detectd
 */
if( Unittest_tests::enabled() && ( ( isset($_SERVER['REQUEST_URI']) && strtolower( substr( $_SERVER['REQUEST_URI'], 0, 13) ) == '/unittest/run') || Kohana::$is_cli ) && Kohana::config('database.unittest'))
{
	// FIXME remove URI check for config test database
	Kohana::config('database')->set( "default", Kohana::config('database.'.Kohana::config('unittest.db_connection')) );
}
