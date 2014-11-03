<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test\DependencyInjection\Loader;

/**
 * @author Rob Loach <robloach@gmail.com>
 */
abstract class FileLoaderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * The name of the class to test.
	 */
	protected $class;

	/**
	 * The extension for the file.
	 */
	protected $extension;

	/**
	 * The expected result from loading the file.
	 */
	protected $expected = array(
		'bldr' => array(
			'description' => 'Description for your project',
			'name' => 'acme/demo-project',
			'profiles' => array(
				'default' => array(
					'description' => 'Sample Profile',
					'jobs' => array(
						0 => 'sampleJob',
					),
				),
			),
			'jobs' => array(
				'sampleJob' => array(
					'description' => 'Runs a sleep for 5 seconds, then sends a message to the screen',
					'tasks' => array(
						0 => array(
							'type' => 'sleep',
							'seconds' => 5,
						),
						1 => array(
							'type' => 'notify',
							'message' => 'Finished Sleeping. Ending now.'
						),
					),
				),
			)
		),
	);

	/**
	 * Set up the FileLoader.
	 */
	public function setUp() {
		// Mock the Container Builder.
		$container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');
		$container->method('hasExtension')->willReturn(true);

		// Mock the Locator.
		$locator = $this->getMock('Symfony\Component\Config\FileLocatorInterface');

		// Build the FileLoader.
		$this->loader = new $this->class($container, $locator);
	}

	/**
	 * Tests the protected loadFile() function from the FileLoader.
	 */
	public function testLoadFile()
	{
		$actual = $this->invokeMethod($this->loader, 'loadFile', array(
			__DIR__ . '/Fixtures/test.' . $this->extension
		));
		$this->assertEquals($this->expected, $actual);
	}

	/**
	 * Tests the getFileExtension function.
	 */
	public function testGetFileExtension()
	{
		$actual = $this->invokeMethod($this->loader, 'getFileExtension');
		$this->assertEquals($this->extension, $actual);
	}

	/**
	 * Invoke a protected or private method from a class.
	 */
	private function invokeMethod(&$object, $methodName, array $parameters = array()) {
		$reflection = new \ReflectionClass(get_class($object));
	    $method = $reflection->getMethod($methodName);
	    $method->setAccessible(true);

	    return $method->invokeArgs($object, $parameters);
	}


}
