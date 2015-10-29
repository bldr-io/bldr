<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Execute\Task;

use Bldr\Block\Core\Task\AbstractTask;
use Bldr\Event;
use Bldr\Event\PostExecuteEvent;
use Bldr\Event\PreExecuteEvent;
use Bldr\Exception\TaskRuntimeException;
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ParallelTask extends AbstractTask
{
    /**
     * @type OutputInterface
     */
    private $output;

    /**
     * Configures the Task
     */
    public function configure()
    {
        $this->setName('parallel')
            ->setDescription('Executes the given commands in parallel, waiting for them all to finish')
            ->addParameter('commands', true, 'Commands to run (string, or array of strings)')
            ->addParameter('cwd', false, 'Sets the working directory for the executable')
            ->addParameter('output', false, 'Sets the location to output to')
            ->addParameter('raw', false, 'Should the output be raw (unformatted)')
            ->addParameter('successCodes', false, 'Sets the status codes allowed for a success (Array)', [0])
            ->addParameter('append', false, 'If output is set, should it append?', false)
            ->addParameter('dry_run', false, 'If set will not run command', false)
            ->addParameter('timeout', false, 'Timeout for the command', 0);
    }

    /**
     * {@inheritDoc}
     */
    public function run(OutputInterface $output)
    {
        $this->output = $output;

        if (get_class($this) === ParallelTask::class) {
            $this->output->writeln(
                ['', sprintf('    <info>[%s]</info> - <comment>Starting</comment>', $this->getName()), '']
            );
        }

        $output->writeln("<fg=blue>==============================\n</fg=blue>");

        /** @type Process[] $processes */
        $processes = [];
        foreach ($this->resolveCommands() as $command) {
            $processes[] = $this->runCommand($command);
        }

        $this->waitForCommandsToFinish($processes);

        /** @type DebugFormatterHelper $debugFormatter */
        $debugFormatter = $this->getHelperSet()->get('debug_formatter');
        foreach ($processes as $process) {
            $output->writeln(
                [
                    "",
                    $debugFormatter->stop(
                        spl_object_hash($process),
                        $process->getCommandLine(),
                        $process->isSuccessful()
                    )
                ]

            );

            if (null !== $dispatcher = $this->getEventDispatcher()) {
                $event = new PostExecuteEvent($this, $process);
                $dispatcher->dispatch(Event::POST_EXECUTE, $event);
            }

            if (!in_array($process->getExitCode(), $this->getParameter('successCodes'))) {
                throw new TaskRuntimeException($this->getName(), $process->getErrorOutput());
            }
        }

        $output->writeln("<fg=blue>==============================\n</fg=blue>");
    }

    /**
     * @param Process $process
     *
     * @return bool
     * @throws TaskRuntimeException
     * @throws \Bldr\Exception\BldrException
     * @throws \Bldr\Exception\ParameterNotFoundException
     * @throws \Bldr\Exception\RequiredParameterException
     */
    private function runCommand(Process $process)
    {
        $output = $this->output;

        /** @type DebugFormatterHelper $debugFormatter */
        $debugFormatter = $this->getHelperSet()->get('debug_formatter');
        if (null !== $dispatcher = $this->getEventDispatcher()) {
            $event = new PreExecuteEvent($this, $process);
            $dispatcher->dispatch(Event::PRE_EXECUTE, $event);

            if ($event->isPropagationStopped()) {
                $output->writeln(
                    ['', sprintf('    <info>[%s]</info> - <comment>Stopped</comment>', $this->getName()), '']
                );

                return true;
            }
        }

        if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE || $this->getParameter('dry_run')) {
            $output->writeln('        // '.$process->getCommandLine());
        }

        if ($this->getParameter('dry_run')) {
            return true;
        }

        if ($this->hasParameter('cwd')) {
            $process->setWorkingDirectory($this->getParameter('cwd'));
        }

        if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $output->writeln(
                sprintf(
                    '        // Setting timeout for %d seconds.',
                    $this->getParameter('timeout')
                )
            );
        }
        $process->setTimeout($this->getParameter('timeout') !== 0 ? $this->getParameter('timeout') : null);

        if ($this->hasParameter('output')) {
            $append = $this->hasParameter('append') && $this->getParameter('append') ? 'a' : 'w';
            $stream = fopen($this->getParameter('output'), $append);
            $output = new StreamOutput($stream, StreamOutput::VERBOSITY_NORMAL, true);
        }

        $output->writeln(
            $debugFormatter->start(
                spl_object_hash($process),
                $process->getCommandLine()
            )
        );

        $process->start();

        return $process;
    }

    /**
     * @return array|Process[]
     *
     * @throws \Bldr\Exception\ParameterNotFoundException
     * @throws \Bldr\Exception\RequiredParameterException
     */
    private function resolveCommands()
    {
        $commands = [];
        foreach ($this->getParameter('commands') as $cmd) {
            $commands[] = is_array($cmd) ? (new ProcessBuilder($cmd))->getProcess() : new Process($cmd);
        }

        return $commands;
    }

    /**
     * @param array|Process[] $processes
     */
    private function waitForCommandsToFinish(array $processes)
    {
        while (true) {
            foreach ($processes as $process) {
                if ($process->isRunning()) {
                    continue 2;
                }
            }
            break;
        }
    }
}
