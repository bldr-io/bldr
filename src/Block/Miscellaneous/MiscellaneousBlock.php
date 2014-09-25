<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Miscellaneous;

use Bldr\DependencyInjection\AbstractBlock;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

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
        $this->addCall('bldr_miscellaneous.sleep', 'Bldr\Block\Miscellaneous\Call\SleepCall');
        $this->addCall('bldr_miscellaneous.service', 'Bldr\Block\Miscellaneous\Call\ServiceCall');

        $this->addService('bldr_miscellaneous.service.envvar_repository', 'Bldr\Block\Miscellaneous\Service\EnvVarRepository')
            ->setPublic(false)
        ;

        $this->addCall(
            'bldr_miscellaneous.export',
            'Bldr\Block\Miscellaneous\Call\ExportCall',
            [new Reference('bldr_miscellaneous.service.envvar_repository')]
        );

        $this->addService(
            'bldr_miscellaneous.service.envvar_subscriber', 'Bldr\Block\Miscellaneous\Service\EnvVarSubscriber',
            [new Reference('bldr_miscellaneous.service.envvar_repository')]
        )
            ->setPublic(false)
            ->addTag('bldr_subscriber')
        ;
    }
}
