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

use Bldr\Call\AbstractCall;
use Symfony\Component\Process\Process;

/**
 * @author Mauricio Walters <nvitius@gmail.com>
 */
class PostExecuteEvent extends AbstractEvent
{
    /**
     * @var AbstractCall
     */
    private $call;

    /**
     * @var Process
     */
    private $process;

    /**
     * @param AbstractCall $call
     * @param Process      $process
     * @param bool         $running
     */
    public function __construct(AbstractCall $call, Process $process, $running = false)
    {
        parent::__construct($running);
        $this->call = $call;
        $this->process = $process;
    }

    public function getCall()
    {
        return $this->call;
    }

    public function getProcess()
    {
        return $this->process;
    }
}
