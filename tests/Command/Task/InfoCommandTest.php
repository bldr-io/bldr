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

use Bldr\Block\Core\Command\Task\InfoCommand;
use Bldr\Registry\TaskRegistry;
use Bldr\Test\Mock\MockApplication;
use Mockery\MockInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class InfoCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type MockInterface
     */
    protected $container;

    /**
     * @type InfoCommand
     */
    protected $command;

    /**
     * @type MockInterface
     */
    protected $task1;

    /**
     * @type MockInterface
     */
    protected $task2;

    /**
     *
     */
    public function setUp()
    {
        $this->container = \Mockery::mock('Symfony\Component\DependencyInjection\Container');

        $this->command = new InfoCommand();
        $this->command->setContainer($this->container);

        $this->task1 = \Mockery::mock('Bldr\Block\Core\Task\AbstractTask');
        $this->task1->shouldReceive('configure');
        $this->task1->shouldReceive('validate');
        $this->task1->shouldReceive('setEventDispatcher');
        $this->task1->shouldReceive('getName')->andReturn('Name 1');

        $this->task2 = \Mockery::mock('Bldr\Task\TaskInterface');
        $this->task2->shouldReceive('configure');
        $this->task2->shouldReceive('validate');
        $this->task2->shouldReceive('getName')->andReturn('Name 2');

        $registry = new TaskRegistry(
            \Mockery::mock('Symfony\Component\EventDispatcher\EventDispatcherInterface'), [$this->task1, $this->task2]
        );

        $this->container
            ->shouldReceive('get')
            ->with('bldr.registry.task')
            ->andReturn($registry);
    }

    /**
     *
     */
    public function testCommand()
    {
        $this->task1->shouldReceive('getDescription')->andReturn('A first description.');
        $this->task1->shouldReceive('getParameterDefinition')->andReturn(
            [
                ['name' => 'option1', 'default' => null, 'description' => '', 'required' => false],
                [
                    'name'        => 'option2',
                    'default'     => 'bldr!',
                    'description' => 'Option 2 description',
                    'required'    => true
                ],
                ['name' => 'option3', 'default' => true, 'description' => '', 'required' => false],
            ]
        );

        $this->task2->shouldReceive('getDescription')->andReturnNull();

        $application = new MockApplication();
        $application->add($this->command);

        $tester = new CommandTester($application->find('task:info'));
        $tester->execute(['task' => 'Name 1']);

        $this->assertEquals(
            <<<EOO

Task Name: Name 1
Task Description: A first description.

Options:
+---------+----------------------+----------+---------+
| Option  | Description          | Required | Default |
+---------+----------------------+----------+---------+
| option1 | No Description       | No       | null    |
| option2 | Option 2 description | Yes      | "bldr!" |
| option3 | No Description       | No       | true    |
+---------+----------------------+----------+---------+

EOO
            ,
            $tester->getDisplay()
        );

        $tester = new CommandTester($application->find('task:info'));
        $tester->execute(['task' => 'Name 2']);

        $this->assertEquals(
            <<<EOO

Task Name: Name 2

EOO
            ,
            $tester->getDisplay()
        );
    }
}
