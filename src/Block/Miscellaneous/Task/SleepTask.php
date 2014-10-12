<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Miscellaneous\Task;

use Bldr\Block\Core\Task\AbstractTask;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class SleepTask extends AbstractTask
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setName('sleep')
            ->setDescription('Sleep for the given amount of time')
            ->addParameter('seconds', true, 'Milliseconds to sleep for.')
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function run(OutputInterface $output)
    {
        sleep($this->getParameter('seconds'));
    }
}
