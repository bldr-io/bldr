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
class TouchCall extends FilesystemCall
{
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

            $this->fileSystem->touch([$file]);

            /** @var FormatterHelper $formatter */
            $formatter = $this->getHelperSet()->get('formatter');
            $this->getOutput()->writeln(
                $formatter->formatSection(
                    $this->getTask()->getName(),
                    "Touching $file"
                )
            );
        }

        return true;
    }
}
