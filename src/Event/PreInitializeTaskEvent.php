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

/**
 * @author Aaron Scherer    <aequasi@gmail.com>
 */
class PreInitializeTaskEvent extends AbstractEvent
{
    /**
     * @var TaskInterface
     */
    private $task;

    /**
     * @param TaskInterface $task
     */
    public function __construct(TaskInterface $task)
    {
        $this->task = $task;
    }

    /**
     * @return TaskInterface
     */
    public function getTask()
    {
        return $this->task;
    }
}
