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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
abstract class AbstractBlock extends Extension implements BlockInterface
{
    /**
     * @var array $config
     */
    protected $config;

    /**
     * @var ContainerBuilder $container
     */
    protected $container;

    /**
     * {@inheritDoc}
     */
    final public function load(array $config, ContainerBuilder $container)
    {
        $configClass = $this->getConfigurationClass();
        if ($configClass !== false) {
            $this->config = (new Processor())->processConfiguration(new $configClass(), $config);
        }

        $this->container = $container;

        $this->assemble($this->config, $this->container);
    }

    /**
     * @return string|Boolean
     */
    protected function getConfigurationClass()
    {
        return false;
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     *
     * @return mixed
     */
    abstract protected function assemble(array $config, ContainerBuilder $container);

    /**
     * @return CompilerPassInterface[]
     */
    public function getCompilerPasses()
    {
        return [];
    }

    /**
     * @param string $name
     * @param string $class
     * @param array  $arguments
     *
     * @return Definition
     */
    protected function addCall($name, $class, array $arguments = [])
    {
        return $this->addService($name, $class, $arguments)
            ->addTag('bldr');
    }

    /**
     * @param string $name
     * @param string $class
     * @param array  $arguments
     *
     * @return Definition
     */
    protected function addService($name, $class, array $arguments = [])
    {
        return $this->container->setDefinition($name, new Definition($class, $arguments));
    }
}
