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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Console\Helper\DebugFormatterHelper;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ExecuteTask extends AbstractTask
{
    /**
     * Configures the Task
     */
    public function configure()
    {
        $this->setName('exec')
            ->setDescription('Executes the given options using the executable')
            ->addParameter('executable', true, 'Executable to run')
            ->addParameter('arguments', false, 'Arguments to run on the executable (Array)', [])
            ->addParameter('cwd', false, 'Sets the working directory for the executable')
            ->addParameter('output', false, 'Sets the location to output to')
            ->addParameter('raw', false, 'Should the output be raw (unformatted)')
            ->addParameter('successCodes', false, 'Sets the status codes allowed for a success (Array)', [0])
            ->addParameter('append', false, 'If output is set, should it append?', false)
            ->addParameter('dry_run', false, 'If set will not run command', false)
            ->addParameter('timeout', false, 'Timeout for the command', 0)
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function run(OutputInterface $output)
    {
        /** @type DebugFormatterHelper $debugFormatter */
        $debugFormatter = $this->getHelperSet()->get('debug_formatter');

        $arguments = $this->resolveProcessArgs();

        $builder = new ProcessBuilder($arguments);

        if (null !== $dispatcher = $this->getEventDispatcher()) {
            $event = new PreExecuteEvent($this, $builder);
            $dispatcher->dispatch(Event::PRE_EXECUTE, $event);

            if ($event->isPropagationStopped()) {
                return true;
            }
        }

        if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERY_VERBOSE) {
            $output->writeln(
                sprintf(
                    '        // Setting timeout for %d seconds.',
                    $this->getParameter('timeout')
                )
            );
        }

        $builder->setTimeout($this->getParameter('timeout') !== 0 ? $this->getParameter('timeout') : null);

        if ($this->hasParameter('cwd')) {
            $builder->setWorkingDirectory($this->getParameter('cwd'));
        }

        $process = $builder->getProcess();

        if (get_class($this) === 'Bldr\Block\Execute\Task\ExecuteTask') {
            $output->writeln(
                ['', sprintf('    <info>[%s]</info> - <comment>Starting</comment>', $this->getName()), '']
            );
        }

        if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE || $this->getParameter('dry_run')) {
            $output->writeln('        // '.$process->getCommandLine());
        }

        if ($this->getParameter('dry_run')) {
            return true;
        }

        if ($this->hasParameter('output')) {
            $append = $this->hasParameter('append') && $this->getParameter('append') ? 'a' : 'w';
            $stream = fopen($this->getParameter('output'), $append);
            $output = new StreamOutput($stream, StreamOutput::VERBOSITY_NORMAL, true);
        }

        $output->writeln("<fg=blue>==============================\n</fg=blue>");
        $output->writeln(
            $debugFormatter->start(
                spl_object_hash($process),
                $process->getCommandLine()
            )
        );

        $process->run(
            function ($type, $buffer) use ($output, $debugFormatter, $process) {
                if ($this->getParameter('raw')) {
                    $output->write($buffer, false, OutputInterface::OUTPUT_RAW);

                    return;
                }

                $output->write(
                    $debugFormatter->progress(
                        spl_object_hash($process),
                        $buffer,
                        Process::ERR === $type
                    )
                );
            }
        );

        $output->writeln(
            $debugFormatter->stop(
                spl_object_hash($process),
                $process->getCommandLine(),
                $process->isSuccessful()
            )
        );
        $output->writeln("<fg=blue>==============================</fg=blue>");

        if (null !== $dispatcher) {
            $event = new PostExecuteEvent($this, $process);
            $dispatcher->dispatch(Event::POST_EXECUTE, $event);
        }

        if (!in_array($process->getExitCode(), $this->getParameter('successCodes'))) {
            throw new TaskRuntimeException($this->getName(), $process->getErrorOutput());
        }
    }

    /**
     * Resolves the Executable and Arguments and returns a merged array
     *
     * @return array
     */
    protected function resolveProcessArgs()
    {
        return array_merge(
            [$this->getParameter('executable')],
            $this->getParameter('arguments')
        );
    }
}
