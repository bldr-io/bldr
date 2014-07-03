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

use Bldr\Model\Call;
use Bldr\Model\Task;
use Bldr\Registry\TaskRegistry;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 */
class TaskRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_stores_tasks_shifts_them_out_and_count_them()
    {
        $firstCall = new Call('type 1');
        $secondCall = new Call('type 2');
        $firstTask = new Task('task 1');
        $firstTask->addCall($firstCall);
        $firstTask->addCall($secondCall);

        $secondTask = new Task('task 2');
        $secondTask->addCall($firstCall);
        $secondTask->addCall($secondCall);

        $tasks = new TaskRegistry();
        $tasks->addTask($firstTask);
        $tasks->addTask($secondTask);

        $this->assertEquals(2, $tasks->count());
        $this->assertEquals($firstTask, $tasks->getNewTask());
        $this->assertEquals($secondTask, $tasks->getNewTask());
        $this->assertContainsOnlyInstancesOf('Bldr\Model\Task', $tasks->getTasks());
    }
}
