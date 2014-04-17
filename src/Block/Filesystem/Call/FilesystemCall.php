<?php

/**
 * This file is part of Bldr.io
 *
 * (c) Aaron Scherer <aequasi@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE
 */

namespace Bldr\Block\Filesystem\Call;

use Bldr\Call\AbstractCall;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Aaron Scherer <aequasi@gmail.com>
 */
abstract class FilesystemCall extends AbstractCall
{
    /**
     * @var Filesystem $fileSystem
     */
    protected $fileSystem;

    /**
     *
     */
    public function __construct()
    {
        $this->fileSystem = new Filesystem();
    }

    /**
     * {@inheritDoc}
     */
    public function configure()
    {
        $this->addOption('files', true, "Files to run the filesystem command on");
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'filesystem:' . parent::getName();
    }

    /**
     * Returns an array of files or directories for the call
     *
     * @return array
     */
    protected function resolveFiles()
    {
        return $this->getOption('files');
    }
}
