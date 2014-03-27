<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder as BaseContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ContainerBuilder extends BaseContainerBuilder
{
    /**
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag = null)
    {
        parent::__construct($parameterBag);

        if (null !== $parameterBag) {
            $extensions = $parameterBag->has('extensions') ? $parameterBag->get('extensions') : [];
            if ($extensions === null) {
                $extensions = [];
            }

            $extensions = $this->getCoreExtensions($extensions);

            foreach ($extensions as $extensionClass => $config) {
                $this->prepareExtension(new $extensionClass, null === $config ? [] : $config);
            }
        }

        $this->compile();
    }

    /**
     * @param string[] $extensions
     *
     * @return array
     */
    private function getCoreExtensions(array $extensions)
    {
        if (!isset($extensions['Bldr\DependencyInjection\BldrExtension'])) {
            $extensions['Bldr\DependencyInjection\BldrExtension'] = [];
        }
        if (!isset($extensions['Bldr\Extension\Execute\DependencyInjection\ExecuteExtension'])) {
            $extensions['Bldr\Extension\Execute\DependencyInjection\ExecuteExtension'] = [];
        }
        if (!isset($extensions['Bldr\Extension\Filesystem\DependencyInjection\FilesystemExtension'])) {
            $extensions['Bldr\Extension\Filesystem\DependencyInjection\FilesystemExtension'] = [];
        }
        if (!isset($extensions['Bldr\Extension\Notify\DependencyInjection\NotifyExtension'])) {
            $extensions['Bldr\Extension\Notify\DependencyInjection\NotifyExtension'] = [];
        }
        if (!isset($extensions['Bldr\Extension\Watch\DependencyInjection\WatchExtension'])) {
            $extensions['Bldr\Extension\Watch\DependencyInjection\WatchExtension'] = [];
        }

        return $extensions;
    }

    /**
     * @param AbstractExtension $extension
     * @param array             $config
     */
    private function prepareExtension(AbstractExtension $extension, array $config)
    {
        $this->registerExtension($extension);
        $this->loadFromExtension($extension->getAlias(), $config);
        foreach ($extension->getCompilerPasses() as $pass) {
            $this->addCompilerPass($pass);
        }
    }
}
