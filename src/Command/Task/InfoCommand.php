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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;

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
            ->setDescription("Lists all the options for the given task.")
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
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $this->findService($input->getArgument('task'));

        $description = ($service->getDescription() !== '' ? $service->getDescription() : 'No Description');
        $output->writeln(
            [
                "",
                '<fg=green>Task Name</fg=green>: '.$service->getName(),
                '<fg=green>Task Description</fg=green>: '.$description,
                "",
                "<fg=green>Options:</fg=green>"
            ]
        );

        /** @var TableHelper $tableHelper */
        $tableHelper = $this->getHelperSet()->get('table');
        $tableHelper->setHeaders(['Option', 'Description', 'Required', "Default"]);
        $tableHelper->setCellHeaderFormat('<fg=red>%s</fg=red>');
        $tableHelper->setCellRowFormat('<fg=blue>%s</fg=blue>');
        $tableHelper->setBorderFormat('<fg=yellow>%s</fg=yellow>');

        foreach ($service->getOptions() as $option) {
            $tableHelper->addRow(
                [
                    $option['name'],
                    $option['description'] !== '' ? $option['description'] : 'No Description',
                    $option['required'] ? 'Yes' : 'No',
                    json_encode($option['default'])
                ]
            );
        }

        $tableHelper->render($output);
    }

    /**
     * @param string $name
     *
     * @return CallInterface
     * @throws \Exception
     */
    private function findService($name)
    {

        $serviceIds = array_keys($this->container->findTaggedServiceIds('bldr'));
        foreach ($serviceIds as $id) {
            /** @var CallInterface $service */
            $service = $this->container->get($id);
            $service->configure();
            if ($service->getName() === $name) {
                return $service;
            }
        }

        throw new \Exception("Service with the name '$name' does not exist.");
    }
}
