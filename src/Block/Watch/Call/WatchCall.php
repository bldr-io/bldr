<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Watch\Call;

use Bldr\Call\AbstractCall;
use Bldr\Call\Traits\FinderAwareTrait;
use Bldr\Model\Task;
use Bldr\Registry\TaskRegistry;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Aaron Scherer <aaron@undergroundelephant.com>
 */
class WatchCall extends AbstractCall
{
    use FinderAwareTrait;

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
            ->addOption('src', true, 'Source to watch')
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

        $source = $this->getOption('src');
        if (!is_array($source)) {
            throw new \Exception("`src` must be an array");
        }

        $this->watchForChanges($this->getFiles($source));
    }

    /**
     * @param SplFileInfo[] $files
     */
    private function watchForChanges(array $files)
    {
        $this->getOutput()
            ->writeln("Watching for changes");

        $previously = [];
        while (true) {
            foreach ($files as $file) {
                /** @var SplFileInfo $file */
                if ($this->checkFile($file->getRealPath(), $previously)) {
                    $this->getOutput()
                        ->writeln(
                            sprintf(
                                "<info>>>>></info> <comment>The following file changed:</comment> <info>%s</info>",
                                $file->getPathname()
                            )
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
            $taskInfo    = $this->config['tasks'][$name];
            $description = isset($taskInfo['description']) ? $taskInfo['description'] : "";

            $this->tasks->addTask(new Task($name, $description, false, $taskInfo['calls']));
        }
    }
}
