<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Definition;

/**
 * This is the model that the job configuration is turned in to.
 *
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class JobDefinition
{
    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $description
     */
    private $description;

    /**
     * @var TaskDefinition[] $tasks
     */
    private $tasks = [];

    /**
     * @param string           $name
     * @param string           $description
     * @param TaskDefinition[] $tasks
     */
    public function __construct($name, $description = '', array $tasks = [])
    {
        $this->name         = $name;
        $this->description  = $description;
        $this->tasks        = $tasks;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return TaskDefinition[]
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * @param TaskDefinition $task
     *
     * @return JobDefinition
     */
    public function addTask(TaskDefinition $task)
    {
        $this->tasks[] = $task;

        return $this;
    }
}
