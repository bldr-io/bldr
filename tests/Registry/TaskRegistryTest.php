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
use Bldr\Test\BaseProphecy;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 */
class TaskRegistryTest extends BaseProphecy
{
    public function testItStoresTasksAndShiftsThemOutAndCountThem()
    {
        $firstTask = $this->prophesy('Bldr\Model\Task');
        $secondTask = $this->prophesy('Bldr\Model\Task');

        $tasks = new TaskRegistry();
        $tasks->addTask($firstTask->reveal());
        $tasks->addTask($secondTask->reveal());

        $this->assertEquals(2, $tasks->count());
        $this->assertEquals($firstTask->reveal(), $tasks->getNewTask());
        $this->assertEquals($secondTask->reveal(), $tasks->getNewTask());
        $this->assertContainsOnlyInstancesOf('Bldr\Model\Task', $tasks->getTasks());
    }
}
