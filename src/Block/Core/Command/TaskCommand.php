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

use Bldr\Block\Core\Task\AbstractTask;
use Bldr\Command\AbstractCommand;
use Bldr\Task\TaskInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class TaskCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('task')
            ->setDescription('Runs a single task')
            ->addArgument('name', InputArgument::REQUIRED, 'Task to use')
            ->setHelp(
                <<<EOF

The <info>%command.name%</info> runs the given task.

To use:

    <info>$ bldr %command.name% <task name></info>

To pass arguments:

    <info>$ bldr %command.name% <task name> --executable=echo --arguments=["Hello", "World"]</info>

EOF
            )
            ->ignoreValidationErrors()
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function doExecute()
    {
        $service = $this->findCall($this->input->getArgument('name'), $this->getOptions());

        $service->run($this->output);
    }

    /**
     * @param string $name
     *
     * @param array $options
     *
     * @throws \Exception
     * @return TaskInterface|AbstractTask
     */
    private function findCall($name, array $options = [])
    {
        $tasks    = array_keys($this->container->findTaggedServiceIds('bldr'));
        $services = [];
        foreach ($tasks as $serviceName) {
            /** @var TaskInterface|AbstractTask $service */

            $service = $this->container->get($serviceName);
            if ($service instanceof AbstractTask) {
                $service->configure();
            }

            if (method_exists($service, 'setEventDispatcher')) {
                $service->setEventDispatcher($this->container->get('bldr.dispatcher'));
            }

            foreach ($options as $name => $value) {
                $service->setParameter($name, $value);
            }

            if (method_exists($service, 'validate')) {
                $service->validate();
            }

            if ($service->getName() === $name) {
                $services[] = $service;
            }
        }

        if (sizeof($services) > 1) {
            throw new \Exception("Multiple calls exist with the '{$name}' tag.");
        }
        if (sizeof($services) === 0) {
            throw new \Exception("No task type found for {$name}.");
        }

        return $services[0];
    }

    /**
     * This method takes the Argv, and parses out all the options passed.
     *
     * @return array
     * @throws \Exception
     */
    private function getOptions()
    {
        $inputString = (string) $this->input;
        $options     = [];

        // Remove the first two arguments from the array (c/call and the name)
        $content = explode(' ', $inputString, 3);
        if (sizeof($content) > 2 && strpos($inputString, '=') === false) {
            throw new \Exception("Option syntax should contain an equal sign. Ex: --executable=php");
        }
        if (sizeof($content) > 2 && strpos($inputString, '--') === false) {
            throw new \Exception("Option syntax should contain double dashes. Ex: --executable=php");
        }

        // Parse out all of the options
        $pieces = explode('--', $content[2]);
        array_shift($pieces);

        foreach ($pieces as $piece) {
            $piece = trim($piece);
            list($name, $value) = explode('=', $piece, 2);

            if (strpos($value, "'[") === 0 && strpos($value, "]'") === (strlen($value) - 2)) {
                $csv   = trim(str_replace(['[', ']'], '', $value), "'");
                $value = str_getcsv($csv, ',');
            }

            $options[$name] = $value;
        }

        return $options;
    }
}
