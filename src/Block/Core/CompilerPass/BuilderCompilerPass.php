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
        $container->setDefinition(
            'bldr.builder',
            new Definition(
                'Bldr\Service\Builder',
                [
                    new Reference('bldr.dispatcher'),
                    new Reference('input'),
                    new Reference('output'),
                    $this->findBldrServices($container)
                ]
            )
        );
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return Reference[]
     */
    private function findBldrServices(ContainerBuilder $container)
    {
        $serviceIds = array_keys($container->findTaggedServiceIds('bldr'));

        $services = [];
        foreach ($serviceIds as $id) {
            $services[] = new Reference($id);
        }

        return $services;
    }
}
