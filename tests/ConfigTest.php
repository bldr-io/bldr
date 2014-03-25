<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test;

use Bldr\Config;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testCreate()
    {
        Config::$NAME = '.test';

        foreach (['yml', 'json'] as $type) {
            foreach ([true, false] as $dist) {
                Config::create($type, ['name' => 'test-config'], $dist, false);

                $config = Config::factory();
                $this->assertEquals('test-config', $config->get('name'));

                $file = Config::$NAME . '.' . $type . ($dist ? '.dist' : '');
                Config::checkForFile($file, true);
            }
        }
    }

    /**
     * @expectedException \Exception
     */
    public function testIsTypeAllowedException()
    {
        Config::isTypeAllowed('badType');
    }

    /**
     * @expectedException \Exception
     */
    public function testCheckForFileException()
    {
        Config::checkForFile(__FILE__);
    }

    /**
     * @expectedException \Exception
     */
    public function testGetFileException()
    {
        Config::$NAME = '.badTest';
        Config::getFile();
    }

    /**
     *
     */
    protected function tearDown()
    {
        foreach (['yml', 'json'] as $type) {
            foreach ([true, false] as $dist) {
                $file = Config::$NAME . '.' . $type . ($dist ? '.dist' : '');
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }
}
