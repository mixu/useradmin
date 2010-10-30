<?php defined('SYSPATH') or die('No direct script access.');
/**
 * [Kohana Cache](api/Kohana_Cache) Eaccelerator driver. Provides an opcode based
 * driver for the Kohana Cache library.
 * 
 * ### Configuration example
 * 
 * Below is an example of an _eaccelerator_ server configuration.
 * 
 *     return array(
 *          'eaccelerator' => array(                          // Driver group
 *                  'driver'         => 'eaccelerator',         // using Eaccelerator driver
 *           ),
 *     )
 * 
 * In cases where only one cache group is required, if the group is named `default` there is
 * no need to pass the group name when instantiating a cache instance.
 * 
 * #### General cache group configuration settings
 * 
 * Below are the settings available to all types of cache driver.
 * 
 * Name           | Required | Description
 * -------------- | -------- | ---------------------------------------------------------------
 * driver         | __YES__  | (_string_) The driver type to use
 * 
 * ### System requirements
 * 
 * *  Kohana 3.0.x
 * *  PHP 5.2.4 or greater
 * *  Eaccelerator PHP extension
 * 
 * @package    Kohana
 * @category   Cache
 * @author     Kohana Team
 * @copyright  (c) 2009-2010 Kohana Team
 * @license    http://kohanaphp.com/license
 */
class Kohana_Cache_Eaccelerator extends Cache {

	/**
	 * Check for existence of the eAccelerator extension This method cannot be invoked externally. The driver must
	 * be instantiated using the `Cache::instance()` method.
	 *
	 * @param  array     configuration
	 * @throws Kohana_Cache_Exception
	 */
	protected function __construct(array $config)
	{
		if ( ! extension_loaded('eaccelerator'))
		{
			throw new Kohana_Cache_Exception('PHP eAccelerator extension is not available.');
		}

		parent::__construct($config);
	}

	/**
	 * Retrieve a cached value entry by id.
	 * 
	 *     // Retrieve cache entry from eaccelerator group
	 *     $data = Cache::instance('eaccelerator')->get('foo');
	 * 
	 *     // Retrieve cache entry from eaccelerator group and return 'bar' if miss
	 *     $data = Cache::instance('eaccelerator')->get('foo', 'bar');
	 *
	 * @param   string   id of cache to entry
	 * @param   string   default value to return if cache miss
	 * @return  mixed
	 * @throws  Kohana_Cache_Exception
	 */
	public function get($id, $default = NULL)
	{
		return (($data = eaccelerator_get($this->_sanitize_id($id))) === FALSE) ? $default : $data;
	}

	/**
	 * Set a value to cache with id and lifetime
	 * 
	 *     $data = 'bar';
	 * 
	 *     // Set 'bar' to 'foo' in eaccelerator group, using default expiry
	 *     Cache::instance('eaccelerator')->set('foo', $data);
	 * 
	 *     // Set 'bar' to 'foo' in eaccelerator group for 30 seconds
	 *     Cache::instance('eaccelerator')->set('foo', $data, 30);
	 *
	 * @param   string   id of cache entry
	 * @param   string   data to set to cache
	 * @param   integer  lifetime in seconds
	 * @return  boolean
	 */
	public function set($id, $data, $lifetime = NULL)
	{
		if ($lifetime === NULL)
		{
			$lifetime = time() + Arr::get($this->_config, 'default_expire', Cache::DEFAULT_EXPIRE);
		}

		return eaccelerator_put($this->_sanitize_id($id), $data, $lifetime);
	}

	/**
	 * Delete a cache entry based on id
	 * 
	 *     // Delete 'foo' entry from the eaccelerator group
	 *     Cache::instance('eaccelerator')->delete('foo');
	 *
	 * @param   string   id to remove from cache
	 * @return  boolean
	 */
	public function delete($id)
	{
		return eaccelerator_rm($this->_sanitize_id($id));
	}

	/**
	 * Delete all cache entries.
	 * 
	 * Beware of using this method when
	 * using shared memory cache systems, as it will wipe every
	 * entry within the system for all clients.
	 * 
	 *     // Delete all cache entries in the eaccelerator group
	 *     Cache::instance('eaccelerator')->delete_all();
	 *
	 * @return  boolean
	 */
	public function delete_all()
	{
		return eaccelerator_clean();
	}
}