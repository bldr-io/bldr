<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Notify;

use Bldr\DependencyInjection\AbstractBlock;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Aaron Scherer <aaron@undergroundelephant.com>
 */
class NotifyBlock extends AbstractBlock
{
    /**
     * Loads a specific configuration.
     *
     * @param array            $config    An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     *
     * @api
     */
    public function assemble(array $config, ContainerBuilder $container)
    {
        $notify = $this->addCall('bldr_notify.notify', 'Bldr\Extension\Notify\Call\NotifyCall');

        if (isset($config['smtp'])) {
            $notify->addMethodCall('setSMTPInfo', [$config['smtp']]);
        }
    }
}
