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
use Symfony\Component\Process\ProcessBuilder;

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
     * @var ProcessBuilder
     */
    private $builder;

    /**
     * @param TaskInterface  $task
     * @param ProcessBuilder $builder
     * @param bool           $running
     */
    public function __construct(TaskInterface $task, ProcessBuilder $builder, $running = false)
    {
        parent::__construct($running);
        $this->task    = $task;
        $this->builder = $builder;
    }

    /**
     * @return TaskInterface
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @return ProcessBuilder
     */
    public function getProcessBuilder()
    {
        return $this->builder;
    }
}
