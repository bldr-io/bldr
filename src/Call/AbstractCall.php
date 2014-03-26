<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Call;

use Bldr\Command\BuildCommand;
use Bldr\Model\Call;
use Bldr\Model\Task;

/**
 * Class AbstractCall
 *
 * @author Aaron Scherer <aequasi@gmail.com>
 */
abstract class AbstractCall implements CallInterface
{
    /**
     * @var BuildCommand $command
     */
    private $command;
    
    /**
     * @var Task $task
     */
    private $task;

    /**
     * @var Call $call
     */
    private $call;

    /**
     * @var Boolean $failOnError
     */
    private $failOnError;

    /**
     * @var integer[] $successStatusCodes
     */
    private $successStatusCodes;

    /**
     * {@inheritDoc}
     */
    public function initialize(BuildCommand $command)
    {
        $this->command            = $command;
        $this->task               = null;
        $this->call               = null;
        $this->failOnError        = false;
        $this->successStatusCodes = [0];

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setTask(Task $task)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setCall(Call $call)
    {
        $this->call = $call;

        return $this;
    }

    /**
     * @return Call
     */
    public function getCall()
    {
        return $this->call;
    }

    /**
     * @return Boolean
     */
    public function getFailOnError()
    {
        return $this->failOnError;
    }

    /**
     * @return integer[]
     */
    public function getSuccessStatusCodes()
    {
        return $this->successStatusCodes;
    }

    /**
     * @return Task
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * {@inheritDoc}
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * {@inheritDoc}
     */
    public function getInput()
    {
        return $this->command->getInput();
    }

    /**
     * {@inheritDoc}
     */
    public function getOutput()
    {
        return $this->command->getOutput();
    }

    public function getHelperSet()
    {
        return $this->command->getHelperSet();
    }
}
