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

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ProfileEvent extends AbstractEvent
{
    /**
     * @return Task[]
     */
    public function getTasks()
    {
        return $this->command->getTasks();
    }

    /**
     * @param Task[] $tasks
     */
    public function setTasks($tasks)
    {
        $this->command->setTasks($tasks);
    }

    /**
     * @param Task $task
     */
    public function addTask(Task $task)
    {
        $this->command->addTask($task);
    }
}
