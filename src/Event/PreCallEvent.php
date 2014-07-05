<?php
/**
 * This file is part of Bldr.io
 *
 * (c) Mauricio Walters <nvitius@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Event;

use Bldr\Model\Call;
use Bldr\Model\Task;

class PreCallEvent extends AbstractEvent {
    /**
     * @var Task $task
     */
    private $task;

    /**
     * @var Call $call
     */
    private $call;

    /**
     * @param Task    $task
     * @param Call    $call
     * @param Boolean $running
     */
    public function __construct(Task $task, Call $call, $running = false)
    {
        parent::__construct($running);
        $this->task = $task;
        $this->call = $call;
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
     * @param Call $call
     */
    public function setCall(Call $call)
    {
        $this->call = $call;
    }
}
