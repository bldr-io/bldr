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
use Bldr\Command\BuildCommand;
use Bldr\Config;
use Bldr\Test\Mock\Call\MockCall;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class BuildCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $container = \Mockery::mock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $container->shouldReceive('findTaggedServiceIds')
            ->andReturn(['exec']);
        $container->shouldReceive('get')
            ->withArgs(['exec'])
            ->andReturn(new MockCall());

        $application  = new Application();
        Config::$NAME = '.test';

        $config = [
            'name'        => 'test',
            'description' => 'test app',
            'profiles'    => [
                'default' => [
                    'description' => 'test profile',
                    'tasks'       => [
                        'test'
                    ]
                ]
            ],
            'tasks'       => [
                'test' => [
                    'calls' => [
                        [
                            'type'      => 'exec',
                            'arguments' => ['ls -l']
                        ]
                    ]
                ]
            ]
        ];

        $application->setConfig(Config::create('yml', $config));

        $application->add(new BuildCommand());

        $command = $application->find('build');
        $command->setContainer($container);
        $commandTester = new CommandTester($command);

        $commandTester->execute(['command' => $command->getName()]);
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuteMultipleServices()
    {
        $container = \Mockery::mock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $container->shouldReceive('findTaggedServiceIds')
            ->andReturn(['exec', 'bad']);
        $container->shouldReceive('get')
            ->withArgs(['exec'])
            ->andReturn(new MockCall());

        $application  = new Application();
        Config::$NAME = '.test';

        $config = [
            'name'        => 'test',
            'description' => 'test app',
            'profiles'    => [
                'default' => [
                    'description' => 'test profile',
                    'tasks'       => [
                        'test'
                    ]
                ]
            ],
            'tasks'       => [
                'test' => [
                    'calls' => [
                        [
                            'type'      => 'exec',
                            'arguments' => ['ls -l']
                        ]
                    ]
                ]
            ]
        ];

        $application->setConfig(Config::create('yml', $config));

        $application->add(new BuildCommand());

        $command = $application->find('build');
        $command->setContainer($container);
        $commandTester = new CommandTester($command);

        $commandTester->execute(['command' => $command->getName()]);
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuteNoServices()
    {
        $container = \Mockery::mock('Symfony\Component\DependencyInjection\ContainerBuilder');
        $container->shouldReceive('findTaggedServiceIds')
            ->andReturn([]);
        $container->shouldReceive('get')
            ->withArgs(['exec'])
            ->andReturn(new MockCall());

        $application  = new Application();
        Config::$NAME = '.test';

        $config = [
            'name'        => 'test',
            'description' => 'test app',
            'profiles'    => [
                'default' => [
                    'description' => 'test profile',
                    'tasks'       => [
                        'test'
                    ]
                ]
            ],
            'tasks'       => [
                'test' => [
                    'calls' => [
                        [
                            'type'      => 'exec',
                            'arguments' => ['ls -l']
                        ]
                    ]
                ]
            ]
        ];

        $application->setConfig(Config::create('yml', $config));

        $application->add(new BuildCommand());

        $command = $application->find('build');
        $command->setContainer($container);
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
