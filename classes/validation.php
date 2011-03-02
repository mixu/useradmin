<?php

class Validation extends Kohana_Validation {
	/**
	 * Field filters
	 * @var array
	 */
	protected $_filters = array();

	/**
	 * Overwrites or appends filters to a field. Each filter will be executed once.
	 * All rules must be valid PHP callbacks.
	 *
	 *     // Run trim() on all fields
	 *     $validation->filter(TRUE, 'trim');
	 *
	 * @param   string  field name
	 * @param   mixed   valid PHP callback
	 * @param   array   extra parameters for the filter
	 * @return  $this
	 */
	public function filter($field, $filter, array $params = NULL)
	{
		if ($field !== TRUE AND ! isset($this->_labels[$field]))
		{
			// Set the field label to the field name
			$this->_labels[$field] = preg_replace('/[^\pL]+/u', ' ', $field);
		}

		// Store the filter and params for this rule
		$this->_filters[$field][$filter] = (array) $params;

		return $this;
	}

	/**
	 * Add filters using an array.
	 *
	 * @param   string  field name
	 * @param   array   list of functions or static method name
	 * @return  $this
	 */
	public function filters($field, array $filters)
	{
		foreach ($filters as $filter => $params)
		{
			$this->filter($field, $filter, $params);
		}

		return $this;
	}

	/**
	 * Executes all validation rules. This should
	 * typically be called within an if/else block.
	 *
	 *     if ($validation->check())
	 *     {
	 *          // The data is valid, do something here
	 *     }
	 *
	 * @param   boolean	$keep_original If set to true, apply filters to the items to check, but return the values intact as they were entered. Defaults to false
	 * @return  boolean
	 */
	public function check($keep_original = false)
	{
		if (Kohana::$profiling === TRUE)
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Validation', __FUNCTION__);
		}

		// New data set
		$data = $this->_errors = array();

		// Store the original data because this class should not modify it post-validation
		$original = $this->getArrayCopy();

		// Get a list of the expected fields
		$expected = Arr::merge(array_keys($original), array_keys($this->_labels));

		// Import the rules locally
		$rules     = $this->_rules;
		$filters   = $this->_filters;

		foreach ($expected as $field)
		{
			if (isset($this[$field]))
			{
				// Use the submitted value
				$data[$field] = $this[$field];
			}
			else
			{
				// No data exists for this field
				$data[$field] = NULL;
			}
			
			if (isset($filters[TRUE]))
			{
				if ( ! isset($filters[$field]))
				{
					// Initialize the filters for this field
					$filters[$field] = array();
				}

				// Append the filters
				$filters[$field] = array_merge($filters[TRUE], $filters[$field]);
			}


			if (isset($rules[TRUE]))
			{
				if ( ! isset($rules[$field]))
				{
					// Initialize the rules for this field
					$rules[$field] = array();
				}

				// Append the rules
				$rules[$field] = array_merge($rules[$field], $rules[TRUE]);
			}
		}

		// Overload the current array with the new one
		$this->exchangeArray($data);

		// Remove the rules that apply to every field
		unset($rules[TRUE], $filters[TRUE]);

		// Bind the validation object to :validation
		$this->bind(':validation', $this);

		foreach ($filters as $field => $set)
		{
			// Get the field value
			$value = $this[$field];

			foreach ($set as $filter => $params)
			{
				// Add the field value to the parameters
				array_unshift($params, $value);

				if (strpos($filter, '::') === FALSE)
				{
					// Use a function call
					$function = new ReflectionFunction($filter);

					// Call $function($this[$field], $param, ...) with Reflection
					$value = $function->invokeArgs($params);
				}
				else
				{
					// Split the class and method of the rule
					list($class, $method) = explode('::', $filter, 2);

					// Use a static method call
					$method = new ReflectionMethod($class, $method);

					// Call $Class::$method($this[$field], $param, ...) with Reflection
					$value = $method->invokeArgs(NULL, $params);
				}
			}

			// Set the filtered value
			$this[$field] = $value;
		}

		// Execute the rules
		foreach ($rules as $field => $set)
		{
			// Get the field value
			$value = $this[$field];

			// Bind the field name and value to :field and :value respectively
			$this->bind(array
			(
				':field' => $field,
				':value' => $value,
			));

			foreach ($set as $array)
			{
				// Rules are defined as array($rule, $params)
				list($rule, $params) = $array;

				foreach ($params as $key => $param)
				{
					if (is_string($param) AND array_key_exists($param, $this->_bound))
					{
						// Replace with bound value
						$params[$key] = $this->_bound[$param];
					}
				}

				// Default the error name to be the rule (except array and lambda rules)
				$error_name = $rule;

				if (is_array($rule))
				{
					// This is an array callback, the method name is the error name
					$error_name = $rule[1];
					$passed = call_user_func_array($rule, $params);
				}
				elseif ( ! is_string($rule))
				{
					// This is a lambda function, there is no error name (errors must be added manually)
					$error_name = FALSE;
					$passed = call_user_func_array($rule, $params);
				}
				elseif (method_exists('Valid', $rule))
				{
					// Use a method in this object
					$method = new ReflectionMethod('Valid', $rule);

					// Call static::$rule($this[$field], $param, ...) with Reflection
					$passed = $method->invokeArgs(NULL, $params);
				}
				elseif (strpos($rule, '::') === FALSE)
				{
					// Use a function call
					$function = new ReflectionFunction($rule);

					// Call $function($this[$field], $param, ...) with Reflection
					$passed = $function->invokeArgs($params);
				}
				else
				{
					// Split the class and method of the rule
					list($class, $method) = explode('::', $rule, 2);

					// Use a static method call
					$method = new ReflectionMethod($class, $method);

					// Call $Class::$method($this[$field], $param, ...) with Reflection
					$passed = $method->invokeArgs(NULL, $params);
				}

				// Ignore return values from rules when the field is empty
				if ( ! in_array($rule, $this->_empty_rules) AND ! Valid::not_empty($value))
					continue;

				if ($passed === FALSE AND $error_name !== FALSE)
				{
					// Add the rule to the errors
					$this->error($field, $error_name, $params);

					// This field has an error, stop executing rules
					break;
				}
			}
		}

		if ($keep_original)
		{
			// Restore the data to its original form
			$this->exchangeArray($original);
		}

		if (isset($benchmark))
		{
			// Stop benchmarking
			Profiler::stop($benchmark);
		}

		return empty($this->_errors);
	}
}