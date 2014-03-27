<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Extension\Watch\Call;

use Bldr\Call\AbstractCall;
use Bldr\Model\Task;

/**
 * @author Aaron Scherer <aaron@undergroundelephant.com>
 */
class WatchCall extends AbstractCall
{
    /**
     * {@inheritDoc}
     */
    public function run(array $arguments)
    {
        if (!$this->getCall()->has('files')) {
            throw new \Exception("Watch must have files.");
        }

        if (!$this->getCall()->has('task') && !$this->getCall()->has('profile')) {
            throw new \Exception("Watch must have either a task, or a profile");
        }

        $files = glob(getcwd() . '/' . $this->getCall()->files);
        $this->watchForChanges($files);
    }

    /**
     * @param string[] $files
     */
    private function watchForChanges(array $files)
    {
        $this->getOutput()->writeln("Watching for changes");

        $previously = [];
        while (true) {
            foreach ($files as $name) {
                if ($this->checkFile($name, $previously)) {
                    $this->getOutput()->writeln(sprintf("<info>>>>></info> <comment>The %s file changed.</comment>", $name));
                    foreach ($this->getTasks() as $task) {
                        $this->getCommand()->runTask($task);
                    }
                    break;
                }
            }

            sleep(1);
        }

        $this->watchForChanges($files);
    }

    /**
     * @param string $name
     * @param array  $previously
     *
     * @return Boolean
     */
    private function checkFile($name, array &$previously)
    {
        $hash = sha1_file($name);
        $changed = array_key_exists($name, $previously) ? ($previously[$name] !== $hash) : false;
        $previously[$name] = $hash;

        return $changed;
    }

    /**
     * @return array|Task[]
     */
    private function getTasks()
    {
        if ($this->getCall()->has('profile')) {
            return $this->getCommand()->fetchTasks($this->getCall()->profile);
        } else {
            return $this->getCommand()->buildTasks([$this->getCall()->tasks]);
        }
    }
}
