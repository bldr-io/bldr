<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Tests;

use Bldr\Application;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Aaron Scherer <aaron@undergroundelephant.com>
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the Application::__construct($name, $version) method
     *
     * @return Application
     */
    public function testConstructor()
    {
        $application = new Application();

        $this->assertInstanceOf(
            'Bldr\Application',
            $application
        );

        $this->assertInstanceOf(
            'Symfony\Component\Console\Application',
            $application
        );

        return $application;
    }

    /**
     * Tests the Application::getDispatcher() method
     *
     * @param Application $application
     *
     * @depends testConstructor
     */
    public function testGetDispatcher(Application $application)
    {
        $this->assertInstanceOf(
            'Symfony\Component\EventDispatcher\EventDispatcher',
            $application->getDispatcher()
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testReadConfigException()
    {
        $app = new Application();
        $this->changeConfig($app);

        $app = new \ReflectionClass($app);
        $method = $app->getMethod('readConfig');
        $method->setAccessible(true);

        $method->invoke($app);
    }

    public function testReadConfig()
    {
        $app = new Application();
        $this->changeConfig($app);

        $config = ['name' => 'test-config'];

        file_put_contents(getcwd().'/.test.yml', Yaml::dump($config));

        $class  = new \ReflectionClass($app);
        $method = $class->getMethod('readConfig');
        $method->setAccessible(true);

        $config = $method->invoke($app);

        rename(getcwd() . '/.test.yml', getcwd() . '/.test.yml.dist');

        $config = $method->invoke($app);

        unlink(getcwd() . '/.test.yml.dist');

        return $config;
    }

    /**
     * @depends testReadConfig
     */
    public function testSetConfig(array $config)
    {
        $app = new Application();
        $app->setConfig($config);

        return $app;
    }

    /**
     * @depends testSetConfig
     */
    public function testGetConfig(Application $app)
    {
        $this->assertEquals(['name' => 'test-config'], $app->getConfig());
    }

    /**
     * Changes the config name
     */
    private function changeConfig(Application $application)
    {
        // Need to change the config name so we don't conflict with the projects config
        $class      = new \ReflectionClass('Bldr\Application');
        $configName = $class->getProperty('configName');
        $configName->setAccessible(true);
        $configName->setValue($application, '.test.yml');
    }

    /**
     *
     */
    protected function tearDown()
    {
        if (file_exists(getcwd() . '/.test.yml')) {
            unlink(getcwd() . '/.test.yml');
        }
        if (file_exists(getcwd() . '/.test.yml.dist')) {
            unlink(getcwd() . '/.test.yml.dist');
        }
    }
}
