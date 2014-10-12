<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Filesystem\Task;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class TouchTask extends FilesystemTask
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        parent::configure();
        $this->setName('touch')
            ->setDescription('Touches all the files provided in `files`')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function run(OutputInterface $output)
    {
        foreach ($this->resolveFiles() as $file) {
            $this->fileSystem->touch([$file]);
            $output->writeln(
                ["", sprintf("    <info>[%s]</info> - <comment>Touching %s</comment>", $this->getName(), $file), ""]
            );
        }
    }
}
