<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test\Helper;

use Bldr\Helper\DialogHelper;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
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
