<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Core\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class BuilderCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->buildBuilder($container);
        $this->addSubscribers($container);
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $tagName
     *
     * @return Reference[]
     */
    private function findBldrServicesTaggedWith(ContainerBuilder $container, $tagName)
    {
        $serviceIds = array_keys($container->findTaggedServiceIds($tagName));

        $services = [];
        foreach ($serviceIds as $id) {
            $services[] = new Reference($id);
        }

        return $services;
    }

    private function buildBuilder(ContainerBuilder $container)
    {
        $container->setDefinition(
            'bldr.builder',
            new Definition(
                'Bldr\Service\Builder',
                [
                    new Reference('bldr.dispatcher'),
                    new Reference('input'),
                    new Reference('output'),
                    $this->findBldrServicesTaggedWith($container, 'bldr')
                ]
            )
        );
    }

    private function addSubscribers(ContainerBuilder $container)
    {
        $dispatcher = $container->findDefinition('bldr.dispatcher');
        foreach ($this->findBldrServicesTaggedWith($container, 'bldr_subscriber') as $subscriber) {
            $dispatcher->addMethodCall('addSubscriber', [$subscriber]);
        }
    }
}
