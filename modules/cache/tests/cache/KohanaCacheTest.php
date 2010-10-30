<?php

class KohanaCacheTest extends PHPUnit_Framework_TestCase {

	static protected $test_instance;

	public function setUp()
	{
		self::$test_instance = Cache::instance('file');
		self::$test_instance->delete_all();

		self::$test_instance->set('testGet1', 'foo', 3600);
	}

	public function tearDown()
	{
		self::$test_instance->delete_all();
		self::$test_instance = NULL;
	}

	/**
	 * Tests the cache static instance method
	 */
	public function testInstance()
	{
		$file_instance  = Cache::instance('file');
		$file_instance2 = Cache::instance('file');

		// Try and load a Cache instance
		$this->assertType('Kohana_Cache', Cache::instance());
		$this->assertType('Kohana_Cache_File', $file_instance);

		// Test instances are only initialised once
		$this->assertTrue(spl_object_hash($file_instance) == spl_object_hash($file_instance2));

		// Test the publically accessible Cache instance store
		$this->assertTrue(spl_object_hash(Cache::$instances['file']) == spl_object_hash($file_instance));

		// Get the constructor method
		$constructorMethod = new ReflectionMethod($file_instance, '__construct');

		// Test the constructor for hidden visibility
		$this->assertTrue($constructorMethod->isProtected(), '__construct is does not have protected visibility');
	}

	public function testGet()
	{
		// Try and get a non property
		$this->assertNull(self::$test_instance->get('testGet0'));

		// Try and get a non property with default return value
		$this->assertEquals('bar', self::$test_instance->get('testGet0', 'bar'));

		// Try and get a real cached property
		$this->assertEquals('foo', self::$test_instance->get('testGet1'));
	}

	public function testSet()
	{
		$value = 'foobar';
		$value2 = 'snafu';

		// Set a new property
		$this->assertTrue(self::$test_instance->set('testSet1', $value));

		// Test the property exists
		$this->assertEquals(self::$test_instance->get('testSet1'), $value);

		// Test short set
		$this->assertTrue(self::$test_instance->set('testSet2', $value2, 3));

		// Test the property exists
		$this->assertEquals(self::$test_instance->get('testSet2'), $value2);

		// Allow test2 to expire
		sleep(4);

		// Test the property has expired
		$this->assertNull(self::$test_instance->get('testSet2'));
	}

	public function testDelete()
	{
		
	}

	public function testDeleteAll()
	{
		
	}
}