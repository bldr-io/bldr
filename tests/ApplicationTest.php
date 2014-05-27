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

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Application
     */
    public static function createApplication()
    {
        $package = \Mockery::mock('Composer\Package\PackageInterface');
        $package->shouldReceive('getPrettyVersion')->andReturn('test');

        $embeddedComposer = \Mockery::mock('Dflydev\EmbeddedComposer\Core\EmbeddedComposerInterface');
        $embeddedComposer->shouldReceive('findPackage')->once()->andReturn($package);

        return Application::create($embeddedComposer);
    }

    /**
     * Tests the Application::__construct($name, $version) method
     *
     * @throws \PHPUnit_Framework_Exception
     * @return Application
     */
    public function testFactory()
    {
        $application = self::createApplication();

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

        $app      = self::createApplication();
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
        $app      = self::createApplication();
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
        $app    = self::createApplication();
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
