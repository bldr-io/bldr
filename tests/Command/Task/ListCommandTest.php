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

use Bldr\Block\Core\Command\Task\ListCommand;
use Bldr\Registry\TaskRegistry;
use Bldr\Test\Mock\MockApplication;
use Mockery\MockInterface;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class ListCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type MockInterface
     */
    protected $container;

    /**
     * @type ListCommand
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

    public function setUp()
    {
        $this->container = \Mockery::mock('Symfony\Component\DependencyInjection\Container');

        $this->command = new ListCommand();
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

    public function testCommand()
    {
        $this->task1->shouldReceive('getName')->andReturn('Name 1');
        $this->task1->shouldReceive('getDescription')->andReturn('A first description.');

        $this->task2->shouldReceive('getName')->andReturn('Name 2');
        $this->task2->shouldReceive('getDescription')->andReturn('A second description.');

        $application = new MockApplication();
        $application->add($this->command);

        $tester = new CommandTester($application->find('task:list'));
        $tester->execute([]);

        $this->assertEquals(
            <<<EOO
+--------+-----------------------+
| Name   | Description           |
+--------+-----------------------+
| Name 1 | A first description.  |
| Name 2 | A second description. |
+--------+-----------------------+

EOO
            ,
            $tester->getDisplay()
        );
    }
}
