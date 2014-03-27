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

use Bldr\Model\Call;
use Bldr\Model\Task;
use Bldr\Event;
use Bldr\Event as Events;
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
     * @var OutputInterface $output
     */
    private $output;

    public function __construct(EventDispatcherInterface $dispatcher, array $tasks = [])
    {
        $this->dispatcher = $dispatcher;
        $this->tasks      = $tasks;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
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
        $service->run();
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

    private function addEvent($eventName, Event\EventInterface $event)
    {
        $this->dispatcher->dispatch($eventName, $event);
    }
}
