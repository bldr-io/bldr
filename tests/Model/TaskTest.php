<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test\Model;

use Bldr\Model\Call;
use Bldr\Model\Task;

/**
 * @author Aaron Scherer <aequasi@gmail.com.com>
 */
class TaskTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $task = new Task('test', 'test-description', [['type' => 'test', 'foo' => 'bar']]);

        $this->assertInstanceOf(
            'Bldr\Model\Task',
            $task
        );

        $this->assertEquals('test', $task->getName());
        $this->assertEquals('test-description', $task->getDescription());

        $call = new Call('test');
        $call->setOptions(['foo' => 'bar']);

        $this->assertEquals([$call], $task->getCalls());
    }
}
