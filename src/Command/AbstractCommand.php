<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Command;

use Bldr\Application;
use Bldr\Event\EventInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class AbstractCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface|ContainerBuilder $container
     */
    protected $container;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param string         $name
     * @param EventInterface $event
     */
    public function addEvent($name, EventInterface $event)
    {
        $this->getApplication()->addEvent($name, $event);
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return parent::getApplication();
    }
}
