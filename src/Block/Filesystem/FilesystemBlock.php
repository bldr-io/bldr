<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Filesystem;

use Bldr\DependencyInjection\AbstractBlock;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class FilesystemBlock extends AbstractBlock
{
    /**
     * {@inheritDoc}
     */
    public function assemble(array $config, ContainerBuilder $container)
    {
        $this->addService('bldr_filesystem.abstract', 'Bldr\Block\Filesystem\Call\FilesystemCall')
            ->setAbstract(true)
        ;

        $namespace = 'Bldr\Block\Filesystem\Call\\';
        $this->addDecoratedCall('bldr_filesystem.remove', $namespace.'RemoveCall', 'bldr_filesystem.abstract');
        $this->addDecoratedCall('bldr_filesystem.mkdir', $namespace.'MkdirCall', 'bldr_filesystem.abstract');
        $this->addDecoratedCall('bldr_filesystem.touch', $namespace.'TouchCall', 'bldr_filesystem.abstract');
        $this->addDecoratedCall('bldr_filesystem.dump', $namespace.'DumpCall', 'bldr_filesystem.abstract');
    }
}
