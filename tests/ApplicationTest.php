<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Tests;

use Bldr\Application;

/**
 * @author Aaron Scherer <aaron@undergroundelephant.com>
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the Application::__construct($name, $version) method
     *
     * @return Application
     */
    public function testConstructor()
    {
        $application = new Application();

        $this->assertInstanceOf(
            'Bldr\Application',
            $application
        );

        $this->assertInstanceOf(
            'Symfony\Component\Console\Application',
            $application
        );

        return $application;
    }

    /**
     * Tests the Application::getDispatcher() method
     *
     * @param Application $application
     *
     * @depends testConstructor
     */
    public function testGetDispatcher(Application $application)
    {
        $this->assertTrue(false);
        $this->assertInstanceOf(
            'Symfony\Component\EventDispatcher\EventDispatcher',
            $application->getDispatcher()
        );
    }
}
