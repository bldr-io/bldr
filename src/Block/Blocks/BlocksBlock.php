<?php

/**
 * This file is part of bldr
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Blocks;

use Bldr\Block\Blocks\CompilerPass\BlocksCompilerPass;
use Bldr\DependencyInjection\AbstractBlock;
use Bldr\DependencyInjection\ContainerBuilder;
use Bldr\Exception\ClassNotFoundException;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class BlocksBlock extends AbstractBlock
{
    /**
     * {@inheritDoc}
     */
    protected function getConfigurationClass()
    {
        return 'Bldr\Block\Blocks\Configuration';
    }

    /**
     * {@inheritDoc}
     */
    public function getCompilerPasses()
    {
        return [new BlocksCompilerPass()];
    }

    /**
     * {@inheritDoc}
     */
    protected function assemble(array $config, SymfonyContainerBuilder $container)
    {
        $container->setParameter('bldr.vendor_blocks', $config);
    }
}
