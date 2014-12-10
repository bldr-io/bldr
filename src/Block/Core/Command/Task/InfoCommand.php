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
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class InfoCommand extends AbstractCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('task:info')
            ->setDescription('Lists all the options for the given task.')
            ->addArgument('task', InputArgument::REQUIRED, 'The task to list info for')
            ->setHelp(
                <<<EOF

The <info>%command.name%</info> lists all the options for a specified task.

To use:

    <info>$ bldr %command.name% <task_name></info>

EOF
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function doExecute()
    {
        /** @type AbstractTask $service */
        $service = $this->container->get('bldr.registry.task')->findTaskByType($this->input->getArgument('task'));

        $this->output->writeln('');
        $this->output->writeln('<fg=green>Task Name</fg=green>: '.$service->getName());
        if ($service->getDescription() !== null) {
            $this->output->writeln('<fg=green>Task Description</fg=green>: '.$service->getDescription());
        }

        if ($service instanceof AbstractTask) {
            $this->output->writeln(['', '<fg=green>Options:</fg=green>']);
            /** @var TableHelper $tableHelper */
            $tableHelper = $this->getHelperSet()->get('table');
            $tableHelper->setHeaders(['Option', 'Description', 'Required', "Default"]);
            $tableHelper->setCellHeaderFormat('<fg=red>%s</fg=red>');
            $tableHelper->setCellRowFormat('<fg=blue>%s</fg=blue>');
            $tableHelper->setBorderFormat('<fg=yellow>%s</fg=yellow>');
            foreach ($service->getParameterDefinition() as $option) {
                $tableHelper->addRow(
                    [
                        $option['name'],
                        $option['description'] !== '' ? $option['description'] : 'No Description',
                        $option['required'] ? 'Yes' : 'No',
                        json_encode($option['default'])
                    ]
                );
            }
            $tableHelper->render($this->output);
        }
    }
}
