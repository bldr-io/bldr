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

use Bldr\Model\Call;
use Bldr\Model\Task;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
interface CallInterface
{
    /**
     * Configures the Task
     *
     * This method must call CallInterface->setName($name).
     * This method should call CallInterface->setDescription($description).
     * This method may also call AbstractCall->addOption($name, $required, $description, $default)
     */
    public function configure();

    /**
     * Returns the name of the task used in the config file.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns a key/value array of task arguments, and their descriptions.
     *
     * @return string
     */
    public function getDescription();

    /**
     * @return array
     */
    public function getOptions();

    /**
     * Runs the command
     *
     * @return mixed
     */
    public function run();

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param InputInterface           $input
     * @param OutputInterface          $output
     * @param HelperSet                $helperSet
     * @param Task                     $task
     * @param Call                     $call
     *
     * @return CallInterface
     */
    public function initialize(
        EventDispatcherInterface $dispatcher,
        InputInterface $input,
        OutputInterface $output,
        HelperSet $helperSet,
        Task $task,
        Call $call
    );

    /**
     * @return Task
     */
    public function getTask();

    /**
     * @return Call
     */
    public function getCall();

    /**
     * @return InputInterface
     */
    public function getInput();

    /**
     * @return OutputInterface
     */
    public function getOutput();

    /**
     * @return HelperSet
     */
    public function getHelperSet();

    /**
     * @return bool
     */
    public function getFailOnError();

    /**
     * @return int[]
     */
    public function getSuccessStatusCodes();
}
