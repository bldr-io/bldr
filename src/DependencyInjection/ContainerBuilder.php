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

use Bldr\Application;
use Bldr\Block;
use Bldr\Config;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder as BaseContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ContainerBuilder extends BaseContainerBuilder
{
    /**
     * @param Application     $application
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __construct(Application $application, InputInterface $input, OutputInterface $output)
    {
        parent::__construct();

        $this->set('application', $application);
        $this->set('input', $input);
        $this->set('output', $output);

        $this->compile();
    }

    /**
     * @todo Add logic to find other blocks
     */
    public function compile()
    {
        if (null !== $this->parameterBag) {
            $blocks = $this->getCoreBlocks();
            $blocks = array_merge($blocks, $this->getThirdPartyBlocks());
            foreach ($blocks as $block) {
                $this->prepareBlock($block);
            }
        }

        Config::read($this);

        parent::compile();
    }

    /**
     * @return array
     */
    private function getCoreBlocks()
    {
        return [
            new Block\Core\BldrBlock(),
            new Block\Execute\ExecuteBlock(),
            new Block\Filesystem\FilesystemBlock(),
            new Block\Notify\NotifyBlock(),
            new Block\Watch\WatchBlock(),
            new Block\Database\DatabaseBlock(),
            new Block\Database\MysqlBlock(),
            new Block\Miscellaneous\MiscellaneousBlock(),
        ];
    }

    /**
     * Gets all the third party blocks from .bldr/blocks.yml
     *
     * @return array
     */
    private function getThirdPartyBlocks()
    {
        /** @var Application $application */
        $application = $this->get('application');
        $blockFile   = $application->getEmbeddedComposer()->getExternalRootDirectory().'/.bldr/blocks.yml';

        if (!file_exists($blockFile)) {
            return [];
        }

        $blockNames = Yaml::parse(file_get_contents($blockFile));
        $blocks     = [];
        foreach ($blockNames as $block) {
            $blocks[] = new $block();
        }

        return $blocks;
    }

    /**
     * @param AbstractBlock $block
     */
    private function prepareBlock(AbstractBlock $block)
    {
        $this->registerExtension($block);
        $this->loadFromExtension($block->getAlias());
        foreach ($block->getCompilerPasses() as $pass) {
            $this->addCompilerPass($pass);
        }
    }
}
