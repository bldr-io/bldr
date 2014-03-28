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

use Symfony\Component\DependencyInjection\Loader\PhpFileLoader as FileLoader;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
class PhpFileLoader extends FileLoader
{
    /**
     * {@inheritDoc}
     */
    public function supports($resource, $type = null)
    {
        if (is_string($resource) && 'dist' === pathinfo($resource, PATHINFO_EXTENSION)) {
            return $this->supports(str_replace('.dist', '', $resource), $type);
        }

        return is_string($resource) && 'php' === pathinfo($resource, PATHINFO_EXTENSION);
    }
}
