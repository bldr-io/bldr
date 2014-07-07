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

use Bldr\Call\CallInterface;
use Bldr\Command\BuildCommand;
use Bldr\Model\Call;
use Bldr\Model\Task;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ServiceEvent extends AbstractEvent
{
    /**
     * @var Task $task
     */
    private $task;

    /**
     * @var Call $call
     */
    private $call;

    /**
     * @var CallInterface $service
     */
    private $service;

    /**
     * @param BuildCommand  $command
     * @param Task          $task
     * @param Call          $call
     * @param CallInterface $service
     * @param bool       $running
     */
    public function __construct(BuildCommand $command, Task $task, Call $call, CallInterface $service, $running = true)
    {
        parent::__construct($command, $running);
        $this->task    = $task;
        $this->call    = $call;
        $this->service = $service;
    }

    /**
     * @return Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @return Call
     */
    public function getCall()
    {
        return $this->call;
    }

    /**
     * @return CallInterface
     */
    public function getService()
    {
        return $this->service;
    }
}
