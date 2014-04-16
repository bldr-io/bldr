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

use Bldr\Config;
use Bldr\Block;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DependencyInjection\ContainerBuilder as BaseContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;


/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class ContainerBuilder extends BaseContainerBuilder
{
    /**
     * @param ParameterBagInterface|null $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag = null)
    {
        parent::__construct($parameterBag);
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
            new BldrBlock(),
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
     * @param AbstractExtension $extension
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
