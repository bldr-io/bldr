<?php

/**
 * This file is part of bldr
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Blocks\CompilerPass;

use Bldr\DependencyInjection\AbstractBlock;
use Bldr\Exception\ClassNotFoundException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class BlocksCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('bldr.vendor_blocks');

        foreach ($config as $class) {
            if (!class_exists($class)) {
                throw new ClassNotFoundException($class);
            }

            $this->prepareBlock($container, new $class);
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param AbstractBlock    $block
     */
    private function prepareBlock(ContainerBuilder $container, AbstractBlock $block)
    {
        if ($container->hasExtension($block->getAlias())) {
            return;
        }

        $container->registerExtension($block);
        $container->loadFromExtension($block->getAlias());
        foreach ($block->getCompilerPasses() as $pass) {
            $container->addCompilerPass($pass);
        }

        $container->compile();
    }
}
