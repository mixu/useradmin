<?php defined('SYSPATH') or die('No direct access allowed.');

class Useradmin_Message {

	public static function add($type, $message)
	{
		// get session messages
		$messages = Session::instance()->get('messages');
		// initialize if necessary
		if (! is_array($messages))
		{
			$messages = array();
		}
		// append to messages
		$messages[$type][] = $message;
		// set messages
		Session::instance()->set('messages', $messages);
	}

	public static function count()
	{
		return count(Session::instance()->get('messages'));
	}

	public static function output()
	{
		$str = '';
		$messages = Session::instance()->get('messages');
		Session::instance()->delete('messages');
		if (! empty($messages))
		{
			foreach ($messages as $type => $messages)
			{
				foreach ($messages as $message)
				{
					$str .= '<div class="' . $type . '">' . $message . '</div>';
				}
			}
		}
		return $str;
	}
}