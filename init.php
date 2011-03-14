<?php
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

/**
 * Set the default database to test database when testing env is detectd
 */
if( Unittest_tests::enabled() && ( strtolower( substr( $_SERVER['REQUEST_URI'], 0, 13) ) == '/unittest/run' || Kohana::$is_cli ) && Kohana::config('database.unittest'))
{
	// FIXME remove URI check for config test database
	Kohana::config('database')->set( "default", Kohana::config('database.unittest') );
}