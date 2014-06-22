<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Helper;

use Symfony\Component\Console\Helper\DialogHelper as BaseDialogHelper;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class DialogHelper extends BaseDialogHelper
{
    /**
     * Build text for asking a question. For example:
     *
     *  "Do you want to continue [yes]:"
     *
     * @param string $question The question you want to ask
     * @param mixed  $default  Default value to add to message, if false no default will be shown
     * @param string $sep      Separation char for between message and user input
     *
     * @return string
     */
    public function getQuestion($question, $default = null, $sep = ':')
    {
        return $default !== null ?
            sprintf('<info>%s</info> [<comment>%s</comment>]%s ', $question, $default, $sep) :
            sprintf('<info>%s</info>%s ', $question, $sep)
        ;
    }
}
