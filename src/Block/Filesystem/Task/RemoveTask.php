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

use Bldr\Exception\TaskRuntimeException;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class RemoveTask extends FilesystemTask
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
     * {@inheritdoc}
     */
    public function run(OutputInterface $output)
    {
        foreach ($this->resolveFiles() as $file) {
            if (!$this->fileSystem->exists($file)) {
                if (!$this->continueOnError()) {
                    throw new TaskRuntimeException($this->getName(), "File `$file` does not exist.");
                }

                if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE) {
                    $output->writeln(
                        [
                            "",
                            sprintf(
                                "<error> [Error] Task: %s \n Message: %s</error>",
                                $this->getName(),
                                "File `$file` does not exist."
                            ),
                            ""
                        ]
                    );
                }

                continue;
            }

            $this->fileSystem->remove([$file]);
            $output->writeln(
                ["", sprintf("    <info>[%s]</info> - <comment>Deleting %s</comment>", $this->getName(), $file), ""]
            );
        }
    }
}
