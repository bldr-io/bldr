<?php

/**
 * This file is part of bldr
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test\Mock;

use Symfony\Component\Console\Application;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class MockApplication extends Application
{
    public function setBuildName()
    {
    }
}
