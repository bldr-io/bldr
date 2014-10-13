<?php

/**
 * This file is part of bldr
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Registry;

use Bldr\Block\Core\Task\AbstractTask;
use Bldr\Exception\TaskNotFoundException;
use Bldr\Task\TaskInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class TaskRegistry
{
    /**
     * @type TaskInterface[]|AbstractTask[] $tasks
     */
    private $tasks;

    /**
     * @param EventDispatcherInterface       $dispatcher
     * @param TaskInterface[]|AbstractTask[] $tasks
     */
    public function __construct(EventDispatcherInterface $dispatcher, array $tasks)
    {
        foreach ($tasks as $task) {
            if ($task instanceof AbstractTask) {
                $task->configure();
            }

            if (method_exists($task, 'setEventDispatcher')) {
                $task->setEventDispatcher($dispatcher);
            }

            $this->tasks[$task->getName()] = $task;
        }
    }

    /**
     * @param string $type
     *
     * @throws TaskNotFoundException
     * @return TaskInterface|AbstractTask
     */
    public function findTaskByType($type)
    {
        foreach ($this->tasks as $task) {
            if ($task->getName() === $type) {
                return $task;
            }
        }

        throw new TaskNotFoundException($type);
    }

    /**
     * @return AbstractTask[]|TaskInterface[]
     */
    public function findAll()
    {
        return $this->tasks;
    }
}
