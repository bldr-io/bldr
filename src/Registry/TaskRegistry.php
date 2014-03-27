<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Registry;

use Bldr\Model\Task;

/**
 * @author Aaron Scherer <aequasi@gmail..com>
 */
class TaskRegistry
{
    /**
     * @var Task[] $tasks
     */
    private $tasks;

    /**
     * @return mixed
     */
    public function getNewTask()
    {
        return array_shift($this->tasks);
    }

    /**
     * @param Task $task
     *
     * @return $this
     */
    public function addTask(Task $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return sizeof($this->tasks);
    }

    /**
     * @return Task[]
     */
    public function getTasks()
    {
        return $this->tasks;
    }
}
