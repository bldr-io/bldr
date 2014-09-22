<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test\DependencyInjection;

use Bldr\DependencyInjection\ContainerBuilder;

/**
 * @author Luis Cordova <cordoval@gmail.com>
 * @group now
 */
class ContainerBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContainerBuilder
     */
    protected $containerBuilder;
    protected $application;
    protected $input;
    protected $output;

    public function setUp()
    {
        $this->application = $this->getMockBuilder('Bldr\Application')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->input = $this->getMockBuilder('Symfony\Component\Console\Input\InputInterface')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->output = $this->getMockBuilder('Symfony\Component\Console\Output\OutputInterface')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->containerBuilder = new ContainerBuilder(
            $this->application,
            $this->input,
            $this->output
        );
    }

    public function testGetThirdPartyBlocks()
    {
        $embeddedComposer = $this->getMockBuilder('Dflydev\EmbeddedComposer\Core\EmbeddedComposer')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $config = $this->getMockBuilder('Bldr\Config')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $embeddedComposer
            ->expects($this->once())
            ->method('getExternalComposerConfig')
            ->willReturn($config)
        ;

        $this->application
            ->expects($this->once())
            ->method('getEmbeddedComposer')
            ->willReturn($embeddedComposer)
        ;

        $this->assertCount(2, $this->containerBuilder->getThirdPartyBlocks());
    }
}
