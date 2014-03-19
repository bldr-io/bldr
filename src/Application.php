<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr;

use Symfony\Component\Console\Application as BaseApplication;
use \Symfony\Component\EventDispatcher\EventDispatcher;

class Application extends BaseApplication
{
    const MANIFEST_URL = 'http://bldr.io/manifest.json';

    /**
     * @var Config $config
     private $config;

    /**
     * @var EventDispatcher $dispatcher
     */
    private $dispatcher;

    public function __construct($name = 'Bldr', $version = '@package_version@')
    {
        $this->dispatcher = new EventDispatcher();

        parent::__construct($name, $version);

        $this->addCommands($this->getCommands());
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return EventDispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @return Command[]
     */
    public function getCommands()
    {
        $names = glob(__DIR__ . '/Command/*Command.php');
        $commands = [];
        foreach ($names as $name) {
            $name = str_replace('.php', '', basename($name));
            $class = 'Bldr\Command\\' . $name;
            $command = new $class();
            if ($command instanceof \Symfony\Component\Console\Command\Command) {
                $commands[] = $command;
            }
        }

        return $commands;
    }
}
