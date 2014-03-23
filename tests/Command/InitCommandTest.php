<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test\Command;

use Bldr\Application;
use Bldr\Command\InitCommand;
use Symfony\Component\Console\Tester\CommandTester;

class InitCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application $application
     */
    protected $application;

    protected function setUp()
    {
        $this->application = new Application();
        Application::$CONFIG = '.test.yml';
    }

    public function testExecute()
    {
        $helper = \Mockery::mock('Bldr\Helper\DialogHelper');
        $helper->shouldReceive('getName')->andReturn('dialog');
        $helper->shouldReceive('setHelperSet')->withArgs([$this->application->getHelperSet()])->andReturn(true);
        $helper->shouldReceive('getQuestion')->andReturn('');
        $helper->shouldReceive('ask')
            ->twice()
            ->andReturn('vendor/name');
        $helper->shouldReceive('askConfirmation')
            ->andReturn();

        $this->application->getHelperSet()->set($helper);
        $this->application->add(new InitCommand());

        $command = $this->application->find('init');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['command' => $command->getName()]);
    }

    protected function tearDown()
    {
        \Mockery::close();

        if (file_exists(getcwd() . '/.test.yml')) {
            unlink(getcwd() . '/.test.yml');
        }
        if (file_exists(getcwd() . '/.test.yml.dist')) {
            unlink(getcwd() . '/.test.yml.dist');
        }
    }
}
