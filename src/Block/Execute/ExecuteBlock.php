<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Execute;

use Bldr\DependencyInjection\AbstractBlock;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ExecuteBlock extends AbstractBlock
{
    /**
     * {@inheritDoc}
     */
    public function assemble(array $config, ContainerBuilder $container)
    {
        $this->addTask('bldr_execute.execute', 'Bldr\Block\Execute\Task\ExecuteTask');
        $this->addTask('bldr_execute.parallel', 'Bldr\Block\Execute\Task\ParallelTask');
        $this->addTask('bldr_execute.apply', 'Bldr\Block\Execute\Task\ApplyTask');

        $this->addService('bldr_execute.service.background', 'Bldr\Block\Execute\Service\BackgroundService')
            ->setPublic(false)
        ;

        $this->addTask(
            'bldr_execute.background',
            'Bldr\Block\Execute\Task\BackgroundTask',
            [new Reference('bldr_execute.service.background')]
        );
    }
}
