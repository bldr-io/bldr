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
 * This is the model that the task configuration is turned in to.
 *
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class TaskDefinition
{
    /**
     * @var string $type
     */
    private $type;

    /**
     * @var array $parameters
     */
    private $parameters;

    /**
     * @var bool $continueOnError
     */
    private $continueOnError = false;

    /**
     * @param string $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     *
     * @return TaskDefinition
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @return boolean
     */
    public function continueOnError()
    {
        return $this->continueOnError;
    }

    /**
     * @param boolean $continueOnError
     *
     * @return TaskDefinition
     */
    public function setContinueOnError($continueOnError)
    {
        $this->continueOnError = $continueOnError;

        return $this;
    }
}
