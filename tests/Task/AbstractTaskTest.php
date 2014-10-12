<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test\Task;

use Bldr\Task\TaskInterface;
use Bldr\Test\Mock\Task\MockTask;
use Mockery\MockInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class AbstractTaskTest extends \PHPUnit_Framework_TestCase
{
    public function testInitialize()
    {
        /** @var MockInterface[] $properties */
        $properties = [
            'dispatcher' => \Mockery::mock('Symfony\Component\EventDispatcher\EventDispatcher'),
            'output'     => \Mockery::mock('Bldr\Output\BldrOutput'),
            'task'       => \Mockery::mock('Bldr\Task\TaskInterface'),
        ];

        $properties['task']->shouldReceive('getProperties')
            ->withNoArgs()
            ->andReturn([]);

        $task = new MockTask();
        $task->configure();
        $task->validate();

        $this->assertInstanceOf(
            'Bldr\Task\TaskInterface',
            $task
        );

        $this->assertInstanceOf(
            'Bldr\Block\Core\Task\AbstractTask',
            $task
        );
    }
}
