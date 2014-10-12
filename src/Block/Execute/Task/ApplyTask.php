<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Execute\Task;

use Bldr\Block\Core\Task\Traits\FinderAwareTrait;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ApplyTask extends ExecuteTask
{
    use FinderAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        parent::configure();
        $this->setName('apply')
            ->addParameter('src', true, 'Source to run the apply on')
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function run(OutputInterface $output)
    {
        $output->writeln(['', sprintf("    <info>[%s]</info> - <comment>Starting</comment>", $this->getName()), '']);

        $arguments = $this->getParameter('arguments');
        $files     = $this->getFiles($this->getParameter('src'));
        foreach ($files as $file) {
            $args   = $arguments;
            $args[] = $file->getRealPath();
            $this->setParameter('arguments', $args);
            parent::run($output);
        }
    }
}
