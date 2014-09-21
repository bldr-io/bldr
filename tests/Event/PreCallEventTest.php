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

/**
 * @author Mauricio Walters <nvitius@gmail.com>
 */
class PreCallEventTest extends \PHPUnit_Framework_TestCase
{
    public static function createPreCallEvent()
    {
        $call = \Mockery::mock('Bldr\Model\Call');
        $task = \Mockery::mock('Bldr\Model\Task');

        return new PreCallEvent($task, $call, false);
    }

    /**
     * Tests the __construct($call, $task) method
     * @throws \PHPUnit_Framework_Exception
     * @return PreCallEvent
     */
    public function testFactory()
    {
        $preCallEvent = self::createPreCallEvent();

        $this->assertInstanceOf(
            'Bldr\Event\PreCallEvent',
            $preCallEvent
        );

        $this->assertInstanceOf(
            'Bldr\Event\AbstractEvent',
            $preCallEvent
        );

        return $preCallEvent;
    }

    public function testGetCall()
    {
        $preCallEvent = self::createPreCallEvent();

        $this->assertInstanceOf(
            'Bldr\Model\Call',
            $preCallEvent->getCall()
        );
    }

    public function testGetTask()
    {
        $preCallEvent = self::createPreCallEvent();

        $this->assertInstanceOf(
            'Bldr\Model\Task',
            $preCallEvent->getTask()
        );
    }

    public function testSetCall()
    {
        $call = \Mockery::mock('Bldr\Model\Call');
        $call->shouldReceive('getType')
            ->once()
            ->andReturn('test');

        $preCallEvent = self::createPreCallEvent();
        $preCallEvent->setCall($call);

        $this->assertEquals('test', $preCallEvent->getCall()->getType());
    }
}
