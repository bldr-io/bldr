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
        $container->setDefinition(
            'bldr.dispatcher',
            new Definition('Symfony\Component\EventDispatcher\EventDispatcher')
        );

        $container->setDefinition(
            'bldr.registry.task',
            new Definition('Bldr\Registry\TaskRegistry')
        );
    }

    public function getCompilerPasses()
    {
        return [new CompilerPass\BuilderCompilerPass];
    }
}
