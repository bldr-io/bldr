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
use Bldr\Block\Execute\Service\BackgroundService;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class BackgroundCall extends AbstractCall
{
    /**
     * @var BackgroundService $background
     */
    private $background;

    /**
     * @param BackgroundService $background
     */
    public function __construct(BackgroundService $background)
    {
        $this->background = $background;
    }

    /**
     * Configures the Task
     */
    public function configure()
    {
        $this->setName('background')
            ->setDescription('Executes the given options using the executable in the background')
            ->addOption('executable', true, 'Executable to run')
            ->addOption('arguments', false, 'Arguments to run on the executable', [])
            ->addOption('cwd', false, 'Sets the working directory for the executable')
            ->addOption('output', false, 'Sets the location to output to')
            ->addOption('append', false, 'If output is set, should it append?', false)
            ->addOption('kill', false, 'Are we killing the task?', false);
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        if ($this->getOption('kill') === false) {
            $this->startProcess();

            return true;
        }

        $this->endProcess();

        return true;
    }

    /**
     * Creates the given process
     *
     * @throws \Exception
     */
    private function startProcess()
    {
        $arguments = $this->resolveProcessArgs();
        $name = sha1(serialize($arguments));
        if ($this->background->hasProcess($name)) {
            throw new \RuntimeException("Service is already running.");
        }

        $builder = new ProcessBuilder($arguments);

        if ($this->hasOption('cwd')) {
            $builder->setWorkingDirectory($this->getOption('cwd'));
        }

        $process = $builder->getProcess();

        if ($this->getOutput()->isVerbose()) {
            $this->getOutput()->writeln($process->getCommandLine());
        }

        if ($this->hasOption('output')) {
            $append = $this->hasOption('append') && $this->getOption('append') ? 'a' : 'w';
            $stream = fopen($this->getOption('output'), $append);
            $output = new StreamOutput($stream, StreamOutput::VERBOSITY_NORMAL, true);
        } else {
            $output = $this->getOutput();
        }

        $process->start(
            function ($type, $buffer) use ($output) {
                $output->write($buffer);
            }
        );

        $this->background->addProcess($name, $process);

        if ($this->getFailOnError()) {
            if (!in_array($process->getExitCode(), $this->getSuccessStatusCodes())) {
                throw new \Exception(
                    "Failed on the {$this->getTask()->getName()} task.\n".$process->getErrorOutput()
                );
            }
        }
    }

    /**
     * Kills the given process
     */
    private function endProcess()
    {
        $arguments = $this->resolveProcessArgs();
        $name = sha1(serialize($arguments));
        if ($this->background->hasProcess($name)) {
            $this->background->getProcess($name)->stop();
            $this->background->removeProcess($name);
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
            [$this->getOption('executable')],
            $this->getOption('arguments')
        );
    }
}
