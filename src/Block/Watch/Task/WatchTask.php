<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Watch\Task;

use Bldr\Block\Core\Task\AbstractTask;
use Bldr\Block\Core\Task\Traits\FinderAwareTrait;
use Bldr\Definition\JobDefinition;
use Bldr\Definition\TaskDefinition;
use Bldr\Exception\TaskRuntimeException;
use Bldr\Registry\JobRegistry;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Aaron Scherer <aaron@undergroundelephant.com>
 */
class WatchTask extends AbstractTask
{
    use FinderAwareTrait;

    /**
     * @var JobRegistry $registry
     */
    private $registry;

    /**
     * @var array $config
     */
    private $config;

    /**
     * @param JobRegistry $registry
     * @param array       $config
     */
    public function __construct(JobRegistry $registry, array $config)
    {
        $this->registry = $registry;
        $this->config   = $config;
    }

    /**
     * Configures the Task
     */
    public function configure()
    {
        $this->setName('watch')
            ->setDescription('Watches the filesystem for changes')
            ->addParameter('src', true, 'Source to watch')
            ->addParameter('profile', false, 'Profile to run on filesystem change')
            ->addParameter('job', false, 'Job to run on filesystem change')
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function run(OutputInterface $output)
    {
        if (getenv('TRAVIS')) {
            throw new \RuntimeException("Travis does not support running the watch task.");
        }

        if (!$this->hasParameter('task') && !$this->hasParameter('profile')) {
            throw new \Exception("Watch must have either a task, or a profile");
        }

        $source = $this->getParameter('src');
        if (!is_array($source)) {
            throw new TaskRuntimeException($this->getName(), "`src` must be an array");
        }

        $this->watchForChanges($output, $this->getFiles($source));
    }

    /**
     * @param OutputInterface $output
     * @param SplFileInfo[]   $files
     *
     * @return void
     */
    private function watchForChanges(OutputInterface $output, array $files)
    {
        $output->writeln("Watching for changes");

        $previously = [];
        while (true) {
            foreach ($files as $file) {
                /** @var SplFileInfo $file */
                if ($this->checkFile($file->getRealPath(), $previously)) {
                    $output->writeln(
                        sprintf(
                            "<info>>>>></info> <comment>The following file changed:</comment> <info>%s</info>",
                            $file->getPathname()
                        )
                    );

                    $this->getJobs();
                    $this->registry->addJob($this->registry->getNewJob());

                    return;
                }
            }
            sleep(1);
        }

        return;
    }

    /**
     * @param string $name
     * @param array  $previously
     *
     * @return bool
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
    private function getJobs()
    {
        if ($this->hasParameter('profile')) {
            $this->fetchJobs($this->getParameter('profile'));

            return;
        }

        $this->buildJobs([$this->getParameter('job')]);
    }

    /**
     * @param string $profileName
     */
    public function fetchJobs($profileName)
    {
        $profile = $this->config['profiles'][$profileName];
        $this->buildJobs($profile['jobs']);
    }

    /**
     * @param string[] $names
     *
     * @return array
     */
    public function buildJobs($names)
    {
        foreach ($names as $name) {
            $jobInfo     = $this->config['jobs'][$name];
            $description = isset($jobInfo['description']) ? $jobInfo['description'] : "";

            $job = new JobDefinition($name, $description);
            foreach ($jobInfo['tasks'] as $taskInfo) {
                $task = new TaskDefinition($taskInfo['type']);
                $task->setContinueOnError(isset($taskInfo['continueOnError']) ? $taskInfo['continueOnError'] : false);
                unset($taskInfo['type'], $taskInfo['continueOnError']);
                $task->setParameters($taskInfo);
                $job->addTask($task);
            }
            $this->registry->addJob($job);
        }
    }
}
