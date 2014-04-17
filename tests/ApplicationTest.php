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
use Bldr\Test\Mock\Command\MockCommand;
use Symfony\Component\Console\Helper\HelperSet;

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
    public function testFactory()
    {
        $application = Application::create(\Mockery::mock('Dflydev\EmbeddedComposer\Core\EmbeddedComposerInterface'));

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
        $container = \Mockery::mock('Bldr\DependencyInjection\ContainerBuilder');
        $container->shouldReceive('getParameter')
            ->once()
            ->withArgs(['name'])
            ->andReturn('test-app');

        $app      = Application::create(\Mockery::mock('Dflydev\EmbeddedComposer\Core\EmbeddedComposerInterface'));
        $ref      = new \ReflectionClass($app);
        $property = $ref->getProperty('container');
        $property->setAccessible(true);
        $property->setValue($app, $container);

        $travis          = getenv('TRAVIS');
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
     *
     */
    public function testGetCommands()
    {
        $app      = Application::create(\Mockery::mock('Dflydev\EmbeddedComposer\Core\EmbeddedComposerInterface'));
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
        $app    = Application::create(\Mockery::mock('Dflydev\EmbeddedComposer\Core\EmbeddedComposerInterface'));
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
        $app = Application::create(\Mockery::mock('Dflydev\EmbeddedComposer\Core\EmbeddedComposerInterface'));

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
        $input->shouldReceive('hasParameterOption')
            ->times(3)
            ->andReturn(false);
        $output = \Mockery::mock('Symfony\Component\Console\Output\OutputInterface');
        $output->shouldReceive('writeln')
            ->withNoArgs();

        $class  = new \ReflectionClass($app);
        $method = $class->getMethod('doRunCommand');
        $method->setAccessible(true);

        $this->assertNull($method->invokeArgs($app, [$command, $input, $output]));
    }

    /**
     *
     */
    protected function tearDown()
    {
        if (file_exists(getcwd().'/.test.yml')) {
            unlink(getcwd().'/.test.yml');
        }
        if (file_exists(getcwd().'/.test.yml.dist')) {
            unlink(getcwd().'/.test.yml.dist');
        }
    }
}
