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
class RequiredParameterException extends BldrException
{
    /**
     * @param string $parameter
     */
    public function __construct($parameter)
    {
        parent::__construct("The required parameter, {$parameter}, is not set, and it is required.", 500);
    }
}
