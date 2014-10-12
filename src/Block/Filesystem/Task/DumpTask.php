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
class DumpTask extends FilesystemTask
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setName('dump')
            ->setDescription('Dumps the `content` into the given file')
            ->addParameter('file', true, 'File to dump content to')
            ->addParameter('append', true, 'If true, will append to the given file.', false)
            ->addParameter('character', true, 'If append is true, character to use to append.', "\n")
            ->addParameter('content', true, 'Content to dump to file')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function run(OutputInterface $output)
    {
        $file = $this->getParameter('file');
        if (strpos($file, '/') !== 0) {
            $file = getcwd().'/'.ltrim($file, '/');
        }

        $content = $this->getParameter('content');
        if ($this->getParameter('append')) {
            $content = file_get_contents($file).$this->getParameter('character').$content;
        }

        $this->fileSystem->dumpFile($file, $content);
    }
}
