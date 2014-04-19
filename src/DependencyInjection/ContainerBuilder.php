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

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ContainerBuilder extends BaseContainerBuilder
{
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct();

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
            new Block\Blocks\BlocksBlock(),
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
