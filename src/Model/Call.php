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
     * @var Boolean $failOnError
     */
    private $failOnError = false;

    /**
     * @var integer[] $successCodes
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
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return boolean
     */
    public function getFailOnError()
    {
        return $this->failOnError;
    }

    /**
     * @param Boolean $failOnError
     *
     * @return Call
     */
    public function setFailOnError($failOnError)
    {
        $this->failOnError = $failOnError;

        return $this;
    }

    /**
     * @return integer[]
     */
    public function getSuccessCodes()
    {
        return $this->successCodes;
    }

    /**
     * @param integer[] $successCodes
     *
     * @return Call
     */
    public function setSuccessCodes(array $successCodes)
    {
        $this->successCodes = $successCodes;

        return $this;
    }
}
