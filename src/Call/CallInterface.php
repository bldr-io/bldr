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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
interface CallInterface
{
    /**
     * Runs the command
     *
     * @return mixed
     */
    public function run();

    /**
     * @param BuildCommand $command
     *
     * @return CallInterface
     */
    public function initialize(BuildCommand $command);

    /**
     * @param Task $task
     *
     * @return CallInterface
     */
    public function setTask(Task $task);

    /**
     * @param Call $call
     *
     * @return CallInterface
     */
    public function setCall(Call $call);

    /**
     * @return BuildCommand
     */
    public function getCommand();

    /**
     * @return InputInterface
     */
    public function getInput();

    /**
     * @return OutputInterface
     */
    public function getOutput();
}
