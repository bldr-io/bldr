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
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

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

        $config = \Mockery::mock('Composer\Config');
        $config->shouldReceive('has')->andReturn(false);

        $embeddedComposer->shouldReceive('getExternalComposerConfig')->andReturn($config);
        $embeddedComposer->shouldReceive('getExternalRootDirectory')->andReturn(__DIR__); // doesn't matter.

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
     * @throws \Exception
     */
    public function testFunctionalRunthrough()
    {
        $application = self::createApplication();
        $application->setAutoExit(false);
        $stream  = 'php://memory';

        $output = new StreamOutput(fopen($stream, 'w', false));
        $application->run(new ArgvInput(['bldr', 'run', 'functionalTest']), $output);
        $this->functionalAsserts($output);

        $output = new StreamOutput(fopen($stream, 'w', false));
        $application->run(new ArgvInput(['bldr', 'functionalTest']), $output);
        $this->functionalAsserts($output);
    }

    /**
     * @param StreamOutput $output
     */
    private function functionalAsserts(StreamOutput $output)
    {
        $baseDir = realpath(__DIR__.'/..');
        rewind($output->getStream());
        $content = stream_get_contents($output->getStream());

        $this->assertContains('Running the fsTest job > Filesystem Block Tests', $content);

        $this->assertContains('Creating tmp/', $content);
        $this->assertFileExists($baseDir.'/tmp');
        $this->assertContains('Creating tmp/test/deep', $content);
        $this->assertFileExists($baseDir.'/tmp/test');
        $this->assertFileExists($baseDir.'/tmp/test/deep');
        $this->assertContains('Touching tmp/test.tmp', $content);
        $this->assertFileExists($baseDir.'/tmp/test.tmp');
        $this->assertContains('Touching tmp/test/deep/test.tmp', $content);
        $this->assertFileExists($baseDir.'/tmp/test/deep/test.tmp');

        $this->assertContains('Running the lint job > Lints the files of the project', $content);

        $this->assertContains('Lint Task Finished', $content);
        $this->assertContains('Build Success!', $content);
    }
}
