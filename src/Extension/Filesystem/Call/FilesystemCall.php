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

    /**
     * Returns an array of files or directories for the call
     *
     * @return array
     */
    protected function resolveFiles()
    {
        if (!$this->getCall()->has('files') && !is_array($this->getCall()->files)) {
            throw new \RuntimeException(
                'The File System Task requires an array of directories or files'
            );
        }

        return $this->getCall()->files;
    }
}
