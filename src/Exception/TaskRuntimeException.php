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
class TaskRuntimeException extends BldrException
{
    /**
     * @param string $task    The task name
     * @param string $message The error output
     */
    public function __construct($task, $message = '')
    {
        parent::__construct(
            sprintf("There was an error running the %s task. %s", $task, empty($message) ? '' : 'Output: '.$message)
        );
    }
}
