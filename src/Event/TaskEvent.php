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

use Bldr\Command\BuildCommand;
use Bldr\Model\Task;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class TaskEvent extends AbstractEvent
{
    /**
     * @var Task $task
     */
    private $task;

    /**
     * @param BuildCommand $command
     * @param Task         $task
     * @param bool         $running
     */
    public function __construct(BuildCommand $command, Task $task, $running = true)
    {
        parent::__construct($command, $running);
        $this->task = $task;
    }

    /**
     * @return Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param Task $task
     */
    public function setTask(Task $task)
    {
        $this->task = $task;
    }
}
