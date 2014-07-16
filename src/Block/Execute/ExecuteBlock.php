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
        $this->addCall('bldr_execute.execute', 'Bldr\Block\Execute\Call\ExecuteCall');
        $this->addCall('bldr_execute.apply', 'Bldr\Block\Execute\Call\ApplyCall');

        $this->addService('bldr_execute.service.background', 'Bldr\Block\Execute\Service\BackgroundService')
            ->setPublic(false);

        $this->addCall(
            'bldr_execute.background',
            'Bldr\Block\Execute\Call\BackgroundCall',
            [new Reference('bldr_execute.service.background')]
        );
    }
}
