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

use Bldr\Event\PreExecuteEvent;

/**
 * @author Mauricio Walters <nvitius@gmail.com>
 */
class PreExecuteEventTest extends \PHPUnit_Framework_TestCase
{
    public static function createPreExecuteEvent()
    {
        $call    = \Mockery::mock('Bldr\Call\AbstractCall');
        $builder = \Mockery::mock('Symfony\Component\Process\ProcessBuilder');

        return new PreExecuteEvent($call, $builder, false);
    }

    /**
     * Tests the __construct($call, $builder) method
     *
     * @throws \PHPUnit_Framework_Exception
     * @return PreExecuteEvent
     */
    public function testFactory()
    {
        $postExecuteEvent = self::createPreExecuteEvent();

        $this->assertInstanceOf(
            'Bldr\Event\PreExecuteEvent',
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
        $postExecuteEvent = self::createPreExecuteEvent();

        $this->assertInstanceOf(
            'Bldr\Call\AbstractCall',
            $postExecuteEvent->getCall()
        );
    }

    public function testGetProcess()
    {
        $postExecuteEvent = self::createPreExecuteEvent();

        $this->assertInstanceOf(
            'Symfony\Component\Process\ProcessBuilder',
            $postExecuteEvent->getProcessBuilder()
        );
    }
}
