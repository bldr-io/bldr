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

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Class AbstractCall
 *
 * @author Aaron Scherer <aaron@undergroundelephant.com>
 */
abstract class AbstractCall implements CallInterface
{
    /**
     * @var InputInterface $input
     */
    protected $input;

    /**
     * @var OutputInterface $output
     */
    protected $output;

    /**
     * @var HelperSet $helperSet
     */
    protected $helperSet;

    /**
     * @var ParameterBag $config
     */
    protected $config;

    /**
     * @var string $taskName
     */
    protected $taskName;

    /**
     * @var array $taskArguments
     */
    protected $taskArguments;

    /**
     * @var Boolean $failOnError
     */
    protected $failOnError;

    /**
     * @var integer[] $successStatusCodes
     */
    protected $successStatusCodes;

    /**
     * {@inheritDoc}
     */
    public function initialize(
        InputInterface $input,
        OutputInterface $output,
        HelperSet $helperSet,
        ParameterBag $config
    ) {
        $this->input     = $input;
        $this->output    = $output;
        $this->helperSet = $helperSet;
        $this->config    = $config;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setTask($name, array $arguments)
    {
        $this->taskName      = $name;
        $this->taskArguments = $arguments;

        return $this;
    }

    /**
     * @param Boolean $fail
     *
     * @return CallInterface
     */
    public function setFailOnError($fail)
    {
        $this->failOnError = $fail;

        return $this;
    }

    /**
     * @param integer[] $codes
     *
     * @return CallInterface
     */
    public function setSuccessStatusCodes(array $codes)
    {
        $this->successStatusCodes = $codes;

        return $this;
    }
}
