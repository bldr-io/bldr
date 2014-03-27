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
use Bldr\Config;
use Bldr\Event as Events;
use Bldr\Event;
use Bldr\Model\Task;
use Bldr\Registry\TaskRegistry;
use Bldr\Service\Builder;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class BuildCommand extends AbstractCommand
{
    /**
     * @var Builder $builder
     */
    private $builder;

    /**
     * @var TaskRegistry $tasks
     */
    private $tasks;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('build')
            ->setDescription("Builds the project for the directory you are in. Must contain a config file.")
            ->addOption('profile', 'p', InputOption::VALUE_REQUIRED, 'Profile to run', 'default')
            ->addOption('tasks', 't', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Tasks to run')
            ->setHelp(
                <<<EOF

The <info>%command.name%</info> builds the current project, using the config file in the root directory.

To use:

    <info>$ bldr %command.full_name% </info>

To specify a profile:

    <info>$ bldr %command.full_name% profile_name</info>

To specify tasks to run:

    <info>$ bldr %command.full_name% --tasks=task_name</info>
    <info>$ bldr %command.full_name% --tasks=task_name -t second_task</info>
    <info>$ bldr %command.full_name% --tasks=task_name,second_task</info>

EOF
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setInput($input)
            ->setOutput($output)
            ->getApplication()
            ->setBuildName();

        $this->tasks = $this->container->get('bldr.registry.task');
        $this->builder = $this->container->get('bldr.builder');
        $this->builder->initialize($input, $output, $this->getHelperSet());

        $this->output->writeln(["\n", Application::$logo, "\n"]);
        //$this->addEvent(Event::START, new Events\BuildEvent($this->tasks, $this->getConfig(), true));

        $this->doExecute($this->input->getOption('profile'), $this->input->getOption('tasks'));

        $this->succeedBuild();
        //$this->addEvent(Event::START, new Events\BuildEvent($this->tasks, $this->getConfig(), false));

        return 0;
    }

    public function doExecute($profileName = null, array $tasks = [])
    {
        if ([] === $tasks) {
            $config = $this->getConfig();

            $profile = $config->get('profiles')[$profileName];

            $projectFormat = [
                sprintf("Building the '%s' project", $config->get('name'))
            ];
            if ($config->has('description')) {
                $projectFormat[] = sprintf(" - %s - ", $config->get('description'));
            }

            $profileFormat = [
                sprintf("Using the '%s' profile", $profileName)
            ];
            if (isset($profile['description'])) {
                $profileFormat[] = sprintf(" - %s - ", $profile['description']);
            }

            $this->output->writeln(
                [
                    "",
                    $this->formatBlock($projectFormat, 'blue', 'black'),
                    "",
                    $this->formatBlock($profileFormat, 'blue', 'black'),
                    ""
                ]
            );

            $this->fetchTasks($profileName);
            $this->addEvent(Event::PRE_PROFILE, new Events\ProfileEvent($this, true));
        } else {
            $this->buildTasks($tasks);
        }

        $this->runTasks();

        if ([] === $tasks) {
            $this->addEvent(Event::POST_PROFILE, new Events\ProfileEvent($this, false));
        }
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->getApplication()
            ->getConfig();
    }

    /**
     * @param string|array $output
     * @param string       $background
     * @param string       $foreground
     *
     * @return string
     */
    private function formatBlock($output, $background, $foreground)
    {
        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');

        return $formatter->formatBlock($output, "bg={$background};fg={$foreground}");
    }

    /**
     * @param string $profileName
     */
    public function fetchTasks($profileName)
    {
        $config = $this->getConfig();

        $profile = $config->get('profiles')[$profileName];
        $this->buildTasks($profile['tasks']);
    }

    /**
     * @param string[] $names
     *
     * @return array
     */
    public function buildTasks($names)
    {
        foreach ($names as $name) {
            $taskInfo    = $this->getConfig()
                ->get('tasks')[$name];
            $description = isset($taskInfo['description']) ? $taskInfo['description'] : "";
            $task        = new Task($name, $description, $taskInfo['calls']);
            $this->tasks->addTask($task);
        }
    }

    /**
     *
     */
    public function runTasks()
    {
        while ($this->tasks->count() > 0) {
            $this->container->get('bldr.builder')
                ->runTask($this->tasks->getNewTask());
        }
    }

    /**
     * @return Integer
     */
    public function succeedBuild()
    {
        $this->output->writeln(["", $this->formatBlock('Build Success!', 'green', 'white'), ""]);

        return 0;
    }
}
