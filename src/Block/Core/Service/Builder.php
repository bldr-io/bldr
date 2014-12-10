<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Core\Service;

use Bldr\Block\Core\Task\AbstractTask;
use Bldr\Definition\JobDefinition;
use Bldr\Definition\TaskDefinition;
use Bldr\Event;
use Bldr\Event\PreTaskEvent;
use Bldr\Exception\TaskRuntimeException;
use Bldr\Registry\JobRegistry;
use Bldr\Registry\TaskRegistry;
use Bldr\Task\TaskInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class Builder
{
    /**
     * @var EventDispatcherInterface $dispatcher
     */
    private $dispatcher;

    /**
     * @var OutputInterface $output
     */
    private $output;

    /**
     * @type HelperSet
     */
    private $helperSet;

    /**
     * @type TaskRegistry $taskRegistry
     */
    private $taskRegistry;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param OutputInterface          $output
     * @param HelperSet                $helperSet
     * @param TaskRegistry             $taskRegistry
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        OutputInterface $output,
        HelperSet $helperSet,
        TaskRegistry $taskRegistry
    ) {
        $this->dispatcher   = $dispatcher;
        $this->output       = $output;
        $this->helperSet    = $helperSet;
        $this->taskRegistry = $taskRegistry;
    }

    /**
     * @param JobRegistry $registry
     */
    public function runJobs(JobRegistry $registry)
    {
        while ($registry->count() > 0) {
            $job = $registry->getNewJob();

            $this->runJob($job);

            $registry->markJobComplete($job);
        }
    }

    /**
     * @param JobDefinition $job
     *
     * @throws \Exception
     */
    public function runJob(JobDefinition $job)
    {
        $this->output->writeln(
            [
                "\n",
                sprintf(
                    '<info>Running the %s job</info><comment>%s</comment>',
                    $job->getName(),
                    $job->getDescription() !== '' ? " > ".$job->getDescription() : ''
                )
            ]
        );

        foreach ($job->getTasks() as $task) {
            $preTaskEvent = new PreTaskEvent($job, $task);
            $this->dispatcher->dispatch(Event::PRE_TASK, $preTaskEvent);

            if ($preTaskEvent->isPropagationStopped()) {
                continue;
            }

            $service = $this->taskRegistry->findTaskByType($task->getType());
            $this->prepareServiceForTask($service, $task);

            try {
                $service->run($this->output);
            } catch (TaskRuntimeException $e) {
                if (!$task->continueOnError()) {
                    throw $e;
                }

                /** @var FormatterHelper $formatter */
                $formatter = $this->helperSet->get('formatter');
                $this->output->writeln(
                    [
                        '',
                        $formatter->formatBlock(
                            [
                                sprintf('[Warning] Task: %s', $task->getType()),
                                sprintf('%s', $e->getMessage())
                            ],
                            'bg=yellow;fg=black',
                            true
                        ),
                        ''
                    ]
                );
            }
        }
    }

    /**
     * @param TaskInterface  $service
     * @param TaskDefinition $task
     *
     * @return TaskInterface
     */
    private function prepareServiceForTask(TaskInterface $service, TaskDefinition $task)
    {
        $this->dispatcher->dispatch(Event::PRE_INITIALIZE_TASK, new Event\PreInitializeTaskEvent($service));

        if ($service instanceof AbstractTask) {
            $service->configure();
        }

        if (method_exists($service, 'setEventDispatcher')) {
            $service->setEventDispatcher($this->dispatcher);
        }

        if (method_exists($service, 'setHelperSet')) {
            $service->setHelperSet($this->helperSet);
        }

        foreach ($task->getParameters() as $name => $value) {
            $service->setParameter($name, $value);
        }

        if (method_exists($service, 'validate')) {
            $service->validate();
        }

        return $service;
    }
}
