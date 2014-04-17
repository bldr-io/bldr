<?php

/**
 * This file is part of bldr
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Miscellaneous\Call;

use Bldr\Call\AbstractCall;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class SleepCall extends AbstractCall
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setName('sleep')
            ->setDescription('Sleep for the given amount of time')
            ->addOption('seconds', true, 'Milliseconds to sleep for.');
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        sleep($this->getOption('seconds'));
    }
}
