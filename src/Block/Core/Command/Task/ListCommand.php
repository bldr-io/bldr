<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Core\Command\Task;

use Bldr\Block\Core\Task\AbstractTask;
use Bldr\Command\AbstractCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ListCommand extends AbstractCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('task:list')
            ->setDescription("Lists all the available tasks for Bldr.")
            ->setHelp(
                <<<EOF

The <info>%command.name%</info> lists all the available tasks for Bldr.

To use:

    <info>$ bldr %command.name%</info>

EOF
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function doExecute()
    {
        $table = new Table($this->output);
        $table->setHeaders(['Name', 'Description']);

        $style = new TableStyle();
        $style->setCellHeaderFormat('<fg=red>%s</fg=red>');
        $style->setCellRowFormat('<fg=blue>%s</fg=blue>');
        $style->setBorderFormat('<fg=yellow>%s</fg=yellow>');

        $table->setStyle($style);


        /** @type AbstractTask[] $services */
        $services = $this->container->get('bldr.registry.task')->findAll();
        foreach ($services as $service) {

            if ($service instanceof AbstractTask) {
                $service->configure();
            }

            $table->addRow(
                [
                    $service->getName(),
                    $service->getDescription() !== '' ? $service->getDescription() : 'No Description'
                ]
            );
        }

        $table->render($this->output);
    }
}
