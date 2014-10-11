<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test\Block\Core\Task;

use Bldr\Test\Mock\Task\MockTask;

/**
 * @author Aaron Scherer <aequasi@gmail.com.com>
 */
class AbstractTaskTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $task = new MockTask();
        $task->configure();
        $task->addParameter('test', false);
        $task->addParameter('required-test', true);
        $task->setParameter('required-test', 'test');
        $task->validate();

        $this->assertInstanceOf(
            'Bldr\Block\Core\Task\AbstractTask',
            $task
        );
        $this->assertInstanceOf(
            'Bldr\Task\TaskInterface',
            $task
        );

        $this->assertEquals('mock', $task->getName());
        $this->assertEquals('mock description', $task->getDescription());
    }
}
