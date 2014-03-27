<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Extension\Filesystem\Call;

use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
abstract class FilesystemCall extends \Bldr\Call\AbstractCall
{
    /**
     * @var Filesystem $fileSystem
     */
    protected $fileSystem;

    public function __construct()
    {
        $this->fileSystem = new Filesystem();
    }
}
