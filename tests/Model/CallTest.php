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

/**
 * @author Aaron Scherer <aequasi@gmail..com>
 */
class CallTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return Call
     */
    public function testMagicSet()
    {
        $call       = new Call('test', []);
        $call->test = 'foo';

        return $call;
    }

    /**
     * @param Call $call
     *
     * @depends testMagicSet
     */
    public function testMagicGet(Call $call)
    {
        $this->assertEquals('foo', $call->test);
    }

    /**
     * @param Call $call
     *
     * @depends testMagicSet
     */
    public function testHas(Call $call)
    {
        $this->assertTrue($call->has('test'));
        $this->assertFalse($call->has('bad'));
    }
}
