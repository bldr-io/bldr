<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Watch;

use Bldr\DependencyInjection\AbstractBlock;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Aaron Scherer <aaron@undergroundelephant.com>
 */
class WatchBlock extends AbstractBlock
{
    /**
     * {@inheritDoc}
     */
    public function assemble(array $config, ContainerBuilder $container)
    {
        $this->addCall(
            'bldr_watch.watch',
            'Bldr\Block\Watch\Call\WatchCall',
            [
                new Reference('bldr.registry.task'),
                [
                    'profiles' => $container->getParameter('profiles'),
                    'tasks'    => $container->getParameter('tasks')
                ]
            ]
        );
    }
}
