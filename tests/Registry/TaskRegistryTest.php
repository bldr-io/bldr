<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test\Registry;
use Bldr\Registry\TaskRegistry;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 */
class TaskRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $tasks = new TaskRegistry();

        while ($tasks->count() > 0) {
            $task = $tasks->getNewTask();

            foreach ($task->getCalls() as $call) {
                $this->runCall($task, $call);

                $service = $this->fetchServiceForCall($call);
                $service->initialize($this->input, $this->output, $this->helperSet, $task, $call);

                $service->run();
            }
        }
    }
}
