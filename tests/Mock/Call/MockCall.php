<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test\Mock\Call;

use Bldr\Call\AbstractCall;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class MockCall extends AbstractCall
{
    /**
     * Configures the Task
     */
    public function configure()
    {
        $this->setName('mock');
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {

    }
}
