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
        $this->addTask(
            'bldr_watch.watch',
            'Bldr\Block\Watch\Task\WatchTask',
            [
                new Reference('bldr.registry.job'),
                [
                    'profiles' => $container->getParameter('profiles'),
                    'jobs'     => $container->getParameter('jobs')
                ]
            ]
        );
    }
}
