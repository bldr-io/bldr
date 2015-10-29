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

use Bldr\Task\TaskInterface;
use Symfony\Component\Process\Process;

/**
 * @author Mauricio Walters <nvitius@gmail.com>
 * @author Aaron Scherer    <aequasi@gmail.com>
 */
class PreExecuteEvent extends AbstractEvent
{
    /**
     * @var TaskInterface
     */
    private $task;

    /**
     * @var Process
     */
    private $process;

    /**
     * @param TaskInterface $task
     * @param Process       $process
     */
    public function __construct(TaskInterface $task, Process $process)
    {
        $this->task    = $task;
        $this->process = $process;
    }

    /**
     * @return TaskInterface
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @return Process
     */
    public function getProcess()
    {
        return $this->process;
    }
}
