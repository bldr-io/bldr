<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test\Call;

use Bldr\Model\Call;
use Bldr\Model\Task;
use Bldr\Test\Mock\Call\MockCall;
use Bldr\Call\CallInterface;
use Mockery\MockInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class AbstractCallTest extends \PHPUnit_Framework_TestCase
{
    public function testInitialize()
    {
        /** @var MockInterface[] $properties */
        $properties = [
            'dispatcher' => \Mockery::mock('Symfony\Component\EventDispatcher\EventDispatcher'),
            'input'      => \Mockery::mock('Symfony\Component\Console\Input\InputInterface'),
            'output'     => \Mockery::mock('Symfony\Component\Console\Output\OutputInterface'),
            'helperSet'  => \Mockery::mock('Symfony\Component\Console\Helper\HelperSet'),
            'task'       => \Mockery::mock('Bldr\Model\Task'),
            'call'       => \Mockery::mock('Bldr\Model\Call'),
        ];

        $properties['call']->shouldReceive('getOptions')
            ->withNoArgs()
            ->andReturn([]);

        $call = new MockCall();

        $result = $call->initialize(
            $properties['dispatcher'],
            $properties['input'],
            $properties['output'],
            $properties['helperSet'],
            $properties['task'],
            $properties['call']
        );

        $this->assertInstanceOf(
            'Bldr\Call\CallInterface',
            $result
        );

        $this->assertInstanceOf(
            'Bldr\Call\AbstractCall',
            $result
        );

        return $call;
    }
}
