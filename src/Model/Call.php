<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Model;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class Call
{

    /**
     * @var string $type
     */
    private $type;

    /**
     * @var string[] $arguments
     */
    private $arguments;

    /**
     * @var array $data
     */
    private $data;

    /**
     * @param string   $type
     * @param string[] $arguments
     */
    public function __construct($type, array $arguments = [])
    {
        $this->type      = $type;
        $this->arguments = $arguments;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param string[] $arguments
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * @param string $argument
     */
    public function addArgument($argument)
    {
        $this->arguments[] = $argument;
    }

    public function getArgument($name)
    {
        return $this->arguments[$name];
    }

    public function hasArgument($name)
    {
        return isset($this->arguments[$name]);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->data[$name]);
    }
}
