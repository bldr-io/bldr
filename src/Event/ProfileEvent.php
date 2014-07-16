<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Event;

use Bldr\Model\Task;
use Bldr\Registry\TaskRegistry;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ProfileEvent extends AbstractEvent
{
    /**
     * @var TaskRegistry
     */
    private $registry;

    /**
     * @return Task[]
     */
    public function getTasks()
    {
        return $this->registry->getTasks();
    }

    /**
     * @param Task[] $tasks
     */
    public function setTasks($tasks)
    {
        $this->registry->setTasks($tasks);
    }

    /**
     * @param Task $task
     */
    public function addTask(Task $task)
    {
        $this->registry->addTask($task);
    }
}
