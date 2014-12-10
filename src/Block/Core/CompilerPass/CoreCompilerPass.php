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

use Bldr\Application;
use Bldr\Block\Core\Command as CoreCommand;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class CoreCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->buildBuilder($container);
        $this->addSubscribers($container);
        $this->buildTaskRegistry($container);

        /** @var Application $application */
        $application = $container->get('application');

        $application->addCommands(
            [
                new CoreCommand\RunCommand(),
                new CoreCommand\TaskCommand(),
                new CoreCommand\Task\InfoCommand(),
                new CoreCommand\Task\ListCommand()
            ]
        );
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

    /**
     * @param ContainerBuilder $container
     */
    private function buildBuilder(ContainerBuilder $container)
    {
        $container->setDefinition(
            'bldr.builder',
            new Definition(
                'Bldr\Block\Core\Service\Builder',
                [
                    new Reference('bldr.dispatcher'),
                    new Reference('output'),
                    new Reference('helper_set'),
                    new Reference('bldr.registry.task')
                ]
            )
        );
    }

    /**
     * @param ContainerBuilder $container
     */
    private function addSubscribers(ContainerBuilder $container)
    {
        $dispatcher = $container->findDefinition('bldr.dispatcher');
        foreach ($this->findBldrServicesTaggedWith($container, 'bldr_subscriber') as $subscriber) {
            $dispatcher->addMethodCall('addSubscriber', [$subscriber]);
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    private function buildTaskRegistry(ContainerBuilder $container)
    {
        $container->setDefinition(
            'bldr.registry.task',
            new Definition(
                'Bldr\Registry\TaskRegistry',
                [
                    new Reference('bldr.dispatcher'),
                    $this->findBldrServicesTaggedWith($container, 'bldr')
                ]
            )
        );
    }
}
