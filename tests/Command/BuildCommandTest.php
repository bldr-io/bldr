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
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Aaron Scherer <aaron@undergroundelephant.com>
 */
class BuildCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application   = new Application();
        Application::$CONFIG = '.test.yml';

        $config = [
            'name'     => 'test',
            'profiles' => [
                'default' => [
                    'tasks' => [
                        'test'
                    ]
                ]
            ],
            'tasks'    => [
                'test' => [
                    'calls' => []
                ]
            ]
        ];

        file_put_contents(getcwd() . '/' . Application::$CONFIG, Yaml::dump($config));

        $ref    = new \ReflectionClass($application);
        $method = $ref->getMethod('readConfig');
        $method->setAccessible(true);

        $application->setConfig($method->invoke($application));

        $application->add(new BuildCommand());

        $command       = $application->find('build');
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
