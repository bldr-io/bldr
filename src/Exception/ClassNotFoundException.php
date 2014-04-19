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

use Exception;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ClassNotFoundException extends BldrException
{
    /**
     * @param string $class
     */
    public function __construct($class)
    {
        parent::__construct(sprintf("Could not find the `%s` class.", $class));
    }
}
