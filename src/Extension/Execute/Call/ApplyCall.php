<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Extension\Execute\Call;

use Symfony\Component\Console\Helper\FormatterHelper;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ApplyCall extends ExecuteCall
{
    /**
     * @var string $fileset
     */
    private $fileset;

    /**
     * @var array $files
     */
    private $files;

    /**
     * {@inheritDoc}
     *
     * Logic obtained from http://stackoverflow.com/a/6144213/248903
     */
    public function run()
    {
        $this->setFileset($this->getCall()->fileset);

        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelperSet()->get('formatter');

        $this->getOutput()->writeln(
            [
                "",
                $formatter->formatSection($this->getTask()->getName(), 'Starting'),
                ""
            ]
        );

        foreach ($this->files as $file) {
            $args   = $arguments;
            $args[] = $file;

            parent::run($args);
        }
    }

    /**
     * @param string $fileset
     */
    public function setFileset($fileset)
    {
        $this->fileset = $fileset;
        $this->files = glob($fileset);
    }
}
