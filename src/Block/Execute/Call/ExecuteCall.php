<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Execute\Call;

use Bldr\Call\AbstractCall;
use Bldr\Event;
use Bldr\Event\PreExecuteEvent;
use Bldr\Event\PostExecuteEvent;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ExecuteCall extends AbstractCall
{
    /**
     * Configures the Task
     */
    public function configure()
    {
        $this->setName('exec')
            ->setDescription('Executes the given options using the executable')
            ->addOption('executable', true, 'Executable to run')
            ->addOption('arguments', false, 'Arguments to run on the executable', [])
            ->addOption('cwd', false, 'Sets the working directory for the executable')
            ->addOption('output', false, 'Sets the location to output to')
            ->addOption('append', false, 'If output is set, should it append?', false)
            ->addOption('dry_run', true, 'If set, will not run command', false)
            ->addOption('timeout', true, 'Timeout for the command', 0)
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $arguments = $this->resolveProcessArgs();

        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelperSet()->get('formatter');

        $builder = new ProcessBuilder($arguments);

        $preExecuteEvent = new PreExecuteEvent($this, $builder);
        $this->getDispatcher()->dispatch(Event::PRE_EXECUTE, $preExecuteEvent);

        if ($preExecuteEvent->isPropagationStopped()) {
            return true;
        }

        if ($this->getOutput()->isVerbose()) {
            $this->getOutput()->writeln(
                sprintf(
                    "Setting timeout for %d seconds.",
                    $this->getOption('timeout') !== 0 ?: 'null'
                )
            );
        }
        $builder->setTimeout($this->getOption('timeout') !== 0 ?: null);

        if ($this->hasOption('cwd')) {
            $builder->setWorkingDirectory($this->getOption('cwd'));
        }

        $process = $builder->getProcess();

        if (get_class($this) === 'Bldr\Extension\Execute\Call\ExecuteCall') {
            $this->getOutput()->writeln(
                [
                    "",
                    $formatter->formatSection($this->getTask()->getName(), 'Starting'),
                    ""
                ]
            );
        }

        if ($this->getOutput()->isVerbose() || $this->getOption('dry_run')) {
            $this->getOutput()->writeln($process->getCommandLine());
        }

        if ($this->getOption('dry_run')) {
            return true;
        }

        if ($this->hasOption('output')) {
            $append = $this->hasOption('append') && $this->getOption('append') ? 'a' : 'w';
            $stream = fopen($this->getOption('output'), $append);
            $output = new StreamOutput($stream, StreamOutput::VERBOSITY_NORMAL, true);
        } else {
            $output = $this->getOutput();
        }

        $process->run(
            function ($type, $buffer) use ($output) {
                $output->write($buffer);
            }
        );

        $postExecuteEvent = new PostExecuteEvent($this, $process);
        $this->getDispatcher()->dispatch(Event::POST_EXECUTE, $postExecuteEvent);

        if ($this->getFailOnError()) {
            if (!in_array($process->getExitCode(), $this->getSuccessStatusCodes())) {
                throw new \Exception(
                    "Failed on the {$this->getTask()->getName()} task.\n".$process->getErrorOutput()
                );
            }
        }

        return true;
    }

    /**
     * Resolves the Executable and Arguments and returns a merged array
     *
     * @return array
     */
    protected function resolveProcessArgs()
    {
        return array_merge(
            [$this->getOption('executable')],
            $this->getOption('arguments')
        );
    }
}
