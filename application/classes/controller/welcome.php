<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Welcome extends Controller {

	public function action_index()
	{
		$this->request->response = '<h1>Hello, world!</h1>'
              .'<p>Build your application as usual. To try out the Kohana Auth module I\'ve build, go to: '.Html::anchor('/user/').'</p>'
              .'<p>The default username is admin and the password is also admin.</p>';
	}

} // End Welcome
