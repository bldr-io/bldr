<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Extension\Execute\DependencyInjection;

use Bldr\DependencyInjection\AbstractExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ExecuteExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $container->setDefinition(
            'bldr_execute.execute',
            new Definition('Bldr\Extension\Execute\Call\ExecuteCall')
        )
            ->addTag('bldr');

        $container->setDefinition(
            'bldr_execute.apply',
            new Definition('Bldr\Extension\Execute\Call\ApplyCall')
        )
            ->addTag('bldr');
    }
}
