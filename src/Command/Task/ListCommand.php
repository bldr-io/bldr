<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Command\Task;

use Bldr\Call\CallInterface;
use Bldr\Command\AbstractCommand;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var TableHelper $tableHelper */
        $tableHelper = $this->getHelperSet()->get('table');
        $tableHelper->setHeaders(['Name', 'Description']);
        $tableHelper->setCellHeaderFormat('<fg=red>%s</fg=red>');
        $tableHelper->setCellRowFormat('<fg=blue>%s</fg=blue>');
        $tableHelper->setBorderFormat('<fg=yellow>%s</fg=yellow>');

        $serviceIds = array_keys($this->container->findTaggedServiceIds('bldr'));
        foreach ($serviceIds as $id) {
            /** @var CallInterface $service */
            $service = $this->container->get($id);
            $service->configure();
            $tableHelper->addRow(
                [
                    $service->getName(),
                    $service->getDescription() !== '' ?: 'No Description'
                ]
            );
        }

        $tableHelper->render($output);
    }
}
