<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Aaron Scherer <aaron@undergroundelephant.com>
 */
class BldrExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $config, SymfonyContainerBuilder $container)
    {
        $configuration = (new Processor())->processConfiguration(new Configuration(), $config);
        $this->addCallOptions($configuration, $config);

        $container->setParameter('name', $configuration['name']);
        $container->setParameter('description', $configuration['description']);
        $container->setParameter('profiles', $configuration['profiles']);
        $container->setParameter('tasks', $configuration['tasks']);

        $container->setDefinition(
            'input',
            new Definition('Symfony\Component\Console\Input\ArgvInput')
        );

        $container->setDefinition(
            'output',
            new Definition('Symfony\Component\Console\Output\ConsoleOutput')
        );

        $container->setDefinition(
            'bldr.dispatcher',
            new Definition('Symfony\Component\EventDispatcher\EventDispatcher')
        );

        $container->setDefinition(
            'bldr.registry.task',
            new Definition('Bldr\Registry\TaskRegistry')
        );
    }

    private function addCallOptions(array &$configuration, array $config)
    {
        foreach ($configuration['tasks'] as $name => $task) {
            foreach ($task['calls'] as $index => $call) {
                $options                                        = $config[0]['tasks'][$name]['calls'][$index];
                $configuration['tasks'][$name]['calls'][$index] = array_merge($call, $options);
            }
        }
    }

    public function getCompilerPasses()
    {
        return [new CompilerPass\BuilderCompilerPass];
    }
}
