<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Extension\Filesystem\Call;

use Symfony\Component\Console\Helper\FormatterHelper;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class DumpCall extends FilesystemCall
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->setName('dump')
            ->setDescription('Dumps the `content` into the given file')
            ->addOption('file', true, 'File to dump content to')
            ->addOption('append', true, 'If true, will append to the given file.', false)
            ->addOption('character', true, 'If append is true, character to use to append.', "\n")
            ->addOption('content', true, 'Content to dump to file');
    }

    /**
     * Runs the command
     *
     * @throws \Exception
     * @return mixed
     */
    public function run()
    {
        $file = $this->getOption('file');
        if (strpos($file, '/') !== 0) {
            $file = getcwd() . '/' . ltrim($file, '/');
        }

        $content = $this->getOption('content');
        if ($this->getOption('append')) {
            $content = file_get_contents($file) . $this->getOption('character') . $content;
        }

        $this->filesystem->dumpFile($file, $content);

        return true;
    }
}
