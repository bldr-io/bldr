<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Tests\Block\Miscellaneous\Call;

use Bldr\Block\Miscellaneous\Call\ExportCall;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 * @author Raul Rodriguez <raulrodriguez782@gmail.com>
 */
class ExportCallTest extends \PHPUnit_Framework_TestCase
{
    protected $method;
    protected $exportCall;

    public function setUp()
    {
        $this->exportCall = new ExportCall();
        $this->exportCall->configure();

        $object = new \ReflectionObject($this->exportCall);
        $this->method = $object->getMethod('setOption');
        $this->method->setAccessible(true);
    }

    public function testSetEnvVarsWithValidValues()
    {
        $this->method->invoke(
            $this->exportCall,
            'env_vars', ['SYMFONY_ENV=prod', 'TRAVIS_DEBUG=true', 'TRAVIS_BOOLEAN=']
        );

        $this->assertFalse(getenv('SYMFONY_ENV'));
        $this->assertFalse(getenv('TRAVIS_DEBUG'));
        $this->assertFalse(getenv('TRAVIS_BOOLEAN'));

        $this->exportCall->run();

        $this->assertEquals('prod', getenv('SYMFONY_ENV'));
        $this->assertEquals('true', getenv('TRAVIS_DEBUG'));
        $this->assertEquals('', getenv('TRAVIS_BOOLEAN'));
    }

    public function testSetEnvVarsWithInvalidValue()
    {
        $this->setExpectedException(
            \RuntimeException::class,
            'env var needs to follow the pattern e.g. SYMFONY_ENV=prod'
        );

        $this->method->invoke(
            $this->exportCall,
            'env_vars', ['TRAVIS_WRONG_VALUE']
        );

        $this->exportCall->run();
    }
}
