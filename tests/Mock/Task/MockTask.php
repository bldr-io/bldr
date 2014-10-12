<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test\Mock\Task;

use Bldr\Block\Core\Task\AbstractTask;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class MockTask extends AbstractTask
{
    /**
     * Configures the Task
     */
    public function configure()
    {
        $this->setName('mock')
            ->setDescription('mock description');
    }

    /**
     * {@inheritDoc}
     */
    public function run(OutputInterface $output)
    {
    }
}
