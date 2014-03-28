<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Extension\Execute\Service;

use Symfony\Component\Process\Process;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class BackgroundService
{
    /**
     * @var Process[] $processes
     */
    private $processes = [];

    /**
     * @param string $name
     * @param Process $process
     */
    public function addProcess($name, Process $process)
    {
        $this->processes[$name] = $process;
    }

    /**
     * @param string $name
     *
     * @return Boolean
     */
    public function hasProcess($name)
    {
        return array_key_exists($name, $this->processes);
    }

    /**
     * @param string $name
     *
     * @return Process
     */
    public function getProcess($name)
    {
        return $this->processes[$name];
    }

    /**
     * @param string $name
     */
    public function removeProcess($name)
    {
        unset($this->processes[$name]);
    }
}
