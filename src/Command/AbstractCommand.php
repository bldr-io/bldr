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
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
abstract class AbstractCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface|ContainerBuilder $container
     */
    protected $container;

    /**
     * @var InputInterface $input
     */
    protected $input;

    /**
     * @var OutputInterface $output
     */
    protected $output;

    /**
     * @type HelperSet
     */
    protected $helperSet;

    /**
     * @return int
     */
    abstract protected function doExecute();

    /**
     * {@inheritdoc}
     */
    final protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setInput($input);
        $this->getApplication()->setBuildName();
        $this->setOutput($output);
        $this->setHelperSet($this->getApplication()->getHelperSet());

        $this->doExecute();
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @param string         $name
     * @param EventInterface $event
     *
     * @return $this
     */
    public function addEvent($name, EventInterface $event)
    {
        $this->container->get('bldr.dispatcher')->dispatch($name, $event);

        return $this;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return parent::getApplication();
    }

    /**
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param InputInterface $input
     *
     * @return $this
     */
    protected function setInput(InputInterface $input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     *
     * @return $this
     */
    protected function setOutput(OutputInterface $output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @return HelperSet
     */
    public function getHelperSet()
    {
        return $this->helperSet;
    }

    /**
     * @param HelperSet $helperSet
     *
     * @return AbstractCommand
     */
    public function setHelperSet(HelperSet $helperSet)
    {
        $this->helperSet = $helperSet;

        return $this;
    }
}
