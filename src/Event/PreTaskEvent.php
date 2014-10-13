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

use Bldr\Definition\JobDefinition;
use Bldr\Definition\TaskDefinition;

/**
 * @author Mauricio Walters <nvitius@gmail.com>
 * @author Aaron Scherer    <aequasi@gmail.com>
 */
class PreTaskEvent extends AbstractEvent
{
    /**
     * @var JobDefinition $job
     */
    private $job;

    /**
     * @var TaskDefinition $task
     */
    private $task;

    /**
     * @param JobDefinition  $job
     * @param TaskDefinition $task
     */
    public function __construct(JobDefinition $job, TaskDefinition $task)
    {
        $this->job = $job;
        $this->task = $task;
    }

    /**
     * @return JobDefinition
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @return TaskDefinition
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param TaskDefinition $task
     */
    public function setTask(TaskDefinition $task)
    {
        $this->task = $task;
    }
}
