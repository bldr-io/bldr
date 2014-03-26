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

use Bldr\Application;
use Bldr\Model\Call;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class CallEvent extends Event implements EventInterface
{
    /**
     * @var Application $application
     */
    private $application;

    /**
     * @var InputInterface $input
     */
    private $input;

    /**
     * @var Call $call
     */
    private $call;

    /**
     * @var Boolean $running
     */
    private $running;

    /**
     * @param Application    $application
     * @param InputInterface $input
     * @param Call           $call
     * @param Boolean        $running
     */
    public function __construct(Application $application, InputInterface $input, Call $call, $running = true)
    {
        $this->application = $application;
        $this->input       = $input;
        $this->call        = $call;
        $this->running     = $running;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->input;
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

    /**
     * @return Boolean
     */
    public function isRunning()
    {
        return $this->running;
    }
}
