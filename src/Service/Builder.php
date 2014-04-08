<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Service;

use Bldr\Call\CallInterface;
use Bldr\Event as Events;
use Bldr\Event;
use Bldr\Model\Call;
use Bldr\Model\Task;
use Bldr\Registry\TaskRegistry;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class Builder
{
    /**
     * @var EventDispatcherInterface $dispatcher
     */
    private $dispatcher;

    /**
     * @var CallInterface[] $tasks
     */
    private $tasks;

    /**
     * @var InputInterface $input
     */
    private $input;

    /**
     * @var OutputInterface $output
     */
    private $output;

    /**
     * @var HelperSet $helperSet
     */
    private $helperSet;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        InputInterface $input,
        OutputInterface $output,
        array $tasks = []
    )
    {
        $this->dispatcher = $dispatcher;
        $this->tasks      = $tasks;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param HelperSet       $helperSet
     */
    public function initialize(InputInterface $input, OutputInterface $output, HelperSet $helperSet)
    {
        $this->input     = $input;
        $this->output    = $output;
        $this->helperSet = $helperSet;
    }

    /**
     * @param TaskRegistry $tasks
     *
     * @throws \Exception
     */
    public function runTasks(TaskRegistry $tasks)
    {
        $failed = false;
        $exception = null;

        while ($tasks->count() > 0) {
            $task = $tasks->getNewTask();
            if ($failed && !$task->isRunOnFailure()) {
                continue;
            }

            try {
                $this->runTask($task);
            } catch (\Exception $e) {
                $exception = $e;
            }
        }

        if ($exception !== null) {
            throw $exception;
        }
    }

    /**
     * @param Task $task
     *
     * @throws \Exception
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

        //$this->addEvent(Event::PRE_TASK, new Events\TaskEvent($this, $task, true));
        foreach ($task->getCalls() as $call) {
            //$this->addEvent(Event::PRE_CALL, new Events\CallEvent($this, $task, $call, true));
            $this->runCall($task, $call);
            //$this->addEvent(Event::POST_CALL, new Events\CallEvent($this, $task, $call, false));
        }
        //$this->addEvent(Event::POST_TASK, new Events\TaskEvent($this, $task, false));

        $this->output->writeln("");
    }

    /**
     * @param Task $task
     * @param Call $call
     */
    private function runCall(Task $task, Call $call)
    {
        $service = $this->fetchServiceForCall($task, $call);
        $service->initialize($this->input, $this->output, $this->helperSet, $task, $call);

        //$this->addEvent(Event::PRE_SERVICE, new Events\ServiceEvent($this, $task, $call, $service, true));
        $service->run();
        //$this->addEvent(Event::POST_SERVICE, new Events\ServiceEvent($this, $task, $call, $service, false));
        $this->output->writeln("");
    }

    /**
     * @param Task $task
     * @param Call $call
     *
     * @throws \Exception
     * @return CallInterface
     */
    private function fetchServiceForCall(Task $task, Call $call)
    {
        $services = [];
        foreach ($this->tasks as $service) {
            $service->configure();

            if ($service->getName() === $call->getType()) {
                $services[] = $service;
            }
        }

        if (sizeof($services) > 1) {
            throw new \Exception("Multiple calls exist with the 'exec' tag.");
        }
        if (sizeof($services) === 0) {
            throw new \Exception("No task type found for {$call->getType()}.");
        }

        return $services[0];
    }

    private function addEvent($eventName, Event\EventInterface $event)
    {
        $this->dispatcher->dispatch($eventName, $event);
    }
}
