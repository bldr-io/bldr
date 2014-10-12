<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Tests\Block\Miscellaneous\Task;

use Bldr\Block\Miscellaneous\Task\ExportTask;
use Bldr\Block\Miscellaneous\Service\EnvironmentVariableRepository;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 * @author Raul Rodriguez <raulrodriguez782@gmail.com>
 */
class ExportTaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type ExportTask
     */
    protected $exportTask;

    /**
     * @type EnvironmentVariableRepository
     */
    protected $environmentVariableRepository;

    /**
     *
     */
    public function setUp()
    {
        $this->environmentVariableRepository = new EnvironmentVariableRepository();
        $this->exportTask = new ExportTask($this->environmentVariableRepository);
        $this->exportTask->configure();
    }

    /**
     *
     */
    public function testSetEnvVarsWithValidValues()
    {
        $this->exportTask->setParameter('arguments', ['SYMFONY_ENV=prod', 'TRAVIS_DEBUG=true', 'TRAVIS_BOOLEAN=']);

        $this->assertCount(0, $this->environmentVariableRepository->getEnvironmentVariables());

        $this->exportTask->run(new NullOutput());

        $this->assertCount(3, $this->environmentVariableRepository->getEnvironmentVariables());
    }

    /**
     *
     */
    public function testSetEnvVarsWithInvalidValue()
    {
        $this->setExpectedException(
            '\Bldr\Exception\TaskRuntimeException',
            'Each argument needs to follow the pattern e.g. SYMFONY_ENV=prod'
        );

        $this->exportTask->setParameter('arguments', ['TRAVIS_WRONG_VALUE']);

        $this->exportTask->run(new NullOutput());
    }
}
