<?php
defined('SYSPATH') or die('No direct access allowed.');

/**
 * AppForm class: generate application-specific markup for forms.
 * 
 * @author Mikito Takada
 * @package default
 * @version 1.0
 *
 */
class Useradmin_Appform {

	/**
	 * Set errors (fieldname => error) from Validation.
	 * @var array
	 */
	public $errors;

	/**
	 * Set default values (fieldname => default value). In the fields, use NULL if you want to use the default value.
	 * @var array
	 */
	public $defaults;

	/**
	 * Set actual values (fieldname => actual value). 
	 * @var array
	 */
	public $values;

	/**
	 * CSS class strings for messages. You can override these.
	 * @var string
	 */
	public $info_class = 'info';

	public $error_class = 'error';

	/**
	 * Add a class to the input attributes array.
	 * @param array $attributes
	 * @param string $class
	 * @return array
	 */
	private static function add_class ($attributes, $class)
	{
		if (isset($attributes['class']))
		{
			$attributes['class'] .= ' ' . $class;
		}
		else
		{
			$attributes['class'] = $class;
		}
		return $attributes;
	}

	/**
	 * Load values for errors, defaults and values from AppForm instance.
	 * @param <type> $name
	 * @param <type> $value
	 * @param <type> $attributes 
	 */
	private function load_values ($name, &$value, &$attributes)
	{
		if (isset($this->errors[$name]))
		{
			$attributes = Appform::add_class($attributes, 'error');
		}
		if (isset($this->defaults[$name]) && $value == NULL)
		{
			$value = $this->defaults[$name];
		}
		if (isset($this->values[$name]) && $value == NULL)
		{
			$value = $this->values[$name];
		}
	}

	/**
	 * Add alert span for error or field info
	 * 
	 * @param string $errorName $this->errors[$name]
	 * @param string $attrInfo  $attributes['info']
	 * @return string
	 */
	private function addAlertSpan($errorName, $attributes = NULL)
	{
		if (isset($errorName))
		{
			$result = '<span class="error">' 
			        . ucfirst($errorName) 
			        . '</span>';
		}
		else 
		{
			if (isset($attributes['info']))
			{
				// else add info span
				$result = '<span class="'.$this->info_class.'">' 
				        . $attributes['info'] 
				        . '</span>';
			}
		}
		return (string) (isset($result))?$result:'';
	}
	
	/**
	 * Generates an opening HTML form tag.
	 *
	 * @param   string  form action
	 * @param   array   html attributes
	 * @return  string
	 */
	public function open ($action = NULL, array $attributes = NULL)
	{
		return Kohana_Form::open($action, $attributes);
	}

	/**
	 * Creates the closing form tag.
	 *
	 * @return  string
	 */
	public function close ()
	{
		return Kohana_Form::close();
	}

	/**
	 * Creates a form input. If no type is specified, a "text" type input will
	 * be returned.
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   html attributes
	 * @return  string
	 */
	public function input($name, $value = NULL, array $attributes = NULL)
	{
		$attributes = Appform::add_class($attributes, 'text');
		$this->load_values($name, $value, $attributes);
		return '<li>'
			. Kohana_Form::input($name, $value, $attributes)
			. $this->addAlertSpan((isset($this->errors[$name])?$this->errors[$name]:NULL), $attributes)
			. '</li>';
	}

	/**
	 * Creates a hidden form input.
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   html attributes
	 * @return  string
	 */
	public function hidden($name, $value = NULL, array $attributes = NULL)
	{
		$this->load_values($name, $value, $attributes);
		return Kohana_Form::hidden($name, $value, $attributes);
	}

	/**
	 * Creates a password form input.
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   html attributes
	 * @return  string
	 */
	public function password($name, $value = NULL, array $attributes = NULL)
	{
		$attributes = Appform::add_class($attributes, 'password');
		$this->load_values($name, $value, $attributes);
		return '<li>'
			. Kohana_Form::password($name, $value, $attributes)
			. $this->addAlertSpan((isset($this->errors[$name])?$this->errors[$name]:NULL), $attributes)
			. '</li>';
	}

