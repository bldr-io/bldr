<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Core;

use Bldr\DependencyInjection\AbstractBlock;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Aaron Scherer <aaron@undergroundelephant.com>
 */
class BldrBlock extends AbstractBlock
{
    /**
     * {@inheritDoc}
     */
    protected function getConfigurationClass()
    {
        return 'Bldr\Block\Core\Configuration';
    }

    /**
     * {@inheritDoc}
     */
    public function assemble(array $config, SymfonyContainerBuilder $container)
    {
        $this->addCallOptions($config, $this->originalConfiguration);

        $this->setParameter('name', $config['name']);
        $this->setParameter('description', $config['description']);
        $this->setParameter('profiles', $config['profiles']);
        $this->setParameter('tasks', $config['tasks']);

        $this->addService('bldr.dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcher');
        $this->addService('bldr.registry.task', 'Bldr\Registry\TaskRegistry');
    }

    private function addCallOptions(array &$configuration, array $configs)
    {
        foreach ($configs as $config) {
            foreach ($configuration['tasks'] as $name => $task) {
                if (!isset($config['tasks'])) {
                    continue;
                }

                foreach ($task['calls'] as $index => $call) {
                    if (isset($config['tasks'][$name], $config['tasks'][$name]['calls'][$index])) {
                        $options                                        = $config['tasks'][$name]['calls'][$index];
                        $configuration['tasks'][$name]['calls'][$index] = array_merge($call, $options);
                    }
                }
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getCompilerPasses()
    {
        return [
            new CompilerPass\BuilderCompilerPass,
            new CompilerPass\CommandCompilerPass
        ];
    }
}
