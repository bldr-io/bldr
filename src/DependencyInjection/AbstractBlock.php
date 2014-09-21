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

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Exception\BadMethodCallException;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
abstract class AbstractBlock extends Extension implements BlockInterface
{
    /**
     * @var array $config
     */
    protected $config = [];

    /**
     * @var SymfonyContainerBuilder $container
     */
    protected $container;

    /**
     * @var array $originalConfiguration
     */
    protected $originalConfiguration;

    /**
     * {@inheritDoc}
     */
    final public function load(array $config, SymfonyContainerBuilder $container)
    {
        $this->originalConfiguration = $config;

        $configClass = $this->getConfigurationClass();
        if ($configClass !== false) {
            $this->config = (new Processor())->processConfiguration(new $configClass(), $config);
        }

        $this->container = $container;

        $this->assemble($this->config, $this->container);
    }

    /**
     * @return string|bool
     */
    protected function getConfigurationClass()
    {
        return false;
    }

    /**
     * @param array                   $config
     * @param SymfonyContainerBuilder $container
     *
     * @return mixed
     */
    abstract protected function assemble(array $config, SymfonyContainerBuilder $container);

    /**
     * @return CompilerPassInterface[]
     */
    public function getCompilerPasses()
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        $className = get_class($this);
        if (substr($className, -5) != 'Block') {
            throw new BadMethodCallException(
                'This extension does not follow the naming convention; you must overwrite the getAlias() method.'
            );
        }
        $classBaseName = substr(strrchr($className, '\\'), 1, -5);

        return Container::underscore($classBaseName);
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
            ->addTag('bldr')
        ;
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

    /**
     * @param $name
     * @param $class
     * @param $parentName
     * @param array $arguments
     *
     * @return Definition
     */
    protected function addDecoratedCall($name, $class, $parentName, array $arguments = [])
    {
        return $this->container->setDefinition($name, new DefinitionDecorator($parentName))
            ->setClass($class)
            ->setArguments($arguments)
            ->addTag('bldr')
        ;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    protected function setParameter($name, $value)
    {
        $this->container->setParameter($name, $value);
    }
}
