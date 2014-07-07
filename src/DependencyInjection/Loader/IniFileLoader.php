<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\DependencyInjection\Loader;

use Symfony\Component\DependencyInjection\Loader\IniFileLoader as FileLoader;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class IniFileLoader extends FileLoader
{
    use SupportsTrait;
}
