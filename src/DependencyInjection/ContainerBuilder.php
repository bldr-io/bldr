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

use Bldr\Config;
use Bldr\Extension;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DependencyInjection\ContainerBuilder as BaseContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;


/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ContainerBuilder extends BaseContainerBuilder
{
    /**
     * @param ParameterBagInterface|null $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag = null)
    {
        parent::__construct($parameterBag);
        $this->compile();
    }

    /**
     *
     */
    public function compile()
    {
        if (null !== $this->parameterBag) {
            $extensions = $this->getCoreExtensions();

            foreach ($extensions as $extension) {
                $this->prepareExtension($extension);
            }
        }

        Config::read($this);

        parent::compile();
    }

    /**
     * @return array
     */
    private function getCoreExtensions()
    {
        $extensions = [
            new BldrExtension(),
            new Extension\Execute\DependencyInjection\ExecuteExtension(),
            new Extension\Filesystem\DependencyInjection\FilesystemExtension(),
            new Extension\Notify\DependencyInjection\NotifyExtension(),
            new Extension\Watch\DependencyInjection\WatchExtension(),
            new Extension\Database\DependencyInjection\DatabaseExtension(),
            new Extension\Database\DependencyInjection\MysqlExtension(),
            new Extension\Miscellaneous\DependencyInjection\MiscellaneousExtension(),
        ];

        return $extensions;
    }

    /**
     * @param AbstractExtension $extension
     */
    private function prepareExtension(AbstractExtension $extension)
    {
        $this->registerExtension($extension);
        $this->loadFromExtension($extension->getAlias());
        foreach ($extension->getCompilerPasses() as $pass) {
            $this->addCompilerPass($pass);
        }
    }
}
