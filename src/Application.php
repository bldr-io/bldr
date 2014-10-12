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
use Bldr\DependencyInjection\ContainerBuilder;
use Bldr\Helper\DialogHelper;
use Bldr\Output\NullBldrOutput;
use Dflydev\EmbeddedComposer\Console\Command as ComposerCmd;
use Dflydev\EmbeddedComposer\Core\EmbeddedComposerAwareInterface;
use Dflydev\EmbeddedComposer\Core\EmbeddedComposerInterface;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class Application extends BaseApplication implements EmbeddedComposerAwareInterface
{
    /**
     * @var string $BUILD_NAME
     */
    public static $BUILD_NAME;

    /**
     * @var string $logo
     */
    public static $logo = <<<EOF
  ______    __       _______   ______
 |   _  \  |  |     |       \ |   _  \
 |  |_)  | |  |     |  .--.  ||  |_)  |
 |   _  <  |  |     |  |  |  ||      /
 |  |_)  | |  `----.|  `--`  ||  |\  \
 |______/  |_______||_______/ | _| `._|
EOF;

    /**
     * @var ContainerInterface $container
     */
    private $container;

    /**
     * @var EmbeddedComposerInterface $embeddedComposer
     */
    private $embeddedComposer;

    /**
     * Are we building via shortcut
     *
     * @var bool $shortcut
     */
    private $shortcut = false;

    /**
     * @param EmbeddedComposerInterface $embeddedComposer
     *
     * @return Application
     */
    public static function create(EmbeddedComposerInterface $embeddedComposer)
    {
        return new Application($embeddedComposer);
    }

    /**
     * @param EmbeddedComposerInterface $embeddedComposer
     */
    public function __construct(EmbeddedComposerInterface $embeddedComposer)
    {
        $this->embeddedComposer = $embeddedComposer;

        parent::__construct('Bldr', $this->getBldrVersion());

        $this->addCommands($this->getCommands());
        $this->setEnvironmentVariables();
    }

    /**
     * Sets Environment Variables
     */
    private function setEnvironmentVariables()
    {
        putenv('WORK_DIR='.__DIR__);
    }

    /**
     * @return string
     */
    private function getBldrVersion()
    {
        $version = '@package_version@';
        if ($version === '@'.'package_version@') {
            $package = $this->embeddedComposer->findPackage('bldr-io/bldr');
            $version = $package->getPrettyVersion();
        }

        return $version;
    }

    /**
     * @return Command[]
     */
    public function getCommands()
    {
        return [
            new ComposerCmd\DumpAutoloadCommand(''),
            new ComposerCmd\InstallCommand(''),
            new ComposerCmd\UpdateCommand('')
        ];
    }

    public function setBuildName()
    {
        $date = new \DateTime('now');

        if (getenv('TRAVIS') === 'true') {
            $name = sprintf(
                "travis_%s",
                getenv('TRAVIS_JOB_NUMBER')
            );
        } else {
            $name = sprintf(
                'local_%s_%s',
                str_replace('/', '_', $this->container->getParameter('name')),
                $date->format("Y-m-d_H-i-s")
            );
        }

        putenv('BUILD_NAME='.$name);
        static::$BUILD_NAME = $name;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getHelp()
    {
        return "\n".self::$logo."\n\n".parent::getHelp();
    }

    /**
     * {@inheritDoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->buildContainer($input, $output);
        } catch (\Exception $e) {
            $output->write(
                [
                    "\n\n",
                    $this->getHelperSet()->get('formatter')->formatBlock(
                        " [Error] Either you have no config file, or the config file is invalid.",
                        "bg=red;fg=white",
                        true
                    )
                ]
            );

            throw $e;
        }

        return parent::doRun($input, $output);
    }

    /**
     * Builds the container with extensions
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws InvalidArgumentException
     */
    private function buildContainer(InputInterface $input, OutputInterface $output)
    {
        $nonContainerCommands = ['install', 'update', 'dumpautoload', 'help'];

        if (in_array($input->getFirstArgument(), $nonContainerCommands)) {
            return;
        }

        $this->container = new ContainerBuilder($this, $input, $output);
        $this->container->compile();
    }

    /**
     * Falls back to "run" for a shortcut
     *
     * @param string $name
     *
     * @return Command
     */
    public function find($name)
    {
        try {
            return parent::find($name);
        } catch (\InvalidArgumentException $e) {
            $this->shortcut = true;

            return parent::find('run');
        }
    }

    /**
     * Resets arguments if shortcutting
     *
     * @return \Symfony\Component\Console\Input\InputDefinition
     */
    public function getDefinition()
    {
        $definition = parent::getDefinition();
        if ($this->shortcut) {
            $definition->setArguments();
        }

        return $definition;
    }

    /**
     * @return EmbeddedComposerInterface
     */
    public function getEmbeddedComposer()
    {
        return $this->embeddedComposer;
    }

    /**
     * @return \Symfony\Component\Console\Input\InputDefinition
     */
    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOptions(
            [
                new InputOption('config-file', null, InputOption::VALUE_REQUIRED, 'Config File to use'),
                new InputOption(
                    'config-format',
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Config Format to use: '.implode(', ', Config::$TYPES)
                ),
                new InputOption('global', null, InputOption::VALUE_NONE, 'Read the global config')
            ]
        );

        return $definition;
    }

    /**
     * Adds a container to Container Aware Commands
     *
     * {@inheritDoc}
     */
    protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output)
    {
        if ($command instanceof ContainerAwareInterface) {
            $command->setContainer($this->container);
        }

        return parent::doRunCommand($command, $input, $output);
    }
}
