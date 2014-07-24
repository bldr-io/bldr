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
    public function testConstructor()
    {
        $call = new Call('mock');

        $this->assertEquals('mock', $call->getType());
        $this->assertEmpty($call->getOptions());
        $this->assertTrue($call->getFailOnError());
        $this->assertEquals([0], $call->getSuccessCodes());
    }
}
