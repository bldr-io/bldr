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
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
abstract class AbstractEvent extends Event implements EventInterface
{
    /**
     * @var BuildCommand $command
     */
    protected $command;

    /**
     * @var bool $running
     */
    protected $running;

    /**
     * @param BuildCommand $command
     * @param bool      $running
     */
    public function __construct(BuildCommand $command, $running = true)
    {
        $this->command = $command;
        $this->running = $running;
    }

    /**
     * @return BuildCommand
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        return $this->running;
    }
}
