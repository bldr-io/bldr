<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test\Service;

use Bldr\Service\Builder;
use Bldr\Test\BaseProphecy;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 */
class TaskRegistryTest extends BaseProphecy
{
    public function testThatItBuildsAProfile()
    {
        $dispatcher = $this->prophesy('EventDispatcher');
        $input = $this->prophesy('Input');
        $output = $this->prophesy('Output');
        $tasks[] = $this->prophesy('Task');
        $tasks[] = $this->prophesy('Task');

        $builder = new Builder(
            $dispatcher,
            $input,
            $output,
            $tasks
        );

        $helperSet = $this->prophesy('HelperSet');
        $builder->initialize($input, $output, $helperSet);
        $tasksRegistry = $this->prophesy('TaskRegistry');

        $builder->runTasks($taskRegistry);
    }
}
