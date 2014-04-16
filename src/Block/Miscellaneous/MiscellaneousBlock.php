<?php

/**
 * This file is part of bldr
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Miscellaneous;

use Bldr\DependencyInjection\AbstractBlock;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class MiscellaneousBlock extends AbstractBlock
{
    /**
     * {@inheritDoc}
     */
    public function assemble(array $config, ContainerBuilder $container)
    {
        $services = [
            'bldr_miscellaneous.sleep' => 'Bldr\Extension\Miscellaneous\Call\SleepCall',
            'bldr_miscellaneous.service' => 'Bldr\Extension\Miscellaneous\Call\ServiceCall',
        ];

        foreach ($services as $name => $class) {
            $this->addCall($name, $class);
        }
    }
}
