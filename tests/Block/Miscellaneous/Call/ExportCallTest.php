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
use Bldr\Block\Miscellaneous\Service\EnvironmentVariableRepository;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 * @author Raul Rodriguez <raulrodriguez782@gmail.com>
 */
class ExportCallTest extends \PHPUnit_Framework_TestCase
{
    protected $method;
    protected $exportCall;
    protected $environmentVariableRepository;

    public function setUp()
    {
        $this->environmentVariableRepository = new EnvironmentVariableRepository();
        $this->exportCall = new ExportCall($this->environmentVariableRepository);
        $this->exportCall->configure();

        $object = new \ReflectionObject($this->exportCall);
        $this->method = $object->getMethod('setOption');
        $this->method->setAccessible(true);
    }

    public function testSetEnvVarsWithValidValues()
    {
        $this->method->invoke(
            $this->exportCall,
            'arguments', ['SYMFONY_ENV=prod', 'TRAVIS_DEBUG=true', 'TRAVIS_BOOLEAN=']
        );

        $this->assertCount(0, $this->environmentVariableRepository->getEnvironmentVariables());

        $this->exportCall->run();

        $this->assertCount(3, $this->environmentVariableRepository->getEnvironmentVariables());
    }

    public function testSetEnvVarsWithInvalidValue()
    {
        $this->setExpectedException(
            '\RuntimeException',
            'Each argument needs to follow the pattern e.g. SYMFONY_ENV=prod'
        );

        $this->method->invoke(
            $this->exportCall,
            'arguments', ['TRAVIS_WRONG_VALUE']
        );

        $this->exportCall->run();
    }
}
