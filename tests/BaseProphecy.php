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

use Prophecy\Prophet;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 */
class BaseProphecy extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Prophet
     */
    protected $prophet;

    public function prophesy($classOrInterface)
    {
        return $this->prophet->prophesize($classOrInterface);
    }

    protected function setup()
    {
        $this->prophet = new Prophet();
    }

    protected function tearDown()
    {
        $this->prophet->checkPredictions();
    }
}
