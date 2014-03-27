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

    public function configure()
    {
        parent::configure();
        $this->addOption('fileset', true, 'The fileset to run the executable on');
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $this->setFileset($this->getOption('fileset'));

        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelperSet()->get('formatter');

        $this->getOutput()->writeln($formatter->formatSection($this->getTask()->getName(), 'Starting'));

        foreach ($this->files as $file) {
            $args = $this->getOption('arguments');
            $args[] = $file;
            $this->setOption('arguments', $args);
            parent::run();
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
