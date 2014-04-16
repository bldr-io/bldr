<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Filesystem\Call;

use Symfony\Component\Console\Helper\FormatterHelper;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class RemoveCall extends FilesystemCall
{
    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        parent::configure();
        $this->setName('remove')
            ->setDescription('Removes all the directories and files provided in `files`');
    }

    /**
     * Runs the command
     *
     * @throws \Exception
     * @return mixed
     */
    public function run()
    {
        $files = $this->resolveFiles();

        foreach ($files as $file) {
            if (!$this->fileSystem->exists($file)) {
                if ($this->getFailOnError()) {
                    throw new \Exception("File `$file` does not exist.");
                }

                continue;
            }

            $this->fileSystem->remove([$file]);

            /** @var FormatterHelper $formatter */
            $formatter = $this->getHelperSet()->get('formatter');
            $this->getOutput()->writeln(
                $formatter->formatSection(
                    $this->getTask()->getName(),
                    "Deleting $file"
                )
            );
        }

        return true;
    }
}
