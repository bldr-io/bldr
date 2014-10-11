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

use Bldr\Block\Execute\Service\BackgroundService;
use Bldr\Block\Core\Task\AbstractTask;
use Bldr\Exception\TaskRuntimeException;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Process\ProcessBuilder;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class BackgroundTask extends AbstractTask
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
            ->addParameter('executable', true, 'Executable to run')
            ->addParameter('arguments', false, 'Arguments to run on the executable', [])
            ->addParameter('cwd', false, 'Sets the working directory for the executable')
            ->addParameter('output', false, 'Sets the location to output to')
            ->addParameter('append', false, 'If output is set, should it append?', false)
            ->addParameter('kill', false, 'Are we killing the task?', false)
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function run(OutputInterface $output)
    {
        if ($this->getParameter('kill') === false) {
            $this->startProcess($output);

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
    private function startProcess(OutputInterface $output)
    {
        $arguments = $this->resolveProcessArgs();
        $name = sha1(serialize($arguments));
        if ($this->background->hasProcess($name)) {
            throw new \RuntimeException("Service is already running.");
        }

        $builder = new ProcessBuilder($arguments);

        if ($this->hasParameter('cwd')) {
            $builder->setWorkingDirectory($this->getParameter('cwd'));
        }

        $process = $builder->getProcess();

        if ($output->getVerbosity() === OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln($process->getCommandLine());
        }

        if ($this->hasParameter('output')) {
            $append = $this->hasParameter('append') && $this->getParameter('append') ? 'a' : 'w';
            $stream = fopen($this->getParameter('output'), $append);
            $output = new StreamOutput($stream, StreamOutput::VERBOSITY_NORMAL, true);
        }

        $process->start(
            function ($type, $buffer) use ($output) {
                $output->write($buffer);
            }
        );

        $this->background->addProcess($name, $process);

        if (!in_array($process->getExitCode(), $this->getParameter('successCodes'))) {
            throw new TaskRuntimeException($this->getName(), $process->getErrorOutput());
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
            [$this->getParameter('executable')],
            $this->getParameter('arguments')
        );
    }
}
