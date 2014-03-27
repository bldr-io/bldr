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
use Bldr\DependencyInjection\AbstractExtension;
use Bldr\Helper\DialogHelper;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class Application extends BaseApplication
{
    const MANIFEST_URL = 'http://bldr.io/manifest.json';

    /**
     * @var string $BUILD_NAME
     */
    public static $BUILD_NAME;

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
     * @var ContainerInterface $container
     */
    private $container;

    /**
     * @param string $name
     * @param string $version
     */
    public function __construct($name = 'Bldr', $version = '@package_version@')
    {
        parent::__construct($name, $version);

        $this->addCommands($this->getCommands());
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
     *
     */
    public function setBuildName()
    {
        $config = $this->getConfig();
        $date   = new \DateTime('now');

        if (getenv('TRAVIS') === 'true') {
            $name = sprintf(
                "travis_%s",
                getenv('TRAVIS_JOB_NUMBER')
            );
        } else {
            $name = sprintf(
                'local_%s_%s',
                str_replace('/', '_', $config->get('name')),
                $date->format("Y-m-d_H-i-s")
            );
        }

        static::$BUILD_NAME = $name;
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
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getHelp()
    {
        return "\n" . self::$logo . "\n\n" . parent::getHelp();
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
            $this->config = Config::factory();
        }

        $this->buildContainer();
        if ($command instanceof ContainerAwareInterface) {
            $command->setContainer($this->container);
        }

        parent::doRunCommand($command, $input, $output);
    }

    /**
     * Builds the container with extensions
     *
     * @throws InvalidArgumentException
     */
    private function buildContainer()
    {
        $container = new ContainerBuilder();

        $container->getParameterBag()->add($this->getConfig()->all());

        if (null !== $this->config) {
            $extensions = $this->config->has('extensions') ? $this->config->get('extensions') : [];
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

            foreach ($extensions as $extensionClass => $config) {
                /** @var AbstractExtension $extension */
                $extension = new $extensionClass;
                if (!($extension instanceof AbstractExtension)) {
                    throw new \RuntimeException(
                        sprintf(
                            "The %s extension doesn't extend %s.",
                            $extensionClass,
                            'Bldr\DependencyInjection\AbstractExtension'
                        )
                    );
                }

                $container->registerExtension($extension);
                $container->loadFromExtension($extension->getAlias(), null === $config ? [] : $config);
                foreach ($extension->getCompilerPasses() as $pass) {
                    $container->addCompilerPass($pass);
                }
            }
        }

        $container->compile();

        $this->container = $container;

        return $container;
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
