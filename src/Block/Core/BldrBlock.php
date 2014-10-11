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
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;

/**
 * @author Aaron Scherer <aaron@undergroundelephant.com>
 */
class BldrBlock extends AbstractBlock
{
    /**
     * {@inheritDoc}
     */
    public function assemble(array $config, SymfonyContainerBuilder $container)
    {
        $this->addTaskOptions($config, $this->originalConfiguration);

        $this->setParameter('name', $config['name']);
        $this->setParameter('description', $config['description']);
        $this->setParameter('profiles', $config['profiles']);
        $this->setParameter('jobs', $config['jobs']);

        $this->addService('bldr.dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcher');
        $this->addService('bldr.registry.job', 'Bldr\Registry\JobRegistry');
    }

    /**
     * {@inheritDoc}
     */
    public function getCompilerPasses()
    {
        return [
            new CompilerPass\CoreCompilerPass()
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getConfigurationClass()
    {
        return 'Bldr\Block\Core\Configuration';
    }

    /**
     * @param array $configuration
     * @param array $configs
     */
    private function addTaskOptions(array &$configuration, array $configs)
    {
        foreach ($configs as $config) {
            foreach ($configuration['jobs'] as $name => $job) {
                if (!isset($config['jobs'])) {
                    continue;
                }

                foreach ($job['tasks'] as $index => $task) {
                    if (isset($config['jobs'][$name], $config['jobs'][$name]['tasks'][$index])) {
                        $options = $config['jobs'][$name]['tasks'][$index];
                        $configuration['jobs'][$name]['tasks'][$index] = array_merge($task, $options);
                    }
                }
            }
        }
    }
}
