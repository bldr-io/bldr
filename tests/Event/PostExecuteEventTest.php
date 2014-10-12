<?php
/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test\Event;

use Bldr\Event\PostExecuteEvent;

/**
 * @author Mauricio Walters <nvitius@gmail.com>
 */
class PostExecuteEventTest extends \PHPUnit_Framework_TestCase
{
    public static function createPostExecuteEvent()
    {
        $task    = \Mockery::mock('Bldr\Task\TaskInterface');
        $process = \Mockery::mock('Symfony\Component\Process\Process');

        $process->shouldReceive('stop')->andReturn('0');

        return new PostExecuteEvent($task, $process, false);
    }

    /**
     * Tests the __construct($call, $process) method
     *
     * @throws \PHPUnit_Framework_Exception
     * @return PostExecuteEvent
     */
    public function testFactory()
    {
        $postExecuteEvent = self::createPostExecuteEvent();

        $this->assertInstanceOf(
            'Bldr\Event\PostExecuteEvent',
            $postExecuteEvent
        );

        $this->assertInstanceOf(
            'Bldr\Event\AbstractEvent',
            $postExecuteEvent
        );

        return $postExecuteEvent;
    }

    public function testGetCall()
    {
        $postExecuteEvent = self::createPostExecuteEvent();

        $this->assertInstanceOf(
            'Bldr\Task\TaskInterface',
            $postExecuteEvent->getTask()
        );
    }

    public function testGetProcess()
    {
        $postExecuteEvent = self::createPostExecuteEvent();

        $this->assertInstanceOf(
            'Symfony\Component\Process\Process',
            $postExecuteEvent->getProcess()
        );
    }
}
