<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Extension\Notify\DependencyInjection;

use Bldr\DependencyInjection\AbstractExtension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Aaron Scherer <aaron@undergroundelephant.com>
 */
class NotifyExtension extends AbstractExtension
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
    public function load(array $config, ContainerBuilder $container)
    {
        $config = (new Processor())->processConfiguration(new Configuration(), $config);

        $notify = $container->setDefinition(
            'bldr_notify_notify',
            new Definition('Bldr\Extension\Notify\Call\NotifyCall')
        )
            ->addTag('bldr');

        if (isset($config['smtp'])) {
            $notify->addMethodCall('setSMTPInfo', [$config['smtp']]);
        }
    }
}
