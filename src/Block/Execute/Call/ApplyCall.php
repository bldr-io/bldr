<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Execute\Call;

use Bldr\Call\Traits\FinderAwareTrait;
use Symfony\Component\Console\Helper\FormatterHelper;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ApplyCall extends ExecuteCall
{
    use FinderAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        parent::configure();
        $this->setName('apply')
            ->addOption('src', true, 'Source to run the apply on')
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelperSet()->get('formatter');

        $this->getOutput()->writeln($formatter->formatSection($this->getTask()->getName(), 'Starting'));

        $arguments = $this->getOption('arguments');
        $files     = $this->getFiles($this->getOption('src'));
        foreach ($files as $file) {
            $args   = $arguments;
            $args[] = $file->getRealPath();
            $this->setOption('arguments', $args);
            parent::run();
        }

        return true;
    }
}
