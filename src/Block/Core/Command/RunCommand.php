<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Core\Command;

use Bldr\Application;
use Bldr\Block\Core\Service\Builder;
use Bldr\Command\AbstractCommand;
use Bldr\Definition\JobDefinition;
use Bldr\Definition\TaskDefinition;
use Bldr\Event as Events;
use Bldr\Event;
use Bldr\Exception\BldrException;
use Bldr\Registry\JobRegistry;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class RunCommand extends AbstractCommand
{
    /**
     * @var Builder $builder
     */
    private $builder;

    /**
     * @var JobRegistry $registry
     */
    private $registry;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('run')
            ->setDescription("Runs the project for the directory you are in. Must contain a config file.")
            ->addArgument('profile', InputArgument::REQUIRED, 'Profile to run')
            ->setHelp(
                <<<EOF

The <info>%command.name%</info> builds the current project, using the config file in the root directory.

To use:

    <info>$ bldr %command.name% <profile_name></info>

EOF
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function doExecute()
    {
        $this->registry = $this->container->get('bldr.registry.job');
        $this->builder  = $this->container->get('bldr.builder');
        $profileName    = $this->input->getArgument('profile');

        $this->output->writeln(["\n", Application::$logo, "\n"]);

        $profile = $this->getProfile($profileName);

        $projectFormat = [];

        if ($this->container->getParameter('name') !== '') {
            $projectFormat[] = sprintf("Building the '%s' project", $this->container->getParameter('name'));
        }
        if ($this->container->getParameter('description') !== '') {
            $projectFormat[] = sprintf(" - %s - ", $this->container->getParameter('description'));
        }

        $profileFormat = [sprintf("Using the '%s' profile", $profileName)];
        if (!empty($profile['description'])) {
            $profileFormat[] = sprintf(" - %s - ", $profile['description']);
        }

        $this->output->writeln(
            [
                "",
                $projectFormat === [] ? '' : $this->formatBlock($projectFormat, 'blue', 'black'),
                "",
                $this->formatBlock($profileFormat, 'blue', 'black'),
                ""
            ]
        );

        $this->fetchJobs($profile);
        $this->builder->runJobs($this->registry);

        $this->output->writeln(['', $this->formatBlock('Build Success!', 'green', 'white', true), '']);
    }

    /**
     * @param string|array $output
     * @param string       $background
     * @param string       $foreground
     *
     * @return string
     */
    private function formatBlock($output, $background, $foreground, $large = false)
    {
        /** @var FormatterHelper $formatter */
        $formatter = $this->getApplication()->getHelperSet()->get('formatter');

        return $formatter->formatBlock($output, "bg={$background};fg={$foreground}", $large);
    }

    /**
     * @param array $profile
     */
    private function fetchJobs(array $profile)
    {
        if (!empty($profile['uses']) && !empty($profile['uses']['before'])) {
            foreach ($profile['uses']['before'] as $name) {
                $this->buildJobs($this->getProfile($name)['jobs']);
            }
        }

        $this->buildJobs($profile['jobs']);

        if (!empty($profile['uses']) && !empty($profile['uses']['after'])) {
            foreach ($profile['uses']['after'] as $name) {
                $this->buildJobs($this->getProfile($name)['jobs']);
            }
        }
    }

    /**
     * @param string[] $names
     *
     * @throws \Exception
     * @return array
     */
    private function buildJobs(array $names)
    {
        $jobs = $this->container->getParameter('jobs');
        foreach ($names as $name) {
            if (!array_key_exists($name, $jobs)) {
                throw new BldrException(
                    sprintf(
                        "Job `%s` does not exist. Found: %s",
                        $name,
                        implode(', ', array_keys($jobs))
                    )
                );
            }

            $jobInfo     = $jobs[$name];
            $description = isset($jobInfo['description']) ? $jobInfo['description'] : "";
            $job         = new JobDefinition($name, $description);

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

    /**
     * @param $name
     *
     * @return mixed
     * @throws \Exception
     */
    private function getProfile($name)
    {
        $profiles = $this->container->getParameter('profiles');
        if (!array_key_exists($name, $profiles)) {
            throw new \Exception(
                sprintf(
                    'There is no profile with the name \'%s\', expecting: (%s)',
                    $name,
                    implode(', ', array_keys($profiles))
                )
            );
        }

        return $profiles[$name];
    }
}
