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
use Bldr\DependencyInjection\ContainerBuilder;
use Bldr\Helper\DialogHelper;
use Dflydev\EmbeddedComposer\Console\Command as ComposerCmd;
use Dflydev\EmbeddedComposer\Core\EmbeddedComposerAwareInterface;
use Dflydev\EmbeddedComposer\Core\EmbeddedComposerInterface;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class Application extends BaseApplication implements EmbeddedComposerAwareInterface
{
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
     * @var ContainerInterface $container
     */
    private $container;

    /**
     * @var EmbeddedComposerInterface $embeddedComposer
     */
    private $embeddedComposer;

    /**
     * @param EmbeddedComposerInterface $embeddedComposer
     *
     * @return Application
     */
    public static function create(EmbeddedComposerInterface $embeddedComposer)
    {
        return new static($embeddedComposer);
    }

    /**
     * @param EmbeddedComposerInterface $embeddedComposer
     */
    public function __construct(EmbeddedComposerInterface $embeddedComposer)
    {
        $this->embeddedComposer = $embeddedComposer;

        $version = '@package_version@';
        if ($version === '@'.'package_version@') {
            $version = $embeddedComposer->findPackage('bldr-io/bldr')->getPrettyVersion();
        }

        parent::__construct('Bldr', $version);

        $this->addCommands($this->getCommands());
    }

    /**
     * @return Command[]
     */
    public function getCommands()
    {
        $commands = [
            new Commands\BuildCommand(),
            new Commands\Task\ListCommand(),
            new Commands\Task\InfoCommand(),
            new ComposerCmd\DumpAutoloadCommand(''),
            new ComposerCmd\InstallCommand(''),
            new ComposerCmd\UpdateCommand('')
        ];

        return $commands;
    }

    /**
     * @todo Fix config references
     */
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
        if ($command instanceof ContainerAwareInterface) {

            try {
                $this->buildContainer($input, $output);
            } catch (\Exception $e) {
                $input = new ArrayInput(['command' => 'help']);

                /** @var FormatterHelper $formatter */
                $formatter = $this->getHelperSet()->get('formatter');
                $output->writeln(
                    [
                        "\n\n",
                        $formatter->formatBlock(
                            [
                                sprintf(
                                    "Until you create a config file, bldr cant run the `%s` command.",
                                    $command->getName()
                                )
                            ],
                            "bg=red;fg=white",
                            true
                        ),
                        $e->getMessage(),
                        "\n\n"
                    ]
                );

                return $this->doRun($input, $output);
            }

            $command->setContainer($this->container);
        }

        parent::doRunCommand($command, $input, $output);
    }

    /**
     * Builds the container with extensions
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return ContainerBuilder
     *
     * @throws InvalidArgumentException
     */
    private function buildContainer(InputInterface $input, OutputInterface $output)
    {
        $this->container = new ContainerBuilder($input, $output);

        return $this->container;
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

    /**
     * @return EmbeddedComposerInterface
     */
    public function getEmbeddedComposer()
    {
        return $this->embeddedComposer;
    }

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
}
