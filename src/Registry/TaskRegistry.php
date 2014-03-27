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

use Bldr\Model\Call;
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

    public function getNewTask()
    {
        return array_pop($this->tasks);
    }

    public function addTask(Task $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    public function count()
    {
        return sizeof($this->tasks);
    }
}
