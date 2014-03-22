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

use Bldr\Test\Mock\Call\MockCall;

/**
 * @author Aaron Scherer <aaron@undergroundelephant.com>
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
        $taskName = $ref->getProperty('taskName');
        $taskName->setAccessible(true);
        $taskArguments = $ref->getProperty('taskArguments');
        $taskArguments->setAccessible(true);

        $call->setTask('mock-task', ['a', 'b', 'c']);

        $this->assertEquals(
            'mock-task',
            $taskName->getValue($call)
        );

        $this->assertEquals(
            ['a', 'b', 'c'],
            $taskArguments->getValue($call)
        );
    }

    /**
     *
     */
    public function testSetFailOnError()
    {
        $call     = new MockCall();
        $ref      = new \ReflectionClass($call);
        $property = $ref->getProperty('failOnError');
        $property->setAccessible(true);

        $call->setFailOnError(true);

        $this->assertTrue(
            $property->getValue($call)
        );
    }

    /**
     *
     */
    public function testSetSuccessStatusCodes()
    {
        $call     = new MockCall();
        $ref      = new \ReflectionClass($call);
        $property = $ref->getProperty('successStatusCodes');
        $property->setAccessible(true);

        $call->setSuccessStatusCodes([0, 1, 2]);

        $this->assertInternalType('array', $property->getValue($call));

        $this->assertEquals(
            [0, 1, 2],
            $property->getValue($call)
        );
    }
}
