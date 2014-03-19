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
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag as Config;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Helper\FormatterHelper;
use Bldr\Helper\DialogHelper;

class Application extends BaseApplication
{
    const MANIFEST_URL = 'http://bldr.io/manifest.json';

    private static $logo = <<<EOF
  ______    __       _______   ______            __    ______
 |   _  \  |  |     |       \ |   _  \          |  |  /  __  \
 |  |_)  | |  |     |  .--.  ||  |_)  |  ______ |  | |  |  |  |
 |   _  <  |  |     |  |  |  ||      /  |______||  | |  |  |  |
 |  |_)  | |  `----.|  `--`  ||  |\  \          |  | |  `--`  |
 |______/  |_______||_______/ | _| `._|         |__|  \______/
EOF;

    /**
     * @var Config $config
     */
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
        $names    = glob(__DIR__ . '/Command/*Command.php');
        $commands = [];
        foreach ($names as $name) {
            $name    = str_replace('.php', '', basename($name));
            $class   = 'Bldr\Command\\' . $name;
            $command = new $class();
            if ($command instanceof \Symfony\Component\Console\Command\Command) {
                $commands[] = $command;
            }
        }

        return $commands;
    }

    protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output)
    {
        $skipYaml = ['Bldr\Command\InitCommand', 'Symfony\Component\Console\Command\ListCommand'];
        if (!in_array(get_class($command), $skipYaml)) {
            $dir = getcwd();
            if (!file_exists($dir . '/.bldr.yml')) {
                throw new \Exception("Could not find a .bldr.yml file in the current directory.");
            }

            $this->config = new Config(Yaml::parse($dir . '/.bldr.yml'));
        }

        parent::doRunCommand($command, $input, $output);
    }

    /**
     * {@inheritDoc}
     */
    public function getHelp()
    {
        return "\n".self::$logo."\n\n".parent::getHelp();
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultHelperSet()
    {
        $helperSet = parent::getDefaultHelperSet();

        $helperSet->set(new DialogHelper());

        return $helperSet;
    }
}
