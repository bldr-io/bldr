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
use Bldr\Registry\TaskRegistry;

/**
 * @author Aaron Scherer <aaron@undergroundelephant.com>
 */
class WatchCall extends AbstractCall
{
    /**
     * @var TaskRegistry $tasks
     */
    private $tasks;

    public function __construct(TaskRegistry $tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if (getenv('TRAVIS')) {
            throw new \RuntimeException("Travis does not support running the watch task.");
        }


        if (!$this->getCall()
            ->has('files')
        ) {
            throw new \Exception("Watch must have files.");
        }

        if (!$this->getCall()
                ->has('task') && !$this->getCall()
                ->has('profile')
        ) {
            throw new \Exception("Watch must have either a task, or a profile");
        }

        if (is_array($this->getCall()->files)) {
            $files = [];
            foreach ($this->getCall()->files as $file) {
                $files = array_merge($files, glob(getcwd() . '/' . $file));
            }
        } else {
            $files = glob(getcwd() . '/' . $this->getCall()->files);
        }
        $this->watchForChanges($files);
    }

    /**
     * @param string[] $files
     */
    private function watchForChanges(array $files)
    {
        $this->getOutput()
            ->writeln("Watching for changes");

        $previously = [];
        while (true) {
            foreach ($files as $name) {
                if ($this->checkFile($name, $previously)) {
                    $this->getOutput()
                        ->writeln(sprintf("<info>>>>></info> <comment>The %s file changed.</comment>", $name));

                    $this->getTasks();
                    $this->tasks->addTask($this->getTask());

                    return;
                }
            }
            sleep(1);
        }
    }

    /**
     * @param string $name
     * @param array  $previously
     *
     * @return Boolean
     */
    private function checkFile($name, array &$previously)
    {
        $hash              = sha1_file($name);
        $changed           = array_key_exists($name, $previously) ? ($previously[$name] !== $hash) : false;
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
            return $this->getCommand()->buildTasks([$this->getCall()->task]);
        }
    }
}
