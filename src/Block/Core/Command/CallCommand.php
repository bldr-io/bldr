<?php

/**
 * This file is part of bldr
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Core\Command;

use Bldr\Call\AbstractCall;
use Bldr\Command\AbstractCommand;
use Bldr\Model\Task;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArgvInput;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class CallCommand extends AbstractCommand
{
    protected function configure()
    {
        $this->setName('call')
            ->setDescription('Runs a single call')
            ->addArgument('name', InputArgument::REQUIRED, 'Call to use')
            ->setHelp(
                <<<EOF

The <info>%command.name%</info> runs the given call.

To use:

    <info>$ bldr %command.name% <call name></info>

To pass arguments:

    <info>$ bldr %command.name% <call name> --executable=echo --arguments=["Hello", "World"]</info>

EOF
            )
            ->ignoreValidationErrors();
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $this->findCall($input->getArgument('name'));

        $options = $this->getOptions((string)$input);

        $task = new Task('no-task', '', false, [array_merge(['type' => $input->getArgument('name')], $options)]);
        $call = $task->getCalls()[0];

        $service->initialize($input, $output, $this->getHelperSet(), $task, $call);
        return $service->run();
    }

    /**
     * @param string $name
     *
     * @return AbstractCall
     * @throws \Exception
     */
    private function findCall($name)
    {
        $calls    = array_keys($this->container->findTaggedServiceIds('bldr'));
        $services = [];
        foreach ($calls as $serviceName) {
            /** @var AbstractCall $service */
            $service = $this->container->get($serviceName);
            $service->configure();

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
     * @param string $inputString
     *
     * @return array
     * @throws \Exception
     */
    private function getOptions($inputString)
    {
        $options = [];

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
                $csv = trim(str_replace(['[', ']'], '', $value), "'");
                $value = str_getcsv($csv, ',');
            }

            $options[$name] = $value;
        }

        return $options;
    }
}
