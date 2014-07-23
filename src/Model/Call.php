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
     * @var array $options
     */
    private $options;

    /**
     * @var bool $failOnError
     */
    private $failOnError = true;

    /**
     * @var int[] $successCodes
     */
    private $successCodes = [0];

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
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return Call
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return bool
     */
    public function getFailOnError()
    {
        return $this->failOnError;
    }

    /**
     * @param bool $failOnError
     *
     * @return Call
     */
    public function setFailOnError($failOnError)
    {
        $this->failOnError = $failOnError;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getSuccessCodes()
    {
        return $this->successCodes;
    }

    /**
     * @param int[] $successCodes
     *
     * @return Call
     */
    public function setSuccessCodes(array $successCodes)
    {
        $this->successCodes = $successCodes;

        return $this;
    }
}
