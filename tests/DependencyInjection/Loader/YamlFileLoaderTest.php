<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Test\DependencyInjection\Loader;

/**
 * @author Rob Loach <robloach@gmail.com>
 *
 * @see Bldr\DependencyInjection\Loader\YamlFileLoader
 */
class YamlFileLoaderTest extends FileLoaderTest
{
    protected $class = 'Bldr\DependencyInjection\Loader\YamlFileLoader';
    protected $extension = 'yml';
}
