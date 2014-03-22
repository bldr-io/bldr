<?php

/**
 * Copyright 2014 Underground Elephant
 *
 * Distribution and reproduction are prohibited.
 *
 * @package     bldr.io
 * @copyright   Underground Elephant 2014
 * @license     No License (Proprietary)
 */
 

namespace Bldr\Test\Helper;


use Bldr\Helper\DialogHelper;

class DialogHelperTest extends \PHPUnit_Framework_TestCase
{

    public function testGetQuestion()
    {
        $helper = new DialogHelper();

        $this->assertEquals(
            '<info>sample-question</info>: ',
            $helper->getQuestion('sample-question')
        );

        $this->assertEquals(
            '<info>sample-question</info> [<comment>sample-default</comment>]: ',
            $helper->getQuestion('sample-question', 'sample-default')
        );

        $this->assertEquals(
            '<info>sample-question</info>> ',
            $helper->getQuestion('sample-question', null, '>')
        );

        $this->assertEquals(
            '<info>sample-question</info> [<comment>sample-default</comment>]> ',
            $helper->getQuestion('sample-question', 'sample-default', '>')
        );

    }
}
