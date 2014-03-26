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

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class AbstractCallTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testInitialize()
    {
        $properties = [
            'input'     => \Mockery::mock('Symfony\Component\Console\Input\InputInterface'),
            'output'    => \Mockery::mock('Symfony\Component\Console\Output\OutputInterface'),
            'helperSet' => \Mockery::mock('Symfony\Component\Console\Helper\HelperSet'),
            'config'    => \Mockery::mock('Symfony\Component\DependencyInjection\ParameterBag\ParameterBag')
        ];

        $call = new MockCall();
        $ref  = new \ReflectionClass($call);

        $result = $call->initialize(
            $properties['input'],
            $properties['output'],
            $properties['helperSet'],
            $properties['config']
        );

        $this->assertInstanceOf(
            'Bldr\Call\CallInterface',
            $result
        );

        $this->assertInstanceOf(
            'Bldr\Call\AbstractCall',
            $result
        );

        foreach ($properties as $name => $class) {
            $property = $ref->getProperty($name);
            $property->setAccessible(true);
            $this->assertEquals(
                $class,
                $property->getValue($call)
            );
        }
    }

    /**
     *
     */
    public function testSetTask()
    {
        $call     = new MockCall();
        $ref      = new \ReflectionClass($call);
        $task = $ref->getProperty('task');
        $task->setAccessible(true);

        $callObj = new Call('mock', ['arg1', 'arg2']);
        $taskObj = new Task('mock-task', 'mock-description', [$callObj]);

        $call->setTask($taskObj);
        $task = $task->getValue($call);

        $this->assertEquals('mock-task', $task->getName());

        $this->assertEquals('mock-description', $task->getDescription());

        $this->assertEquals([$callObj], $task->getCalls());
    }
}
