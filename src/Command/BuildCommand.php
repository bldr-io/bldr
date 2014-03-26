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
use Bldr\Call\CallInterface;
use Bldr\Config;
use Bldr\Event;
use Bldr\Event as Events;
use Bldr\Model\Call;
use Bldr\Model\Task;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class BuildCommand extends AbstractCommand
{
    /**
     * @var Task[] $tasks
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
     * @return Config
     */
    public function getConfig()
    {
        return $this->getApplication()
            ->getConfig();
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

        $this->output->writeln(["\n", Application::$logo, "\n"]);
        $this->addEvent(Event::START, new Events\BuildEvent($this, true));

        $this->doExecute($this->input->getOption('profile'), $this->input->getOption('tasks'));

        $this->succeedBuild();

        $this->addEvent(Event::START, new Events\BuildEvent($this, false));

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

            $this->tasks = $this->fetchTasks($profileName);
            $this->addEvent(Event::PRE_PROFILE, new Events\ProfileEvent($this, true));
        } else {

            $this->tasks = $this->buildTasks($tasks);
        }

        $this->runTasks();

        if ([] === $tasks) {
            $this->addEvent(Event::POST_PROFILE, new Events\ProfileEvent($this, false));
        }
    }

    /**
     * @param string       $profileName
     *
     * @return Task[]
     */
    public function fetchTasks($profileName)
    {
        $config = $this->getConfig();

        $profile = $config->get('profiles')[$profileName];
        $tasks   = $this->buildTasks($profile['tasks']);

        return $tasks;
    }

    /**
     * @param string[] $names
     *
     * @return array
     */
    public function buildTasks($names)
    {
        $tasks = [];
        foreach ($names as $name) {
            $taskInfo     = $this->getConfig()->get('tasks')[$name];
            $description  = isset($taskInfo['description']) ? $taskInfo['description'] : "";
            $task         = new Task($name, $description, $taskInfo['calls']);
            $tasks[$name] = $task;
        }

        return $tasks;
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
     *
     */
    public function runTasks()
    {
        while (sizeof($this->tasks) > 0) {
            $this->runTask(array_shift($this->tasks));
        }
    }

    /**
     * @param Task $task
     */
    public function runTask(Task $task)
    {
        $this->output->writeln(
            [
                "",
                sprintf(
                    "<info>Running the %s task</info><comment>%s</comment>",
                    $task->getName(),
                    $task->getDescription() !== '' ? "\n> " . $task->getDescription() : ''
                ),
                ""
            ]
        );

        $this->addEvent(Event::PRE_TASK, new Events\TaskEvent($this, $task, true));
        foreach ($task->getCalls() as $call) {
            $this->addEvent(Event::PRE_CALL, new Events\CallEvent($this, $task, $call, true));
            $this->runCall($task, $call);
            $this->addEvent(Event::POST_CALL, new Events\CallEvent($this, $task, $call, false));
        }
        $this->addEvent(Event::POST_TASK, new Events\TaskEvent($this, $task, false));

        $this->output->writeln("");
    }

    /**
     * @param Task $task
     * @param Call $call
     */
    private function runCall(Task $task, Call $call)
    {
        $service = $this->fetchServiceForCall($call->getType());

        $service->initialize($this);
        $service->setTask($task);
        $service->setCall($call);

        $this->addEvent(Event::PRE_SERVICE, new Events\ServiceEvent($this, $task, $call, $service, true));
        $service->run($call->getArguments());
        $this->addEvent(Event::POST_SERVICE, new Events\ServiceEvent($this, $task, $call, $service, false));
        $this->output->writeln("");
    }

    /**
     * @param string $type
     *
     * @return CallInterface
     * @throws \Exception
     */
    private function fetchServiceForCall($type)
    {
        $services = array_keys($this->container->findTaggedServiceIds($type));

        if (sizeof($services) > 1) {
            throw new \Exception("Multiple calls exist with the 'exec' tag.");
        }
        if (sizeof($services) === 0) {
            throw new \Exception("No task type found for {$type}.");
        }

        return $this->container->get($services[0]);
    }

    /**
     * @return Integer
     */
    public function succeedBuild()
    {
        $this->output->writeln(["", $this->formatBlock('Build Success!', 'green', 'white'), ""]);

        return 0;
    }

    /**
     * @return Task[]
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * @param Task[] $tasks
     */
    public function setTasks($tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * @param Task $task
     */
    public function addTask(Task $task)
    {
        $this->tasks[] = $task;
    }
}
