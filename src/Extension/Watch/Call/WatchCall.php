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

    /**
     * @var array $config
     */
    private $config;

    public function __construct(TaskRegistry $tasks, array $config)
    {
        $this->tasks  = $tasks;
        $this->config = $config;
    }

    /**
     * Configures the Task
     */
    public function configure()
    {
        $this->setName('watch')
            ->setDescription('Watches the filesystem for changes')
            ->addOption('files', true, 'Files to watch')
            ->addOption('profile', false, 'Profile to run on filesystem change')
            ->addOption('task', false, 'Task to run on filesystem change');
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if (getenv('TRAVIS')) {
            throw new \RuntimeException("Travis does not support running the watch task.");
        }

        if (!$this->hasOption('task') && !$this->hasOption('profile')) {
            throw new \Exception("Watch must have either a task, or a profile");
        }

        $fileOption = $this->getOption('files');
        if (is_array($fileOption)) {
            $files = [];
            foreach ($fileOption as $file) {
                $files = array_merge($files, glob_recursive(getcwd() . '/' . $file));
            }
        } else {
            $files = glob_recursive(getcwd() . '/' . $fileOption);
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
                        ->writeln(
                            sprintf("<info>>>>></info> <comment>The %s file changed.</comment>", $name)
                        );

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
     * Fetches the tasks for either the profile, or the passed task
     */
    private function getTasks()
    {
        if ($this->hasOption('profile')) {
            $this->fetchTasks($this->getOption('profile'));

            return;
        }

        $this->buildTasks([$this->getOption('task')]);
    }

    /**
     * @param string $profileName
     */
    public function fetchTasks($profileName)
    {
        $profile = $this->config['profiles'][$profileName];
        $this->buildTasks($profile['tasks']);
    }

    /**
     * @param string[] $names
     *
     * @return array
     */
    public function buildTasks($names)
    {
        foreach ($names as $name) {
            $taskInfo                = $this->config['tasks'][$name];
            $taskInfo['failOnError'] = false;
            $description             = isset($taskInfo['description']) ? $taskInfo['description'] : "";

            $task = new Task($name, $description, $taskInfo['calls']);
            $this->tasks->addTask($task);
        }
    }
}
