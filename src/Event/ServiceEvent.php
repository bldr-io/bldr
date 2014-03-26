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
use Bldr\Call\CallInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ServiceEvent extends Event implements EventInterface
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
     * @var CallInterface $service
     */
    private $service;

    /**
     * @var Boolean $running
     */
    private $running;

    /**
     * @param Application    $application
     * @param InputInterface $input
     * @param CallInterface  $service
     * @param Boolean        $running
     */
    public function __construct(
        Application $application,
        InputInterface $input,
        CallInterface $service,
        $running = true
    ) {
        $this->application = $application;
        $this->input       = $input;
        $this->service     = $service;
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
     * @return CallInterface
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @return Boolean
     */
    public function isRunning()
    {
        return $this->running;
    }
}
