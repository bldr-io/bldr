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

use Bldr\Application;
use Bldr\Model\Call;
use Bldr\Model\Task;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractCall
 *
 * @author Aaron Scherer <aequasi@gmail.com>
 */
abstract class AbstractCall implements CallInterface
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
     * @var array $options
     */
    private $options = [];

    /**
     * @var InputInterface $input
     */
    private $input;

    /**
     * @var OutputInterface $output
     */
    private $output;

    /**
     * @var HelperSet $helperSet
     */
    private $helperSet;

    /**
     * @var Task $task
     */
    private $task;

    /**
     * @var Call $call
     */
    private $call;

    /**
     * {@inheritDoc}
     */
    public function initialize(
        InputInterface $input,
        OutputInterface $output,
        HelperSet $helperSet,
        Task $task,
        Call $call
    ) {
        $this->input     = $input;
        $this->output    = $output;
        $this->helperSet = $helperSet;
        $this->task      = $task;
        $this->call      = $call;

        $this->setOptionValues($call);

        return $this;
    }

    /**
     * @param Call $call
     *
     * @throws \RuntimeException
     */
    protected function setOptionValues(Call $call)
    {
        foreach ($call->getOptions() as $key => $value) {
            $this->options[$key]['value'] = $value;
        }

        foreach ($this->options as &$option) {
            if ($option['value'] === null && $option['default'] !== null) {
                $option['value'] = $option['default'];
            }

            if (isset($option['required']) && $option['required'] && $option['value'] === null) {
                throw new \RuntimeException(
                    sprintf(
                        "Running the %s task failed. The %s option requires a value.",
                        $this->getTask()
                            ->getName(),
                        $option['name']
                    )
                );
            }
        }
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name of the task
     *
     * @param string $name
     *
     * @return AbstractCall
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
     * Sets the description of the task
     *
     * @param string $description
     *
     * @return AbstractCall
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $name
     * @param bool   $required
     * @param string $description
     * @param mixed  $default
     *
     * @return AbstractCall
     */
    public function addOption($name, $required = false, $description = '', $default = null)
    {
        $this->options[$name] = [
            'name'        => $name,
            'required'    => $required,
            'description' => $description,
            'default'     => $default,
            'value'       => null
        ];

        return $this;
    }

    /**
     * Removes the named option
     *
     * @param string $name
     *
     * @return AbstractCall
     */
    public function removeOption($name)
    {
        unset($this->options[$name]);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasOption($name)
    {
        if (array_key_exists($name, $this->options)) {
            if (null !== $this->options[$name]['value']) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * {@inheritDoc}
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * {@inheritDoc}
     */
    public function getHelperSet()
    {
        return $this->helperSet;
    }

    /**
     * @return bool
     */
    public function getFailOnError()
    {
        return $this->getCall()
            ->getFailOnError();
    }

    /**
     * {@inheritDoc}
     */
    public function getCall()
    {
        return $this->call;
    }

    /**
     * @return int[]
     */
    public function getSuccessStatusCodes()
    {
        return $this->getCall()
            ->getSuccessCodes();
    }

    /**
     * @param string $name
     *
     * @return string|int
     * @throws \RuntimeException
     */
    protected function getOption($name)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new \RuntimeException($name.' is not a valid option.');
        }

        $this->replaceTokens($this->options[$name]['value']);

        return $this->options[$name]['value'];
    }

    /**
     * Tokenize the given option, if it is a string.
     *
     * @param mixed $option
     *
     * @return mixed
     */
    private function replaceTokens(&$option)
    {
        if (!is_string($option)) {
            if (is_array($option)) {
                foreach ($option as &$opt) {
                    $this->replaceTokens($opt);
                }
            }

            return;
        }

        $token_format = '/\$(.+)\$/';

        preg_match_all($token_format, $option, $matches, PREG_SET_ORDER);

        if (sizeof($matches) < 1) {
            return;
        }

        foreach ($matches as $match) {
            $option = str_replace($match[0], getenv($match[1]), $option);
        }
    }

    /**
     * @param string     $name
     * @param string|int $value
     *
     * @return AbstractCall
     * @throws \RuntimeException
     */
    protected function setOption($name, $value)
    {
        if (!array_key_exists($name, $this->options)) {
            throw new \RuntimeException($name.' is not a valid option.');
        }

        $this->options[$name]['value'] = $value;

        return $this;
    }
}
