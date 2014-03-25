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
     * Called at the very start, before loading profiles
     */
    const START = 'bldr.event.start';

    /**
     * Called before descending into a profile
     */
    const PRE_PROFILE = 'bldr.event.profile.before';

    /**
     * Called before descending into a task
     */
    const PRE_TASK = 'bldr.event.task.before';

    /**
     * Called before descending into a call
     */
    const PRE_CALL = 'bldr.event.call.before';

    /**
     * Called before running the service
     */
    const PRE_SERVICE = 'bldr.event.service.before';

    /**
     * Called after running the service
     */
    const POST_SERVICE = 'bldr.event.service.after';

    /**
     * Called after running a call
     */
    const POST_CALL = 'bldr.event.call.after';

    /**
     * Called after running a task
     */
    const POST_TASK = 'bldr.event.task.after';

    /**
     * Called after running a profile
     */
    const POST_PROFILE = 'bldr.event.profile.after';

    /**
     * Called after everything has ran
     */
    const END = 'bldr.event.end';
}
