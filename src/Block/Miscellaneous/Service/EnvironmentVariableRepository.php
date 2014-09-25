<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Miscellaneous\Service;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 * @author Raul Rodriguez <raulrodriguez782@gmail.com>
 */
class EnvironmentVariableRepository
{
    protected $environmentVariables = [];

    public function addEnvironmentVariable($environmentVariable)
    {
        $this->environmentVariables[] = $environmentVariable;
    }

    public function getEnvironmentVariables()
    {
        return $this->environmentVariables;
    }
}
