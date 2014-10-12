<?php

/**
 * This file is part of bldr
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Exception;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ParameterNotFoundException extends BldrException
{
    /**
     * @param string $parameter
     */
    public function __construct($parameter)
    {
        parent::__construct("The given parameter, {$parameter}, is not defined, or set, in the given task.", 500);
    }
}
 