	/**
	 * Creates a file upload form input.
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   html attributes
	 * @return  string
	 */
	public function file($name, array $attributes = NULL)
	{
		$this->load_values($name, $dummy, $attributes);
		return '<li>'
			. Kohana_Form::file($name, $attributes)
			. $this->addAlertSpan((isset($this->errors[$name])?$this->errors[$name]:NULL), $attributes)
			. '</li>';
	}

	/**
	 * Creates a checkbox form input.
	 *
	 * @param   string   input name
	 * @param   string   input value
	 * @param   boolean  checked status
	 * @param   array    html attributes
	 * @return  string
	 */
	public function checkbox($name, $value = NULL, $checked = FALSE, array $attributes = NULL)
	{
		$this->load_values($name, $value, $attributes);
		return '<li>'
			. Kohana_Form::checkbox($name, $value, $checked, $attributes)
			. $this->addAlertSpan((isset($this->errors[$name])?$this->errors[$name]:NULL), $attributes)
			. '</li>';
	}

	/**
	 * Creates a radio form input.
	 *
	 * @param   string   input name
	 * @param   string   input value
	 * @param   boolean  checked status
	 * @param   array    html attributes
	 * @return  string
	 */
	public function radio($name, $value = NULL, $checked = FALSE, array $attributes = NULL)
	{
		$this->load_values($name, $value, $attributes);
		return '<li>'
			. Kohana_Form::radio($name, $value, $checked, $attributes)
			. $this->addAlertSpan((isset($this->errors[$name])?$this->errors[$name]:NULL), $attributes)
			. '</li>';
	}

	/**
	 * Creates a textarea form input.
	 *
	 * @param   string   textarea name
	 * @param   string   textarea body
	 * @param   array    html attributes
	 * @param   boolean  encode existing HTML characters
	 * @return  string
	 */
	public function textarea($name, $body = '', array $attributes = NULL, $double_encode = TRUE)
	{
		$this->load_values($name, $body, $attributes);
		return '<li>'
			. Kohana_Form::textarea($name, $body, $attributes, $double_encode)
			. $this->addAlertSpan((isset($this->errors[$name])?$this->errors[$name]:NULL), $attributes)
			. '</li>';
	}

	/**
	 * Creates a select form input.
	 *
	 * @param   string   input name
	 * @param   array    available options
	 * @param   string   selected option
	 * @param   array    html attributes
	 * @return  string
	 */
	public function select($name, array $options = NULL, $selected = NULL, array $attributes = NULL)
	{
		$this->load_values($name, $selected, $attributes);
		return '<li>'
			. Kohana_Form::select($name, $options, $selected, $attributes)
			. $this->addAlertSpan((isset($this->errors[$name])?$this->errors[$name]:NULL), $attributes)
			. '</li>';
	}

	/**
	 * Creates a submit form input.
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   html attributes
	 * @return  string
	 */
	public function submit($name, $value, array $attributes = NULL)
	{
		return Kohana_Form::submit($name, $value, 
		Appform::add_class($attributes, 'submit'));
	}

	/**
	 * Creates a button form input. Note that the body of a button is NOT escaped,
	 * to allow images and other HTML to be used.
	 *
	 * @param   string  input name
	 * @param   string  input value
	 * @param   array   html attributes
	 * @return  string
	 */
	public function button($name, $body, array $attributes = NULL)
	{
		return Kohana_Form::button($name, $body, $attributes);
	}

	/**
	 * Creates a form label.
	 *
	 * @param   string  target input
	 * @param   string  label text
	 * @param   array   html attributes
	 * @return  string
	 */
	public function label($input, $text = NULL, array $attributes = NULL)
	{
		return Kohana_Form::label($input, $text, $attributes);
	}
}
