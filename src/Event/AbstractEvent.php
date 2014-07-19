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

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
abstract class AbstractEvent extends Event implements EventInterface
{
    /**
     * @var bool $running
     */
    protected $running;

    /**
     * @param bool $running
     */
    public function __construct($running = true)
    {
        $this->running = $running;
    }

    /**
     * @return bool
     */
    public function isRunning()
    {
        return $this->running;
    }
}
