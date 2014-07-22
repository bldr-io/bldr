<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test\Command\Task;

use Bldr\Command\Task\InfoCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class InfoCommandTest extends \PHPUnit_Framework_TestCase
{
    protected $container;
    protected $command;
    protected $call1;
    protected $call2;

    public function setUp()
    {
        $this->container = \Mockery::mock('Symfony\Component\DependencyInjection\Container');
        $this->command = new InfoCommand();
        $this->call1 = \Mockery::mock('Bldr\Call\CallInterface');
        $this->call1->shouldReceive('configure');
        $this->call2 = \Mockery::mock('Bldr\Call\CallInterface');
        $this->call2->shouldReceive('configure');

        $this->command->setContainer($this->container);

        $this->container
            ->shouldReceive('findTaggedServiceIds')
            ->with('bldr')
            ->andReturn(array('call1' => true, 'call2' => true)); // value is not important

        $this->container
            ->shouldReceive('get')
            ->with('call1')
            ->andReturn($this->call1);

        $this->container
            ->shouldReceive('get')
            ->with('call2')
            ->andReturn($this->call2);
    }

    public function testCommand()
    {
        $this->call1->shouldReceive('getName')->andReturn('Name 1');
        $this->call1->shouldReceive('getDescription')->andReturn('A first description.');
        $this->call1->shouldReceive('getOptions')->andReturn(array(
            array('name' => 'option1', 'default' => null, 'description' => '', 'required' => false),
            array('name' => 'option2', 'default' => 'bldr!', 'description' => 'Option 2 description', 'required' => true),
        ));

        $this->call2->shouldReceive('getName')->andReturn('Name 2');

        $application = new Application();
        $application->add($this->command);

        $tester = new CommandTester($application->find('task:info'));
        $tester->execute(['task' => 'Name 1']);

        $this->assertEquals(<<<EOO

Task Name: Name 1
Task Description: A first description.

Options:
+---------+----------------------+----------+---------+
| Option  | Description          | Required | Default |
+---------+----------------------+----------+---------+
| option1 | No Description       | No       | NULL    |
+---------+----------------------+----------+---------+
| option2 | Option 2 description | Yes      | 'bldr!' |
+---------+----------------------+----------+---------+
EOO
        , $tester->getDisplay());
    }
}
