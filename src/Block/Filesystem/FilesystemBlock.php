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
        $this->addService('bldr_filesystem.abstract', 'Bldr\Block\Filesystem\Task\FilesystemTask')
            ->setAbstract(true)
        ;

        $namespace = 'Bldr\Block\Filesystem\Task\\';
        $this->addDecoratedTask('bldr_filesystem.remove', $namespace.'RemoveTask', 'bldr_filesystem.abstract');
        $this->addDecoratedTask('bldr_filesystem.mkdir', $namespace.'MkdirTask', 'bldr_filesystem.abstract');
        $this->addDecoratedTask('bldr_filesystem.touch', $namespace.'TouchTask', 'bldr_filesystem.abstract');
        $this->addDecoratedTask('bldr_filesystem.dump', $namespace.'DumpTask', 'bldr_filesystem.abstract');
    }
}
