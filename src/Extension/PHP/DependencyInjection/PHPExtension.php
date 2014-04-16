<?php

/**
 * This file is part of bldr
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Extension\PHP\DependencyInjection;

use Bldr\DependencyInjection\AbstractExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class PHPExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $services = [
            'phpcs' => 'Bldr\Extension\PHP\Call\PHPCSCall',
            'phpmd' => 'Bldr\Extension\PHP\Call\PHPMDCall',
            'phpunit' => 'Bldr\Extension\PHP\Call\PHPUnitCall',
            'behat' => 'Bldr\Extension\PHP\Call\BehatCall'
        ];

        foreach ($services as $service => $class) {
            if (!($location = $this->findService($service))) {
                continue;
            }

            $container->setDefinition($service, new Definition($class, [$location]))
                ->addTag('bldr');
        }
    }

    /**
     * Checks for the given service, in the following order
     *
     * * getcwd()/bin/
     * * getcwd()/vendor/bin/
     * * PATH/
     *
     * @param string $service
     *
     * @return Boolean|string
     */
    private function findService($service)
    {
        $locations = [
            getcwd().'/bin',
            getcwd().'/vendor/bin',
            getenv('PATH'),
        ];

        foreach ($locations as $location) {
            if (file_exists($locations.'/'.$service)) {
                return $locations.'/'.$service;
            }
        }

        return false;
    }
}
