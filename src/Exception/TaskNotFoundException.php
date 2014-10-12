<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Exception;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class TaskNotFoundException extends BldrException
{
    /**
     * @param string $task
     */
    public function __construct($task)
    {
        parent::__construct(sprintf("Could not find the `%s` task.", $task));
    }
}
