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
        $this->getApplication()
            ->setBuildName();

        $output->writeln(["\n", Application::$logo, "\n"]);
        $config = $this->getApplication()
            ->getConfig();
        $this->addEvent(Event::START, new Events\BuildEvent($this->getApplication(), $input, true));

        if ([] === $tasks = $input->getOption('tasks')) {
            $tasks = $this->getTasks($output, $input->getOption('profile'), $config);
        }

        $this->addEvent(Event::PRE_PROFILE, new Events\ProfileEvent($this->getApplication(), $input, $tasks, true));
        $this->runTasks($input, $output, $tasks);
        $this->addEvent(Event::POST_PROFILE, new Events\ProfileEvent($this->getApplication(), $input, $tasks, false));

        $this->succeedBuild($output);

        $this->addEvent(Event::START, new Events\BuildEvent($this->getApplication(), $input, false));

        return 0;
    }

    /**
     * @param OutputInterface $output
     * @param string          $profileName
     * @param ParameterBag    $config
     *
     * @return mixed
     */
    private function getTasks(OutputInterface $output, $profileName, ParameterBag $config)
    {
        $profile = $config->get('profiles')[$profileName];
        $tasks   = $this->buildTasks($config, $profile['tasks']);

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

        $output->writeln(
            [
                "",
                $this->formatBlock($projectFormat, 'blue', 'black'),
                "",
                $this->formatBlock($profileFormat, 'blue', 'black'),
                ""
            ]
        );

        return $tasks;
    }

    /**
     * @param Config $config
     * @param        $names
     *
     * @return array
     */
    private function buildTasks(Config $config, $names)
    {
        $tasks = [];
        foreach ($names as $name) {
            $taskInfo     = $config->get('tasks')[$name];
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
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Task[]          $tasks
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
     * @param Task            $task
     */
    private function runTask(InputInterface $input, OutputInterface $output, Task $task)
    {
        $output->writeln(
            [
                "",
                sprintf(
                    "<info>Running the %s task</info>\n<comment>%s</comment>",
                    $task->getName(),
                    $task->getDescription() !== '' ? '> ' . $task->getDescription() : ''
                ),
                ""
            ]
        );

        $this->addEvent(Event::PRE_TASK, new Events\TaskEvent($this->getApplication(), $input, $task, true));
        foreach ($task->getCalls() as $call) {
            $this->addEvent(Event::PRE_CALL, new Events\CallEvent($this->getApplication(), $input, $call, true));
            $this->runCall($input, $output, $task, $call);
            $this->addEvent(Event::POST_CALL, new Events\CallEvent($this->getApplication(), $input, $call, false));
        }
        $this->addEvent(Event::POST_TASK, new Events\TaskEvent($this->getApplication(), $input, $task, false));

        $output->writeln("");
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param Task            $task
     * @param Call            $call
     */
    private function runCall(InputInterface $input, OutputInterface $output, Task $task, Call $call)
    {

        $config = $this->getApplication()
            ->getConfig();

        $service = $this->fetchServiceForCall($call->getType());

        $service->initialize($input, $output, $this->getHelperSet(), $config);
        $service->setTask($task);
        $service->setCall($call);

        $this->addEvent(Event::PRE_SERVICE, new Events\ServiceEvent($this->getApplication(), $input, $service, true));
        $service->run($call->getArguments());
        $this->addEvent(Event::POST_SERVICE, new Events\ServiceEvent($this->getApplication(), $input, $service, false));
        $output->writeln("");
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
     * @param OutputInterface $output
     *
     * @return Integer
     */
    public function succeedBuild(OutputInterface $output)
    {
        $output->writeln(["", $this->formatBlock('Build Success!', 'green', 'white'), ""]);

        return 0;
    }
}
