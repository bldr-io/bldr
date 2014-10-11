<?php
/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Blrd\Test\Event;

use Bldr\Event\PreCallEvent;
use Bldr\Event\PreTaskEvent;

/**
 * @author Mauricio Walters <nvitius@gmail.com>
 */
class PreCallEventTest extends \PHPUnit_Framework_TestCase
{
    public static function createPreTaskEvent()
    {
        $jobDefinition  = \Mockery::mock('Bldr\Definition\JobDefinition');
        $taskDefinition = \Mockery::mock('Bldr\Definition\TaskDefinition');

        return new PreTaskEvent($jobDefinition, $taskDefinition, false);
    }

    /**
     * Tests the __construct($call, $task) method
     * @throws \PHPUnit_Framework_Exception
     * @return PreCallEvent
     */
    public function testFactory()
    {
        $preTaskEvent = self::createPreTaskEvent();

        $this->assertInstanceOf(
            'Bldr\Event\PreTaskEvent',
            $preTaskEvent
        );

        $this->assertInstanceOf(
            'Bldr\Event\AbstractEvent',
            $preTaskEvent
        );

        return $preTaskEvent;
    }

    public function testGetTask()
    {
        $preTaskEvent = self::createPreTaskEvent();

        $this->assertInstanceOf(
            'Bldr\Definition\TaskDefinition',
            $preTaskEvent->getTask()
        );
    }

    public function testGetJob()
    {
        $preTaskEvent = self::createPreTaskEvent();

        $this->assertInstanceOf(
            'Bldr\Definition\JobDefinition',
            $preTaskEvent->getJob()
        );
    }

    public function testSetTask()
    {
        $task = \Mockery::mock('Bldr\Definition\TaskDefinition');
        $task->shouldReceive('getType')
            ->once()
            ->andReturn('test');

        $preTaskEvent = self::createPreTaskEvent();
        $preTaskEvent->setTask($task);

        $this->assertEquals('test', $preTaskEvent->getTask()->getType());
    }
}
