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

use Bldr\Output\NullBldrOutput;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class MockApplication extends Application
{
    public function setBuildName()
    {
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        return parent::run(null, new NullBldrOutput());
    }
}
 