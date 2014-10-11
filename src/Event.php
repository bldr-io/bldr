<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr;

/**
 * Events for the BuildCommand
 *
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class Event
{
    /**
     * Called before descending into a task
     */
    const PRE_TASK = 'bldr.event.task.before';

    /**
     * Called before executing a process
     */
    const PRE_EXECUTE = 'bldr.event.execute.before';

    /**
     * Called before a TaskInterface is initialized
     */
    const PRE_INITIALIZE_TASK = 'bldr.event.task.initialize.before';

    /**
     * Called after executing a process
     */
    const POST_EXECUTE = 'bldr.event.execute.after';

    /**
     * Called after running a task
     */
    const POST_TASK = 'bldr.event.task.after';
}
