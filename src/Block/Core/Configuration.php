<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Core;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('bldr');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('name')
                    ->defaultValue('')
                ->end()
                ->scalarNode('description')
                    ->defaultValue('')
                ->end()
                ->append($this->getProfilesNode())
                ->append($this->getTasksNode())
            ->end()
        ;

        return $treeBuilder;
    }

    private function getProfilesNode()
    {
        $treeBuilder = new TreeBuilder();
        $node        = $treeBuilder->root('profiles');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('description')
                        ->defaultValue('')
                    ->end()
                    ->arrayNode('uses')
                        ->children()
                            ->arrayNode('before')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('after')
                                ->prototype('scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode('jobs')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    private function getTasksNode()
    {
        $treeBuilder = new TreeBuilder();
        $node        = $treeBuilder->root('jobs');

        $node
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('description')->defaultValue('')->end()
                    ->arrayNode('tasks')
                        ->requiresAtLeastOneElement()
                        ->prototype('array')
                            ->ignoreExtraKeys()
                            ->children()
                                ->scalarNode('type')->isRequired()->end()
                                ->booleanNode('continueOnError')->defaultFalse()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
