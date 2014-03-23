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

use Bldr\Command as Commands;
use Bldr\Helper\DialogHelper;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag as Config;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Application extends BaseApplication
{
    const MANIFEST_URL = 'http://bldr.io/manifest.json';

    /**
     * @var string $configName
     */
    public static $CONFIG = '.bldr.yml';

    public static $logo = <<<EOF
  ______    __       _______   ______
 |   _  \  |  |     |       \ |   _  \
 |  |_)  | |  |     |  .--.  ||  |_)  |
 |   _  <  |  |     |  |  |  ||      /
 |  |_)  | |  `----.|  `--`  ||  |\  \
 |______/  |_______||_______/ | _| `._|
EOF;

    /**
     * @var Config $config
     */
    private $config;

    /**
     * @var EventDispatcher $dispatcher
     */
    private $dispatcher;

    /**
     * @var ContainerInterface $container
     */
    private $container;

    /**
     * @param string $name
     * @param string $version
     */
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
        $commands   = [];
        $commands[] = new Commands\InitCommand();
        $commands[] = new Commands\BuildCommand();

        return $commands;
    }

    /**
     * Loads the config for the necessary commands, and sets the container for classes that need it.
     *
     * @param Command         $command
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void
     */
    protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output)
    {
        $skipYaml = ['Bldr\Command\InitCommand', 'Symfony\Component\Console\Command\ListCommand'];
        if (!in_array(get_class($command), $skipYaml)) {
            $this->config = $this->readConfig();
        }

        $this->buildContainer();
        if ($command instanceof ContainerAwareInterface) {
            $command->setContainer($this->container);
        }

        parent::doRunCommand($command, $input, $output);
    }

    /**
     * Cehcks for .bldr.yml and then .bldr.yml.dist
     *
     * @return Config
     * @throws \Exception
     */
    private function readConfig()
    {
        $dir = getcwd();
        $file = $dir . '/' . static::$CONFIG;
        if (!file_exists($file)) {
            $file .= '.dist';
            if (!file_exists($file)) {
                throw new \Exception(
                    sprintf(
                        "Could not find a %s or a %s file in the current directory.",
                        static::$CONFIG,
                        static::$CONFIG . '.dist'
                    )
                );
            }
        }

        return new Config(Yaml::parse($file));
    }

    /**
     * Builds the container with extensions
     *
     * @throws InvalidArgumentException
     */
    private function buildContainer()
    {
        $container  = new ContainerBuilder();

        /** @var ExtensionInterface[] $extensions */
        $extensions = [
            new Extension\Execute\DependencyInjection\ExecuteExtension(),
            new Extension\Filesystem\DependencyInjection\FilesystemExtension(),
        ];

        if (null !== $this->config && $this->config->has('extensions')) {
            foreach ($this->config->get('extensions') as $extensionClass => $arguments) {
                $extensions[] = new $extensionClass($arguments);
            }
        }

        foreach ($extensions as $extension) {
            $container->registerExtension($extension);
            $container->loadFromExtension($extension->getAlias());
        }

        $container->compile();

        $this->container = $container;

        return $container;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getHelp()
    {
        return "\n" . self::$logo . "\n\n" . parent::getHelp();
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
