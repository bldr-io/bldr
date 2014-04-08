<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test;

use Bldr\Application;
use Bldr\Config;
use Bldr\Test\Mock\Command\MockCommand;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the Application::__construct($name, $version) method
     *
     * @throws \PHPUnit_Framework_Exception
     * @return Application
     */
    public function testConstructor()
    {
        $application = new Application('test', 'test-version');

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
     *
     */
    public function testSetBuildName()
    {
        $app = new Application();
        $config = ['name' => 'test-app'];
        $app->setConfig(new Config($config));

        $travis = getenv('TRAVIS');
        $travisJobNumber = getenv('TRAVIS_JOB_NUMBER');
        putenv('TRAVIS=true');
        putenv('TRAVIS_JOB_NUMBER=test');
        $app->setBuildName();
        $this->assertEquals('travis_test', $app::$BUILD_NAME);
        putenv('TRAVIS=false');
        $date = date('Y-m-d_H-i-s');
        $app->setBuildName();
        $this->assertEquals('local_test-app_'.$date, $app::$BUILD_NAME);

        putenv("TRAVIS={$travis}");
        putenv("TRAVIS_JOB_NUMBER={$travisJobNumber}");
    }

    /**
     */
    public function testSetConfig()
    {
        $app = new Application();
        $app->setConfig(new Config(['name' => 'test-config']));

        return $app;
    }

    /**
     * @depends testSetConfig
     */
    public function testGetConfig(Application $app)
    {
        $this->assertEquals(
            ['name' => 'test-config'],
            $app->getConfig()
                ->all()
        );
    }

    /**
     *
     */
    public function testGetCommands()
    {
        $app      = new Application();
        $commands = $app->getCommands();
        $this->assertNotEmpty($commands);
        foreach ($commands as $command) {
            $this->assertInstanceOf(
                'Symfony\Component\Console\Command\Command',
                $command
            );
        }
    }

    /**
     *
     */
    public function testGetDefaultHelperSet()
    {
        $app    = new Application();
        $class  = new \ReflectionClass($app);
        $method = $class->getMethod('getDefaultHelperSet');
        $method->setAccessible(true);

        /** @var HelperSet $helperSet */
        $helperSet = $method->invoke($app);

        $this->assertInstanceOf(
            'Bldr\Helper\DialogHelper',
            $helperSet->get('dialog')
        );
    }

    /**
     *
     */
    public function testDoRunCommand()
    {
        $app = new Application();

        $command = new MockCommand();
        $command->setApplication($app);
        $command->setHelperSet($app->getHelperSet());

        $input = \Mockery::mock('Symfony\Component\Console\Input\InputInterface');
        $input->shouldReceive('bind')
            ->andReturn(true);
        $input->shouldReceive('isInteractive')
            ->andReturn(false);
        $input->shouldReceive('validate')
            ->andReturn(true);
        $output = \Mockery::mock('Symfony\Component\Console\Output\OutputInterface');

        $class  = new \ReflectionClass($app);
        $method = $class->getMethod('doRunCommand');
        $method->setAccessible(true);

        $config = ['name' => 'test-config'];
        file_put_contents(getcwd() . '/.test.yml', Yaml::dump($config));

        $this->assertNull($method->invokeArgs($app, [$command, $input, $output]));
    }

    /**
     *
     */
    public function testGetBuildContainer()
    {
        $app    = new Application();
        $config = [
            'profiles' => [
                'test' => [
                    'tasks' => [
                        'test'
                    ]
                ]
            ],
            'tasks' => [
                'test' => [
                    'calls' => [

                    ]
                ]
            ],
            'extensions' => [
                'Bldr\Test\Mock\DependencyInjection\MockExtension' => null
            ]
        ];
        $app->setConfig(new Config($config));

        $class  = new \ReflectionClass($app);
        $method = $class->getMethod('buildContainer');
        $method->setAccessible(true);

        /** @var ContainerBuilder $container */
        $container = $method->invoke($app);

        $this->assertInstanceOf(
            'Symfony\Component\DependencyInjection\ContainerBuilder',
            $container
        );
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
