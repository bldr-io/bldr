<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Command;

use Bldr\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Bldr\Helper\DialogHelper;
use Symfony\Component\Console\Helper\FormatterHelper;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class BuildCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('build')
            ->setDescription("Builds the project for the directory you are in. Must contain a .bldr.yml file.")
            ->addOption('profile', 'p', InputOption::VALUE_REQUIRED, 'Profile to run', 'default')
            ->setHelp(
                <<<EOF

The <info>%command.name%</info> builds the current project, using the .bldr.yml file in the root directory.

To use:

    <info>$ bldr %command.full_name% </info>

EOF
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(["\n", Application::$logo, "\n"]);

        /** @var ParameterBag $config */
        $config      =
            $this->getApplication()
                ->getConfig();
        $profileName = $input->getOption('profile');
        $profile     = $config->get('profiles')[$profileName];

        /** @var DialogHelper $dialog */
        $dialog = $this->getHelper('dialog');
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');

        $output->writeln(
            [
                "",
                $formatter->formatBlock(
                    [
                        sprintf("Building the '%s' project", $config->get('name')),
                        sprintf(" - %s - ", $config->get('description'))
                    ],
                    'bg=blue;fg=white',
                    true
                ),
                "",
                $formatter->formatBlock(
                    [
                        sprintf("Using the '%s' profile", $profileName),
                        sprintf(" - %s - ", $profile['description'])
                    ],
                    'bg=green;fg=white',
                    true
                ),
                ""
            ]
        );

        $this->runTasks($input, $output, $profile['tasks']);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param array           $tasks
     */
    private function runTasks(InputInterface $input, OutputInterface $output, array $tasks)
    {
        foreach ($tasks as $task) {
            $this->runTask($input, $output, $task);
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param string          $taskName
     */
    private function runTask(InputInterface $input, OutputInterface $output, $taskName)
    {
        /** @var ParameterBag $config */
        $config = $this->getApplication()->getConfig();
        $task   = $config->get('tasks')[$taskName];

        /** @var DialogHelper $dialog */
        $dialog = $this->getHelper('dialog');
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');

        $output->writeln(
            [
                "",
                sprintf("<info>Running the %s task</info>\n<comment>> %s</comment>", $taskName, $task['description']),
                ""
            ]
        );

        foreach ($task['calls'] as $call) {
            var_dump($call);
        }
    }
}

