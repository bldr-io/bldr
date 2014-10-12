<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Core\Task;

use Bldr\Exception\BldrException;
use Bldr\Exception\ParameterNotFoundException;
use Bldr\Exception\RequiredParameterException;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Bldr\Task\TaskInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
abstract class AbstractTask implements TaskInterface
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
     * @var EventDispatcherInterface $dispatcher
     */
    private $dispatcher;

    /**
     * @var array $parameters
     */
    private $parameters = [];

    /**
     * @type HelperSet
     */
    private $helperSet;

    /**
     * Returns whether or not AbstractTask will continue to the next AbstractTask when there is an error.
     *
     * @type bool
     */
    private $continueOnError;

    public function configure()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        if (!isset($this->name)) {
            throw new BldrException("Name is not set.");
        }

        return $this->name;
    }

    /**
     * Sets the name of the task
     *
     * @param string $name
     *
     * @return AbstractTask
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the description of the AbstractTask
     *
     * @param string $description
     *
     * @return AbstractTask
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     *
     * @return AbstractTask
     */
    public function setEventDispatcher($dispatcher)
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    /**
     * @return array
     */
    public function getParameterDefinition()
    {
        return $this->parameters;
    }

    /**
     * Adds a parameter to the definition of the Task
     *
     * @param string $name        Name of the task parameter
     * @param bool   $required    If true, task parameter is required
     * @param string $description Description of the task parameter
     * @param mixed  $default     Default value of the task parameter, null by default
     *
     * @return mixed
     */
    public function addParameter($name, $required = false, $description = '', $default = null)
    {
        $this->parameters[$name] =[
            'name'        => $name,
            'required'    => $required,
            'description' => $description,
            'default'     => $default,
            'value'       => null
        ];

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getParameters()
    {
        $parameters = [];
        foreach ($this->parameters as $name => $parameter) {
            $parameters[$name] = $this->getParameter($name);
        }

        return $parameters;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ParameterNotFoundException
     * @throws RequiredParameterException
     */
    public function getParameter($name)
    {
        if (!array_key_exists($name, $this->parameters)) {
            throw new ParameterNotFoundException($name);
        }

        $param = $this->parameters[$name];
        $value = $param['value'];
        if (null === $value) {
            if (true === $param['required']) {
                throw new RequiredParameterException($name);
            }

            $value = $param['default'];
        }

        $value = $this->replaceTokens($value);

        return $value;
    }


    /**
     * Returns true if the Task has a parameter with the given name, and the value is not null.
     * Returns false otherwise.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasParameter($name)
    {
        if (array_key_exists($name, $this->parameters)) {
            if (null !== $this->parameters[$name]['value']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function continueOnError()
    {
        return $this->continueOnError;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameter($name, $value)
    {
        if (!array_key_exists($name, $this->parameters)) {
            $this->addParameter($name, false, '', null);
        }

        $this->parameters[$name]['value'] = $value;
    }

    /**
     * Tokenize the given option, if it is a string.
     *
     * @param mixed $option
     *
     * @return mixed
     */
    private function replaceTokens($option)
    {
        if (is_array($option)) {
            $tokenizedOptions = [];
            foreach ($option as $key => $opt) {
                $tokenizedOptions[$key] = $this->replaceTokens($opt);
            }

            return $tokenizedOptions;
        }

        return preg_replace_callback('/\$(.+)\$|\$\{(.+)\}/', function ($match) {
            $val = isset($match[2]) ? getenv($match[2]) : getenv($match[1]);

            return $val !== false ? $val : $match[0];
        }, $option);
    }

    /**
     * {@inheritdoc}
     */
    public function validate()
    {
        foreach ($this->parameters as $name => $parameter) {
            if (null === $parameter['value']) {
                if (true === $parameter['required']) {
                    throw new RequiredParameterException($name);
                }
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getHelperSet()
    {
        return $this->helperSet;
    }

    /**
     * Returns true if the HelperSet is set
     *
     * @return bool
     */
    public function hasHelperSet()
    {
        return $this->helperSet === null;
    }

    public function setHelperSet(HelperSet $helperSet)
    {
        $this->helperSet = $helperSet;

        return $this;
    }
}
