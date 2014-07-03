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
use Prophecy\Prophet;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 */
class TaskRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function testItStoresTasksAndShiftsThemOutAndCountThem()
    {
//        $firstCall = new Call('type 1');
//        $secondCall = new Call('type 2');
//        $firstTask = new Task('task 1');
//        $firstTask->addCall($firstCall);
//        $firstTask->addCall($secondCall);
//
//        $secondTask = new Task('task 2');
//        $secondTask->addCall($firstCall);
//        $secondTask->addCall($secondCall);

        $prophet = new Prophet();
        $firstTask = $prophet->prophesize('Bldr\Model\Task');
        $secondTask = $prophet->prophesize('Bldr\Model\Task');

        $tasks = new TaskRegistry();
        $tasks->addTask($firstTask->reveal());
        $tasks->addTask($secondTask->reveal());

        $this->assertEquals(2, $tasks->count());
        $this->assertEquals($firstTask, $tasks->getNewTask());
        $this->assertEquals($secondTask, $tasks->getNewTask());
        $this->assertContainsOnlyInstancesOf('Bldr\Model\Task', $tasks->getTasks());
    }
}
