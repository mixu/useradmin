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