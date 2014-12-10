<?php

/**
 * This file is part of bldr
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Task;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
interface TaskInterface
{
    /**
     * Executes the task.
     *
     * Can throw exceptions on failure
     *
     * @param OutputInterface $output Output stream
     *
     * @return void
     */
    public function run(OutputInterface $output);

    /**
     * Returns the name of the TaskDefinitionInterface.
     * Used in the TaskRepository, when searching for a task to use.
     *
     * @return TaskInterface
     */
    public function getName();

    /**
     * Returns the description. Visible when calling bldr task:list
     *
     * @return
     */
    public function getDescription();

    /**
     * Returns an array of the parameters given to the Task
     *
     * @return array
     */
    public function getParameters();

    /**
     * Returns the value of the given parameter name.
     *
     * @param string $name The Parameter Name
     *
     * @return mixed
     */
    public function getParameter($name);

    /**
     * Returns the HelperSet object from the Application
     *
     * @return HelperSet
     */
    public function getHelperSet();

    /**
     * Sets a configuration parameter.
     *
     * @param string $name  Name of the task parameter
     * @param mixed  $value Parameter value
     *
     * @return void
     */
    public function setParameter($name, $value);

    /**
     * Returns true if Bldr should continue running when a Task fails.
     *
     * @return bool
     */
    public function continueOnError();

    /**
     * Validates all of the parameters
     *
     * @return bool
     * @throws \Exception
     */
    public function validate();

    /**
     * Returns either the instances event dispatcher, or null.
     *
     * @return EventDispatcherInterface|null
     */
    public function getEventDispatcher();
}
