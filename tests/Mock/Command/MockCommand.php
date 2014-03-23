<?php

/**
 * Copyright 2014 Underground Elephant
 *
 * Distribution and reproduction are prohibited.
 *
 * @package     bldr.io
 * @copyright   Underground Elephant 2014
 * @license     No License (Proprietary)
 */

namespace Bldr\Test\Mock\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Aaron Scherer <aaron@undergroundelephant.com>
 */
class MockCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface|ContainerBuilder $container
     */
    private $container;

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

    protected function configure()
    {
        $this->setName('mock:command');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return true;
    }
}
