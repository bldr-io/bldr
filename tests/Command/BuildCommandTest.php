<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test\Command;


use Bldr\Command\InitCommand;
use Symfony\Component\Console\Application;

class BuildCommandTest extends \PHPUnit_Framework_TestCase
{

    public function testExecute()
    {
        return $this->markTestIncomplete();

        $application = new Application();
        // Need to change the config name so we don't conflict with the projects config
        $class      = new \ReflectionClass($application);
        $configName = $class->getProperty('configName');
        $configName->setAccessible(true);
        $configName->setValue($application, '.test.yml');

        $application->add(new InitCommand());
    }
}
